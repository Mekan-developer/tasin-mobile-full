<template>
  <div class="mt-8 p-4 bg-gradient-to-r from-gray-50 to-blue-50 dark:from-midnight/50 dark:to-deepblue/50 rounded-xl border border-gray-200 dark:border-midnight">
    <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Статистика по товарам</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div class="bg-white dark:bg-deepblue p-3 rounded-lg border border-gray-200 dark:border-midnight">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
            <AppIcon name="Package" size="16" class="text-blue-600 dark:text-blue-400" />
          </div>
          <div>
            <p class="text-sm text-gray-500 dark:text-fog">Всего товаров</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ productsCount }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white dark:bg-deepblue p-3 rounded-lg border border-gray-200 dark:border-midnight">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
            <AppIcon name="Sparkles" size="16" class="text-amber-600 dark:text-amber-400" />
          </div>
          <div>
            <p class="text-sm text-gray-500 dark:text-fog">Новинок</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ newCount }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white dark:bg-deepblue p-3 rounded-lg border border-gray-200 dark:border-midnight">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
            <AppIcon name="Eye" size="16" class="text-green-600 dark:text-green-400" />
          </div>
          <div>
            <p class="text-sm text-gray-500 dark:text-fog">Всего просмотров</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ totalViews }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white dark:bg-deepblue p-3 rounded-lg border border-gray-200 dark:border-midnight">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
            <AppIcon name="Folder" size="16" class="text-purple-600 dark:text-purple-400" />
          </div>
          <div>
            <p class="text-sm text-gray-500 dark:text-fog">Категорий</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ categoriesCount }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import AppIcon from '@/components/icons/AppIcon.vue'

/** Панель статистики по товарам. */
export default {
  name: 'ProductStats',
  components: { AppIcon },
  props: {
    productsCount: { type: Number, default: 0 },
    newCount: { type: Number, default: 0 },
    totalViews: { type: Number, default: 0 },
    categoriesCount: { type: Number, default: 0 }
  }
}
</script>
