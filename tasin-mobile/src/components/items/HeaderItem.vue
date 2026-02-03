<template>
  <!-- Хедер категории: размеры и отступы как у основного хедера (AppHeader / .layout-header) -->
  <header class="category-header">
    <button class="back-button" @click="$router.back()" aria-label="Назад">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M15 18l-6-6 6-6"/>
      </svg>
    </button>
    <div class="header-text">
      <h1 class="page-title">{{ categoryName }}</h1>
      <span class="product-count">{{ productCount }} товаров</span>
    </div>
    <!-- Спейсер справа той же ширины, что и кнопка — заголовок по центру как в AppHeader -->
    <span class="header-spacer" aria-hidden="true" />
  </header>
</template>

<script setup>
defineProps({
  categoryName: {
    type: String,
    required: true
  },
  productCount: {
    type: Number,
    required: true
  }
})
</script>

<style scoped>
/* Высота из base.css (--header-height): всегда как layout-header — 86px до 768px, 64px с 768px */
.category-header {
  flex-shrink: 0;
  position: sticky;
  top: 0;
  z-index: 10;
  display: flex;
  align-items: center;
  gap: 12px;
  width: 100%;
  min-width: 0;
  height: var(--header-height);
  min-height: calc(var(--header-height) + env(safe-area-inset-top, 0px));
  padding: 0 16px;
  padding-top: env(safe-area-inset-top, 0px);
  padding-left: calc(16px + env(safe-area-inset-left, 0px));
  padding-right: calc(16px + env(safe-area-inset-right, 0px));
  background-color: var(--bg-primary);
  border-bottom: 1px solid var(--border-color);
  transition: background-color 0.3s, border-color 0.3s;
}

/* Кнопка «Назад»: размер как у лого в AppHeader (40px), на мобильных — с фоном */
.back-button {
  flex-shrink: 0;
  background: var(--hover-bg, rgba(0, 0, 0, 0.06));
  border: 1px solid var(--border-color, rgba(0, 0, 0, 0.08));
  cursor: pointer;
  width: 40px;
  height: 40px;
  min-width: 40px;
  min-height: 40px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--text-primary);
  transition: background 0.2s, transform 0.15s;
  -webkit-tap-highlight-color: transparent;
}

.back-button:active {
  background: var(--hover-bg);
  transform: scale(0.96);
}

/* Спейсер справа = ширина кнопки «Назад», чтобы заголовок был по центру как в AppHeader */
.header-spacer {
  flex-shrink: 0;
  width: 40px;
  height: 40px;
  min-width: 40px;
  min-height: 40px;
}

/* Блок заголовка по центру оставшегося места — как в AppHeader */
.header-text {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 2px;
  min-width: 0;
  text-align: center;
}

.page-title {
  font-size: clamp(1rem, 4.5vw, 1.5rem);
  font-weight: bold;
  color: var(--text-primary);
  margin: 0;
  line-height: 1.25;
  letter-spacing: -0.02em;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 100%;
}

.product-count {
  font-size: var(--text-sm);
  color: var(--text-muted);
  font-weight: 400;
}

@media (min-width: 768px) {
  .category-header {
    padding: 0 24px;
    padding-left: calc(24px + env(safe-area-inset-left, 0px));
    padding-right: calc(24px + env(safe-area-inset-right, 0px));
  }

  .back-button:hover {
    background: var(--hover-bg, rgba(0, 0, 0, 0.04));
  }
}

/* Десктоп: как AppHeader — кнопка 40px, заголовок по центру */
@media (min-width: 1024px) {
  .category-header {
    padding: 0 32px;
    padding-left: calc(32px + env(safe-area-inset-left, 0px));
    padding-right: calc(32px + env(safe-area-inset-right, 0px));
  }

  .back-button {
    width: 40px;
    height: 40px;
    min-width: 40px;
    min-height: 40px;
    border-radius: 12px;
    background: transparent;
    border-color: transparent;
  }
}

@media (min-width: 1280px) {
  .category-header {
    padding: 0 48px;
    padding-left: calc(48px + env(safe-area-inset-left, 0px));
    padding-right: calc(48px + env(safe-area-inset-right, 0px));
  }
}
</style>
