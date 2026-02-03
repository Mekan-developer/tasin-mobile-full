<template>
  <div class="category-page">
    <!-- PWA: хедер страницы не двигается при pull-to-refresh; спиннер показывается ниже -->
    <header v-if="categoryName" class="category-page-header" :class="{ 'is-pwa': isPwa }">
      <HeaderItem :categoryName="categoryName" :productCount="products.length" />
    </header>
    <div class="category-page-body" :class="{ 'has-fixed-header': isPwa && categoryName }">
      <!-- Спиннер только в PWA, ниже HeaderItem; высота 0–56px при потягивании -->
      <div
        v-if="isPwa && (pageIsRefreshing || pagePullDistance > 0)"
        class="category-page-pull-loader"
        :style="{ height: (pageIsRefreshing ? 56 : Math.min(pagePullDistance, 56)) + 'px' }"
        aria-hidden="true"
      >
        <span class="category-page-pull-spinner" :class="{ 'is-active': pageIsRefreshing || pagePullDistance > 20 }" />
      </div>

      <div v-if="productsStore.loading">Загрузка...</div>
    <div v-else-if="productsStore.error">{{ productsStore.error }}</div>
    <div v-else class="products-list h-auto min-h-0" :class="{ 'any-variants-expanded': variantsExpandedCount > 0 }">
        <div v-for="product in products" :key="product.id" :id="`product-${product.id}`" class="product-card">
          <div class="product-main-content">
            <h3 class="product-name">{{ product.name }}</h3>

            <div class="product-image-container">
              <SwaggerItem
                :images="product.images"
                :activeImage="product.activeImage || 0"
                :productName="product.name"
                :productPrice="formatPriceWithCurrency(product.price, product.currency)"
                :resolve-image-path="resolveImagePath"
                @update:activeImage="(idx) => product.activeImage = idx"
                @openImage="openFullImage(product)"
              />
            </div>

            <LinksItem
              :productId="+product.id"
              :productName="product.name"
              :productDescription="product.description"
              :viewCount="product.views"
              :isInCart="isInCart(product.id)"
              :cartQuantity="getCartQuantity(product.id)"
              @toggleCart="toggleCart(product)"
              @share="shareProduct(product)"
              @copyLink="copyProductLink(product)"
            />
          </div>

          <div v-if="product.description" class="product-description py-2 px-4 bg-white/5 border border-white/10 h-full">
            <p class="text-gray-300 leading-tight">
              {{ product.description }}
            </p>
          </div>

          <VariantsItem :variants="product.variants || []" @expanded-change="onVariantsExpandedChange" />
        </div>

    </div>
    </div>

    <ImageModal
      v-model="isModalOpen"
      :product="currentModalProduct"
      :resolve-image-path="resolveImagePath"
      @update:active-image="(idx) => {
      const p = products.find(pr => pr.id === activeModalProductId)
      if (p) p.activeImage = idx
      }"
      />

    <!-- Диалог «Поделиться»: список приложений, когда нет Web Share API -->
    <Teleport to="body">
      <div v-if="shareDialogOpen" class="share-dialog-backdrop" @click.self="shareDialogOpen = false">
        <div class="share-dialog" role="dialog" aria-label="Поделиться">
          <h3 class="share-dialog-title">Отправить в</h3>
          <div class="share-dialog-actions">
            <button type="button" class="share-dialog-btn" @click="shareCopyLink">
              <span class="share-dialog-btn-icon">📋</span>
              <span>Скопировать ссылку</span>
            </button>
            <button type="button" class="share-dialog-btn" @click="shareViaWhatsApp">
              <span class="share-dialog-btn-icon">WhatsApp</span>
              <span>WhatsApp</span>
            </button>
            <button type="button" class="share-dialog-btn" @click="shareViaTelegram">
              <span class="share-dialog-btn-icon">Telegram</span>
              <span>Telegram</span>
            </button>
            <button type="button" class="share-dialog-btn" @click="shareViaGmail">
              <span class="share-dialog-btn-icon">Gmail</span>
              <span>Gmail</span>
            </button>
            <button type="button" class="share-dialog-btn" @click="shareViaImo">
              <span class="share-dialog-btn-icon">IMO</span>
              <span>IMO</span>
            </button>
          </div>
          <button type="button" class="share-dialog-cancel" @click="shareDialogOpen = false">Отмена</button>
        </div>
      </div>
    </Teleport>

    <!-- Toast Notification Component -->
    <NotificationView
      :message="toastMessage"
      :type="toastType"
      @close="handleToastClose"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick, watch, onUnmounted, inject } from 'vue'
