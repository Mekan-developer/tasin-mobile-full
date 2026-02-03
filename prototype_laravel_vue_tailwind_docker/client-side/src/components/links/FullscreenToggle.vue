<template>
  <button @click="toggle" :disabled="!isSupported">
      <AppIcon v-if="isFullscreen" name="Shrink" size="20" :color="'#181c36'" />
      <AppIcon v-else name="Expand" size="20" :color="'#181c36'"/>
  </button>
</template>

<script>
export default {
  name: 'FullscreenToggle',
  data() {
    return {
      isSupported: !!document.documentElement.requestFullscreen,
      isFullscreen: !!document.fullscreenElement
    }
  },
  methods: {
    async enter() {
      if (!this.isSupported) return
      await document.documentElement.requestFullscreen()
    },
    async exit() {
      if (!document.fullscreenElement) return
      await document.exitFullscreen()
    },
    async toggle() {
      if (this.isFullscreen) await this.exit()
      else await this.enter()
    },
    _onChange() {
      this.isFullscreen = !!document.fullscreenElement
    }
  },
  mounted() {
    document.addEventListener('fullscreenchange', this._onChange)
  },
  beforeUnmount() {
    document.removeEventListener('fullscreenchange', this._onChange)
  }
}
</script>
