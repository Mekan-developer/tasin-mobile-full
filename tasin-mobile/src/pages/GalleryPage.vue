<template>
  <div class="gallery-page">

    <!-- Поиск по названию товара или по названию модели (варианта) -->
    <div class="gallery-search-wrap">
      <span class="gallery-search-icon" aria-hidden="true">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
      </span>
      <input
        v-model.trim="searchQuery"
        type="search"
        class="gallery-search"
        placeholder="По названию или модели..."
        autocomplete="off"
        aria-label="Поиск по названию или модели"
      />
    </div>

    <div v-if="productsStore.loading" class="gallery-loading">Загрузка...</div>
    <div v-else-if="productsStore.error" class="gallery-error">{{ productsStore.error }}</div>
    <div v-else-if="filteredGalleryItems.length === 0" class="gallery-empty">
      {{ searchQuery ? 'Ничего не найдено' : 'Нет изображений' }}
    </div>
    <div v-else class="gallery-grid">
      <button
        v-for="(item, idx) in filteredGalleryItems"
        :key="`${item.product.id}-${item.imageIndex}-${idx}`"
        type="button"
        class="gallery-item"
        @click="openDetail(item)"
      >
        <img
          :src="resolveImagePath(item.imagePath)"
          :alt="item.product.name"
          class="gallery-img"
          loading="lazy"
        />
      </button>
    </div>

    <!-- Модальное окно: данные товара и доступные модели -->
    <Teleport to="body">
      <Transition name="fade">
        <div
          v-if="detailOpen"
          class="gallery-detail-backdrop"
          @click.self="detailOpen = false"
        >
          <div class="gallery-detail" role="dialog" aria-label="Данные товара">
            <button type="button" class="gallery-detail-close" @click="detailOpen = false" aria-label="Закрыть">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
              </svg>
            </button>
            <template v-if="selectedItem">
              <img
                :src="resolveImagePath(selectedItem.imagePath)"
                :alt="selectedItem.product.name"
                class="gallery-detail-img"
              />
              <div class="gallery-detail-info">
                <h3 class="gallery-detail-name">{{ selectedItem.product.name }}</h3>
                <p v-if="selectedItem.product.price != null" class="gallery-detail-price">
                  Цена: {{ formatPriceWithCurrency(selectedItem.product.price, selectedItem.product.currency) }}
                </p>
                <p v-if="selectedItem.product.description" class="gallery-detail-desc">
                  {{ selectedItem.product.description }}
                </p>
                <div v-if="variantsList.length" class="gallery-detail-models">
                  <h4>В каких моделях есть</h4>
                  <ul>
                    <li v-for="(v, i) in variantsList" :key="i">
                      <span class="model-name">{{ v.model }}</span>
                      <span v-if="v.price" class="model-price">{{ v.price }}</span>
                    </li>
                  </ul>
                </div>
              </div>
            </template>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, inject } from 'vue'
import { useProductsStore } from '@/stores/product'
import { formatPriceWithCurrency } from '@/composables/usePriceDisplay'

/** Резолв пути картинки: имя файла из API → URL из assets/img */
function resolveImagePath(path) {
  if (!path) return ''
  const name = path.startsWith('@/') ? path.split('/').pop() : path
  try {
    return new URL(`../assets/img/${name}`, import.meta.url).href
  } catch {
    return path
  }
}

const productsStore = useProductsStore()
const mainLayout = inject('mainLayout', null)

/** Плоский список: каждое изображение каждого продукта (product, imagePath, imageIndex) */
const galleryItems = computed(() => {
  const list = []
  for (const product of productsStore.products) {
    const images = product.images ?? []
    images.forEach((imagePath, imageIndex) => {
      list.push({ product, imagePath, imageIndex })
    })
  }
  return list
})

const searchQuery = ref('')

/** Совпадает ли товар с поиском: по названию или по названию модели (варианта) */
function productMatchesSearch(product, q) {
  if (!q) return true
  const lower = q.toLowerCase()
  const nameMatch = product.name && String(product.name).toLowerCase().includes(lower)
  const variants = product.variants ?? []
  const variantMatch = variants.some((v) => v.model && String(v.model).toLowerCase().includes(lower))
  return nameMatch || variantMatch
}

