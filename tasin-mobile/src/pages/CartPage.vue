<template>
  <div class="cart-page">
    <h2 class="cart-title">Корзина</h2>

    <div v-if="cartStore.items.length === 0" class="cart-empty">
      Корзина пуста. Добавьте товары с главной или из категорий.
    </div>

    <ul v-else class="cart-list">
      <li v-for="item in cartStore.items" :key="item.id" class="cart-item">
        <div class="cart-item-image-wrap">
          <img
            v-if="item.image"
            :src="resolveImagePath(item.image)"
            :alt="item.name"
            class="cart-item-image"
          />
          <div v-else class="cart-item-image-placeholder">Нет фото</div>
        </div>
        <div class="cart-item-info">
          <h3 class="cart-item-name">{{ item.name }}</h3>
          <p class="cart-item-price">{{ formatPriceWithCurrency(item.price, item.currency) }}</p>
          <div class="cart-item-actions">
            <div class="cart-item-quantity">
              <button
                type="button"
                class="cart-qty-btn"
                :disabled="item.quantity <= 1"
                @click="setQuantity(item.id, item.quantity - 1)"
                aria-label="Уменьшить"
              >
                −
              </button>
              <span class="cart-qty-value">{{ item.quantity }}</span>
              <button
                type="button"
                class="cart-qty-btn"
                @click="setQuantity(item.id, item.quantity + 1)"
                aria-label="Увеличить"
              >
                +
              </button>
            </div>
            <button
              type="button"
              class="cart-remove"
              @click="cartStore.removeFromCart(item.id)"
              title="Удалить"
              aria-label="Удалить из корзины"
            >
              Удалить
            </button>
          </div>
        </div>
        <p class="cart-item-subtotal">{{ formatPriceWithCurrency((Number(item.price) || 0) * item.quantity, item.currency) }}</p>
      </li>
    </ul>

    <div v-if="cartStore.items.length > 0" class="cart-footer">
      <p class="cart-total">
        Итого: <strong>{{ formatPrice(cartStore.totalPrice) }} {{ cartStore.totalCurrency ?? 'TMT' }}</strong>
      </p>
      <button type="button" class="cart-submit" @click="onSubmitOrder">
        Отправить заказ
      </button>
    </div>

    <AuthModal
      v-model="authModalOpen"
      title="Войдите или зарегистрируйтесь"
      description="Чтобы отправить заказ поставщику"
      :initial-form="authInitialForm"
      @google-sign-in="onGoogleSignIn"
      @phone-auth="onPhoneAuth"
    />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useCartStore } from '@/stores/cart'
import { formatPhoneDisplay } from '@/composables/usePhoneFormat'
import { formatPriceWithCurrency } from '@/composables/usePriceDisplay'
import AuthModal from '@/components/common/AuthModal.vue'

const CLIENT_PROFILE_KEY = 'clientProfile'

const cartStore = useCartStore()
const authModalOpen = ref(false)
const profileLoaded = ref(0)
/** Начальные данные для модалки входа (из профиля) */
const authInitialForm = ref({ fullName: '', phone: '+993 ' })

/** Зарегистрирован = в профиле есть имя и телефон */
const isRegistered = computed(() => {
  profileLoaded.value
  try {
    const raw = localStorage.getItem(CLIENT_PROFILE_KEY)
    if (!raw) return false
    const p = JSON.parse(raw)
    return !!(p.fullName && p.phone)
  } catch {
    return false
  }
})

/** Загрузить профиль из localStorage в форму модалки */
function loadProfile() {
  try {
    const raw = localStorage.getItem(CLIENT_PROFILE_KEY)
    if (raw) {
      const p = JSON.parse(raw)
      authInitialForm.value = {
        fullName: p.fullName || '',
        phone: p.phone ? formatPhoneDisplay(p.phone) : ''
      }
    }
  } catch (_) {}
}

function onSubmitOrder() {
  if (isRegistered.value) {
    submitOrder()
  } else {
    loadProfile()
    authModalOpen.value = true
  }
}

/** Отправка заказа (после входа): пока тост, позже — API */
function submitOrder() {
  const profile = JSON.parse(localStorage.getItem(CLIENT_PROFILE_KEY) || '{}')
  console.log('Order:', { profile, items: cartStore.items, total: cartStore.totalPrice })
  alert('Заказ отправлен поставщику. С вами свяжутся по номеру ' + (profile.phone || ''))
}

/** Обработчик входа через Google (модалка эмитит, страница реагирует) */
function onGoogleSignIn() {
  alert('Вход через Google будет доступен позже. Используйте «Войти по номеру телефона».')
}

