import './assets/main.css'
import { registerSW } from 'virtual:pwa-register'

registerSW({ immediate: true })

import { createApp } from 'vue'
import App from './App.vue'
import { createPinia } from 'pinia'
import { router } from './router'

createApp(App).use(createPinia()).use(router).mount('#app')

/** Скрывает экран-заставку PWA через 3 секунды после монтирования приложения */
const splashEl = document.getElementById('pwa-splash')
if (splashEl) {
  setTimeout(() => {
    splashEl.style.display = 'none'
  }, 3000)
}
