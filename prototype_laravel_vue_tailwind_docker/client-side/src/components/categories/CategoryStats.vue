<template>
  <div class="mt-8 p-4 bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl border border-gray-200">
    <h3 class="font-semibold text-gray-900 mb-3">Статистика по категориям</h3>
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
      <div class="bg-white p-3 rounded-lg border border-gray-200">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
            <AppIcon name="Folder" size="16" class="text-blue-600" />
          </div>
          <div>
            <p class="text-sm text-gray-500">Всего категорий</p>
            <p class="text-lg font-bold text-gray-900">{{ categoriesCount }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white p-3 rounded-lg border border-gray-200">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
            <AppIcon name="CheckCircle" size="16" class="text-emerald-600" />
          </div>
          <div>
            <p class="text-sm text-gray-500">Активных</p>
            <p class="text-lg font-bold text-gray-900">{{ activeCount }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white p-3 rounded-lg border border-gray-200">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
            <AppIcon name="Eye" size="16" class="text-green-600" />
          </div>
          <div>
            <p class="text-sm text-gray-500">Цена показывается</p>
            <p class="text-lg font-bold text-gray-900">{{ showPriceCount }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white p-3 rounded-lg border border-gray-200">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
            <AppIcon name="Package" size="16" class="text-purple-600" />
          </div>
          <div>
            <p class="text-sm text-gray-500">Всего продуктов</p>
            <p class="text-lg font-bold text-gray-900">{{ productsCount }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white p-3 rounded-lg border border-gray-200">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
            <AppIcon name="TrendingUp" size="16" class="text-amber-600" />
          </div>
          <div>
            <p class="text-sm text-gray-500">Самые просматриваемые</p>
            <p class="text-lg font-bold text-gray-900">{{ mostPopularName }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import AppIcon from '@/components/icons/AppIcon.vue'

/** Панель статистики по категориям. */
export default {
  name: 'CategoryStats',
  components: { AppIcon },
  props: {
    categoriesCount: { type: Number, default: 0 },
    activeCount: { type: Number, default: 0 },
    showPriceCount: { type: Number, default: 0 },
    productsCount: { type: Number, default: 0 },
    mostPopularName: { type: String, default: '—' }
  }
}
</script>
