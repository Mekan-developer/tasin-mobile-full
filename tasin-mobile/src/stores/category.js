import { defineStore } from 'pinia'
import { categoryService } from '@/services/categoryService'

export const useCategoryStore = defineStore('category', {

  state:() => ({
    categories: [],
    loading: false,
    error: null,
    slides: [],
  }),

  actions: {
    async fetchCategories() {
      this.loading = true
      this.error = null
      try {
        const response = await categoryService.getCategories()
        this.categories = response.data
        // await this.fetchSlides() // Загружаем слайды после категорий
      } catch (error) {
        this.error = error
        console.error('fetchCategories error:', error)
      } finally {
        this.loading = false
      }
    },
    async fetchSlides() {
      this.loading = true
      this.error = null
      try {
        const response = await categoryService.getSlides()
        this.slides = response.data
      } catch (error) {
        this.error = error
        console.error('fetchSlides error:', error)
      } finally {
        this.loading = false
      }
    }
  }
})
