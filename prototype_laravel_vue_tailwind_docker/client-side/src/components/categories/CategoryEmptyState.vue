<template>
  <div class="text-center py-12 border-2 border-dashed border-gray-300 rounded-xl">
    <AppIcon name="FolderOpen" size="48" class="text-gray-300 mx-auto mb-4" />
    <h3 class="text-lg font-medium text-gray-900 mb-2">Категорий пока нет</h3>
    <p class="text-gray-600 mb-6">Создайте первую категорию для организации продуктов</p>
    <button
      @click="$emit('create')"
      class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors"
    >
      Создать категорию
    </button>
  </div>
</template>

<script>
import AppIcon from '@/components/icons/AppIcon.vue'

/** Пустое состояние: нет категорий, кнопка создания. */
export default {
  name: 'CategoryEmptyState',
  components: { AppIcon },
  emits: ['create']
}
</script>
