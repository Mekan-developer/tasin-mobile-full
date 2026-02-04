<template>
  <div class="categories-manager p-4 md:p-6">
    <!-- Header -->
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

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div
        v-for="category in categories"
        :key="category.id"
        class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-lg transition-shadow duration-200"
      >
        <div class="flex items-start justify-between">
          <div class="flex items-center gap-3">
            <!-- Category Icon -->
            <div class="w-12 h-12 bg-gradient-to-br from-blue-50 to-indigo-100 rounded-lg flex items-center justify-center">
              <img
                v-if="category.image_icon"
                :src="getImageUrl(category.image_icon)"
                :alt="category.name"
                class="w-8 h-8 object-contain"
              />
              <AppIcon v-else name="Folder" size="24" class="text-indigo-600" />
            </div>

            <div>
              <h3 class="font-semibold text-gray-900">{{ category.name }}</h3>
              <div class="flex items-center gap-3 mt-1">
                <span class="text-xs text-gray-500">Порядок: {{ category.order }}</span>
                <span
                  :class="category.show_price ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                  class="px-2 py-1 rounded-full text-xs font-medium"
                >
                  {{ category.show_price ? 'Цена показ.' : 'Цена скрыта' }}
                </span>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-1">
            <button
              @click="toggleShowPrice(category)"
              :title="category.show_price ? 'Скрыть цену' : 'Показать цену'"
              class="p-1.5 hover:bg-gray-100 rounded-lg transition-colors"
            >
              <AppIcon
                :name="category.show_price ? 'Eye' : 'EyeOff'"
                size="16"
                class="text-gray-600"
              />
            </button>
            <button
              @click="openEditModal(category)"
              title="Редактировать"
              class="p-1.5 hover:bg-blue-50 rounded-lg transition-colors"
            >
              <AppIcon name="Edit" size="16" class="text-blue-600" />
            </button>
            <button
              @click="deleteCategory(category.id)"
              title="Удалить"
              class="p-1.5 hover:bg-red-50 rounded-lg transition-colors"
            >
              <AppIcon name="Trash2" size="16" class="text-red-600" />
            </button>
          </div>
        </div>

        <!-- Stats -->
        <div class="mt-4 pt-4 border-t border-gray-100">
          <div class="flex items-center justify-between text-sm">
            <div class="flex items-center gap-4">
              <div class="flex items-center gap-1.5">
                <AppIcon name="Package" size="14" class="text-gray-400" />
                <span class="text-gray-600">Продукты:</span>
                <span class="font-semibold">{{ getCategoryProductsCount(category.id) }}</span>
              </div>
            </div>
            <span class="text-xs text-gray-400">ID: {{ category.id }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div
      v-if="categories.length === 0"
      class="text-center py-12 border-2 border-dashed border-gray-300 rounded-xl"
    >
      <AppIcon name="FolderOpen" size="48" class="text-gray-300 mx-auto mb-4" />
      <h3 class="text-lg font-medium text-gray-900 mb-2">Категорий пока нет</h3>
      <p class="text-gray-600 mb-6">Создайте первую категорию для организации продуктов</p>
      <button
        @click="openCreateModal"
        class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors"
      >
        Создать категорию
      </button>
    </div>

    <!-- Create/Edit Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50"
      @click.self="closeModal"
    >
      <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
          <h2 class="text-xl font-bold text-gray-900">
            {{ editingCategory ? 'Редактировать' : 'Создать' }} категорию
          </h2>
          <button
            @click="closeModal"
            class="p-1.5 hover:bg-gray-100 rounded-lg transition-colors"
          >
            <AppIcon name="X" size="20" class="text-gray-500" />
          </button>
        </div>

        <!-- Modal Form -->
        <form @submit.prevent="saveCategory" class="p-6">
          <div class="space-y-4">
            <!-- Name -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">
                Название категории *
              </label>
              <input
                v-model="form.name"
                type="text"
                required
                placeholder="Например: Смартфоны"
                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
              />
            </div>

            <!-- Order -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">
                Порядок отображения
              </label>
              <input
                v-model.number="form.order"
                type="number"
                min="1"
                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
              />
              <p class="text-xs text-gray-500 mt-1">Чем меньше число, тем выше в списке</p>
            </div>

            <!-- Show Price Toggle -->
            <div class="flex items-center justify-between">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Показывать цену
                </label>
                <p class="text-xs text-gray-500">Показывать цену продуктов в этой категории</p>
              </div>
              <button
                type="button"
                @click="form.show_price = !form.show_price"
                :class="form.show_price ? 'bg-indigo-600' : 'bg-gray-300'"
                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none"
              >
                <span
                  :class="form.show_price ? 'translate-x-6' : 'translate-x-1'"
                  class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                />
              </button>
            </div>

            <!-- Image Upload -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">
                Иконка категории
              </label>
              <div class="flex items-center gap-3">
                <div
                  v-if="form.image_icon || imagePreview"
                  class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center"
                >
                  <img
                    :src="imagePreview || getImageUrl(form.image_icon)"
                    :alt="form.name"
                    class="w-12 h-12 object-contain"
                  />
                </div>
                <div class="flex-1">
                  <input
                    type="file"
                    ref="fileInput"
                    @change="handleImageUpload"
                    accept="image/*"
                    class="hidden"
                  />
                  <button
                    type="button"
                    @click="$refs.fileInput.click()"
                    class="w-full px-4 py-2.5 border-2 border-dashed border-gray-300 rounded-lg hover:border-indigo-500 hover:bg-indigo-50 transition-colors"
                  >
                    <div class="flex flex-col items-center">
                      <AppIcon name="Upload" size="20" class="text-gray-400 mb-1" />
                      <span class="text-sm text-gray-600">Загрузить изображение</span>
                      <span class="text-xs text-gray-400 mt-1">PNG, JPG до 2MB</span>
                    </div>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Modal Actions -->
          <div class="flex gap-3 mt-8 pt-6 border-t border-gray-200">
            <button
              type="button"
              @click="closeModal"
              class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors"
            >
              Отмена
            </button>
            <button
              type="submit"
              :disabled="loading"
              class="flex-1 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="loading" class="flex items-center justify-center gap-2">
                <svg class="animate-spin h-4 w-4 text-white" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Сохранение...
              </span>
              <span v-else>{{ editingCategory ? 'Сохранить' : 'Создать' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Stats Panel -->
    <div class="mt-8 p-4 bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl border border-gray-200">
      <h3 class="font-semibold text-gray-900 mb-3">Статистика по категориям</h3>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-3 rounded-lg border border-gray-200">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
              <AppIcon name="Folder" size="16" class="text-blue-600" />
            </div>
            <div>
              <p class="text-sm text-gray-500">Всего категорий</p>
              <p class="text-lg font-bold text-gray-900">{{ categories.length }}</p>
            </div>
          </div>
        </div>
        <div class="bg-white p-3 rounded-lg border border-gray-200">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
              <AppIcon name="Eye" size="16" class="text-green-600" />
            </div>
            <div>
              <p class="text-sm text-gray-500">Цена показывается</p>
              <p class="text-lg font-bold text-gray-900">
                {{ categories.filter(c => c.show_price).length }}
              </p>
            </div>
          </div>
        </div>
        <div class="bg-white p-3 rounded-lg border border-gray-200">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
              <AppIcon name="Package" size="16" class="text-purple-600" />
            </div>
            <div>
              <p class="text-sm text-gray-500">Всего продуктов</p>
              <p class="text-lg font-bold text-gray-900">{{ products.length }}</p>
            </div>
          </div>
        </div>
        <div class="bg-white p-3 rounded-lg border border-gray-200">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
              <AppIcon name="TrendingUp" size="16" class="text-amber-600" />
            </div>
            <div>
              <p class="text-sm text-gray-500">Самые просматриваемые</p>
              <p class="text-lg font-bold text-gray-900">{{ mostPopularCategory }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '@/api/http'

export default {
  name: 'CategoriesManager',

  data() {
    return {
      categories: [],
      products: [],
      showModal: false,
      editingCategory: null,
      loading: false,
      imagePreview: null,
      form: {
        name: '',
        order: 1,
        show_price: true,
        image_icon: null
      }
    }
  },

  computed: {
    mostPopularCategory() {
      if (this.products.length === 0) return '—'

      const categoryViews = {}
      this.products.forEach(product => {
        const categoryId = product.category_id
        if (!categoryViews[categoryId]) {
          categoryViews[categoryId] = 0
        }
        categoryViews[categoryId] += product.views || 0
      })

      const maxCategoryId = Object.keys(categoryViews).reduce((a, b) =>
        categoryViews[a] > categoryViews[b] ? a : b
      )

      const category = this.categories.find(c => c.id == maxCategoryId)
      return category ? category.name : '—'
    }
  },

  mounted() {
    this.loadCategories()
    this.loadProducts()
  },

  methods: {
    async loadCategories() {
      try {
        const response = await api.get('/categories')
        this.categories = response.data.sort((a, b) => a.order - b.order)
      } catch (error) {
        console.error('Error loading categories:', error)
      }
    },

    async loadProducts() {
      try {
        const response = await api.get('/products')
        this.products = response.data
      } catch (error) {
        console.error('Error loading products:', error)
      }
    },

    getCategoryProductsCount(categoryId) {
      return this.products.filter(p => p.category_id === categoryId).length
    },

    getImageUrl(filename) {
      return `/storage/categories/${filename}`
    },

    openCreateModal() {
      this.editingCategory = null
      this.form = {
        name: '',
        order: this.categories.length + 1,
        show_price: true,
        image_icon: null
      }
      this.imagePreview = null
      this.showModal = true
    },

    openEditModal(category) {
      this.editingCategory = category
      this.form = {
        name: category.name,
        order: category.order,
        show_price: category.show_price,
        image_icon: category.image_icon
      }
      this.imagePreview = null
      this.showModal = true
    },

    closeModal() {
      this.showModal = false
      this.editingCategory = null
      this.imagePreview = null
    },

    handleImageUpload(event) {
      const file = event.target.files[0]
      if (file) {
        // Создаем preview
        const reader = new FileReader()
        reader.onload = (e) => {
          this.imagePreview = e.target.result
        }
        reader.readAsDataURL(file)

        // Сохраняем файл для отправки
        this.form.image_icon = file
      }
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
        this.closeModal()
      } catch (error) {
        console.error('Error saving category:', error)
      } finally {
        this.loading = false
      }
    },

    async toggleShowPrice(category) {
      try {
        await api.patch(`/categories/${category.id}`, {
          show_price: !category.show_price
        })
        category.show_price = !category.show_price
      } catch (error) {
        console.error('Error toggling show_price:', error)
      }
    },

    async deleteCategory(id) {
      if (!confirm('Вы уверены, что хотите удалить эту категорию?')) return

      try {
        await api.delete(`/categories/${id}`)
        await this.loadCategories()
      } catch (error) {
        console.error('Error deleting category:', error)
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

/* Animation for modal */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9;
}

::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .grid-cols-3 {
    grid-template-columns: 1fr;
  }
}
</style>