/** Список изображений с учётом поиска по названию или модели */
const filteredGalleryItems = computed(() => {
  const q = searchQuery.value
  if (!q) return galleryItems.value
  return galleryItems.value.filter((item) => productMatchesSearch(item.product, q))
})

const detailOpen = ref(false)
const selectedItem = ref(null)

const variantsList = computed(() => {
  const p = selectedItem.value?.product
  return Array.isArray(p?.variants) ? p.variants : []
})

function openDetail(item) {
  selectedItem.value = item
  detailOpen.value = true
}

onMounted(async () => {
  await productsStore.fetchProducts()
  mainLayout?.registerRefresh?.(() => productsStore.fetchProducts())
})
onUnmounted(() => mainLayout?.unregisterRefresh?.())
</script>

<style scoped>
.gallery-page {
  padding: 16px;
  min-height: 100%;
  background-color: var(--bg-secondary);
}

.gallery-title {
  font-size: var(--text-xl);
  font-weight: 600;
  color: var(--text-primary);
  margin: 0 0 16px;
}

.gallery-search-wrap {
  position: relative;
  margin-bottom: 16px;
}

.gallery-search-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-muted);
  pointer-events: none;
  display: flex;
  align-items: center;
  justify-content: center;
}

.gallery-search {
  width: 100%;
  padding: 12px 12px 12px 44px;
  font-size: var(--text-base);
  color: var(--text-primary);
  background: var(--bg-card);
  border: 1px solid var(--border-color);
  border-radius: 12px;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.gallery-search::placeholder {
  color: var(--text-muted);
}

.gallery-search:focus {
  border-color: var(--accent-color);
  box-shadow: 0 0 0 2px rgba(33, 150, 243, 0.2);
}

.gallery-loading,
.gallery-error,
.gallery-empty {
  padding: 24px;
  text-align: center;
  color: var(--text-muted);
}

.gallery-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
}

.gallery-item {
  aspect-ratio: 1;
  border: none;
  padding: 0;
  border-radius: 12px;
  overflow: hidden;
  background: var(--bg-card);
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
  -webkit-tap-highlight-color: transparent;
}

.gallery-item:active {
  transform: scale(0.98);
}

.gallery-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

/* Модальное окно с данными товара */
.gallery-detail-backdrop {
  position: fixed;
  inset: 0;
  z-index: 2000;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
  padding-bottom: max(16px, env(safe-area-inset-bottom));
}

.gallery-detail {
  position: relative;
  width: 100%;
  max-width: 400px;
  max-height: 90vh;
  overflow-y: auto;
  background: var(--bg-card);
  border-radius: 16px;
  box-shadow: 0 8px 32px var(--shadow-strong);
  border: 1px solid var(--border-color);
}

.gallery-detail-close {
  position: absolute;
  top: 12px;
  right: 12px;
  z-index: 2;
  width: 40px;
  height: 40px;
  border: none;
  border-radius: 50%;
  background: rgba(0, 0, 0, 0.4);
  color: #fff;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  -webkit-tap-highlight-color: transparent;
}

.gallery-detail-img {
  width: 100%;
  aspect-ratio: 1;
  object-fit: cover;
  display: block;
}

.gallery-detail-info {
  padding: 16px;
}

.gallery-detail-name {
  font-size: var(--text-lg);
  font-weight: 600;
  color: var(--text-primary);
  margin: 0 0 8px;
}

.gallery-detail-price {
  font-size: var(--text-base);
  font-weight: 500;
  color: var(--accent-color);
  margin: 0 0 8px;
}

.gallery-detail-desc {
  font-size: var(--text-sm);
  color: var(--text-secondary);
  margin: 0 0 16px;
  line-height: 1.4;
}

.gallery-detail-models h4 {
  font-size: var(--text-sm);
  font-weight: 600;
  color: var(--text-primary);
  margin: 0 0 8px;
}

.gallery-detail-models ul {
  list-style: none;
  margin: 0;
  padding: 0;
}

.gallery-detail-models li {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 6px 0;
  border-bottom: 1px solid var(--border-color);
  font-size: var(--text-sm);
}

.gallery-detail-models .model-name {
  color: var(--text-primary);
}

.gallery-detail-models .model-price {
  color: var(--text-secondary);
  font-weight: 500;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

@media (min-width: 768px) {
  .gallery-grid {
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
  }
}

@media (min-width: 1024px) {
  .gallery-grid {
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
  }
}
</style>
