<template>
  <div class="product-image-preview">
    <div class="image-container flex items-center justify-center overflow-hidden rounded-lg">
      <img
        v-if="displaySrc"
        :src="displaySrc"
        :alt="alt"
        class="max-w-[70px] max-h-[70px] w-auto h-auto object-contain"
      />
      <AppIcon v-else :name="fallbackIcon" size="32" class="text-indigo-500 opacity-60" />
    </div>
  </div>
</template>

<script>
import AppIcon from '@/components/icons/AppIcon.vue'
import { productImageUrl } from '@/utils/storageUrl'

/** Превью изображения товара: 70×70, прозрачный фон. */
export default {
  name: 'ProductImagePreview',
  components: { AppIcon },
  props: {
    src: { type: String, default: null },
    alt: { type: String, default: '' },
    fallbackIcon: { type: String, default: 'Package' }
  },
  computed: {
    displaySrc() {
      if (!this.src) return null
      if (this.src.startsWith('data:') || this.src.startsWith('http')) return this.src
      return productImageUrl(this.src)
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
