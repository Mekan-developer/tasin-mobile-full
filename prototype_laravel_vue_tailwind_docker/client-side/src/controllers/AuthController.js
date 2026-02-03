import store from '@/store'
import api from '../api/http'
import { AuthDto } from '../dto/AuthDto'
import { mapUserToDto } from '../dto/UserDto'
import router from '../router'
import authService from '../services/authService'

export default class AuthController {
  static async login(credentials) {
    try {
      const { data } = await api.post('/login', credentials)


      let auth = new AuthDto({ token: data.data.token, user: mapUserToDto(data.data.user) })
      store.dispatch('notifications/notify', {message: 'Вы успешно вошли в систему!',type: 'success'})

      authService.login(auth)
      return { success: true} // <-- добавляем success
    } catch (error) {
      const status = error.response?.status
      const apiMessage = error.response?.data?.message || error.response?.data?.error
      let message = 'Ошибка на сервере'

      if (status === 401) {
        message = 'Неверный логин или пароль!'
      } else if (status === 403 && apiMessage) {
        message = apiMessage
      } else if (apiMessage) {
        message = apiMessage
      }

      store.dispatch('notifications/notify', {message, type: 'error'})
      return { success: false , error: apiMessage || 'Login failed'}
    }
  }

  // static async register(payload) {
  //   const { data } = await api.post('/register', payload)
  //   return new AuthDto({ token: data.token, user: mapUserToDto(data.user) })
  // }

  // static async me() {
  //   const { data } = await api.get('/me')
  //   return mapUserToDto(data.user)
  // }

  static async refresh() {
    const { data } = await api.post('/refresh', {}, { withCredentials: true })
    let auth = new AuthDto({ token: data.data.token, user: mapUserToDto(data.data.user) })
    return authService.refreshToken(auth)
  }

  static async logout() {
     try {
      await api.post('/logout')
       store.dispatch('notifications/notify', {message: 'Вы успешно вышли из системы!',type: 'success'})
    } finally {
      authService.logout();
      router.push('/login')
    }

  }
}
