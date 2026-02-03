<template>
  <Transition name="fade">
    <div v-if="modelValue" class="image-modal" @click="$emit('update:modelValue', false)">
      <div
        ref="modalContentRef"
        class="modal-content"
        @click.stop
      >
        <img
          v-if="product && product.images && product.images.length > 0"
          :src="resolveImagePath(product.images[product.activeImage ?? 0])"
          class="modal-img"
          alt=""
        />

        <div v-if="product && product.images && product.images.length > 1" class="modal-indicators">
          <div
            v-for="(_, i) in (product.images || [])"
            :key="i"
            class="modal-dot"
            :class="{ active: product.activeImage === i }"
          />
        </div>

        <button class="close-modal" @click="$emit('update:modelValue', false)" type="button">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
          </svg>
        </button>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, watch, nextTick, onUnmounted } from 'vue'

/** Fullscreen image modal with swipe and indicators */
const props = defineProps({
  modelValue: { type: Boolean, default: false },
  product: { type: Object, default: null },       // { images: string[], activeImage: number }
  resolveImagePath: { type: Function, default: (p) => p }
})

const emit = defineEmits(['update:modelValue', 'update:activeImage'])

const touchStart = ref(0)
const touchY = ref(0)

function handleTouchStart(e) {
  touchStart.value = e.touches[0].clientX
  touchY.value = e.touches[0].clientY
}

function handleTouchEnd(e) {
  if (!props.product || !props.product.images || props.product.images.length <= 1) return
  const touchEndX = e.changedTouches[0].clientX
  const touchEndY = e.changedTouches[0].clientY
  const diffX = touchStart.value - touchEndX
  const diffY = touchY.value - touchEndY

  if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 40) {
    const len = props.product.images.length
    const next = diffX > 0
      ? (props.product.activeImage + 1) % len
      : (props.product.activeImage - 1 + len) % len
    emit('update:activeImage', next)
  }
}

/** Touch-обработчики через addEventListener с passive: true (убирает violation в консоли) */
const modalContentRef = ref(null)
let attachedEl = null

function attachTouchListeners() {
  const el = modalContentRef.value
  if (el && el !== attachedEl) {
    el.addEventListener('touchstart', handleTouchStart, { passive: true })
    el.addEventListener('touchend', handleTouchEnd, { passive: true })
    attachedEl = el
  }
}

function detachTouchListeners() {
  if (attachedEl) {
    attachedEl.removeEventListener('touchstart', handleTouchStart)
    attachedEl.removeEventListener('touchend', handleTouchEnd)
    attachedEl = null
  }
}

watch(() => props.modelValue, (open) => {
  nextTick(() => (open ? attachTouchListeners() : detachTouchListeners()))
}, { immediate: true })

onUnmounted(detachTouchListeners)
</script>

<style scoped>
/* Минимальное полноэкранное окно изображения: тёмный фон, простая кнопка закрытия */
.image-modal {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.92);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
}

.modal-content {
  position: relative;
  width: 100%;
  height: 85vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 16px;
}

.modal-img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
  border-radius: 8px;
}

.modal-indicators {
  position: absolute;
  bottom: 24px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 6px;
}

.modal-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.35);
  transition: width 0.2s, background 0.2s;
}

.modal-dot.active {
  background: #fff;
  width: 16px;
  border-radius: 3px;
}

.close-modal {
  position: absolute;
  top: 16px;
  right: 16px;
  background: rgba(255, 255, 255, 0.12);
  border: none;
  color: #fff;
  width: 44px;
  height: 44px;
  min-width: 44px;
  min-height: 44px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background 0.2s, transform 0.15s;
  -webkit-tap-highlight-color: transparent;
}

.close-modal:active {
  background: rgba(255, 255, 255, 0.2);
  transform: scale(0.96);
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.25s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
