<template>
  <div class="categories-manager p-4 md:p-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Управление категориями</h1>
        <p class="text-gray-600 dark:text-fog mt-1">Всего категорий: {{ categories.length }}</p>
      </div>
      <div class="flex flex-wrap items-center gap-2">
        <div class="flex items-center gap-1 p-1 bg-gray-100 dark:bg-midnight rounded-lg">
          <button
            :class="viewMode === 'cards' ? 'bg-white dark:bg-deepblue shadow text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-fog hover:text-gray-700 dark:hover:text-gray-300'"
            class="p-2 rounded-md transition-all"
            title="Карточки"
            @click="viewMode = 'cards'"
          >
            <AppIcon name="LayoutGrid" size="18" />
          </button>
          <button
            :class="viewMode === 'table' ? 'bg-white dark:bg-deepblue shadow text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-fog hover:text-gray-700 dark:hover:text-gray-300'"
            class="p-2 rounded-md transition-all"
            title="Таблица"
            @click="viewMode = 'table'"
          >
            <AppIcon name="Table" size="18" />
          </button>
        </div>
        <ExcelComponent
          :categories="categories"
          :products="products"
          :load-categories="loadCategories"
          @imported="loadCategories"
        />
        <button
          @click="openCreateModal"
          class="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl"
        >
          <AppIcon name="Plus" size="18" />
          Добавить категорию
        </button>
      </div>
    </div>

    <div
      v-if="categoriesLoading"
      class="flex items-center justify-center min-h-[240px]"
    >
      <LoaderSpinner size="lg" />
    </div>

    <div v-else-if="viewMode === 'cards'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <CategoryCard
        v-for="category in categories"
        :key="category.id"
        :category="category"
        :products-count="getCategoryProductsCount(category.id)"
        @toggle-price="toggleShowPrice"
        @toggle-active="toggleActive"
        @edit="openEditModal"
        @delete="deleteCategory"
      />
    </div>

    <div v-else class="relative">
      <div
        v-if="reorderLoading"
        class="absolute inset-0 bg-white/80 dark:bg-deepblue/80 flex items-center justify-center z-10 rounded-xl"
      >
        <LoaderSpinner size="lg" />
      </div>
      <DraggableTable
        v-model:items="categories"
        item-key="id"
        @reorder="onCategoriesReorder"
      >
      <template #header>
        <th class="px-4 py-3">Иконка</th>
        <th class="px-4 py-3">Название</th>
        <th class="px-4 py-3">Порядок</th>
        <th class="px-4 py-3">Цена</th>
        <th class="px-4 py-3">Статус</th>
        <th class="px-4 py-3">Просмотры</th>
        <th class="px-4 py-3">Продукты</th>
        <th class="px-4 py-3 text-right">Действия</th>
      </template>
      <template #row="{ item }">
        <td class="px-4 py-3">
          <CategoryImagePreview :src="item.image_icon" :alt="item.name" />
        </td>
        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ item.name }}</td>
        <td class="px-4 py-3 text-gray-600 dark:text-fog">{{ item.order }}</td>
        <td class="px-4 py-3">
          <span
            :class="item.show_price ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-400'"
            class="px-2 py-1 rounded-full text-xs font-medium"
          >
            {{ item.show_price ? 'Показ.' : 'Скрыта' }}
          </span>
        </td>
        <td class="px-4 py-3">
          <span
            :class="item.is_active !== false ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-400' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400'"
            class="px-2 py-1 rounded-full text-xs font-medium"
          >
            {{ item.is_active !== false ? 'Активна' : 'Скрыта' }}
          </span>
        </td>
        <td class="px-4 py-3 text-gray-600 dark:text-fog">{{ item.views ?? 0 }}</td>
        <td class="px-4 py-3 text-gray-600 dark:text-fog">{{ getCategoryProductsCount(item.id) }}</td>
        <td class="px-4 py-3 text-right">
          <div class="flex items-center justify-end gap-1">
            <button
              @click="toggleShowPrice(item)"
              :title="item.show_price ? 'Скрыть цену' : 'Показать цену'"
              class="p-1.5 hover:bg-gray-100 dark:hover:bg-fogactive rounded-lg transition-colors"
            >
              <AppIcon :name="item.show_price ? 'Eye' : 'EyeOff'" size="16" class="text-gray-600 dark:text-gray-400" />
            </button>
            <button
              @click="toggleActive(item)"
              :title="item.is_active !== false ? 'Скрыть категорию' : 'Активировать'"
              class="p-1.5 hover:bg-gray-100 dark:hover:bg-fogactive rounded-lg transition-colors"
            >
              <AppIcon :name="item.is_active !== false ? 'CheckCircle' : 'Circle'" size="16" :class="item.is_active !== false ? 'text-emerald-600' : 'text-gray-400'" />
            </button>
            <button @click="openEditModal(item)" title="Редактировать" class="p-1.5 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors">
              <AppIcon name="Edit" size="16" class="text-blue-600 dark:text-blue-400" />
            </button>
            <button @click="deleteCategory(item.id)" title="Удалить" class="p-1.5 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors">
              <AppIcon name="Trash2" size="16" class="text-red-600 dark:text-red-400" />
            </button>
          </div>
        </td>
      </template>
    </DraggableTable>
    </div>

    <CategoryEmptyState v-if="!categoriesLoading && categories.length === 0" @create="openCreateModal" />

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
      :active-count="categories.filter(c => c.is_active !== false).length"
      :show-price-count="categories.filter(c => c.show_price).length"
      :products-count="products.length"
      :most-popular-name="mostPopularCategory"
    />
  </div>
