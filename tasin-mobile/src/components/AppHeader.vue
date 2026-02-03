<template>
  <!-- Хедер: лого слева, название по центру, переключатель темы справа -->
  <div class="header">
    <div class="header-logo">
      <ProjectLogo />
    </div>
    <div class="header-text">
      <h1 class="page-title">Täsin Mobile</h1>
    </div>
    <div class="header-toggle">
      <ThemeToggle />
    </div>
  </div>
</template>

<script setup>
import ProjectLogo from '@/components/icons/ProjectLogo.vue'
import ThemeToggle from '@/components/ThemeToggle.vue'
</script>

<style scoped>
.header {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 12px;
  width: 100%;
  min-width: 0;
  height: 100%;
}

/* Логотип всегда слева, фиксированный размер под мобильный хедер */
.header-logo {
  flex-shrink: 0;
  order: 1;
  width: 40px;
  height: 40px;
}

.header-logo :deep(div),
.header-logo :deep(img) {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

/* Название в центре, при нехватке места обрезается с многоточием */
.header-text {
  flex: 1;
  min-width: 0;
  text-align: center;
  order: 2;
}

.page-title {
  font-size: clamp(1rem, 4.5vw, 1.5rem);
  font-weight: bold;
  color: var(--text-primary);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  margin: 0;
}

/* Переключатель темы справа */
.header-toggle {
  flex-shrink: 0;
  order: 3;
  color: var(--text-primary);
  transition: color 0.3s;
}
</style>
