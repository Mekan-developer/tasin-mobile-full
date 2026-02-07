<template>
  <div class="products-manager p-4 md:p-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Управление товарами</h1>
        <p class="text-gray-600 dark:text-fog mt-1">Всего товаров: {{ products.length }}</p>
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
        <select
          v-model="filterCategoryId"
          class="px-4 py-2.5 border border-gray-300 dark:border-midnight rounded-lg bg-white dark:bg-deepblue text-gray-700 dark:text-gray-200 text-sm"
        >
          <option :value="null">Все категории</option>
          <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
        </select>
        <button
          @click="openCreateModal"
          class="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl"
        >
          <AppIcon name="Plus" size="18" />
          Добавить товар
        </button>
      </div>
    </div>

    <div
      v-if="productsLoading"
      class="flex items-center justify-center min-h-[240px]"
    >
      <LoaderSpinner size="lg" />
    </div>

    <div v-else-if="viewMode === 'cards'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <ProductCard
        v-for="product in filteredProducts"
        :key="product.id"
        :product="product"
        :categories="categories"
        @edit="openEditModal"
        @delete="deleteProduct"
      />
    </div>

    <div v-else class="overflow-x-auto rounded-xl border border-gray-200 dark:border-midnight">
      <table class="w-full text-sm text-left">
        <thead class="bg-gray-50 dark:bg-midnight/50 text-gray-600 dark:text-gray-400 uppercase">
          <tr>
            <th class="px-4 py-3">Изображение</th>
            <th class="px-4 py-3">Название</th>
            <th class="px-4 py-3">Категория</th>
            <th class="px-4 py-3">Цена</th>
            <th class="px-4 py-3">Новинка</th>
            <th class="px-4 py-3">Просмотры</th>
            <th class="px-4 py-3 text-right">Действия</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-midnight">
          <tr
            v-for="product in filteredProducts"
            :key="product.id"
            class="bg-white dark:bg-deepblue hover:bg-gray-50 dark:hover:bg-fogactive/20 transition-colors"
          >
            <td class="px-4 py-3">
              <ProductImagePreview :src="getProductFirstImage(product)" :alt="product.name" />
            </td>
            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ product.name }}</td>
            <td class="px-4 py-3 text-gray-600 dark:text-fog">{{ getCategoryName(product.category_id) }}</td>
            <td class="px-4 py-3 text-gray-600 dark:text-fog">{{ product.price }} {{ product.currency }}</td>
            <td class="px-4 py-3">
              <span
                v-if="product.is_new"
                class="px-2 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-400 rounded-full text-xs font-medium"
              >
                Да
              </span>
              <span v-else class="text-gray-400">—</span>
            </td>
            <td class="px-4 py-3 text-gray-600 dark:text-fog">{{ product.views ?? 0 }}</td>
            <td class="px-4 py-3 text-right">
              <div class="flex items-center justify-end gap-1">
                <button @click="openEditModal(product)" title="Редактировать" class="p-1.5 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors">
                  <AppIcon name="Edit" size="16" class="text-blue-600 dark:text-blue-400" />
                </button>
                <button @click="deleteProduct(product.id)" title="Удалить" class="p-1.5 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors">
                  <AppIcon name="Trash2" size="16" class="text-red-600 dark:text-red-400" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <ProductEmptyState v-if="!productsLoading && filteredProducts.length === 0" @create="openCreateModal" />

    <ProductModal
      v-model="showModal"
      :form="form"
      :categories="categories"
      :loading="loading"
      :editing="!!editingProduct"
      @update:form="form = $event"
      @save="saveProduct"
    />

    <ProductStats
      :products-count="filteredProducts.length"
      :new-count="filteredProducts.filter((p) => p.is_new).length"
      :total-views="filteredProducts.reduce((s, p) => s + (p.views || 0), 0)"
      :categories-count="categories.length"
    />
  </div>
</template>

<script>
import api from '@/api/http'
import store from '@/store'
import AppIcon from '@/components/icons/AppIcon.vue'
import ProductCard from '@/components/products/ProductCard.vue'
import ProductImagePreview from '@/components/products/ProductImagePreview.vue'
import ProductEmptyState from '@/components/products/ProductEmptyState.vue'
import ProductModal from '@/components/products/ProductModal.vue'
import ProductStats from '@/components/products/ProductStats.vue'
import LoaderSpinner from '@/components/loaders/LoaderSpinner.vue'

