<template>
  <header class="flex flex-row justify-between items-center w-full">
    <ProjectLogo />
    <ThemeToggle class="ml-auto" />
  </header>
</template>

<script setup>
import ProjectLogo from './ProjectLogo.vue'
import ThemeToggle from '@/components/ThemeToggle.vue'
</script>
