<!--
  Модальное окно входа/регистрации: Google или телефон + имя.
  Переиспользуемый компонент для корзины, профиля и др.
-->
<template>
  <Teleport to="body">
    <Transition name="auth-fade">
      <div
        v-if="modelValue"
        class="auth-modal-backdrop"
        role="presentation"
        @click.self="close"
      >
        <div class="auth-modal" role="dialog" aria-label="Вход или регистрация">
          <button
            type="button"
            class="auth-modal-close"
            aria-label="Закрыть"
            @click="close"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </button>
          <h3 class="auth-modal-title">{{ title }}</h3>
          <p v-if="description" class="auth-modal-desc">{{ description }}</p>
          <div class="auth-modal-actions">
            <button type="button" class="auth-modal-btn auth-modal-google" @click="onGoogleSignIn">
              <span class="auth-modal-btn-icon">G</span>
              Войти через Google
            </button>
            <button
              v-if="!showPhoneForm"
              type="button"
              class="auth-modal-btn auth-modal-phone"
              @click="showPhoneForm = true"
            >
              Войти по номеру телефона
            </button>
            <div v-else class="auth-modal-phone-form">
              <input
                v-model.trim="form.fullName"
                type="text"
                class="auth-modal-input"
                placeholder="Ваше имя"
              />
              <input
                v-model="form.phone"
                type="tel"
                class="auth-modal-input"
                placeholder="+993 62 724494"
                maxlength="14"
                @input="form.phone = formatPhoneDisplay(form.phone)"
              />
              <button type="button" class="auth-modal-btn auth-modal-submit" @click="submitPhoneAuth">
                Продолжить
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue'
import { formatPhoneDisplay, normalizePhone, isPhoneValid } from '@/composables/usePhoneFormat'

const props = defineProps({
  /** Открыто ли окно (v-model) */
  modelValue: { type: Boolean, default: false },
  /** Заголовок модалки */
  title: { type: String, default: 'Войдите или зарегистрируйтесь' },
  /** Подзаголовок (опционально) */
  description: { type: String, default: '' },
  /** Начальные значения полей при открытии (например из профиля) */
  initialForm: { type: Object, default: () => ({ fullName: '', phone: '+993 ' }) }
})

const emit = defineEmits(['update:modelValue', 'google-sign-in', 'phone-auth'])

const showPhoneForm = ref(false)
const form = ref({ fullName: '', phone: '+993 ' })

/** Синхронизация начальных данных при открытии и сброс при закрытии */
watch(() => props.modelValue, (open) => {
  if (open) {
    form.value = {
      fullName: props.initialForm.fullName || '',
      phone: props.initialForm.phone ? formatPhoneDisplay(props.initialForm.phone) : '+993 '
    }
    showPhoneForm.value = false
  }
}, { immediate: true })

/** Закрыть модалку */
function close() {
  emit('update:modelValue', false)
}

/** Клик «Войти через Google» — родитель обрабатывает */
function onGoogleSignIn() {
  emit('google-sign-in')
}

/** Валидация и отправка телефона + имя; эмит нормализованных данных */
function submitPhoneAuth() {
  const { fullName, phone } = form.value
  if (!fullName) {
    alert('Введите имя')
    return
  }
  if (!isPhoneValid(phone)) {
    alert('Введите номер в формате +993 XX XXXXXX (8 цифр после +993)')
    return
  }
  const normalizedPhone = normalizePhone(phone)
  emit('phone-auth', { fullName, phone: normalizedPhone })
  showPhoneForm.value = false
  close()
}
</script>

<style scoped>
.auth-modal-backdrop {
  position: fixed;
  inset: 0;
  z-index: 2000;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
  padding-bottom: max(16px, env(safe-area-inset-bottom));
}

.auth-modal {
  position: relative;
  width: 100%;
  max-width: 380px;
  padding: 24px 20px;
  background: var(--bg-card);
  border-radius: 16px;
  box-shadow: 0 8px 32px var(--shadow-strong);
  border: 1px solid var(--border-color);
}

.auth-modal-close {
  position: absolute;
  top: 12px;
  right: 12px;
  width: 40px;
  height: 40px;
  border: none;
  border-radius: 50%;
  background: var(--hover-bg);
  color: var(--text-primary);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  -webkit-tap-highlight-color: transparent;
}

.auth-modal-title {
  font-size: var(--text-lg);
  font-weight: 600;
  color: var(--text-primary);
  margin: 0 0 4px;
}

.auth-modal-desc {
  font-size: var(--text-sm);
  color: var(--text-secondary);
  margin: 0 0 20px;
}

.auth-modal-actions {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.auth-modal-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  width: 100%;
  padding: 14px 16px;
  font-size: var(--text-base);
  font-weight: 500;
  border: 1px solid var(--border-color);
  border-radius: 12px;
  cursor: pointer;
  transition: background 0.2s, border-color 0.2s;
  -webkit-tap-highlight-color: transparent;
  background: var(--bg-primary);
  color: var(--text-primary);
}

.auth-modal-btn:hover {
  background: var(--hover-bg);
  border-color: var(--accent-color);
}

.auth-modal-google .auth-modal-btn-icon {
  font-weight: 700;
  color: #4285f4;
}

.auth-modal-phone-form {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.auth-modal-input {
  width: 100%;
  padding: 12px 14px;
  font-size: var(--text-base);
  color: var(--text-primary);
  background: var(--bg-primary);
  border: 1px solid var(--border-color);
  border-radius: 12px;
  outline: none;
  box-sizing: border-box;
}

.auth-modal-input:focus {
  border-color: var(--accent-color);
}

.auth-modal-submit {
  background: var(--accent-color);
  color: #fff;
  border-color: var(--accent-color);
}

.auth-modal-submit:hover {
  background: var(--accent-hover);
  border-color: var(--accent-hover);
}

.auth-fade-enter-active,
.auth-fade-leave-active {
  transition: opacity 0.2s ease;
}
.auth-fade-enter-from,
.auth-fade-leave-to {
  opacity: 0;
}
</style>
