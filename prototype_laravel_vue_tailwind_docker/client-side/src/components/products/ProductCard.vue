<template>
  <div class="bg-white dark:bg-deepblue border border-gray-200 dark:border-midnight rounded-xl p-4 hover:shadow-lg transition-shadow duration-200">
    <div class="flex items-start justify-between">
      <div class="flex items-center gap-3">
        <ProductImagePreview
          :src="firstImage"
          :alt="product.name"
        />
        <div>
          <h3 class="font-semibold text-gray-900 dark:text-white">{{ product.name }}</h3>
          <div class="flex items-center gap-2 flex-wrap mt-1">
            <span class="text-sm font-medium text-indigo-600 dark:text-indigo-400">{{ product.price }} {{ product.currency }}</span>
            <span
              v-if="product.is_new"
              class="px-2 py-0.5 bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-400 rounded-full text-xs font-medium"
            >
              Новинка
            </span>
          </div>
          <p class="text-xs text-gray-500 dark:text-fog mt-0.5">{{ categoryName }}</p>
        </div>
      </div>
      <div class="flex items-center gap-1">
        <button @click="$emit('edit', product)" title="Редактировать" class="p-1.5 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors">
          <AppIcon name="Edit" size="16" class="text-blue-600 dark:text-blue-400" />
        </button>
        <button @click="$emit('delete', product.id)" title="Удалить" class="p-1.5 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors">
          <AppIcon name="Trash2" size="16" class="text-red-600 dark:text-red-400" />
        </button>
      </div>
    </div>
    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-midnight">
      <div class="flex items-center justify-between text-sm">
        <div class="flex items-center gap-1.5">
          <AppIcon name="Eye" size="14" class="text-gray-400" />
          <span class="text-gray-600 dark:text-fog">Просмотры:</span>
          <span class="font-semibold">{{ product.views ?? 0 }}</span>
        </div>
        <span class="text-xs text-gray-400 dark:text-fog">ID: {{ product.id }}</span>
      </div>
    </div>
  </div>
</template>

<script>
import AppIcon from '@/components/icons/AppIcon.vue'
import ProductImagePreview from './ProductImagePreview.vue'

/** Карточка товара: изображение, название, цена, категория, действия. */
export default {
  name: 'ProductCard',
  components: { AppIcon, ProductImagePreview },
  props: {
    product: { type: Object, required: true },
    categories: { type: Array, default: () => [] }
  },
  emits: ['edit', 'delete'],
  computed: {
    firstImage() {
      const imgs = this.product.images
      if (!Array.isArray(imgs) || imgs.length === 0) return null
      const idx = this.product.active_image ?? 0
      return imgs[idx] || imgs[0]
    },
    categoryName() {
      const cat = this.categories.find((c) => c.id === this.product.category_id)
      return cat ? cat.name : `Категория #${this.product.category_id}`
    }
  }
}
</script>
