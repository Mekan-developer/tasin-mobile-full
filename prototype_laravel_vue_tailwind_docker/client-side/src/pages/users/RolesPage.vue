<template>
  <div class="space-y-6">
    <div v-if="errorMessage" class="mb-2 rounded border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
      {{ errorMessage }}
    </div>
    <div v-if="successMessage" class="mb-2 rounded border border-green-200 bg-green-50 px-3 py-2 text-sm text-green-700">
      {{ successMessage }}
    </div>

    <div class="rounded-md bg-white p-4 shadow">
      <div class="mb-3 flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-800">Roles & Permissions</h2>
        <span v-if="loading" class="text-sm text-gray-500">Loading...</span>
      </div>
      <div v-if="roles.length === 0" class="text-sm text-gray-500">No roles yet</div>
      <div v-else class="space-y-3">
        <div
          v-for="role in roles"
          :key="role.id"
          class="rounded border border-gray-200 p-3"
        >
          <div class="flex items-center justify-between">
            <span class="font-semibold capitalize text-gray-800">{{ role.name }}</span>
            <span class="text-xs text-gray-500">{{ (role.permissions || []).length }} permissions</span>
          </div>
          <div class="mt-2 text-sm text-gray-600">
            <span v-if="(role.permissions || []).length === 0" class="text-gray-400">—</span>
            <span v-else>{{ role.permissions.map((p) => p.name).join(', ') }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="rounded-md bg-white p-4 shadow space-y-4">
      <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-800">Manage user roles</h2>
        <span v-if="loading" class="text-sm text-gray-500">Loading...</span>
      </div>

      <div class="grid gap-3 md:grid-cols-3">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700">User</label>
          <select v-model="selectedUserId" @change="onUserChange" class="w-full rounded border border-gray-300 px-3 py-2 text-sm">
            <option disabled value="">Select user</option>
            <option v-for="user in users" :key="user.id" :value="user.id">
              {{ user.name }} ({{ user.email }})
            </option>
          </select>
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700">Role</label>
          <select v-model="selectedRole" class="w-full rounded border border-gray-300 px-3 py-2 text-sm">
            <option disabled value="">Select role</option>
            <option v-for="role in roles" :key="role.id" :value="role.name">
              {{ role.name }}
            </option>
          </select>
        </div>
        <div class="flex items-end gap-2">
          <button
            class="flex-1 rounded bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-blue-300"
            :disabled="!selectedUserId || !selectedRole || loading"
            @click="assignRole"
          >
            Assign role
          </button>
          <button
            class="flex-1 rounded border border-red-500 px-3 py-2 text-sm font-semibold text-red-600 hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-60"
            :disabled="!selectedUserId || !selectedRole || loading"
            @click="revokeRole"
          >
            Revoke
          </button>
        </div>
      </div>

      <div class="grid gap-3 md:grid-cols-[2fr,1fr]">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700">Sync roles (override)</label>
          <div class="rounded border border-gray-200 p-2">
            <div class="flex flex-wrap gap-2">
              <label
                v-for="role in roles"
                :key="role.id"
                class="flex items-center gap-1 rounded border border-gray-200 px-2 py-1 text-sm"
              >
                <input
                  type="checkbox"
                  class="h-4 w-4"
                  :value="role.name"
                  v-model="selectedRoles"
                />
                <span>{{ role.name }}</span>
              </label>
            </div>
          </div>
        </div>
        <div class="flex items-end">
          <button
            class="w-full rounded bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700 disabled:cursor-not-allowed disabled:bg-indigo-300"
            :disabled="!selectedUserId || selectedRoles.length === 0 || loading"
            @click="syncRoles"
          >
            Sync selected roles
          </button>
        </div>
      </div>

      <div class="pt-2">
        <h3 class="mb-2 text-sm font-semibold text-gray-700">Users & their roles</h3>
        <div class="overflow-x-auto rounded border border-gray-200">
          <table class="min-w-full text-left text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
              <tr>
                <th class="px-4 py-2">Name</th>
                <th class="px-4 py-2">Email</th>
                <th class="px-4 py-2">Roles</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in users" :key="user.id" class="border-t">
                <td class="px-4 py-2 font-medium text-gray-800">{{ user.name }}</td>
                <td class="px-4 py-2 text-gray-700">{{ user.email }}</td>
                <td class="px-4 py-2 text-gray-700">
                  {{ formatRoles(user.roles) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import permissionService from '@/services/permissionService'
import userService from '@/services/userService'

export default {
  name: 'RolesPage',
  data() {
    return {
      loading: false,
      roles: [],
      users: [],
      selectedUserId: '',
      selectedRole: '',
      selectedRoles: [],
      errorMessage: '',
      successMessage: ''
    }
  },
  async mounted() {
    await this.loadData()
  },
  methods: {
    async loadData() {
      this.errorMessage = ''
      this.successMessage = ''
      try {
        this.loading = true
        await Promise.all([this.loadRoles(), this.loadUsers()])
      } catch (error) {
        this.errorMessage = this.extractError(error, 'Failed to load roles or users')
      } finally {
        this.loading = false
      }
    },
    async loadRoles() {
      const data = await permissionService.getRoles()
      this.roles = data || []
    },
    async loadUsers() {
      const response = await userService.getAll()
      if (response?.success) {
        this.users = response.data || []
        // Инициализируем выбранного пользователя, если есть
        if (!this.selectedUserId && this.users.length > 0) {
          this.selectedUserId = this.users[0].id
          this.onUserChange()
        }
      }
    },
    onUserChange() {
      const user = this.users.find((u) => u.id === this.selectedUserId)
      this.selectedRoles = user?.roles?.map((r) => r.name) || []
    },
    formatRoles(roles) {
      if (!roles || roles.length === 0) return '—'
      return roles.map((r) => r.name).join(', ')
    },
    async assignRole() {
      if (!this.selectedUserId || !this.selectedRole) return
      this.errorMessage = ''
      this.successMessage = ''
      try {
        this.loading = true
        await permissionService.assignRole(this.selectedUserId, this.selectedRole)
        this.successMessage = 'Role assigned'
        await this.loadUsers()
        this.onUserChange()
      } catch (error) {
        this.errorMessage = this.extractError(error, 'Failed to assign role')
      } finally {
        this.loading = false
      }
    },
    async revokeRole() {
      if (!this.selectedUserId || !this.selectedRole) return
      this.errorMessage = ''
      this.successMessage = ''
      try {
        this.loading = true
        await permissionService.revokeRole(this.selectedUserId, this.selectedRole)
        this.successMessage = 'Role revoked'
        await this.loadUsers()
        this.onUserChange()
      } catch (error) {
        this.errorMessage = this.extractError(error, 'Failed to revoke role')
      } finally {
        this.loading = false
      }
    },
    async syncRoles() {
      if (!this.selectedUserId || this.selectedRoles.length === 0) return
      this.errorMessage = ''
      this.successMessage = ''
      try {
        this.loading = true
        await permissionService.syncRoles(this.selectedUserId, this.selectedRoles)
        this.successMessage = 'Roles synced'
        await this.loadUsers()
        this.onUserChange()
      } catch (error) {
        this.errorMessage = this.extractError(error, 'Failed to sync roles')
      } finally {
        this.loading = false
      }
    },
    extractError(error, fallback) {
      return error?.response?.data?.message || error?.response?.data?.error || fallback
    }
  }
}
</script>

<style scoped>
</style>
