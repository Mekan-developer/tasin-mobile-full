<template>
  <div v-if="loading" class="loader-backdrop">
    <div class="spinner"></div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  name: 'GlobalLoader',
  computed: {
    ...mapGetters('ui', ['loading'])
  }
}
</script>

<style>
.loader-backdrop {
  position: fixed; inset: 0; display:flex; align-items:center; justify-content:center;
  background: rgba(0,0,0,.15); backdrop-filter: blur(2px); z-index: 9999;
}
.spinner {
  width: 40px; height: 40px; border: 3px solid #ddd; border-top-color: #0aa;
  border-radius: 50%; animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg) } }
</style>