import { useRoute } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import HeaderItem from '@/components/items/HeaderItem.vue'
import SwaggerItem from '@/components/items/SwaggerItem.vue'
import LinksItem from '@/components/items/LinksItem.vue'
import VariantsItem from '@/components/items/VariantsItem.vue'
import NotificationView from '@/components/common/NotificationView.vue'
import ImageModal from '@/components/common/ImageModal.vue'
import { useCategoryStore } from '@/stores/category'
import { useProductsStore } from '@/stores/product'
import { formatPriceWithCurrency } from '@/composables/usePriceDisplay'

const mainLayout = inject('mainLayout', null)
/** PWA: standalone (установленное приложение) — хедер фиксирован, спиннер под ним */
const isPwa = computed(() => {
  if (typeof window === 'undefined') return false
  return window.matchMedia('(display-mode: standalone)').matches || !!window.navigator.standalone
})
const pageIsRefreshing = computed(() => mainLayout?.isRefreshing?.value ?? false)
const pagePullDistance = computed(() => mainLayout?.pullDistance?.value ?? 0)

const productsStore = useProductsStore()
const products = computed(() => productsStore.products)

/** Количество карточек с раскрытым блоком «Доступные модели» — для выравнивания сетки */
const variantsExpandedCount = ref(0)
function onVariantsExpandedChange(expanded) {
  variantsExpandedCount.value = Math.max(0, variantsExpandedCount.value + (expanded ? 1 : -1))
}
watch(products, () => { variantsExpandedCount.value = 0 }, { deep: true })

/** Обновление списка при pull-to-refresh в MainLayout */
async function doRefresh() {
  await productsStore.fetchProductsByCategory(categoryId.value, true)
}

onMounted(async () => {
  await loadCategoryNames()
  await productsStore.fetchProductsByCategory(categoryId.value)
  mainLayout?.registerRefresh(doRefresh)
})
onUnmounted(() => mainLayout?.unregisterRefresh?.())

const route = useRoute()
const categoryNames = ref([])
const categoryId = computed(() => route.params.id)
watch(categoryId, (id) => {
  productsStore.fetchProductsByCategory(id, true)
})


const categoryStore = useCategoryStore()
async function loadCategoryNames() {
  try {
    categoryNames.value = Array.isArray(categoryStore.categories) ? categoryStore.categories : []
  } catch (e) {
    categoryNames.value = []
  }
}

const categoryName = computed(() => {
  const list = categoryNames.value
  if (!Array.isArray(list)) return 'Категория'
  const c = list.find((cat) => String(cat.id) === String(categoryId.value))
  return c?.name ?? 'Категория'
})

// async function loadProducts() {
//   const id = categoryId.value
//   if(!id){
//     products.value = []
//     return
//   }
//   try {
//     const { data } = await axios.get(`${API_BASE}/products?category_id=${id}`)
//     const list = Array.isArray(data) ? data : []
//     // Нормализация: у каждого продукта есть images[] и activeImage
//     products.value = list.map((p) => ({
//       ...p,
//       images: p.images ?? [],
//       activeImage: p.activeImage ?? 0
//     }))
//   } catch (e) {
//     products.value = []
//   }
// }



const toastMessage = ref('')
const toastType = ref('success')

const showToast = (message, type = 'success') => {
  toastMessage.value = message
  toastType.value = type
}

const handleToastClose = () => {
  toastMessage.value = ''
}

const currentModalProduct = computed(() =>
  products.value.find(p => p.id === activeModalProductId.value) ?? null
)

const cartStore = useCartStore()
const isInCart = (productId) => cartStore.isInCart(productId)
const addToCart = (product) => cartStore.addToCart(product)
const removeFromCart = (productId) => cartStore.removeFromCart(productId)

const isModalOpen = ref(false)
const activeModalProductId = ref(null)

const openFullImage = (product) => {
  activeModalProductId.value = product.id
  isModalOpen.value = true
}

const getCartQuantity = (productId) => cartStore.getItemQuantity(productId)

const toggleCart = (product) => {
  if (isInCart(product.id)) {
    removeFromCart(product.id)
    showToast('Товар удален из корзины', 'info')
  } else {
    addToCart(product)
    showToast('Товар добавлен в корзину!', 'success')
  }
}


const copyProductLink = async (product) => {
  const productUrl = `${window.location.origin}${route.path}#product-${product.id}`

  try {
    await navigator.clipboard.writeText(productUrl)
    showToast('Ссылка скопирована!', 'success')
  } catch (e) {
    // Fallback for older browsers
    const textArea = document.createElement('textarea')
    textArea.value = productUrl
    textArea.style.position = 'fixed'
    textArea.style.opacity = '0'
    document.body.appendChild(textArea)
    textArea.select()
    console.log(e)
    try {
      document.execCommand('copy')
      showToast('Ссылка скопирована!', 'success')
    } catch (e) {
      console.error('Failed to copy:', e)
    }
    document.body.removeChild(textArea)
  }
}

