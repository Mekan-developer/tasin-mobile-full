  import AuthController from '../controllers/AuthController'
import store from '../store'
import { mapUserToDto } from '../dto/UserDto'

export const authService = {
  async login(auth) {
      store.dispatch('auth/setToken', auth.token)
      store.dispatch('user/setUser', auth.user)
  },

  async register(userData) {
    try {
      const auth = await AuthController.register(userData)
      store.dispatch('auth/setToken', auth.token)
      store.dispatch('user/setUser', auth.user)
      return { success: true, user: auth.user, token: auth.token }
    } catch (error) {
      return {
        success: false,
        error: error.response?.data?.error || 'Registration failed',
      }
    }
  },

  async logout() {
      store.dispatch('auth/logout')
      store.dispatch('user/setUser', null)
  },

  async refreshToken(auth) {
    try {
      store.dispatch('auth/setToken', auth.token)
      store.dispatch('user/setUser', auth.user)
      return { success: true, user: auth.user, token: auth.token }
    } catch (error) {
      return {
        success: false,
        error: error.response?.data?.error || 'Token refresh failed',
      }
    }
  },

  async getCurrentUser() {
    try {
      const user = await AuthController.me()
      return { success: true, user }
    } catch (error) {
      return {
        success: false,
        error: error.response?.data?.error || 'Failed to get user data',
      }
    }
  },

  isAuthenticated() {
    return !!store.state.auth.token || !!localStorage.getItem('access_token')
  },

  getCurrentUserFromStorage() {
    const user = localStorage.getItem('user')
    return user ? mapUserToDto(JSON.parse(user)) : null
  },

  getToken() {
    return store.state.auth.token || localStorage.getItem('access_token')
  },
}

export default authService
