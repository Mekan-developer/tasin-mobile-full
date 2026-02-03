<template>
  <div class="actions">
    <button class="action view" :title="`${viewCount || 0} просмотров`">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
      </svg>
      <span class="count">{{ viewCount || 0 }}</span>
    </button>
    <button class="action" @click="shareProduct" title="Поделиться">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><polyline points="16 6 12 2 8 6"/><line x1="12" y1="2" x2="12" y2="15"/>
      </svg>
    </button>
    <button class="action" @click="copyProductLink" title="Копировать ссылку">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
      </svg>
    </button>
    <button class="action cart" :class="{ 'in-cart': isInCart }" @click="toggleCart" :title="isInCart ? 'Удалить из корзины' : 'В корзину'">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
      </svg>
      <span v-if="cartQuantity > 0" class="badge">{{ cartQuantity }}</span>
    </button>
  </div>
</template>

<script setup>
defineProps({
  productId: {
    type: Number,
    required: true
  },
  productName: {
    type: String,
    required: true
  },
  productDescription: {
    type: String,
    default: ''
  },
  viewCount: {
    type: Number,
    default: 0
  },
  isInCart: {
    type: Boolean,
    required: true
  },
  cartQuantity: {
    type: Number,
    default: 0
  }
})

const emit = defineEmits(['toggleCart', 'share', 'copyLink'])

const toggleCart = () => {
  emit('toggleCart')
}

const shareProduct = async () => {
  emit('share')
}

const copyProductLink = async () => {
  emit('copyLink')
}
</script>

<style scoped>
/* Минимальный ряд действий: только иконки, без рамок, лёгкий отклик */
.actions {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 2px 0;
}

.action {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 4px;
  width: 44px;
  height: 44px;
  min-width: 44px;
  min-height: 44px;
  padding: 0;
  border: none;
  background: transparent;
  color: var(--text-muted);
  border-radius: 12px;
  cursor: pointer;
  transition: color 0.2s, background 0.15s, transform 0.15s;
  -webkit-tap-highlight-color: transparent;
}

.action:active {
  background: var(--hover-bg);
  transform: scale(0.95);
}

.action svg {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
}

.action.view {
  margin-right: auto;
  padding-left: 10px;
  padding-right: 12px;
  min-width: auto;
  width: auto;
}

.action.view .count {
  font-size: 13px;
  font-weight: 500;
  color: var(--text-muted);
}

.action.cart.in-cart {
  color: var(--accent-color);
}

.action.cart .badge {
  position: absolute;
  top: 2px;
  right: 2px;
  min-width: 16px;
  height: 16px;
  padding: 0 4px;
  background: var(--accent-color);
  color: #fff;
  font-size: 10px;
  font-weight: 600;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
