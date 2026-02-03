<template>
  <router-view />
  <GlobalLoader />
</template>

<script>
import GlobalLoader from '@/components/loaders/GlobalLoader.vue'
export default {
  name: 'App',
  components: {
    GlobalLoader
  }
}
</script>
<style scoped>


</style>
