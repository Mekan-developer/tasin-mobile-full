<template>
  <div class="profile-page">
    <h2 class="profile-title">Профиль</h2>

    <!-- Есть данные: показываем только имя, телефон и email -->
    <template v-if="hasProfileData && !showForm">
      <div class="profile-view">
        <div class="profile-view-row">
          <span class="profile-view-label">ФИО</span>
          <span class="profile-view-value">{{ form.fullName }}</span>
        </div>
        <div class="profile-view-row">
          <span class="profile-view-label">Телефон</span>
          <span class="profile-view-value">{{ displayPhone }}</span>
        </div>
        <div class="profile-view-row" v-if="form.email">
          <span class="profile-view-label">Email</span>
          <span class="profile-view-value">{{ form.email }}</span>
        </div>
      </div>
      <button type="button" class="profile-edit" @click="showForm = true">Редактировать</button>
    </template>

    <!-- Нет данных: при входе открываем AuthModal; кнопка — если закрыли без регистрации -->
    <template v-else-if="!hasProfileData">
      <p class="profile-desc">Заполните данные для отправки заказа поставщику.</p>
      <button type="button" class="profile-edit" @click="authModalOpen = true">Войти или зарегистрироваться</button>
      <AuthModal
        v-model="authModalOpen"
        title="Регистрация"
        description="Войдите или укажите имя и телефон"
        :initial-form="{ fullName: form.fullName, phone: form.phone }"
        @phone-auth="onPhoneAuth"
        @google-sign-in="onGoogleSignIn"
      />
    </template>

    <!-- Форма редактирования (полная) -->
    <template v-else>
      <p class="profile-desc">Данные для отправки заказа поставщику.</p>
      <form class="profile-form" @submit.prevent="save">
        <label class="profile-label">
          <span class="profile-label-text">ФИО</span>
          <input
            v-model="form.fullName"
            type="text"
            class="profile-input"
            placeholder="Имя Фамилия"
            autocomplete="name"
          />
        </label>
        <label class="profile-label">
          <span class="profile-label-text">Телефон</span>
          <input
            v-model="form.phone"
            type="tel"
            class="profile-input"
            placeholder="+993 62 724494"
            autocomplete="tel"
            maxlength="14"
            @input="form.phone = formatPhoneDisplay(form.phone)"
          />
        </label>
        <label class="profile-label">
          <span class="profile-label-text">Email</span>
          <input
            v-model="form.email"
            type="email"
            class="profile-input"
            placeholder="email@example.com"
            autocomplete="email"
          />
        </label>
        <label class="profile-label">
          <span class="profile-label-text">Адрес доставки</span>
          <textarea
            v-model="form.address"
            class="profile-textarea"
            placeholder="Город, улица, дом, квартира"
            rows="3"
          />
        </label>
        <label class="profile-label">
          <span class="profile-label-text">Комментарий к заказу</span>
          <textarea
            v-model="form.comment"
            class="profile-textarea"
            placeholder="Пожелания по доставке или заказу"
            rows="2"
          />
        </label>
        <button type="submit" class="profile-submit">Сохранить</button>
        <button type="button" class="profile-cancel" @click="showForm = false">Отмена</button>
      </form>
      <p v-if="saved" class="profile-saved">Данные сохранены</p>
    </template>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { formatPhoneDisplay, normalizePhone } from '@/composables/usePhoneFormat'
import AuthModal from '@/components/common/AuthModal.vue'

const STORAGE_KEY = 'clientProfile'

const form = reactive({
  fullName: '',
  phone: '',
  email: '',
  address: '',
  comment: ''
})

const saved = ref(false)
const showForm = ref(false)
const authModalOpen = ref(false)

/** Есть ли минимальные данные профиля (имя и телефон) */
const hasProfileData = computed(() => !!(form.fullName && form.phone))

/** Телефон для отображения в виде +993 XX XXXXXX */
const displayPhone = computed(() => formatPhoneDisplay(form.phone) || '—')

function load() {
  try {
    const raw = localStorage.getItem(STORAGE_KEY)
    if (raw) {
      const data = JSON.parse(raw)
      Object.assign(form, data)
      if (form.phone) form.phone = formatPhoneDisplay(form.phone)
    }
  } catch (_) {}
}

