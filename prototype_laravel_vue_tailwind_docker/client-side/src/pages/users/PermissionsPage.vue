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
        <h2 class="text-lg font-semibold text-gray-800">Permissions</h2>
        <span v-if="loading" class="text-sm text-gray-500">Loading...</span>
      </div>
      <div v-if="permissions.length === 0" class="text-sm text-gray-500">No permissions</div>
      <div v-else class="flex flex-wrap gap-2">
        <span
          v-for="perm in permissions"
          :key="perm.id || perm.name"
          class="rounded border border-gray-200 px-2 py-1 text-xs font-medium text-gray-700"
        >
          {{ perm.name }}
        </span>
      </div>
    </div>

    <div class="rounded-md bg-white p-4 shadow space-y-4">
      <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-800">Give permission to user</h2>
        <span v-if="loading" class="text-sm text-gray-500">Loading...</span>
      </div>

      <div class="grid gap-3 md:grid-cols-3">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700">User</label>
          <select v-model="selectedUserId" class="w-full rounded border border-gray-300 px-3 py-2 text-sm">
            <option disabled value="">Select user</option>
            <option v-for="user in users" :key="user.id" :value="user.id">
              {{ user.name }} ({{ user.email }})
            </option>
          </select>
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700">Permission</label>
          <select v-model="selectedPermission" class="w-full rounded border border-gray-300 px-3 py-2 text-sm">
            <option disabled value="">Select permission</option>
            <option v-for="perm in permissions" :key="perm.id || perm.name" :value="perm.name">
              {{ perm.name }}
            </option>
          </select>
        </div>
        <div class="flex items-end">
          <button
            class="w-full rounded bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-blue-300"
            :disabled="!selectedUserId || !selectedPermission || loading"
            @click="givePermission"
          >
            Give permission
          </button>
        </div>
      </div>

      <div class="pt-2">
        <h3 class="mb-2 text-sm font-semibold text-gray-700">Users & permissions</h3>
        <div class="overflow-x-auto rounded border border-gray-200">
          <table class="min-w-full text-left text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
              <tr>
                <th class="px-4 py-2">Name</th>
                <th class="px-4 py-2">Email</th>
                <th class="px-4 py-2">Permissions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in users" :key="user.id" class="border-t">
                <td class="px-4 py-2 font-medium text-gray-800">{{ user.name }}</td>
                <td class="px-4 py-2 text-gray-700">{{ user.email }}</td>
                <td class="px-4 py-2 text-gray-700">
                  {{ formatPermissions(user.permissions) }}
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
  name: 'PermissionsPage',
  data() {
    return {
      loading: false,
      permissions: [],
      users: [],
      selectedUserId: '',
      selectedPermission: '',
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
        await Promise.all([this.loadPermissions(), this.loadUsers()])
      } catch (error) {
        this.errorMessage = this.extractError(error, 'Failed to load permissions or users')
      } finally {
        this.loading = false
      }
    },
    async loadPermissions() {
      const data = await permissionService.getPermissions()
      this.permissions = data || []
    },
    async loadUsers() {
      const response = await userService.getAll()
      if (response?.success) {
        this.users = response.data || []
        if (!this.selectedUserId && this.users.length > 0) {
          this.selectedUserId = this.users[0].id
        }
      }
    },
    formatPermissions(perms) {
      if (!perms || perms.length === 0) return '—'
      return perms.map((p) => p.name).join(', ')
    },
    async givePermission() {
      if (!this.selectedUserId || !this.selectedPermission) return
      this.errorMessage = ''
      this.successMessage = ''
      try {
        this.loading = true
        await permissionService.givePermission(this.selectedUserId, this.selectedPermission)
        this.successMessage = 'Permission granted'
        await this.loadUsers()
      } catch (error) {
        this.errorMessage = this.extractError(error, 'Failed to give permission')
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
