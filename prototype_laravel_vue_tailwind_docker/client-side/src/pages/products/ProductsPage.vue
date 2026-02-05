<template>
  <div class="products-manager p-4 md:p-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Товары</h1>
        <p class="text-gray-600 mt-1">Всего: {{ products.length }}</p>
      </div>
      <div class="flex items-center gap-3">
        <select
          v-model="filterCategoryId"
          class="border rounded-lg px-3 py-2 text-sm"
        >
          <option :value="null">Все категории</option>
          <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
        </select>
        <button @click="openCreateModal" class="btn-primary">
          <AppIcon name="Plus" size="18" />
          Добавить товар
        </button>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div
        v-for="product in filteredProducts"
        :key="product.id"
        class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-lg transition-shadow"
      >
        <h3 class="font-semibold text-gray-900 truncate">{{ product.name }}</h3>
        <p class="text-sm text-gray-500 mt-1">{{ product.price }} {{ product.currency }}</p>
        <p class="text-xs text-gray-400 mt-1">Категория ID: {{ product.category_id }} · Просмотры: {{ product.views ?? 0 }}</p>
        <div class="flex items-center gap-1 mt-3">
          <button @click="openEditModal(product)" class="p-1.5 hover:bg-blue-50 rounded-lg" title="Редактировать">
            <AppIcon name="Edit" size="16" class="text-blue-600" />
          </button>
          <button @click="deleteProduct(product.id)" class="p-1.5 hover:bg-red-50 rounded-lg" title="Удалить">
            <AppIcon name="Trash2" size="16" class="text-red-600" />
          </button>
        </div>
      </div>
    </div>

    <div v-if="filteredProducts.length === 0" class="text-center py-12 text-gray-500">
      Нет товаров.
    </div>

    <!-- Modal create/edit -->
    <div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click.self="closeModal">
      <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-6 max-h-[90vh] overflow-y-auto">
        <h2 class="text-lg font-semibold mb-4">{{ editingProduct ? 'Редактировать товар' : 'Новый товар' }}</h2>
        <form @submit.prevent="saveProduct" class="space-y-3">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Категория *</label>
            <select v-model.number="form.category_id" required class="w-full border rounded-lg px-3 py-2">
              <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Название *</label>
            <input v-model="form.name" type="text" required class="w-full border rounded-lg px-3 py-2" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Описание</label>
            <textarea v-model="form.description" rows="2" class="w-full border rounded-lg px-3 py-2"></textarea>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Цена *</label>
              <input v-model.number="form.price" type="number" step="0.01" required class="w-full border rounded-lg px-3 py-2" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Валюта *</label>
              <input v-model="form.currency" type="text" required class="w-full border rounded-lg px-3 py-2" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Изображения (имена через запятую)</label>
            <input v-model="form.imagesStr" type="text" class="w-full border rounded-lg px-3 py-2" placeholder="img1.jpeg, img2.jpeg" />
          </div>
          <div class="flex items-center gap-2">
            <input v-model="form.is_new" type="checkbox" class="rounded" />
            <label class="text-sm">Новинка</label>
          </div>
          <div class="flex gap-2 pt-4">
            <button type="submit" class="btn-primary flex-1" :disabled="loading">{{ loading ? 'Сохранение...' : 'Сохранить' }}</button>
            <button type="button" @click="closeModal" class="px-4 py-2 border rounded-lg">Отмена</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import api from '@/api/http'
import AppIcon from '@/components/icons/AppIcon.vue'

/** Страница управления товарами (API apiResource products, фильтр по категории). */
export default {
  name: 'ProductsPage',
  components: { AppIcon },
  data() {
    return {
      products: [],
      categories: [],
      filterCategoryId: null,
      showModal: false,
      editingProduct: null,
      loading: false,
      form: {
        category_id: null,
        name: '',
        description: '',
        price: 0,
        currency: 'TMT',
        imagesStr: '',
        is_new: false
      }
    }
  },
  computed: {
    filteredProducts() {
      if (!this.filterCategoryId) return this.products
      return this.products.filter(p => p.category_id === this.filterCategoryId)
    }
  },
  mounted() {
    this.loadProducts()
    this.loadCategories()
  },
  methods: {
    async loadProducts() {
      try {
        const url = this.filterCategoryId ? `/products?category_id=${this.filterCategoryId}` : '/products'
        const res = await api.get(url)
        const list = res.data?.data ?? res.data ?? []
        this.products = Array.isArray(list) ? list : []
      } catch (e) {
        console.error('Error loading products:', e)
      }
    },
    async loadCategories() {
      try {
        const res = await api.get('/categories')
        const list = res.data?.data ?? res.data ?? []
        this.categories = Array.isArray(list) ? list : []
        if (this.categories.length && !this.form.category_id) this.form.category_id = this.categories[0].id
      } catch (e) {
        console.error('Error loading categories:', e)
      }
    },
    openCreateModal() {
      this.editingProduct = null
      this.form = {
        category_id: this.categories[0]?.id ?? null,
        name: '',
        description: '',
        price: 0,
        currency: 'TMT',
        imagesStr: '',
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
        imagesStr: Array.isArray(images) ? images.join(', ') : '',
        is_new: product.is_new ?? false
      }
      this.showModal = true
    },
    closeModal() {
      this.showModal = false
      this.editingProduct = null
    },
    getImagesArray(str) {
      if (!str || typeof str !== 'string') return []
      return str.split(',').map(s => s.trim()).filter(Boolean)
    },
    async saveProduct() {
      this.loading = true
      try {
        const payload = {
          category_id: this.form.category_id,
          name: this.form.name,
          description: this.form.description || null,
          price: this.form.price,
          currency: this.form.currency,
          images: this.getImagesArray(this.form.imagesStr),
          is_new: this.form.is_new
        }
        if (this.editingProduct) {
          await api.put(`/products/${this.editingProduct.id}`, payload)
        } else {
          await api.post('/products', payload)
        }
        await this.loadProducts()
        this.closeModal()
      } catch (e) {
        console.error('Error saving product:', e)
      } finally {
        this.loading = false
      }
    },
    async deleteProduct(id) {
      if (!confirm('Удалить товар?')) return
      try {
        await api.delete(`/products/${id}`)
        await this.loadProducts()
      } catch (e) {
        console.error('Error deleting product:', e)
      }
    }
  },
  watch: {
    filterCategoryId() {
      this.loadProducts()
    }
  }
}
</script>

<style scoped>
@reference "../../assets/app.css";
.products-manager { max-width: 1200px; margin: 0 auto; }
.btn-primary {
  @apply flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg;
}
</style>
