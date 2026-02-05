<template>
  <div class="slides-manager p-4 md:p-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Слайды</h1>
        <p class="text-gray-600 mt-1">Всего: {{ slides.length }}</p>
      </div>
      <button
        @click="openCreateModal"
        class="btn-primary"
      >
        <AppIcon name="Plus" size="18" />
        Добавить слайд
      </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div
        v-for="slide in slides"
        :key="slide.id"
        class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-lg transition-shadow"
      >
        <div
          class="h-24 rounded-lg mb-3 flex items-center justify-center text-white font-bold"
          :style="{ backgroundColor: slide.bg_color || '#6366f1' }"
        >
          {{ slide.badge || slide.title }}
        </div>
        <h3 class="font-semibold text-gray-900 truncate">{{ slide.title }}</h3>
        <p class="text-sm text-gray-500 mt-1">{{ slide.price }} {{ slide.currency }}</p>
        <div class="flex items-center gap-1 mt-3">
          <button @click="openEditModal(slide)" class="p-1.5 hover:bg-blue-50 rounded-lg" title="Редактировать">
            <AppIcon name="Edit" size="16" class="text-blue-600" />
          </button>
          <button @click="deleteSlide(slide.id)" class="p-1.5 hover:bg-red-50 rounded-lg" title="Удалить">
            <AppIcon name="Trash2" size="16" class="text-red-600" />
          </button>
        </div>
      </div>
    </div>

    <div v-if="slides.length === 0" class="text-center py-12 text-gray-500">
      Нет слайдов. Добавьте первый.
    </div>

    <!-- Modal create/edit -->
    <div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click.self="closeModal">
      <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 max-h-[90vh] overflow-y-auto">
        <h2 class="text-lg font-semibold mb-4">{{ editingSlide ? 'Редактировать слайд' : 'Новый слайд' }}</h2>
        <form @submit.prevent="saveSlide" class="space-y-3">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Бейдж</label>
            <input v-model="form.badge" type="text" class="w-full border rounded-lg px-3 py-2" placeholder="НОВИНКА" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Заголовок *</label>
            <input v-model="form.title" type="text" required class="w-full border rounded-lg px-3 py-2" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Описание</label>
            <textarea v-model="form.description" rows="2" class="w-full border rounded-lg px-3 py-2"></textarea>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Цвет фона (hex)</label>
            <input v-model="form.bg_color" type="text" class="w-full border rounded-lg px-3 py-2" placeholder="#22c55e" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Изображение (имя файла)</label>
            <input v-model="form.image" type="text" required class="w-full border rounded-lg px-3 py-2" />
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Цена *</label>
              <input v-model.number="form.price" type="number" step="0.01" required class="w-full border rounded-lg px-3 py-2" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Валюта *</label>
              <input v-model="form.currency" type="text" required class="w-full border rounded-lg px-3 py-2" placeholder="TMT" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Старая цена</label>
            <input v-model.number="form.old_price" type="number" step="0.01" class="w-full border rounded-lg px-3 py-2" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Скидка</label>
            <input v-model="form.discount" type="text" class="w-full border rounded-lg px-3 py-2" placeholder="−10%" />
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

/** Страница управления слайдами (API apiResource slides). */
export default {
  name: 'SlidesPage',
  components: { AppIcon },
  data() {
    return {
      slides: [],
      showModal: false,
      editingSlide: null,
      loading: false,
      form: {
        badge: '',
        title: '',
        description: '',
        bg_color: '#6366f1',
        image: '',
        price: 0,
        currency: 'TMT',
        old_price: null,
        discount: ''
      }
    }
  },
  mounted() {
    this.loadSlides()
  },
  methods: {
    async loadSlides() {
      try {
        const res = await api.get('/slides')
        const list = res.data?.data ?? res.data ?? []
        this.slides = Array.isArray(list) ? list : []
      } catch (e) {
        console.error('Error loading slides:', e)
      }
    },
    openCreateModal() {
      this.editingSlide = null
      this.form = { badge: '', title: '', description: '', bg_color: '#6366f1', image: '', price: 0, currency: 'TMT', old_price: null, discount: '' }
      this.showModal = true
    },
    openEditModal(slide) {
      this.editingSlide = slide
      this.form = {
        badge: slide.badge ?? '',
        title: slide.title ?? '',
        description: slide.description ?? '',
        bg_color: slide.bg_color ?? '#6366f1',
        image: slide.image ?? '',
        price: slide.price ?? 0,
        currency: slide.currency ?? 'TMT',
        old_price: slide.old_price ?? null,
        discount: slide.discount ?? ''
      }
      this.showModal = true
    },
    closeModal() {
      this.showModal = false
      this.editingSlide = null
    },
    async saveSlide() {
      this.loading = true
      try {
        const payload = { ...this.form }
        if (this.editingSlide) {
          await api.put(`/slides/${this.editingSlide.id}`, payload)
        } else {
          await api.post('/slides', payload)
        }
        await this.loadSlides()
        this.closeModal()
      } catch (e) {
        console.error('Error saving slide:', e)
      } finally {
        this.loading = false
      }
    },
    async deleteSlide(id) {
      if (!confirm('Удалить слайд?')) return
      try {
        await api.delete(`/slides/${id}`)
        await this.loadSlides()
      } catch (e) {
        console.error('Error deleting slide:', e)
      }
    }
  }
}
</script>

<style scoped>
@reference "../../assets/app.css";
.slides-manager { max-width: 1200px; margin: 0 auto; }
.btn-primary {
  @apply flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg;
}
</style>
