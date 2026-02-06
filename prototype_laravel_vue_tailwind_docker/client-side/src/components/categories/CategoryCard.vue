<template>
  <div class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-lg transition-shadow duration-200">
    <div class="flex items-start justify-between">
      <div class="flex items-center gap-3">
        <div class="w-12 h-12 bg-gradient-to-br from-blue-50 to-indigo-100 rounded-lg flex items-center justify-center">
          <img
            v-if="category.image_icon"
            :src="imageUrl(category.image_icon)"
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
      <div class="flex items-center gap-1">
        <button
          @click="$emit('toggle-price', category)"
          :title="category.show_price ? 'Скрыть цену' : 'Показать цену'"
          class="p-1.5 hover:bg-gray-100 rounded-lg transition-colors"
        >
          <AppIcon :name="category.show_price ? 'Eye' : 'EyeOff'" size="16" class="text-gray-600" />
        </button>
        <button @click="$emit('edit', category)" title="Редактировать" class="p-1.5 hover:bg-blue-50 rounded-lg transition-colors">
          <AppIcon name="Edit" size="16" class="text-blue-600" />
        </button>
        <button @click="$emit('delete', category.id)" title="Удалить" class="p-1.5 hover:bg-red-50 rounded-lg transition-colors">
          <AppIcon name="Trash2" size="16" class="text-red-600" />
        </button>
      </div>
    </div>
    <div class="mt-4 pt-4 border-t border-gray-100">
      <div class="flex items-center justify-between text-sm">
        <div class="flex items-center gap-1.5">
          <AppIcon name="Package" size="14" class="text-gray-400" />
          <span class="text-gray-600">Продукты:</span>
          <span class="font-semibold">{{ productsCount }}</span>
        </div>
        <span class="text-xs text-gray-400">ID: {{ category.id }}</span>
      </div>
    </div>
  </div>
</template>

<script>
import AppIcon from '@/components/icons/AppIcon.vue'
import { categoryImageUrl } from '@/utils/storageUrl'

/** Карточка категории: иконка, название, порядок, show_price, кол-во продуктов, действия. */
export default {
  name: 'CategoryCard',
  components: { AppIcon },
  props: {
    category: { type: Object, required: true },
    productsCount: { type: Number, default: 0 }
  },
  emits: ['toggle-price', 'edit', 'delete'],
  methods: {
    imageUrl(filename) {
      return categoryImageUrl(filename)
    }
  }
}
</script>
