import { createApp } from 'vue'
import './assets/app.css'
import App from './App.vue'
import router from './router'
import NProgress from 'nprogress'
import 'nprogress/nprogress.css'
import store from './store'
import AppIcon from "@/components/icons/AppIcon.vue"
import { createI18n } from 'vue-i18n'

// Импорт языковых файлов
import en from './locales/en.json'
import ru from './locales/ru.json'
import tm from './locales/tm.json'

const i18n = createI18n({
  locale: import.meta.env.VITE_DEFAULT_LOCALE, // язык по умолчанию
  fallbackLocale: import.meta.env.VITE_FALLBACK_LOCALE, // если перевод отсутствует
  messages: {
    en,
    ru,
    tm,
  }
})

const app = createApp(App)

app.component("AppIcon", AppIcon)
app.use(store)
app.use(i18n)
app.use(router)

// старт/стоп при навигации`
router.beforeEach(async (to, from, next) => {
  NProgress.start()
  store.dispatch('ui/startLoading')
  next()
})
router.afterEach(() => {
  NProgress.done()
  store.dispatch('ui/stopLoading')
})

app.mount('#app')
