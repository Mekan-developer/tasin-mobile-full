<template>
  <div
    v-if="modelValue"
    class="fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-50"
    @click.self="$emit('update:modelValue', false)"
  >
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md">
      <div class="flex items-center justify-between p-5">
        <h2 class="text-lg font-semibold text-gray-900">
          {{ editing ? 'Редактировать' : 'Создать' }} категорию
        </h2>
        <button
          @click="$emit('update:modelValue', false)"
          class="p-2 hover:bg-gray-100 rounded-md transition-colors"
        >
          <AppIcon name="X" size="18" class="text-gray-500" />
        </button>
      </div>

      <form @submit.prevent="$emit('save')" class="px-5 pb-5">
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Название категории *</label>
            <input
              :value="form.name"
              @input="$emit('update:form', { ...form, name: $event.target.value })"
              type="text"
              required
              placeholder="Например: Смартфоны"
              class="w-full px-3 py-2 border border-gray-300 text-gray-700 rounded-md focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 placeholder-gray-400"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Порядок отображения</label>
            <input
              :value="form.order"
              @input="$emit('update:form', { ...form, order: +$event.target.value })"
              type="number"
              min="1"
              class="w-full px-3 py-2 border border-gray-300 text-gray-700 rounded-md focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 placeholder-gray-400"
            />
            <p class="text-xs text-gray-500 mt-1.5">Чем меньше число, тем выше в списке</p>
          </div>

          <div class="flex items-center justify-between pt-2">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-0.5">Показывать цену</label>
              <p class="text-xs text-gray-500">Показывать цену продуктов в этой категории</p>
            </div>
            <button
              type="button"
              @click="$emit('update:form', { ...form, show_price: !form.show_price })"
              :class="form.show_price ? 'bg-indigo-600' : 'bg-gray-300'"
              class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
            >
              <span
                :class="form.show_price ? 'translate-x-6' : 'translate-x-1'"
                class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
              />
            </button>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Иконка категории</label>
            <div class="flex items-center gap-3">
              <div v-if="form.image_icon || imagePreview" class="w-14 h-14 bg-gray-50 rounded-md flex items-center justify-center border border-gray-200">
                <img
                  :src="imagePreview || imageUrl(form.image_icon)"
                  :alt="form.name"
                  class="w-10 h-10 object-contain"
                />
              </div>
              <div class="flex-1">
                <input type="file" ref="fileInput" @change="onFileChange" accept="image/*" class="hidden" />
                <button
                  type="button"
                  @click="$refs.fileInput.click()"
                  class="w-full px-4 py-3 border border-gray-300 rounded-md hover:border-indigo-500 hover:bg-gray-50 transition-colors"
                >
                  <div class="flex items-center justify-center gap-2">
                    <AppIcon name="Upload" size="16" class="text-gray-400" />
                    <span class="text-sm text-gray-700">Загрузить изображение</span>
                  </div>
                </button>
              </div>
            </div>
            <p class="text-xs text-gray-500 mt-1.5">PNG, JPG до 2MB</p>
          </div>
        </div>

        <div class="flex gap-3 mt-6 pt-5 border-t border-gray-100">
          <button
            type="button"
            @click="$emit('update:modelValue', false)"
            class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50 transition-colors"
          >
            Отмена
          </button>
          <button
            type="submit"
            :disabled="loading"
            class="flex-1 px-4 py-2.5 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="loading" class="flex items-center justify-center gap-2">
              <svg class="animate-spin h-4 w-4 text-white" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Сохранение...
            </span>
            <span v-else>{{ editing ? 'Сохранить' : 'Создать' }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import AppIcon from '@/components/icons/AppIcon.vue'
import { categoryImageUrl } from '@/utils/storageUrl'

/** Модальное окно создания/редактирования категории. */
export default {
  name: 'CategoryModal',
  components: { AppIcon },
  props: {
    modelValue: { type: Boolean, default: false },
    form: { type: Object, required: true },
    imagePreview: { type: String, default: null },
    loading: { type: Boolean, default: false },
    editing: { type: Boolean, default: false }
  },
  emits: ['update:modelValue', 'update:form', 'save', 'image-upload'],
  methods: {
    imageUrl(filename) {
      return categoryImageUrl(filename)
    },
    onFileChange(event) {
      const file = event.target.files[0]
      if (file) {
        const reader = new FileReader()
        reader.onload = (e) => this.$emit('image-upload', { file, preview: e.target.result })
        reader.readAsDataURL(file)
      }
    }
  }
}
</script>
