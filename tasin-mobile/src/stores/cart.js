import { ref, computed } from 'vue'
import { defineStore } from 'pinia'

export const useCartStore = defineStore('cart', () => {
  const items = ref([])

  // Добавить товар в корзину
  const addToCart = (product) => {
    const existingItem = items.value.find(item => item.id === product.id)
    if (existingItem) {
      existingItem.quantity += 1
    } else {
      items.value.push({
        id: product.id,
        name: product.name,
        price: product.price,
        currency: product.currency || 'TMT',
        image: product.images && product.images.length > 0 ? product.images[0] : null,
        color: product.color,
        quantity: 1
      })
    }
    // Сохранить в localStorage
    localStorage.setItem('cart', JSON.stringify(items.value))
  }

  // Получить количество конкретного товара
  const getItemQuantity = (productId) => {
    const item = items.value.find(item => item.id === productId)
    return item ? item.quantity : 0
  }
  // Удалить товар из корзины
  const removeFromCart = (productId) => {
    const index = items.value.findIndex(item => item.id === productId)
    if (index > -1) {
      items.value.splice(index, 1)
      localStorage.setItem('cart', JSON.stringify(items.value))
    }
  }

  /** Изменить количество товара; если quantity <= 0 — удалить из корзины */
  const updateQuantity = (productId, quantity) => {
    const item = items.value.find(item => item.id === productId)
    if (!item) return
    if (quantity <= 0) {
      removeFromCart(productId)
      return
    }
    item.quantity = quantity
    localStorage.setItem('cart', JSON.stringify(items.value))
  }

  // Проверить, есть ли товар в корзине
  const isInCart = (productId) => {
    return items.value.some(item => item.id === productId)
  }

  // Получить количество товаров в корзине
  const totalItems = computed(() => {
    return items.value.reduce((sum, item) => sum + item.quantity, 0)
  })

  /** Сумма корзины (цена × количество по каждому товару) */
  const totalPrice = computed(() => {
    return items.value.reduce((sum, item) => sum + (Number(item.price) || 0) * item.quantity, 0)
  })

  /** Валюта итога: одна для всех позиций или null при смешанных */
  const totalCurrency = computed(() => {
    const list = items.value
    if (!list.length) return 'TMT'
    const first = list[0].currency || 'TMT'
    const same = list.every(item => (item.currency || 'TMT') === first)
    return same ? first : null
  })

  // Загрузить корзину из localStorage
  const loadCart = () => {
    const saved = localStorage.getItem('cart')
    if (saved) {
      items.value = JSON.parse(saved)
    }
  }

  // Инициализация
  loadCart()

  return {
    items,
    addToCart,
    removeFromCart,
    updateQuantity,
    isInCart,
    totalItems,
    totalPrice,
    totalCurrency,
    loadCart,
    getItemQuantity
  }
})
