import { defineStore } from 'pinia'
import { productService, normalizeProductData } from '@/services/productService'

export const useProductsStore = defineStore('products', {
  state:() => ({
    products: [],
    currentProduct: null,
    loading: false,
    error: null,
    cache: {}
  }),

  getters: {
    /**
     * Получить продукты по категории (из уже загруженных)
     * @param {Object} state
     * @returns {Function}
     */
    getProductsByCategory: (state) => (categoryId) => {
      return state.products.filter(product => product.category_id === categoryId)
    },

    /**
     * Получить продукт по ID
     * @param {Object} state
     * @returns {Function}
     */
    getProductById: (state) => (productId) => {
      return state.products.find(product => product.id === productId) || state.currentProduct
    },

    /**
     * Количество продуктов
     */
    productsCount: (state) => state.products.length
  },

  actions: {
    async fetchProducts() {
      this.loading = true
      this.error = null
      try {
        const response = await productService.getProducts()
        this.products = normalizeProductData(response.data)
      } catch (error) {
        this.error = error.message || 'Ошибка загрузки продуктов'
        console.error('fetchProducts error:', error)
        throw error
      } finally {
        this.loading = false
      }
    },

     /**
     * Загрузить продукты по категории
     * @param {number|string} categoryId - ID категории
     * @param {boolean} force - Принудительно загрузить, игнорируя кэш
     */
     async fetchProductsByCategory(categoryId, force = false) {
      if (!categoryId) {
        this.products = []
        return
      }

      // Проверка кэша (если не принудительная загрузка)
      if (!force && this.cache[categoryId]) {
        this.products = this.cache[categoryId]
        return
      }

      this.loading = true
      this.error = null

      try {
        const response = await productService.getProductsByCategory(categoryId)
        const normalizedProducts = normalizeProductData(response.data)

        this.products = normalizedProducts
        // Сохраняем в кэш
        this.cache[categoryId] = normalizedProducts

      } catch (error) {
        this.error = error.message || `Ошибка загрузки продуктов категории ${categoryId}`
        console.error('fetchProductsByCategory error:', error)
        this.products = []
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * Загрузить конкретный продукт
     * @param {number|string} productId - ID продукта
     */
    async fetchProductById(productId) {
      if (!productId) {
        this.currentProduct = null
        return
      }

      this.loading = true
      this.error = null

      try {
        const response = await productService.getProductById(productId)
        this.currentProduct = normalizeProductData(response.data)

        // Обновляем в общем списке продуктов, если он там есть
        const index = this.products.findIndex(p => p.id === productId)
        if (index !== -1) {
          this.products[index] = { ...this.products[index], ...this.currentProduct }
        }

      } catch (error) {
        this.error = error.message || `Ошибка загрузки продукта ${productId}`
        console.error('fetchProductById error:', error)
        this.currentProduct = null
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * Очистить кэш категории
     * @param {number|string} categoryId - ID категории
     */
    clearCategoryCache(categoryId) {
      if (categoryId) {
        delete this.cache[categoryId]
      } else {
        this.cache = {}
      }
    },

    /**
     * Очистить ошибку
     */
    clearError() {
      this.error = null
    }

  }
})
