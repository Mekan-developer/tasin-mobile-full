import { fileURLToPath, URL } from 'node:url'

import { defineConfig, loadEnv } from 'vite'
import tailwindcss from '@tailwindcss/vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'
import { VitePWA } from 'vite-plugin-pwa'

// https://vite.dev/config/
export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '')
  const apiBase = (env.VITE_API_URL || 'http://192.168.50.72:3000').replace(/\/$/, '')
  const apiOriginEscaped = apiBase.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')
  const apiCachePattern = new RegExp(`^${apiOriginEscaped}/`)

  return {
  plugins: [
    vue(),
    vueDevTools(),
    tailwindcss(),
    VitePWA({
      registerType: 'autoUpdate',
      strategies: 'generateSW',

      includeAssets: ['favicon.ico', 'tasin-mobile.png', 'tasin-mobile.png', 'tasin-mobile.png'],
      manifest: false,

      workbox: {
        globPatterns: ['**/*.{js,css,html,ico,png,svg,woff2}'],
        runtimeCaching: [
          {
            urlPattern: /^https:\/\/fonts\.googleapis\.com\/.*/i,
            handler: 'CacheFirst',
            options: {
              cacheName: 'google-fonts-cache',
              expiration: { maxEntries: 10, maxAgeSeconds: 60 * 60 * 24 * 365 },
              cacheableResponse: { statuses: [0, 200] }
            }
          },
          // Кэш API: при отсутствии сети ответы берутся из кэша (офлайн-режим)
          {
            urlPattern: apiCachePattern,
            handler: 'NetworkFirst',
            options: {
              cacheName: 'api-cache',
              networkTimeoutSeconds: 10,
              expiration: { maxEntries: 100, maxAgeSeconds: 60 * 60 * 24 },
              cacheableResponse: { statuses: [0, 200] }
            }
          }
        ]
      },
      devOptions: {enabled: true, type: 'module'},
      injectRegister: 'auto',
      pwaAssets: {disabled: true, config: true},
    })
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
    },
  },
  server: {
    host: '192.168.50.72',
    port: 5173
  },
  }
})
