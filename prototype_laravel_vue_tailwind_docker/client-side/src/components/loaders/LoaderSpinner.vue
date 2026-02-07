<template>
  <span
    :class="[
      'inline-block border-2 border-gray-200 dark:border-gray-600 border-t-current rounded-full animate-spin',
      sizeClass
    ]"
    :style="color !== 'currentColor' ? { borderTopColor: color } : {}"
    role="status"
    aria-label="Загрузка"
  />
</template>

<script>
/** Небольшой спиннер для использования внутри кнопок и инлайн-элементов. */
export default {
  name: 'LoaderSpinner',
  props: {
    size: { type: String, default: 'sm' },
    color: { type: String, default: 'currentColor' }
  },
  computed: {
    sizeClass() {
      const map = { xs: 'w-3 h-3', sm: 'w-4 h-4', md: 'w-5 h-5', lg: 'w-6 h-6' }
      return map[this.size] || map.sm
    }
  }
}
</script>
