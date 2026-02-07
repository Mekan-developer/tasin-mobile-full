<template>
  <div class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-lg transition-shadow duration-200">
    <div class="flex items-start justify-between">
      <div class="flex items-center gap-3">
        <CategoryImagePreview
          :src="category.image_icon"
          :alt="category.name"
        />
        <div>
          <h3 class="font-semibold text-gray-900">{{ category.name }}</h3>
          <div class="flex items-center gap-2 flex-wrap mt-1">
            <span class="text-xs text-gray-500">Порядок: {{ category.order }}</span>
            <span
              :class="category.show_price ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
              class="px-2 py-1 rounded-full text-xs font-medium"
            >
              {{ category.show_price ? 'Цена показ.' : 'Цена скрыта' }}
            </span>
            <span
              :class="category.is_active !== false ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'"
              class="px-2 py-1 rounded-full text-xs font-medium"
            >
              {{ category.is_active !== false ? 'Активна' : 'Скрыта' }}
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
        <button
          @click="$emit('toggle-active', category)"
          :title="category.is_active !== false ? 'Скрыть категорию' : 'Активировать'"
          class="p-1.5 hover:bg-gray-100 rounded-lg transition-colors"
        >
          <AppIcon :name="category.is_active !== false ? 'CheckCircle' : 'Circle'" size="16" :class="category.is_active !== false ? 'text-emerald-600' : 'text-gray-400'" />
        </button>
        <button @click="$emit('delete', category.id)" title="Удалить" class="p-1.5 hover:bg-red-50 rounded-lg transition-colors">
          <AppIcon name="Trash2" size="16" class="text-red-600" />
        </button>
      </div>
    </div>
    <div class="mt-4 pt-4 border-t border-gray-100">
      <div class="flex items-center justify-between text-sm">
        <div class="flex items-center gap-3">
          <div class="flex items-center gap-1.5">
            <AppIcon name="Package" size="14" class="text-gray-400" />
            <span class="text-gray-600">Продукты:</span>
            <span class="font-semibold">{{ productsCount }}</span>
          </div>
          <div class="flex items-center gap-1.5">
            <AppIcon name="Eye" size="14" class="text-gray-400" />
            <span class="text-gray-600">Просмотры:</span>
            <span class="font-semibold">{{ category.views ?? 0 }}</span>
          </div>
        </div>
        <span class="text-xs text-gray-400">ID: {{ category.id }}</span>
      </div>
    </div>
  </div>
</template>

<script>
import AppIcon from '@/components/icons/AppIcon.vue'
import CategoryImagePreview from './CategoryImagePreview.vue'

/** Карточка категории: иконка, название, порядок, show_price, кол-во продуктов, действия. */
export default {
  name: 'CategoryCard',
  components: { AppIcon, CategoryImagePreview },
  props: {
    category: { type: Object, required: true },
    productsCount: { type: Number, default: 0 }
  },
  emits: ['toggle-price', 'toggle-active', 'edit', 'delete']
}
</script>
