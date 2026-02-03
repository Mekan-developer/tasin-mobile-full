import api from '@/api/http'

export default {
  // Все роли с их разрешениями
  async getRoles() {
    const response = await api.get('/roles')
    return response.data
  },

  // Все разрешения
  async getPermissions() {
    const response = await api.get('/roles/permissions')
    return response.data
  },

  // Назначить роль пользователю
  async assignRole(userId, role) {
    const response = await api.post(`/roles/${userId}/assign-role`, { role })
    return response.data
  },

  // Удалить роль у пользователя
  async revokeRole(userId, role) {
    const response = await api.post(`/roles/${userId}/revoke-role`, { role })
    return response.data
  },

  // Синхронизировать список ролей пользователя
  async syncRoles(userId, roles) {
    const response = await api.post(`/roles/${userId}/sync-roles`, { roles })
    return response.data
  },

  // Выдать отдельное разрешение пользователю
  async givePermission(userId, permission) {
    const response = await api.post(`/roles/${userId}/give-permission`, { permission })
    return response.data
  }
}

