<template>
  <div class="flex items-center gap-4">
    <!-- Fullscreen Toggle -->
    <FullscreenToggle />

    <!-- User Dropdown -->
    <div class="relative">
      <!-- User Menu Button -->
      <button
        @click="toggleUserMenu"
        class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all duration-200 hover:bg-gray-100/80 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 group"
        :class="{ 'bg-gray-100/80': userMenuOpen }"
        aria-label="Меню пользователя"
        aria-haspopup="true"
        :aria-expanded="userMenuOpen"
      >
        <!-- User Avatar -->
        <div class="relative">
          <div class="w-10 h-10 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-sm shadow-md ring-2 ring-white ring-offset-1">
            {{ userInitials }}
          </div>
          <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-500 rounded-full ring-2 ring-white"></div>
        </div>

        <!-- User Info (only visible on larger screens) -->
        <div class="hidden md:block text-left min-w-0">
          <div class="flex items-center gap-2">
            <span class="text-sm font-semibold text-gray-900 truncate max-w-[120px]">
              {{ currentUser?.name || 'John Doe' }}
            </span>
            <!-- Role Badge -->
            <span
              :class="roleBadgeClass"
              class="px-2 py-0.5 rounded-full text-xs font-medium"
            >
              {{ currentUser?.role || 'user' }}
            </span>
          </div>
          <p class="text-xs text-gray-500 truncate max-w-[140px]">
            {{ currentUser?.email || 'user@example.com' }}
          </p>
        </div>

        <!-- Chevron Icon -->
        <AppIcon
          name="ChevronDown"
          size="16"
          :class="[
            'text-gray-400 transition-transform duration-300 flex-shrink-0',
            { 'rotate-180': userMenuOpen }
          ]"
        />
      </button>

      <!-- Dropdown Menu -->
      <transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="transform scale-95 opacity-0"
        enter-to-class="transform scale-100 opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="transform scale-100 opacity-100"
        leave-to-class="transform scale-95 opacity-0"
      >
        <div
          v-if="userMenuOpen"
          class="absolute right-0 mt-2 w-72 z-50"
        >
          <!-- Dropdown Backdrop Click (for mobile) -->
          <div
            class="fixed inset-0 z-40"
            @click="toggleUserMenu"
          ></div>

          <!-- Dropdown Content -->
          <div class="relative z-50 bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden">
            <!-- Header -->
            <div class="p-4 bg-gradient-to-r from-gray-50 to-gray-100/50 border-b border-gray-200">
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-full flex items-center justify-center text-white font-bold text-base shadow-md">
                  {{ userInitials }}
                </div>
                <div class="flex-1 min-w-0">
                  <h3 class="font-semibold text-gray-900 truncate">{{ currentUser?.name || 'John Doe' }}</h3>
                  <p class="text-sm text-gray-500 truncate">{{ currentUser?.email || 'user@example.com' }}</p>
                </div>
              </div>
              <div class="mt-3 flex items-center justify-between text-xs">
                <span class="px-2.5 py-1 bg-gray-100 text-gray-700 rounded-full font-medium">
                  {{ currentUser?.role || 'user' }}
                </span>
                <span class="text-gray-400">ID: #{{ currentUser?.id || '0001' }}</span>
              </div>
            </div>

            <!-- Menu Items -->
            <div class="p-2">
              <!-- Profile Button -->
              <button
                @click="toggleVisiblePopUpLayout"
                class="flex items-center gap-3 w-full p-3 rounded-lg hover:bg-gray-50 text-gray-700 hover:text-gray-900 transition-colors duration-200 group"
              >
                <div class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center group-hover:bg-indigo-100 transition-colors">
                  <AppIcon
                    name="User"
                    size="18"
                    class="text-indigo-600 group-hover:text-indigo-700"
                  />
                </div>
                <div class="flex-1 text-left">
                  <span class="font-medium">{{ $t('profile') }}</span>
                  <p class="text-xs text-gray-500">Управление вашим профилем</p>
                </div>
                <AppIcon
                  name="ChevronRight"
                  size="16"
                  class="text-gray-300 group-hover:text-gray-400"
                />
              </button>

              <!-- Divider -->
              <div class="my-1 border-t border-gray-100"></div>

              <!-- Logout Button -->
              <LogoutButton
                variant="header"
                :show-icon="true"
                class="w-full"
              />
            </div>

            <!-- Footer -->
            <div class="p-3 bg-gray-50/50 border-t border-gray-100">
              <div class="flex items-center justify-between text-xs text-gray-500">
                <span>Версия 1.0.0</span>
                <span>{{ new Date().toLocaleDateString() }}</span>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </div>

    <!-- Profile Popup -->
    <popUpLayout
      :is-visible-pop-up-layout="isVisiblePopUpLayout"
      :mounted="mounted"
      title="Профиль пользователя"
      @toggle-visible-pop-up-layout="isVisiblePopUpLayout = false"
      @on-after-leave="onAfterLeave"
    >
      <template #content>
        <div class="space-y-6 p-4 md:p-6">
          <!-- User Header -->
          <div class="flex items-center gap-4 p-4 bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl">
            <div class="w-16 h-16 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-full flex items-center justify-center text-white text-xl font-bold shadow-lg">
              {{ userInitials }}
            </div>
            <div>
              <h2 class="text-lg font-bold text-gray-900">{{ userData.name || currentUser?.name }}</h2>
              <p class="text-sm text-gray-600">{{ userData.email || currentUser?.email }}</p>
              <div class="mt-2 flex items-center gap-2">
                <span
                  :class="roleBadgeClass"
                  class="px-3 py-1 rounded-full text-xs font-semibold"
                >
                  {{ currentUser?.role || 'user' }}
                </span>
                <span class="text-xs text-gray-500">• Зарегистрирован: {{ formatDate(userData.created_at) }}</span>
              </div>
            </div>
          </div>

          <!-- Profile Details -->
          <div class="grid gap-4">
            <div class="space-y-2">
              <label class="text-sm font-medium text-gray-700">Личная информация</label>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="p-4 bg-white border border-gray-200 rounded-xl">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                      <AppIcon name="Mail" size="18" class="text-blue-600" />
                    </div>
                    <div class="flex-1">
                      <p class="text-xs text-gray-500">Email</p>
                      <p class="font-medium text-gray-900">{{ userData.email }}</p>
                    </div>
                  </div>
                </div>

                <div class="p-4 bg-white border border-gray-200 rounded-xl">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                      <AppIcon name="User" size="18" class="text-green-600" />
                    </div>
                    <div class="flex-1">
                      <p class="text-xs text-gray-500">Имя пользователя</p>
                      <p class="font-medium text-gray-900">{{ userData.name }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Additional Info Section -->
            <div class="space-y-2">
              <label class="text-sm font-medium text-gray-700">Дополнительная информация</label>
              <div class="p-4 bg-gray-50 rounded-xl">
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <p class="text-xs text-gray-500">Статус аккаунта</p>
                    <div class="flex items-center gap-2 mt-1">
                      <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                      <span class="text-sm font-medium text-gray-900">Активен</span>
                    </div>
                  </div>
                  <div>
                    <p class="text-xs text-gray-500">Последний вход</p>
                    <p class="text-sm font-medium text-gray-900">{{ formatLastLogin(userData.last_login_at) }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-4 border-t border-gray-200">
              <button
                class="flex-1 px-4 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors duration-200"
              >
                Редактировать профиль
              </button>
              <button
                class="px-4 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200"
              >
                Настройки
              </button>
            </div>
          </div>
        </div>
      </template>
    </popUpLayout>
  </div>
</template>

<script>
import AuthController from '@/controllers/AuthController';
import { mapState, mapGetters } from 'vuex';
import LogoutButton from '../links/LogoutButton.vue';
import PopUpLayout from '../popUpLayout.vue';
import ProfileController from '@/controllers/ProfileController';
import FullscreenToggle from '@/components/links/FullscreenToggle.vue'
import { popUpLayoutMixins } from "@/mixins/popUpLayoutMixins";

export default {
  name: "HeaderDashboard",
  components: { LogoutButton, PopUpLayout, FullscreenToggle },
  mixins: [popUpLayoutMixins],

  data() {
    return {
      userMenuOpen: false,
      loading: false,
      userData: {}
    };
  },

  computed: {
    ...mapState("user", ["currentUser"]),
    ...mapGetters('ui', ['isCollapsed']),

    userInitials() {
      if (!this.currentUser?.name) return 'JD';
      return this.currentUser.name
        .split(' ')
        .map(word => word[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
    },

    roleBadgeClass() {
      const role = this.currentUser?.role?.toLowerCase();
      const classes = {
        'super-admin': 'bg-purple-100 text-purple-800',
        'admin': 'bg-red-100 text-red-800',
        'owner': 'bg-blue-100 text-blue-800',
        'manager': 'bg-green-100 text-green-800',
        'user': 'bg-gray-100 text-gray-800'
      };
      return classes[role] || classes.user;
    }
  },

  watch: {
    isVisiblePopUpLayout(newValue) {
      if (newValue) {
        this.getProfile();
      } else {
        document.body.style.overflow = 'auto';
      }
    },
    userMenuOpen(newValue) {
      // Блокируем прокрутку при открытом меню на мобильных
      if (newValue && window.innerWidth < 768) {
        document.body.style.overflow = 'hidden';
      } else {
        document.body.style.overflow = 'auto';
      }
    }
  },

  methods: {
    toggleUserMenu() {
      this.userMenuOpen = !this.userMenuOpen;
    },

    async handleLogout() {
      this.loading = true;
      try {
        await AuthController.logout();
        this.$router.push('/login');
      } finally {
        this.loading = false;
      }
    },

    async getProfile() {
      try {
        const profile = await ProfileController.getProfile();
        this.userData = profile.data;
      } catch (error) {
        console.error('Failed to fetch user profile:', error);
        // Fallback данные
        this.userData = {
          email: this.currentUser?.email || 'user@example.com',
          name: this.currentUser?.name || 'John Doe',
          created_at: new Date().toISOString(),
          last_login_at: new Date().toISOString()
        };
      }
    },

    formatDate(dateString) {
      if (!dateString) return 'Не указано';
      const date = new Date(dateString);
      return date.toLocaleDateString('ru-RU', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
      });
    },

    formatLastLogin(dateString) {
      if (!dateString) return 'Недавно';
      const date = new Date(dateString);
      const now = new Date();
      const diffMs = now - date;
      const diffMins = Math.floor(diffMs / 60000);

      if (diffMins < 1) return 'Только что';
      if (diffMins < 60) return `${diffMins} мин назад`;
      if (diffMins < 1440) return `${Math.floor(diffMins / 60)} ч назад`;

      return date.toLocaleDateString('ru-RU', {
        day: 'numeric',
        month: 'short'
      });
    }
  }
};
</script>

<style scoped>
/* Custom scrollbar for dropdown */
.dropdown-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: #cbd5e1 #f8fafc;
}

.dropdown-scrollbar::-webkit-scrollbar {
  width: 4px;
}

.dropdown-scrollbar::-webkit-scrollbar-track {
  background: #f8fafc;
  border-radius: 10px;
}

.dropdown-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
}

.dropdown-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Smooth hover effects */
.hover-lift {
  transition: transform 0.2s ease;
}

.hover-lift:hover {
  transform: translateY(-1px);
}

/* Glass effect for dropdown */
.glass-effect {
  backdrop-filter: blur(8px);
  background: rgba(255, 255, 255, 0.95);
}

/* Mobile optimizations */
@media (max-width: 768px) {
  .user-menu-content {
    position: fixed;
    top: 4rem;
    right: 1rem;
    left: 1rem;
    width: auto !important;
  }
}
</style>
