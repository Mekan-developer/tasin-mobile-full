
// src/services/productService.js
import api from './api'

export const productService = {
  /**
   * Получить все продукты
   */
  getProducts() {
    return api.get('/products')
  },

  /**
   * Получить продукты по категории
   * @param {number|string} categoryId - ID категории
   */
  getProductsByCategory(categoryId) {
    return api.get('/products', {
      params: { category_id: categoryId }
    })
  },

  /**
   * Получить конкретный продукт
   * @param {number|string} productId - ID продукта
   */
  getProductById(productId) {
    return api.get(`/products/${productId}`)
  },

  /**
   * Создать новый продукт
   * @param {Object} productData - Данные продукта
   */
  createProduct(productData) {
    return api.post('/products', productData)
  },

  /**
   * Обновить продукт
   * @param {number|string} productId - ID продукта
   * @param {Object} productData - Данные для обновления
   */
  updateProduct(productId, productData) {
    return api.put(`/products/${productId}`, productData)
  },

  /**
   * Удалить продукт
   * @param {number|string} productId - ID продукта
   */
  deleteProduct(productId) {
    return api.delete(`/products/${productId}`)
  }
}

/**
 * Нормализует данные продукта (приводит images и activeImage к ожидаемому формату)
 * @param {Object|Array} data - Данные продукта или массив продуктов
 * @returns {Object|Array} Нормализованные данные
 */
export const normalizeProductData = (data) => {
  const normalize = (product) => ({
    ...product,
    images: product.images ?? [],
    activeImage: product.activeImage ?? 0
  })

  if (Array.isArray(data)) {
    return data.map(normalize)
  }

  return normalize(data)
}
