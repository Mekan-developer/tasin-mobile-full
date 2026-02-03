<script setup>
  import { ref, computed, onMounted, watch, nextTick } from 'vue'

  const props = defineProps({
    variants: {
      type: Array,
      default: () => []
    }
  })

  const emit = defineEmits(['expandedChange'])
  const isExpanded = ref(false)
  const gridWrapRef = ref(null)
  const hasScroll = ref(false)

  const showGrid = computed(() => isExpanded.value)

  /** Проверяет, есть ли прокрутка в контейнере списка вариантов */
  function checkHasScroll() {
    nextTick(() => {
      const el = gridWrapRef.value
      if (!el) {
        hasScroll.value = false
        return
      }
      hasScroll.value = el.scrollHeight > el.clientHeight
    })
  }

  function toggle() {
    isExpanded.value = !isExpanded.value
    emit('expandedChange', isExpanded.value)
    checkHasScroll()
  }

  watch(showGrid, checkHasScroll)
  watch(() => props.variants?.length, checkHasScroll)
  onMounted(checkHasScroll)
</script>


<template>
  <div class="variants-section h-full" v-if="variants && variants.length > 0">
    <button
      type="button"
      class="variants-header"
      :class="{ 'is-expanded': isExpanded }"
      :aria-expanded="isExpanded"
      @click="toggle()"
    >
      <span>Доступные модели:</span>
      <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <polyline points="6 9 12 15 18 9"/>
      </svg>
    </button>
    <Transition name="expand">
      <div v-show="showGrid" class="variants-grid-wrap" :class="{ 'has-scroll': hasScroll }">
        <div ref="gridWrapRef" class="variants-grid-scroll">
          <div class="variants-grid-inner">
            <div v-for="(variant, idx) in variants" :key="idx" class="variant-item">
              <span class="model-name">{{ variant.model }}</span>
              <span class="model-price">{{ variant.price }}</span>
            </div>
          </div>
        </div>
        <div class="variants-scroll-fade" aria-hidden="true"></div>
      </div>
    </Transition>
  </div>

</template>

<style scoped>
/* Минимальный список вариантов; раскрытие по клику на заголовок (все экраны) */
.variants-section {
  background: var(--bg-secondary);
  padding: 10px 12px;
  border-top: 1px solid var(--border-color);
}

.variants-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  padding: 0;
  border: none;
  background: transparent;
  font-size: var(--text-xs);
  font-weight: 500;
  color: var(--text-muted);
  letter-spacing: 0.3px;
  margin-bottom: 0;
  cursor: pointer;
  -webkit-tap-highlight-color: transparent;
}

.variants-header .chevron {
  width: 16px;
  height: 16px;
  margin-left: 6px;
  color: var(--text-muted);
  transition: transform 0.25s ease;
}

.variants-header.is-expanded .chevron {
  transform: rotate(180deg);
}

.variants-header.is-expanded {
  margin-bottom: 6px;
}

/* Обёртка: фиксированная высота и эффект «есть ещё» при прокрутке */
.variants-grid-wrap {
  position: relative;
}

.variants-grid-scroll {
  max-height: 200px;
  overflow-y: auto;
  -webkit-overflow-scrolling: touch;
}

/* Градиент внизу, показывающий что есть прокрутка */
.variants-scroll-fade {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 32px;
  pointer-events: none;
  opacity: 0;
  background: linear-gradient(to top, var(--bg-secondary) 20%, transparent);
  transition: opacity 0.2s;
}

.variants-grid-wrap.has-scroll .variants-scroll-fade {
  opacity: 1;
}

/* Компактная сетка: при expand помещается больше элементов */
.variants-grid-inner {
  display: grid;
  grid-template-columns: 1fr;
  gap: 4px;
  padding-bottom: 4px;
}

@media (min-width: 480px) {
  .variants-grid-inner {
    grid-template-columns: repeat(2, 1fr);
  }
}

.variant-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 8px;
  padding: 8px 10px;
  min-height: 0;
  background: var(--bg-primary);
  border-radius: 8px;
  border: 1px solid var(--border-color);
  transition: background 0.2s;
}

.model-name {
  font-size: var(--text-xs);
  font-weight: 500;
  color: var(--text-primary);
  line-height: 1.3;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  min-width: 0;
}

.model-price {
  font-size: var(--text-xs);
  font-weight: 600;
  color: var(--accent-color);
  flex-shrink: 0;
}

/* Анимация раскрытия/сворачивания списка вариантов: высота + прозрачность */
.expand-enter-active,
.expand-leave-active {
  transition: max-height 0.35s ease-out, opacity 0.25s ease;
  overflow: hidden;
}

.expand-enter-from,
.expand-leave-to {
  max-height: 0;
  opacity: 0;
}

.expand-enter-to,
.expand-leave-from {
  max-height: 320px;
  opacity: 1;
}
</style>