/** Страница управления товарами: список, создание, редактирование, статистика. */
export default {
  name: 'ProductsIndexPage',
  components: { AppIcon, ProductCard, ProductEmptyState, ProductImagePreview, ProductModal, ProductStats, LoaderSpinner },
  data() {
    return {
      products: [],
      categories: [],
      filterCategoryId: null,
      productsLoading: false,
      viewMode: localStorage.getItem('products-view-mode') || 'cards',
      showModal: false,
      editingProduct: null,
      loading: false,
      form: {
        category_id: null,
        name: '',
        description: '',
        price: 0,
        currency: 'TMT',
        images: [],
        is_new: false
      }
    }
  },
  watch: {
    viewMode(val) {
      localStorage.setItem('products-view-mode', val)
    },
    filterCategoryId() {
      this.loadProducts()
    }
  },
  computed: {
    filteredProducts() {
      if (!this.filterCategoryId) return this.products
      return this.products.filter((p) => p.category_id === this.filterCategoryId)
    }
  },
  mounted() {
    this.loadProducts()
    this.loadCategories()
  },
  methods: {
    async loadProducts() {
      this.productsLoading = true
      try {
        const url = this.filterCategoryId ? `/products?category_id=${this.filterCategoryId}` : '/products'
        const res = await api.get(url)
        const list = res.data?.data ?? res.data ?? []
        this.products = Array.isArray(list) ? list : []
        return this.products
      } catch (e) {
        console.error('Error loading products:', e)
        return []
      } finally {
        this.productsLoading = false
      }
    },
    async loadCategories() {
      try {
        const res = await api.get('/categories')
        const list = res.data?.data ?? res.data ?? []
        this.categories = Array.isArray(list) ? list : []
      } catch (e) {
        console.error('Error loading categories:', e)
      }
    },
    getProductFirstImage(product) {
      const imgs = product.images
      if (!Array.isArray(imgs) || imgs.length === 0) return null
      const idx = product.active_image ?? 0
      return imgs[idx] || imgs[0]
    },
    getCategoryName(categoryId) {
      const cat = this.categories.find((c) => c.id === categoryId)
      return cat ? cat.name : `#${categoryId}`
    },
    openCreateModal() {
      this.editingProduct = null
      this.form = {
        category_id: this.categories[0]?.id ?? null,
        name: '',
        description: '',
        price: 0,
        currency: 'TMT',
        images: [],
        is_new: false
      }
      this.showModal = true
    },
    openEditModal(product) {
      this.editingProduct = product
      const images = product.images ?? []
      this.form = {
        category_id: product.category_id,
        name: product.name ?? '',
        description: product.description ?? '',
        price: product.price ?? 0,
        currency: product.currency ?? 'TMT',
        images: Array.isArray(images) ? images.map((f) => (typeof f === 'string' ? { filename: f } : f)) : [],
        is_new: product.is_new ?? false
      }
      this.showModal = true
    },
    getImagesPayload() {
      const arr = this.form.images || []
      const existing = arr.filter((x) => x?.filename && !x?.file).map((x) => (typeof x === 'string' ? x : x.filename))
      const files = arr.filter((x) => x?.file)
      return { existing, files }
    },
    async saveProduct() {
      this.loading = true
      try {
        const { existing, files } = this.getImagesPayload()
        const hasFiles = files.length > 0
        let res
        if (hasFiles || existing.length > 0) {
          const fd = new FormData()
          fd.append('category_id', this.form.category_id)
          fd.append('name', this.form.name)
          fd.append('description', this.form.description || '')
          fd.append('price', this.form.price)
          fd.append('currency', this.form.currency)
          fd.append('is_new', this.form.is_new ? '1' : '0')
          fd.append('existing_images', JSON.stringify(existing))
          files.forEach((item) => fd.append('images[]', item.file))
          if (this.editingProduct) {
            fd.append('_method', 'PUT')
            res = await api.post(`/products/${this.editingProduct.id}`, fd)
          } else {
            res = await api.post('/products', fd)
          }
        } else {
          const payload = {
            category_id: this.form.category_id,
            name: this.form.name,
            description: this.form.description || null,
            price: this.form.price,
            currency: this.form.currency,
            images: [],
            is_new: this.form.is_new
          }
          if (this.editingProduct) {
            res = await api.put(`/products/${this.editingProduct.id}`, payload)
          } else {
            res = await api.post('/products', payload)
          }
        }
        await this.loadProducts()
        this.showModal = false
        this.editingProduct = null
        store.dispatch('notifications/notify', { message: 'Товар сохранён', type: 'success' })
      } catch (e) {
        console.error('Error saving product:', e)
        let msg = e.response?.data?.message
        if (!msg && e.response?.data?.errors) {
          msg = Object.values(e.response.data.errors).flat().join('. ')
        }
        store.dispatch('notifications/notify', { message: msg || e.message || 'Ошибка сохранения', type: 'error' })
      } finally {
        this.loading = false
      }
    },
    async deleteProduct(id) {
      if (!confirm('Вы уверены, что хотите удалить этот товар?')) return
      try {
        await api.delete(`/products/${id}`)
        await this.loadProducts()
        store.dispatch('notifications/notify', { message: 'Товар удалён', type: 'success' })
      } catch (e) {
        console.error('Error deleting product:', e)
        store.dispatch('notifications/notify', { message: 'Ошибка удаления', type: 'error' })
      }
    }
  }
}
</script>

<style scoped>
.products-manager {
  max-width: 1200px;
  margin: 0 auto;
}
</style>
