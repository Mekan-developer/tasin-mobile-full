// composables/useTheme.js - синглтон версия
import { ref, watch, computed } from 'vue'

// Создаем глобальные переменные вне функции (синглтон)
const theme = ref('system') // 'light', 'dark', 'system'
const isDark = computed(() => {
  if (theme.value === 'system') {
    return window.matchMedia('(prefers-color-scheme: dark)').matches
  }
  return theme.value === 'dark'
})

const themes = [
  { value: 'light', label: 'Светлая', icon: '☀️' },
  { value: 'dark', label: 'Темная', icon: '🌙' },
  { value: 'system', label: 'Как в системе', icon: '🖥️' },
]

const applyTheme = () => {
  const darkMode = isDark.value

  if (darkMode) {
    document.documentElement.classList.add('dark')
  } else {
    document.documentElement.classList.remove('dark')
  }

  document.documentElement.setAttribute('data-theme', darkMode ? 'dark' : 'light')
}

const setTheme = (newTheme) => {
  theme.value = newTheme
  localStorage.setItem('theme', newTheme)
}

const initTheme = () => {
  const savedTheme = localStorage.getItem('theme')
  if (savedTheme && ['light', 'dark', 'system'].includes(savedTheme)) {
    theme.value = savedTheme
  } else {
    // Проверяем системные предпочтения
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
    theme.value = systemPrefersDark ? 'dark' : 'light'
  }
  applyTheme()
}

// Следим за системными изменениями
const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)')
const handleSystemChange = () => {
  if (theme.value === 'system') {
    applyTheme()
  }
}

// Инициализируем один раз при загрузке модуля
let initialized = false
if (!initialized) {
  initTheme()
  mediaQuery.addEventListener('change', handleSystemChange)
  watch(theme, applyTheme)
  initialized = true
}

// Экспортируем функцию, которая возвращает один и тот же экземпляр
export function useTheme() {
  return {
    theme,
    isDark,
    themes,
    setTheme,
    toggleTheme: () => {
      setTheme(isDark.value ? 'light' : 'dark')
    },
  }
}