const scrollToHash = (hash) => {
  if (!hash) return

  const cleanHash = hash.startsWith('#') ? hash : `#${hash}`

  const tryScroll = (attempts = 0) => {
    if (attempts > 15) {
      console.warn('Failed to find element:', cleanHash)
      return
    }

    const productElement = document.querySelector(cleanHash)
    if (productElement) {
      setTimeout(() => {
        const headerOffset = 100
        const elementPosition = productElement.getBoundingClientRect().top
        const offsetPosition = elementPosition + window.pageYOffset - headerOffset

        window.scrollTo({
          top: offsetPosition,
          behavior: 'smooth'
        })

        productElement.style.transition = 'box-shadow 0.3s'
        productElement.style.boxShadow = '0 0 0 4px rgba(33, 150, 243, 0.3)'
        setTimeout(() => {
          productElement.style.boxShadow = ''
        }, 2000)
      }, 150)
    } else {
      setTimeout(() => tryScroll(attempts + 1), 300)
    }
  }

  tryScroll()
}

const handleHashChange = () => {
  const hash = window.location.hash || route.hash
  if (hash) {
    nextTick(() => {
      scrollToHash(hash)
    })
  }
}

watch(() => route.hash, (newHash) => {
  if (newHash) {
    nextTick(() => {
      scrollToHash(newHash)
    })
  }
})

watch(() => window.location.hash, (newHash) => {
  if (newHash) {
    nextTick(() => {
      scrollToHash(newHash)
    })
  }
}, { immediate: true })

onMounted(() => {
  const hash = window.location.hash || route.hash
  if (hash) {
    nextTick(() => {
      scrollToHash(hash)
    })

    setTimeout(() => {
      scrollToHash(hash)
    }, 800)
  }

  window.addEventListener('hashchange', handleHashChange)
})

onUnmounted(() => {
  window.removeEventListener('hashchange', handleHashChange)
})






/** Резолв пути картинки: имя файла из API → URL из assets/img */
const resolveImagePath = (path) => {
  if (!path) return ''
  const name = path.startsWith('@/') ? path.split('/').pop() : path
  try {
    return new URL(`../assets/img/${name}`, import.meta.url).href
  } catch {
    return path
  }
}

watch(products, () => {
  if (products.value.length > 0) {
    nextTick(() => {
      const hash = window.location.hash
      if (hash) {
        scrollToHash(hash)
      }
    })
  }
}, { immediate: true })

/** Ссылка на товар для шаринга (с хешем) */
function getProductShareUrl(product) {
  return `${window.location.origin}${route.path}#product-${product.id}`
}

const shareDialogOpen = ref(false)
/** Данные для шаринга: title, text, url, price, imageUrl (для fallback и текста с ценой) */
const sharePayload = ref({ title: '', text: '', url: '', price: null, imageUrl: '' })

/** Текст для шаринга: название, цена, описание */
function getShareText(product) {
  const parts = [product.name]
  if (product.price != null && product.price !== '') {
    parts.push(`Цена: ${formatPriceWithCurrency(product.price, product.currency)}`)
  }
  if (product.description) parts.push(product.description)
  return parts.join('\n')
}

/** URL первого изображения товара для шаринга */
function getProductImageUrl(product) {
  const img = product.images?.[product.activeImage ?? 0] || product.images?.[0]
  return img ? resolveImagePath(img) : ''
}

const shareProduct = async (product) => {
  const url = getProductShareUrl(product)
  const text = getShareText(product)
  const imageUrl = getProductImageUrl(product)
  const shareData = {
    title: product.name,
    text,
    url
  }

  try {
    if (navigator.share) {
      let files = []
      if (imageUrl) {
        try {
          const res = await fetch(imageUrl)
          const blob = await res.blob()
          const file = new File([blob], 'product.jpg', { type: blob.type || 'image/jpeg' })
          files = [file]
        } catch (_) {}
      }
      if (files.length) Object.assign(shareData, { files })
      await navigator.share(shareData)
    } else {
      sharePayload.value = {
        title: product.name,
        text,
        url,
        price: product.price,
        priceFormatted: formatPriceWithCurrency(product.price, product.currency),
        imageUrl
      }
      shareDialogOpen.value = true
    }
  } catch (err) {
    if (err?.name !== 'AbortError') console.log('Error sharing:', err)
  }
}

