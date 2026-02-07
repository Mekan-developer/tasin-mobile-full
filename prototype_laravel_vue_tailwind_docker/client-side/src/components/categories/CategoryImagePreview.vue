<template>
  <div class="category-image-preview">
    <div
      class="image-container flex items-center justify-center overflow-hidden rounded-lg"
      :class="{ 'cursor-pointer hover:opacity-80': editable }"
      @click="editable && $refs.fileInput?.click()"
    >
      <img
        v-if="displaySrc"
        :src="displaySrc"
        :alt="alt"
        class="max-w-[70px] max-h-[70px] w-auto h-auto object-contain"
      />
      <AppIcon v-else :name="fallbackIcon" size="32" class="text-indigo-500 opacity-60" />
    </div>
    <template v-if="editable">
      <input
        ref="fileInput"
        type="file"
        accept="image/*"
        class="hidden"
        @change="onFileChange"
      />
      <button
        v-if="!hideUploadButton"
        type="button"
        @click="$refs.fileInput?.click()"
        class="mt-2 w-full px-3 py-2 border border-gray-300 dark:border-midnight rounded-md hover:border-indigo-500 dark:hover:border-indigo-500 hover:bg-gray-50 dark:hover:bg-fogactive/20 transition-colors flex items-center justify-center gap-2"
      >
        <AppIcon name="Upload" size="16" class="text-gray-500 dark:text-fog" />
        <span class="text-sm text-gray-700 dark:text-gray-300">{{ displaySrc ? 'Заменить' : 'Загрузить' }}</span>
      </button>
    </template>
  </div>
</template>

<script>
import AppIcon from '@/components/icons/AppIcon.vue'
import { categoryImageUrl } from '@/utils/storageUrl'

/** Превью изображения категории: 70x70, прозрачный фон. Режим только просмотр или с загрузкой. */
export default {
  name: 'CategoryImagePreview',
  components: { AppIcon },
  props: {
    /** URL изображения ( filename, data URL или полный URL). */
    src: { type: String, default: null },
    alt: { type: String, default: '' },
    /** Режим редактирования: показывать кнопку загрузки. */
    editable: { type: Boolean, default: false },
    /** Скрыть кнопку загрузки (при editable, если клик по контейнеру). */
    hideUploadButton: { type: Boolean, default: false },
    /** Иконка при отсутствии изображения. */
    fallbackIcon: { type: String, default: 'Folder' }
  },
  emits: ['upload'],
  computed: {
    displaySrc() {
      if (!this.src) return null
      if (this.src.startsWith('data:') || this.src.startsWith('http')) return this.src
      return categoryImageUrl(this.src)
    }
  },
  methods: {
    onFileChange(event) {
      const file = event.target.files[0]
      if (file) {
        const reader = new FileReader()
        reader.onload = (e) => this.$emit('upload', { file, preview: e.target.result })
        reader.readAsDataURL(file)
      }
      event.target.value = ''
    }
  }
}
</script>

<style scoped>
.image-container {
  width: 70px;
  height: 70px;
  min-width: 70px;
  min-height: 70px;
  background: transparent;
}
</style>
