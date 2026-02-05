<template>
  <div class="categories-manager p-4 md:p-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Управление категориями</h1>
        <p class="text-gray-600 mt-1">Всего категорий: {{ categories.length }}</p>
      </div>
      <button
        @click="openCreateModal"
        class="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl"
      >
        <AppIcon name="Plus" size="18" />
        Добавить категорию
      </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <CategoryCard
        v-for="category in categories"
        :key="category.id"
        :category="category"
        :products-count="getCategoryProductsCount(category.id)"
        @toggle-price="toggleShowPrice"
        @edit="openEditModal"
        @delete="deleteCategory"
      />
    </div>

    <CategoryEmptyState v-if="categories.length === 0" @create="openCreateModal" />

    <CategoryModal
      v-model="showModal"
      :form="form"
      :image-preview="imagePreview"
      :loading="loading"
      :editing="!!editingCategory"
      @update:form="form = $event"
      @save="saveCategory"
      @image-upload="handleImageUpload"
    />

    <CategoryStats
      :categories-count="categories.length"
      :show-price-count="categories.filter(c => c.show_price).length"
      :products-count="products.length"
      :most-popular-name="mostPopularCategory"
    />
  </div>
</template>

<script>
import api from '@/api/http'
import AppIcon from '@/components/icons/AppIcon.vue'
import CategoryCard from '@/components/categories/CategoryCard.vue'
import CategoryEmptyState from '@/components/categories/CategoryEmptyState.vue'
import CategoryModal from '@/components/categories/CategoryModal.vue'
import CategoryStats from '@/components/categories/CategoryStats.vue'

/** Страница управления категориями: список, создание, редактирование, статистика. */
export default {
  name: 'CategoriesIndexPage',
  components: { AppIcon, CategoryCard, CategoryEmptyState, CategoryModal, CategoryStats },
  data() {
    return {
      categories: [],
      products: [],
      showModal: false,
      editingCategory: null,
      loading: false,
      imagePreview: null,
      form: { name: '', order: 1, show_price: true, image_icon: null }
    }
  },
  computed: {
    mostPopularCategory() {
      if (this.products.length === 0) return '—'
      const categoryViews = {}
      this.products.forEach((p) => {
        const id = p.category_id
        categoryViews[id] = (categoryViews[id] || 0) + (p.views || 0)
      })
      const maxId = Object.keys(categoryViews).reduce((a, b) =>
        categoryViews[a] > categoryViews[b] ? a : b
      )
      const cat = this.categories.find((c) => c.id == maxId)
      return cat ? cat.name : '—'
    }
  },
  mounted() {
    this.loadCategories()
    this.loadProducts()
  },
  methods: {
    async loadCategories() {
      try {
        const res = await api.get('/categories')
        const list = res.data?.data ?? res.data ?? []
        this.categories = Array.isArray(list) ? list.sort((a, b) => (a.order || 0) - (b.order || 0)) : []
      } catch (e) {
        console.error('Error loading categories:', e)
      }
    },
    async loadProducts() {
      try {
        const res = await api.get('/products')
        const list = res.data?.data ?? res.data ?? []
        this.products = Array.isArray(list) ? list : []
      } catch (e) {
        console.error('Error loading products:', e)
      }
    },
    getCategoryProductsCount(categoryId) {
      return this.products.filter((p) => p.category_id === categoryId).length
    },
    openCreateModal() {
      this.editingCategory = null
      this.form = { name: '', order: this.categories.length + 1, show_price: true, image_icon: null }
      this.imagePreview = null
      this.showModal = true
    },
    openEditModal(category) {
      this.editingCategory = category
      this.form = { name: category.name, order: category.order, show_price: category.show_price, image_icon: category.image_icon }
      this.imagePreview = null
      this.showModal = true
    },
    handleImageUpload({ file, preview }) {
      this.imagePreview = preview
      this.form = { ...this.form, image_icon: file }
    },
    async saveCategory() {
      this.loading = true
      try {
        const formData = new FormData()
        formData.append('name', this.form.name)
        formData.append('order', this.form.order)
        formData.append('show_price', this.form.show_price)
        if (this.form.image_icon instanceof File) {
          formData.append('image_icon', this.form.image_icon)
        } else if (this.form.image_icon) {
          formData.append('image_icon', this.form.image_icon)
        }
        if (this.editingCategory) {
          await api.put(`/categories/${this.editingCategory.id}`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
          })
        } else {
          await api.post('/categories', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
          })
        }
        await this.loadCategories()
        this.showModal = false
        this.editingCategory = null
        this.imagePreview = null
      } catch (e) {
        console.error('Error saving category:', e)
      } finally {
        this.loading = false
      }
    },
    async toggleShowPrice(category) {
      try {
        await api.patch(`/categories/${category.id}`, { show_price: !category.show_price })
        category.show_price = !category.show_price
      } catch (e) {
        console.error('Error toggling show_price:', e)
      }
    },
    async deleteCategory(id) {
      if (!confirm('Вы уверены, что хотите удалить эту категорию?')) return
      try {
        await api.delete(`/categories/${id}`)
        await this.loadCategories()
      } catch (e) {
        console.error('Error deleting category:', e)
      }
    }
  }
}
</script>

<style scoped>
.categories-manager {
  max-width: 1200px;
  margin: 0 auto;
}
@media (max-width: 768px) {
  .grid-cols-3 {
    grid-template-columns: 1fr;
  }
}
</style>