/** Fallback: скопировать текст (название, цена, описание) и ссылку (clipboard API + fallback) */
function shareCopyLink() {
  const p = sharePayload.value
  const text = [p.text, p.url || window.location.href].filter(Boolean).join('\n')
  const done = () => {
    showToast('Ссылка скопирована!', 'success')
    shareDialogOpen.value = false
  }
  const fail = () => {
    showToast('Не удалось скопировать', 'error')
  }
  if (navigator.clipboard?.writeText) {
    navigator.clipboard.writeText(text).then(done).catch(() => {
      copyViaTextarea(text) ? done() : fail()
    })
  } else {
    copyViaTextarea(text) ? done() : fail()
  }
}

/** Копирование в буфер через временный textarea (работает без HTTPS в части контекстов) */
function copyViaTextarea(text) {
  try {
    const ta = document.createElement('textarea')
    ta.value = text
    ta.style.position = 'fixed'
    ta.style.opacity = '0'
    ta.style.left = '-9999px'
    document.body.appendChild(ta)
    ta.focus()
    ta.select()
    const ok = document.execCommand('copy')
    document.body.removeChild(ta)
    return ok
  } catch {
    return false
  }
}

/** Открыть WhatsApp: текст (название, цена, описание) и ссылка */
function shareViaWhatsApp() {
  const p = sharePayload.value
  const text = [p.text, p.url || window.location.href].filter(Boolean).join('\n')
  window.open(`https://wa.me/?text=${encodeURIComponent(text)}`, '_blank', 'noopener')
  shareDialogOpen.value = false
}

/** Открыть Telegram: название и цена в превью, ссылка в url */
function shareViaTelegram() {
  const p = sharePayload.value
  const text = [p.title, p.price != null && p.price !== '' ? `Цена: ${p.priceFormatted || p.price}` : ''].filter(Boolean).join('\n')
  window.open(`https://t.me/share/url?url=${encodeURIComponent(p.url || window.location.href)}&text=${encodeURIComponent(text)}`, '_blank', 'noopener')
  shareDialogOpen.value = false
}

/** Открыть Gmail: тема — название, тело — текст и ссылка */
function shareViaGmail() {
  const p = sharePayload.value
  const subject = p.title || 'Ссылка'
  const body = [p.text, p.url || window.location.href].filter(Boolean).join('\n\n')
  window.open(`mailto:?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`, '_blank', 'noopener')
  shareDialogOpen.value = false
}

/** Открыть IMO: копируем текст и ссылку, открываем imo.im */
function shareViaImo() {
  const p = sharePayload.value
  const text = [p.text, p.url || window.location.href].filter(Boolean).join('\n')
  if (navigator.clipboard?.writeText) {
    navigator.clipboard.writeText(text).then(() => showToast('Ссылка скопирована. Откройте IMO и вставьте.', 'success'))
  } else if (copyViaTextarea(text)) {
    showToast('Ссылка скопирована. Откройте IMO и вставьте.', 'success')
  }
  window.open('https://imo.im/', '_blank', 'noopener')
  shareDialogOpen.value = false
}
</script>

<style scoped>

/* Страница категории: колонка на всю высоту, на ПК — ширина 100% */
.category-page {
  display: flex;
  flex-direction: column;
  width: 100%;
}

/* Хедер всегда прилипает к верху при скролле (браузер и PWA); только body скроллится */
.category-page-header {
  position: sticky;
  top: 0;
  z-index: 50;
  flex-shrink: 0;
  background-color: var(--bg-primary);
  border-bottom: 1px solid var(--border-color);
}

/* PWA: фиксирован сверху, не двигается при pull-to-refresh */
.category-page-header.is-pwa {
  position: fixed;
  left: 0;
  right: 0;
}

/* Отступ контента под фиксированным хедером (только PWA) */
.category-page-body.has-fixed-header {
  padding-top: calc(var(--header-height) + env(safe-area-inset-top, 0px));
}

/* Полоса со спиннером под хедером (только PWA); высота задаётся из JS при pull */
.category-page-pull-loader {
  flex-shrink: 0;
  overflow: hidden;
  transition: height 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--bg-secondary);
}

.category-page-pull-spinner {
  width: 28px;
  height: 28px;
  border: 3px solid var(--border-color);
  border-top-color: var(--accent-color);
  border-radius: 50%;
  opacity: 0;
  transition: opacity 0.2s;
}
.category-page-pull-spinner.is-active {
  opacity: 1;
  animation: category-page-spin 0.8s linear infinite;
}
@keyframes category-page-spin {
  to { transform: rotate(360deg); }
}

