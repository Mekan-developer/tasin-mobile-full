<template>
  <div class="text-center py-12 border-2 border-dashed border-gray-300 dark:border-midnight rounded-xl">
    <AppIcon name="Package" size="48" class="text-gray-300 dark:text-fog mx-auto mb-4" />
    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Товаров пока нет</h3>
    <p class="text-gray-600 dark:text-fog mb-6">Создайте первый товар для каталога</p>
    <button
      @click="$emit('create')"
      class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors"
    >
      Добавить товар
    </button>
  </div>
</template>

<script>
import AppIcon from '@/components/icons/AppIcon.vue'

/** Пустое состояние: нет товаров, кнопка создания. */
export default {
  name: 'ProductEmptyState',
  components: { AppIcon },
  emits: ['create']
}
</script>