/** Сохранение профиля после входа по телефону из модалки */
function onPhoneAuth({ fullName, phone }) {
  try {
    const existing = localStorage.getItem(CLIENT_PROFILE_KEY)
    const data = existing ? { ...JSON.parse(existing), fullName, phone } : { fullName, phone, email: '', address: '', comment: '' }
    localStorage.setItem(CLIENT_PROFILE_KEY, JSON.stringify(data))
    profileLoaded.value++
  } catch (_) {}
}

/** Резолв пути картинки: имя из API → URL из assets/img */
function resolveImagePath(path) {
  if (!path) return ''
  const name = path.startsWith('@/') ? path.split('/').pop() : path
  try {
    return new URL(`../assets/img/${name}`, import.meta.url).href
  } catch {
    return path
  }
}

function formatPrice(value) {
  const n = Number(value)
  if (Number.isNaN(n)) return '0'
  return n.toLocaleString('ru-RU')
}

function setQuantity(productId, quantity) {
  cartStore.updateQuantity(productId, Math.max(0, quantity))
}
</script>

<style scoped>
.cart-page {
  padding: 16px;
  min-height: 100%;
  background-color: var(--bg-secondary);
  padding-bottom: 100px;
}

.cart-title {
  font-size: var(--text-xl);
  font-weight: 600;
  color: var(--text-primary);
  margin: 0 0 16px;
}

.cart-empty {
  padding: 32px 16px;
  text-align: center;
  color: var(--text-muted);
  font-size: var(--text-base);
}

.cart-list {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.cart-item {
  display: grid;
  grid-template-columns: 80px 1fr auto;
  gap: 12px;
  align-items: center;
  padding: 12px;
  background: var(--bg-card);
  border-radius: 14px;
  border: 1px solid var(--border-color);
  box-shadow: 0 2px 8px var(--shadow-color);
}

.cart-item-image-wrap {
  width: 80px;
  height: 80px;
  border-radius: 10px;
  overflow: hidden;
  background: var(--bg-secondary);
  flex-shrink: 0;
}

.cart-item-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.cart-item-image-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: var(--text-xs);
  color: var(--text-muted);
}

.cart-item-info {
  min-width: 0;
}

.cart-item-name {
  font-size: var(--text-base);
  font-weight: 500;
  color: var(--text-primary);
  margin: 0 0 4px;
  line-height: 1.3;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.cart-item-price {
  font-size: var(--text-sm);
  color: var(--text-secondary);
  margin: 0 0 8px;
}

.cart-item-actions {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.cart-item-quantity {
  display: flex;
  align-items: center;
  gap: 4px;
  border: 1px solid var(--border-color);
  border-radius: 10px;
  overflow: hidden;
  background: var(--bg-primary);
}

.cart-qty-btn {
  width: 36px;
  height: 32px;
  border: none;
  background: transparent;
  color: var(--text-primary);
  font-size: 18px;
  font-weight: 500;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s;
  -webkit-tap-highlight-color: transparent;
}

.cart-qty-btn:hover:not(:disabled) {
  background: var(--hover-bg);
}

.cart-qty-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.cart-qty-value {
  min-width: 28px;
  text-align: center;
  font-size: var(--text-base);
  font-weight: 600;
  color: var(--text-primary);
}

.cart-remove {
  padding: 6px 10px;
  font-size: var(--text-sm);
  color: var(--badge-bg, #f44336);
  background: transparent;
  border: 1px solid var(--border-color);
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
  -webkit-tap-highlight-color: transparent;
}

.cart-remove:hover {
  background: rgba(244, 67, 54, 0.08);
}

.cart-item-subtotal {
  font-size: var(--text-base);
  font-weight: 600;
  color: var(--accent-color);
  margin: 0;
  text-align: right;
  white-space: nowrap;
}

.cart-footer {
  margin-top: 20px;
  padding: 16px;
  background: var(--bg-card);
  border-radius: 14px;
  border: 1px solid var(--border-color);
}

.cart-total {
  font-size: var(--text-lg);
  color: var(--text-primary);
  margin: 0;
}

.cart-total strong {
  color: var(--accent-color);
}

.cart-submit {
  width: 100%;
  margin-top: 12px;
  padding: 14px 20px;
  font-size: var(--text-base);
  font-weight: 600;
  color: #fff;
  background: var(--accent-color);
  border: none;
  border-radius: 12px;
  cursor: pointer;
  transition: background 0.2s, transform 0.15s;
  -webkit-tap-highlight-color: transparent;
}

.cart-submit:active {
  transform: scale(0.98);
}

.cart-submit:hover {
  background: var(--accent-hover);
}

@media (min-width: 768px) {
  .cart-item {
    grid-template-columns: 100px 1fr auto;
    gap: 16px;
    padding: 16px;
  }

  .cart-item-image-wrap {
    width: 100px;
    height: 100px;
  }
}
</style>
