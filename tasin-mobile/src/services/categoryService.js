import api from './api'

export const categoryService = {
  getCategories() {
    return api.get('/categories')
  },

  getCategory(id) {
    return api.get(`/categories/${id}`)
  },

  createCategory(data) {
    return api.post('/categories', data)
  },

  getSlides() {
    return api.get('/slides')
  },
}