.products-list {
  flex: 1;
  overflow-y: auto;
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 20px;
  padding-bottom:0;
  background-color: var(--bg-secondary);
}

.product-card {
  background: var(--bg-card);
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 4px 20px var(--shadow-color);
  border: 1px solid var(--border-color);
  transition: box-shadow 0.2s, transform 0.2s;
}

.product-main-content {
  display: flex;
  flex-direction: column;
  padding: 12px 12px 0 12px;
  gap: 6px;
}

.product-image-container {
  width: 100%;
  flex-shrink: 0;
}

.product-name {
  font-size: var(--text-base);
  font-weight: 500;
  margin: 0;
  color: var(--text-primary);
  line-height: 1.3;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 100%;
}

.product-description p {
  font-size: var(--text-sm);
}

/* Toast Notification */
.toast {
  position: fixed;
  bottom: 100px;
  left: 50%;
  transform: translateX(-50%);
  background: var(--bg-card);
  color: var(--text-primary);
  padding: 12px 20px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 10px;
  box-shadow: 0 4px 20px var(--shadow-strong);
  z-index: 2000;
  border: 1px solid var(--border-color);
}

.toast-success {
  border-left: 4px solid #4caf50;
}

.toast-info {
  border-left: 4px solid #2196f3;
}

.toast svg {
  flex-shrink: 0;
  width: 20px;
  height: 20px;
}

/* Диалог «Поделиться»: список приложений (fallback при отсутствии Web Share API) */
.share-dialog-backdrop {
  position: fixed;
  inset: 0;
  z-index: 3000;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: flex-end;
  justify-content: center;
  padding: 0 16px 24px;
  padding-bottom: max(24px, env(safe-area-inset-bottom, 0px));
}
.share-dialog {
  width: 100%;
  max-width: 400px;
  background: var(--bg-card);
  border-radius: 16px 16px 0 0;
  padding: 20px 16px;
  box-shadow: 0 -4px 20px var(--shadow-strong);
  border: 1px solid var(--border-color);
}
.share-dialog-title {
  font-size: var(--text-lg);
  font-weight: 600;
  color: var(--text-primary);
  margin: 0 0 16px;
  text-align: center;
}
.share-dialog-actions {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 12px;
}
.share-dialog-btn {
  display: flex;
  align-items: center;
  gap: 12px;
  width: 100%;
  padding: 14px 16px;
  border: 1px solid var(--border-color);
  background: var(--bg-primary);
  color: var(--text-primary);
  font-size: var(--text-base);
  border-radius: 12px;
  cursor: pointer;
  transition: background 0.2s, transform 0.15s;
  -webkit-tap-highlight-color: transparent;
}
.share-dialog-btn:active {
  background: var(--hover-bg);
  transform: scale(0.98);
}
.share-dialog-btn-icon {
  font-size: 18px;
  min-width: 28px;
  text-align: center;
}
.share-dialog-cancel {
  width: 100%;
  padding: 12px;
  border: none;
  background: transparent;
  color: var(--text-muted);
  font-size: var(--text-base);
  border-radius: 12px;
  cursor: pointer;
  -webkit-tap-highlight-color: transparent;
}
.share-dialog-cancel:active {
  background: var(--hover-bg);
}

/* Планшет: сетка 2 колонки; когда все свёрнуты — равная высота ряда (stretch); при раскрытии одной — только она растёт */
@media (min-width: 768px) {
  .products-list {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
    padding: 24px;
    padding-bottom: 48px;
    align-items: stretch;
  }

  .products-list.any-variants-expanded {
    align-items: start;
  }

  .product-main-content {
    padding: 14px 14px 0 14px;
    gap: 14px;
  }
}

/* Десктоп: 3 колонки; align-items: start уже задан в 768px */
@media (min-width: 1024px) {
  .products-list {
    grid-template-columns: repeat(3, 1fr);
    gap: 28px;
    padding: 32px;
    padding-bottom: 56px;
  }

  .product-card {
    border-radius: 10px;
    cursor: default;
  }

  .product-card:hover {
    box-shadow: 0 8px 28px var(--shadow-strong);
  }

  .product-main-content {
    padding: 22px 22px 0 22px;
    gap: 16px;
  }
}

/* Широкий десктоп: 4 колонки */
@media (min-width: 1280px) {
  .products-list {
    grid-template-columns: repeat(4, 1fr);
    gap: 32px;
    padding: 40px 48px;
    padding-bottom: 64px;
  }

  .product-main-content {
    padding: 16px 16px 0 16px;
    gap: 18px;
  }
}
</style>
