<template>
  <div class="multi-image-picker">
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
      {{ label }}
    </label>
    <div class="flex flex-wrap gap-3">
      <!-- Существующие изображения (превью по URL) -->
      <div
        v-for="(item, idx) in items"
        :key="item.id || item.filename || idx"
        class="relative group"
      >
        <div class="w-20 h-20 rounded-lg overflow-hidden border border-gray-200 dark:border-midnight bg-gray-50 dark:bg-midnight/50 flex items-center justify-center">
          <img
            :src="getPreviewUrl(item)"
            :alt="`Изображение ${idx + 1}`"
            class="w-full h-full object-contain"
          />
        </div>
        <button
          v-if="removable"
          type="button"
          @click="removeAt(idx)"
          class="absolute -top-1 -right-1 w-6 h-6 rounded-full bg-red-500 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow"
          :title="'Удалить'"
        >
          <AppIcon name="X" size="14" />
        </button>
      </div>
      <!-- Кнопка добавления -->
      <div
        v-if="items.length < maxCount"
        class="w-20 h-20 rounded-lg border-2 border-dashed border-gray-300 dark:border-midnight hover:border-indigo-500 dark:hover:border-indigo-500 flex items-center justify-center cursor-pointer transition-colors"
        @click="$refs.fileInput?.click()"
      >
        <AppIcon name="Plus" size="24" class="text-gray-500 dark:text-fog" />
      </div>
    </div>
    <input
      ref="fileInput"
      type="file"
      accept="image/*"
      multiple
      class="hidden"
      @change="onFileChange"
    />
    <p v-if="hint" class="text-xs text-gray-500 dark:text-fog mt-1.5">{{ hint }}</p>
  </div>
</template>

<script>
import AppIcon from '@/components/icons/AppIcon.vue'
import { productImageUrl } from '@/utils/storageUrl'

/** Множественный выбор изображений: превью, добавление, удаление. */
export default {
  name: 'MultiImagePicker',
  components: { AppIcon },
  props: {
    /** Модель: массив { id, filename?, preview? } — filename для существующих, preview для preview data URL. */
    modelValue: { type: Array, default: () => [] },
    label: { type: String, default: 'Изображения' },
    hint: { type: String, default: 'PNG, JPG до 2MB. Кликните + для добавления.' },
    maxCount: { type: Number, default: 10 },
    removable: { type: Boolean, default: true }
  },
  emits: ['update:modelValue'],
  computed: {
    items() {
      return this.modelValue || []
    }
  },
  methods: {
    getPreviewUrl(item) {
      const obj = typeof item === 'string' ? { filename: item } : item
      if (obj?.preview) return obj.preview
      if (obj?.filename) return productImageUrl(obj.filename)
      return ''
    },
    removeAt(idx) {
      const next = this.items.filter((_, i) => i !== idx)
      this.$emit('update:modelValue', next)
    },
    async onFileChange(event) {
      const files = Array.from(event.target.files || [])
      event.target.value = ''
      if (files.length === 0) return
      const remaining = this.maxCount - this.items.length
      const limited = files.slice(0, Math.max(0, remaining))
      if (limited.length === 0) return
      const readAsDataUrl = (f) =>
        new Promise((resolve) => {
          const r = new FileReader()
          r.onload = (e) => resolve({ file: f, preview: e.target.result })
          r.readAsDataURL(f)
        })
      const results = await Promise.all(limited.map(readAsDataUrl))
      const toAdd = results.map((r) => ({
        id: `file-${Date.now()}-${Math.random().toString(36).slice(2)}`,
        file: r.file,
        preview: r.preview
      }))
      this.$emit('update:modelValue', [...this.items, ...toAdd])
    }
  }
}
</script>