function save() {
  try {
    const toSave = { ...form }
    if (form.phone) toSave.phone = normalizePhone(form.phone) || form.phone
    localStorage.setItem(STORAGE_KEY, JSON.stringify(toSave))
    saved.value = true
    setTimeout(() => { saved.value = false }, 2000)
    showForm.value = false
  } catch (_) {}
}

/** Сохранение данных из AuthModal (телефон + имя) */
function onPhoneAuth(payload) {
  try {
    const existing = localStorage.getItem(STORAGE_KEY)
    const data = existing
      ? { ...JSON.parse(existing), fullName: payload.fullName, phone: payload.phone }
      : { fullName: payload.fullName, phone: payload.phone, email: '', address: '', comment: '' }
    localStorage.setItem(STORAGE_KEY, JSON.stringify(data))
    load()
  } catch (_) {}
}

function onGoogleSignIn() {
  alert('Вход через Google будет доступен позже. Используйте «Войти по номеру телефона».')
}

onMounted(() => {
  load()
  if (!hasProfileData.value) authModalOpen.value = true
})

watch(authModalOpen, (open) => {
  if (!open) load()
})
</script>

<style scoped>
.profile-page {
  padding: 16px;
  max-width: 480px;
  margin: 0 auto;
  min-height: 100%;
  background-color: var(--bg-secondary);
}

.profile-title {
  font-size: var(--text-xl);
  font-weight: 600;
  color: var(--text-primary);
  margin: 0 0 8px;
}

.profile-desc {
  font-size: var(--text-sm);
  color: var(--text-secondary);
  margin: 0 0 20px;
  line-height: 1.4;
}

/* Блок просмотра: только имя, телефон, email */
.profile-view {
  background: var(--bg-card);
  border-radius: 14px;
  border: 1px solid var(--border-color);
  padding: 16px;
  margin-bottom: 16px;
}

.profile-view-row {
  display: flex;
  flex-direction: column;
  gap: 4px;
  padding: 10px 0;
  border-bottom: 1px solid var(--border-color);
}

.profile-view-row:last-child {
  border-bottom: none;
}

.profile-view-label {
  font-size: var(--text-xs);
  font-weight: 500;
  color: var(--text-muted);
}

.profile-view-value {
  font-size: var(--text-base);
  color: var(--text-primary);
}

.profile-edit {
  width: 100%;
  padding: 12px 20px;
  font-size: var(--text-base);
  font-weight: 600;
  color: var(--accent-color);
  background: transparent;
  border: 1px solid var(--accent-color);
  border-radius: 12px;
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
  -webkit-tap-highlight-color: transparent;
}

.profile-edit:hover {
  background: var(--accent-color);
  color: #fff;
}

/* Форма */
.profile-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.profile-label {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.profile-label-text {
  font-size: var(--text-sm);
  font-weight: 500;
  color: var(--text-primary);
}

.profile-input,
.profile-textarea {
  width: 100%;
  padding: 12px 14px;
  font-size: var(--text-base);
  color: var(--text-primary);
  background: var(--bg-card);
  border: 1px solid var(--border-color);
  border-radius: 12px;
  outline: none;
  transition: border-color 0.2s;
  box-sizing: border-box;
}

.profile-textarea {
  resize: vertical;
  min-height: 72px;
}

.profile-input::placeholder,
.profile-textarea::placeholder {
  color: var(--text-muted);
}

.profile-input:focus,
.profile-textarea:focus {
  border-color: var(--accent-color);
}

.profile-submit {
  margin-top: 8px;
  padding: 14px 20px;
  font-size: var(--text-base);
  font-weight: 600;
  color: #fff;
  background: var(--accent-color);
  border: none;
  border-radius: 12px;
  cursor: pointer;
  transition: background 0.2s, transform 0.15s;
  -webkit-tap-highlight-color: transparent;
}

.profile-submit:active {
  transform: scale(0.98);
}

.profile-submit:hover {
  background: var(--accent-hover);
}

.profile-cancel {
  margin-top: 4px;
  padding: 12px 20px;
  font-size: var(--text-base);
  color: var(--text-muted);
  background: transparent;
  border: 1px solid var(--border-color);
  border-radius: 12px;
  cursor: pointer;
  transition: border-color 0.2s;
}

.profile-cancel:hover {
  border-color: var(--text-muted);
}

.profile-saved {
  margin-top: 16px;
  font-size: var(--text-sm);
  color: var(--slider-bg, #22c55e);
  font-weight: 500;
}
</style>
