<template>
  <div>
    <div class="w-auto h-[40px]">
      <img :src="projectLogo" alt="logo elyeterli dal" class="w-full h-full object-contain" />
    </div>
  </div>
</template>

<script setup>
import logo_for_dark_mode from '@/assets/logo/for_dark_mode.png'
import logo_light_mode2 from '@/assets/logo/for_light_mode2.png'
import { computed, watch } from 'vue'
import { useTheme } from '@/composables/useTheme'
const { isDark } = useTheme()

const projectLogo = computed(() => {
  console.log('isDark in ProjectLogo:', isDark.value)
  return isDark.value ? logo_for_dark_mode : logo_light_mode2
})

watch(
  () => isDark.value,
  (newVal) => {
    console.log('Theme changed in ProjectLogo. isDark:', newVal)
  },
)
</script>