</template>

<script>
import api from '@/api/http'
import store from '@/store'
import AppIcon from '@/components/icons/AppIcon.vue'
import CategoryCard from '@/components/categories/CategoryCard.vue'
import CategoryImagePreview from '@/components/categories/CategoryImagePreview.vue'
import ExcelComponent from '@/components/categories/ExcelComponent.vue'
import CategoryEmptyState from '@/components/categories/CategoryEmptyState.vue'
import CategoryModal from '@/components/categories/CategoryModal.vue'
import CategoryStats from '@/components/categories/CategoryStats.vue'
import LoaderSpinner from '@/components/loaders/LoaderSpinner.vue'

/** Страница управления категориями: список, создание, редактирование, статистика. */
export default {
  name: 'CategoriesIndexPage',
  components: { AppIcon, CategoryCard, CategoryEmptyState, CategoryImagePreview, ExcelComponent, CategoryModal, CategoryStats, LoaderSpinner },
  data() {
    return {
      categories: [],
      products: [],
      categoriesLoading: false,
      reorderLoading: false,
      viewMode: localStorage.getItem('categories-view-mode') || 'cards',
      showModal: false,
      editingCategory: null,
      loading: false,
      imagePreview: null,
      form: { name: '', order: 1, show_price: true, is_active: true, image_icon: null }
    }
  },
  watch: {
    viewMode(val) {
      localStorage.setItem('categories-view-mode', val)
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
      this.categoriesLoading = true
      try {
        const res = await api.get('/categories')
        const list = res.data?.data ?? res.data ?? []
        this.categories = Array.isArray(list) ? list.sort((a, b) => (a.order || 0) - (b.order || 0)) : []
        return this.categories
      } catch (e) {
        console.error('Error loading categories:', e)
        return []
      } finally {
        this.categoriesLoading = false
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
    /** Обновляет порядок категорий на сервере после drag-and-drop. */
    async onCategoriesReorder(reordered) {
      this.reorderLoading = true
      const withNewOrder = reordered.map((cat, idx) => ({ ...cat, order: idx + 1 }))
      try {
        await Promise.all(
          withNewOrder.map((c) => api.patch(`/categories/${c.id}`, { order: c.order }))
        )
        this.categories = withNewOrder
        store.dispatch('notifications/notify', { message: 'Порядок сохранён', type: 'success' })
      } catch (e) {
        console.error('Error reordering:', e)
        store.dispatch('notifications/notify', { message: 'Ошибка сохранения порядка', type: 'error' })
        await this.loadCategories()
      } finally {
        this.reorderLoading = false
      }
    },
    openCreateModal() {
      this.editingCategory = null
      this.form = { name: '', order: this.categories.length + 1, show_price: true, is_active: true, image_icon: null }
      this.imagePreview = null
      this.showModal = true
    },
    openEditModal(category) {
      this.editingCategory = category
      this.form = { name: category.name, order: category.order, show_price: category.show_price, is_active: category.is_active !== false, image_icon: category.image_icon }
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
        formData.append('is_active', this.form.is_active !== false)
        if (this.form.image_icon instanceof File) {
          formData.append('image_icon', this.form.image_icon)
        }
        if (this.editingCategory) {
          formData.append('_method', 'PUT')
          await api.post(`/categories/${this.editingCategory.id}`, formData)
        } else {
          await api.post('/categories', formData)
        }
        await this.loadCategories()
        this.showModal = false
        this.editingCategory = null
        this.imagePreview = null
        store.dispatch('notifications/notify', { message: 'Категория сохранена', type: 'success' })
      } catch (e) {
        console.error('Error saving category:', e)
        let msg = e.response?.data?.message
        if (!msg && e.response?.data?.errors) {
          msg = Object.values(e.response.data.errors).flat().join('. ')
        }
        store.dispatch('notifications/notify', { message: msg || e.message || 'Ошибка сохранения', type: 'error' })
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
    async toggleActive(category) {
      try {
        const newVal = !(category.is_active !== false)
        await api.patch(`/categories/${category.id}`, { is_active: newVal })
        category.is_active = newVal
      } catch (e) {
        console.error('Error toggling is_active:', e)
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
    },
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
