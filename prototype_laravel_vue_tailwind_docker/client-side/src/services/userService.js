import api from '@/api/http'

export default {
  // Получить всех пользователей (для super-admin, требует permission:view_users)
  async getAll() {
    const response = await api.get('/users')
    return response.data
  },

  // Получить пользователя по id (если понадобится)
  async getById(id) {
    const response = await api.get(`/users/${id}`)
    return response.data
  }
}

