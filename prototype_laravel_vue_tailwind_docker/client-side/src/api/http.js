import axios from 'axios'
import store from '../store'
import router from '@/router'
import AuthController from '@/controllers/AuthController'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  headers: {
    Accept: 'application/json',
  },
  withCredentials: true
})

api.interceptors.request.use((config) => {
  const token = store.getters['auth/isAuthenticated']
    ? store.state.auth.token
    : localStorage.getItem('access_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

// refresh token when response get 401
let isRefreshing = false
let queuedRequests = []

function subscribeTokenRefresh(cb) {
  queuedRequests.push(cb)
}

function onRefreshed(newToken) {
  queuedRequests.forEach((cb) => cb(newToken))
  queuedRequests = []
}

api.interceptors.response.use(
  (response) => response,
  async (error) => {
    const originalRequest = error.config

     // если ошибка была на login или refresh endpoint → сразу отдать пользователю
    if (originalRequest.url.includes('/login') || originalRequest.url.includes('/refresh')) {
      return Promise.reject(error)
    }

    // 403: напр. ресторан отключён — показываем сообщение, выходим, редирект на логин
    if (error.response && error.response.status === 403) {
      const msg = error.response.data?.message || error.response.data?.error || 'Доступ запрещён'
      store.dispatch('notifications/notify', { message: msg, type: 'error' })
      store.dispatch('auth/logout')
      router.push('/login')
      return Promise.reject(error)
    }

    if (error.response && error.response.status === 401 && !originalRequest._retry) {
      if (isRefreshing) {
        return new Promise((resolve) => {
          subscribeTokenRefresh((newToken) => {
            originalRequest.headers.Authorization = `Bearer ${newToken}`
            resolve(api(originalRequest))
          })
        })
      }

      originalRequest._retry = true
      isRefreshing = true
      try {
        const result = await AuthController.refresh()

        store.dispatch('auth/setToken', result.token)
        onRefreshed(result.token)
        originalRequest.headers.Authorization = `Bearer ${result.token}`
        return api(originalRequest)
      } catch (refreshError) {
        console.error('Refresh error:', refreshError)
        store.dispatch('auth/logout')
        return Promise.reject(refreshError)
      } finally {
        isRefreshing = false
      }
    }
    return Promise.reject(error)
  },
)

export default api
