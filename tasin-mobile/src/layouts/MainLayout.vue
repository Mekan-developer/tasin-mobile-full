<template>
  <div class="layout">
    <!-- Header -->
    <header class="layout-header" v-show="!$route.meta.hideHeader">
      <AppHeader />
    </header>

    <!-- Main: только эта область скроллится; pull-to-refresh показывает лоадер здесь -->
    <main
      class="layout-main"
      ref="mainRef"
      @touchstart.passive="onTouchStart"
      @touchmove="onTouchMove"
      @touchend="onTouchEnd"
    >
      <!-- Лоадер при потягивании вниз / обновлении (без движения хедера и футера) -->
      <div
        class="layout-main-pull-loader"
        :style="{ height: loaderHeight + 'px' }"
        aria-hidden="true"
      >
        <span
          class="layout-main-pull-spinner"
          :class="{ 'is-active': isRefreshing || pullDistance > 20 }"
        />
      </div>
      <div class="layout-main-scroll" ref="scrollRef">
        <slot></slot>
      </div>
    </main>

    <!-- Footer -->
    <footer class="layout-footer">
      <AppFooter />
    </footer>
  </div>
</template>

<script setup>
import { ref, provide, computed } from 'vue'
import { useRoute } from 'vue-router'
import AppHeader from '@/components/AppHeader.vue'
import AppFooter from '@/components/AppFooter.vue'

const route = useRoute()
const mainRef = ref(null)
const scrollRef = ref(null)
const pullDistance = ref(0)
const isRefreshing = ref(false)
const touchStartY = ref(0)
const refreshCallback = ref(null)

/** Высота полосы с лоадером при pull/refresh (0–56px). При скрытом хедере (ProductPage) — 0, спиннер показывается на странице под своим хедером */
const loaderHeight = computed(() => {
  if (route.meta.hideHeader) return 0
  if (isRefreshing.value) return 56
  return Math.min(pullDistance.value, 56)
})

/** Регистрация обработчика обновления + состояние pull/refresh для страниц со своим хедером (PWA) */
provide('mainLayout', {
  registerRefresh: (fn) => { refreshCallback.value = fn },
  unregisterRefresh: () => { refreshCallback.value = null },
  isRefreshing,
  pullDistance
})

function onTouchStart(e) {
  touchStartY.value = e.touches[0].clientY
}

function onTouchMove(e) {
  const el = scrollRef.value
  if (!el) return
  if (el.scrollTop > 0) return
  const y = e.touches[0].clientY
  const delta = y - touchStartY.value
  if (delta > 0) {
    pullDistance.value = Math.min(delta * 0.5, 80)
    if (pullDistance.value > 10) e.preventDefault()
  }
}

/** Минимальное время показа спиннера при обновлении (мс) */
const MIN_REFRESH_DISPLAY_MS = 2000

async function onTouchEnd() {
  if (pullDistance.value < 50 || isRefreshing.value) {
    pullDistance.value = 0
    return
  }
  isRefreshing.value = true
  const refreshStart = Date.now()
  try {
    const fn = refreshCallback.value
    if (fn) {
      const p = fn()
      if (p && typeof p.then === 'function') await p
    }
  } finally {
    const elapsed = Date.now() - refreshStart
    const remain = Math.max(0, MIN_REFRESH_DISPLAY_MS - elapsed)
    if (remain > 0) await new Promise((r) => setTimeout(r, remain))
    isRefreshing.value = false
    pullDistance.value = 0
  }
}
</script>

<style scoped>
/* Высота 100% чтобы main (flex:1) занял оставшееся место, футер всегда внизу */
.layout {
  display: flex;
  flex-direction: column;
  height: 100%;
  min-height: 100vh;
  min-height: 100dvh;
  background-color: var(--bg-secondary);
  position: relative;
}

/* Высота из base.css (--header-height): 86px до 768px, 64px с 768px — как HeaderItem */
.layout-header {
  position: sticky;
  top: 0;
  z-index: 100;
  background-color: var(--bg-primary);
  border-bottom: 1px solid var(--border-color);
  padding: 0 16px;
  padding-top: env(safe-area-inset-top, 0px);
  padding-left: calc(16px + env(safe-area-inset-left, 0px));
  padding-right: calc(16px + env(safe-area-inset-right, 0px));
  height: var(--header-height);
  min-height: calc(var(--header-height) + env(safe-area-inset-top, 0px));
  display: flex;
  align-items: center;
  transition: background-color 0.3s, border-color 0.3s;
}

/* flex: 1 + min-height: 0 — main не скроллится сам, скролл только в .layout-main-scroll; pull-to-refresh лоадер сверху */
.layout-main {
  flex: 1 1 0;
  min-height: 0;
  height: calc(100% - 186px);
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

/** Полоса с лоадером при pull-to-refresh (высота 0–56px) */
.layout-main-pull-loader {
  flex-shrink: 0;
  overflow: hidden;
  transition: height 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--bg-secondary);
}

/** Спиннер: виден при потягивании или обновлении */
.layout-main-pull-spinner {
  width: 28px;
  height: 28px;
  border: 3px solid var(--border-color);
  border-top-color: var(--accent-color);
  border-radius: 50%;
  opacity: 0;
  transition: opacity 0.2s;
}
.layout-main-pull-spinner.is-active {
  opacity: 1;
  animation: layout-spin 0.8s linear infinite;
}
@keyframes layout-spin {
  to { transform: rotate(360deg); }
}

/** Область контента с вертикальным скроллом */
.layout-main-scroll {
  flex: 1 1 0;
  min-height: 0;
  overflow-y: auto;
  padding-bottom: 72px;
  -webkit-overflow-scrolling: touch;
}

/* Нижняя навигация прижата к низу экрана; в PWA учитываем safe-area */
.layout-footer {
  position: static;
  bottom: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  background-color: var(--footer-bg);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border-top: 1px solid var(--footer-border);
  padding-bottom: max(12px, env(safe-area-inset-bottom, 0px), constant(safe-area-inset-bottom, 0px));
  transition: background-color 0.3s, border-color 0.3s;
  height: auto;
  min-height: 60px;
  box-sizing: border-box;
  -webkit-transform: translateZ(0);
  transform: translateZ(0);
}

@media (min-width: 768px) {
  .layout-header {
    padding: 0 24px;
  }

  .layout-main-scroll {
    padding-bottom: 80px;
  }
}

/* Desktop: хедер и основной блок адаптированы под широкий экран */
@media (min-width: 1024px) {
  .layout-header {
    padding: 0 32px;
  }

  .layout-main-scroll {
    padding-bottom: 88px;
  }

  .layout-footer {
    padding: 12px 32px;
  }
}

@media (min-width: 1280px) {
  .layout-header {
    padding: 0 48px;
  }

  .layout-footer {
    padding: 14px 48px;
  }
}
</style>
