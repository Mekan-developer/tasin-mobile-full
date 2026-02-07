<template>
  <div class="relative h-full">
    <nav class="flex flex-col justify-between h-full bg-transparent dark:bg-gradient-to-b dark:from-deepblue/95 dark:to-midnight transition-all duration-300">
      <!-- Top Section: Logo and Navigation -->
      <div class="flex-1 overflow-hidden">

        <!-- Navigation Section -->
        <div class="p-3">
          <!-- Navigation Header -->
          <div v-if="!isCollapsed" class="px-2 mb-3">
            <div class="flex items-center justify-between">
              <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Главное меню</span>
              <span class="text-xs text-gray-400 dark:text-fog/60">{{ menuItemsCount }} пунктов</span>
            </div>
          </div>

          <!-- Navigation Links -->
          <div class="space-y-0.5 overflow-x-hidden overflow-y-auto custom-scrollbar"
               :style="{ maxHeight: 'calc(100vh - 200px)' }">
            <!-- Home Link -->
            <SidebarLink
              @click="setDropdown(false)"
              to="/home"
              icon="Home"
              :label="$t('home')"
              :badge="isHomeActive ? '' : null"
            />

            <!-- Restaurants Link (conditional) -->
            <SidebarLink
              v-if="isSuperAdmin || isOwner"
              @click="setDropdown(false)"
              :to="isSuperAdmin ? '/restaurants' : '/restaurants/my'"
              icon="Building2"
              :label="isSuperAdmin ? $t('restaurants') : $t('my_restaurant')"
            />

            <!-- Categories Link -->
            <SidebarLink
              @click="setDropdown(false)"
              to="/categories"
              icon="Folder"
              :label="$t('categories')"
            />

            <!-- Slides Link -->
            <SidebarLink
              @click="setDropdown(false)"
              to="/slides"
              icon="Image"
              :label="$t('slides')"
            />

            <!-- Products Link -->
            <SidebarLink
              @click="setDropdown(false)"
              to="/products"
              icon="Package"
              :label="$t('products')"
            />

            <!-- Icons Link -->
            <SidebarLink
              @click="setDropdown(false)"
              to="/icons"
              icon="Shapes"
              :label="$t('icons')"
            />

            <!-- Settings Link -->
            <SidebarLink
              @click="setDropdown(false)"
              to="/settings"
              icon="Settings"
              :label="$t('settings')"
              :badge="hasSettingsUpdates ? 'new' : null"
            />

            <!-- Tables Link -->
            <SidebarLink
              @click="setDropdown(false)"
              to="/tables"
              icon="Table"
              :label="$t('tables')"
            />

            <!-- Dropdown Menu -->
            <SidebarDropdown
              :label="$t('dropdown')"
              icon="FolderTree"
              :badge="hasDropdownUpdates ? '3' : null"
            >
              <SidebarLink
                to="/dropdown/page1"
                icon="Circle"
                :label="$t('dropdown_1')"
              />
              <SidebarLink
                to="/dropdown/page2"
                icon="Circle"
                :label="$t('dropdown_2')"
              />
              <SidebarLink
                to="/dropdown/page3"
                icon="Circle"
                :label="$t('dropdown_3')"
              />
            </SidebarDropdown>

            <!-- Users Link (Owner only) -->
            <SidebarLink
              v-if="isOwner"
              @click="setDropdown(false)"
              to="/users"
              icon="Users"
              :label="$t('users')"
              :count="usersCount"
            />

            <!-- Divider -->
            <div v-if="!isCollapsed && hasAdminSection" class="px-2 py-3">
              <div class="border-t border-gray-200 dark:border-fogactive/20"></div>
            </div>

            <!-- Admin Section (Optional) -->
            <div v-if="!isCollapsed && isSuperAdmin" class="px-2 mb-2">
              <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Администрирование</span>
            </div>

            <!-- Admin Links (Example) -->
            <SidebarLink
              v-if="isSuperAdmin"
              @click="setDropdown(false)"
              to="/admin/logs"
              icon="FileText"
              :label="'Логи системы'"
              :badge="hasNewLogs ? 'alert' : null"
            />
          </div>
        </div>
      </div>

      <!-- Bottom Section -->
      <div class="flex flex-col border-t border-gray-200 dark:border-fogactive/20 bg-gray-50/95 dark:bg-midnight/80 backdrop-blur-sm">
        <!-- User Mini Profile (when expanded) -->
        <div v-if="!isCollapsed" class="p-3 border-b border-gray-200 dark:border-fogactive/20">
          <div class="flex items-center gap-3">
            <div class="relative">
              <div class="w-8 h-8 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                {{ userInitials }}
              </div>
              <div class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-green-500 rounded-full ring-1 ring-white dark:ring-midnight"></div>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ currentUser?.name || 'Пользователь' }}</p>
              <p class="text-xs text-gray-500 dark:text-fog truncate">{{ currentUserRoleText }}</p>
            </div>
          </div>
        </div>

        <!-- Language Switcher -->
        <div class="px-3 py-2">
          <div
            v-if="isCollapsed"
            @click="setCollapse(false)"
            class="cursor-pointer group p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-fogactive/30 transition-all duration-200 flex justify-center"
            :title="$t('languages')"
          >
            <AppIcon
              name="Globe"
              size="20"
              class="text-gray-500 dark:text-fog group-hover:text-gray-900 dark:group-hover:text-white transition-colors"
            />
          </div>

          <div
            v-else
            class="space-y-2"
          >
            <div class="flex items-center justify-between px-2">
              <div class="flex items-center gap-2">
                <AppIcon name="Globe" size="14" class="text-gray-500 dark:text-fog" />
                <span class="text-xs font-medium text-gray-600 dark:text-gray-300">
                  {{ $t('language') }}
                </span>
              </div>
              <span class="text-xs text-gray-400 dark:text-fog/60">{{ currentLanguage.toUpperCase() }}</span>
            </div>
            <LanguageSwitcher />
          </div>
        </div>

        <!-- Logout Button -->
        <div class="px-3 pb-3 pt-2 border-t border-gray-200 dark:border-fogactive/20">
          <LogoutButton
            variant="sidebar"
            :show-icon="true"
            :is-collapsed="isCollapsed"
          />
        </div>
      </div>
    </nav>
  </div>
</template>

<script>
import LogoutButton from '../links/LogoutButton.vue';
import SidebarLink from '../links/SidebarLink.vue';
import { mapGetters, mapActions, mapState } from 'vuex';
import SidebarDropdown from '../dropdowns/SidebarDropdown.vue';
import LanguageSwitcher from '../links/LanguageSwitcher.vue';

export default {
  name: "SidebarDashboard",
  components: {
    LogoutButton,
    SidebarLink,
    SidebarDropdown,
    LanguageSwitcher
  },

  data() {
    return {
      loading: false,
      hasSettingsUpdates: false,
      hasDropdownUpdates: false,
      hasNewLogs: false,
      usersCount: 24
    }
  },

  computed: {
    ...mapGetters("ui", ["isCollapsed"]),
    ...mapState("user", ["currentUser"]),

    // Computed для проверки роли
    isSuperAdmin() {
      return this.currentUser?.role === 'super-admin';
    },

    isOwner() {
      return this.currentUser?.role === 'owner';
    },

    // Количество пунктов меню
    menuItemsCount() {
      let count = 5; // базовые пункты: Home, Icons, Settings, Tables, Dropdown
      if (this.isSuperAdmin || this.isOwner) count++; // Restaurants
      if (this.isOwner) count++; // Users
      if (this.isSuperAdmin) count++; // Admin logs
      return count;
    },

    // Есть ли секция администрирования
    hasAdminSection() {
      return this.isSuperAdmin;
    },

    // Активна ли домашняя страница
    isHomeActive() {
      return this.$route.path === '/home';
    },

    // Инициалы пользователя
    userInitials() {
      if (!this.currentUser?.name) return 'U';
      return this.currentUser.name
        .split(' ')
        .map(word => word[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
    },

    // Текст роли пользователя
    currentUserRoleText() {
      const roleMap = {
        'super-admin': 'Супер-админ',
        'admin': 'Администратор',
        'owner': 'Владелец',
        'manager': 'Менеджер',
        'user': 'Пользователь'
      };
      return roleMap[this.currentUser?.role] || 'Пользователь';
    },

    // Текущий язык
    currentLanguage() {
      return this.$i18n.locale;
    }
  },

  methods: {
    ...mapActions("ui", ["toggleDropdown", "setDropdown", "setCollapse"]),
  }
};
</script>

<style scoped>
/* Custom scrollbar for sidebar */
.custom-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: rgba(71, 89, 129, 0.5) rgba(24, 28, 54, 0.3);
}

.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: rgba(24, 28, 54, 0.3);
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(71, 89, 129, 0.5);
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(71, 89, 129, 0.7);
}

/* Smooth transitions for sidebar elements */
.sidebar-transition {
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Glass effect for bottom section */
.glass-effect {
  backdrop-filter: blur(8px);
  background: rgba(0, 2, 14, 0.8);
}

/* Hover effects */
.hover-lift {
  transition: transform 0.2s ease;
}

.hover-lift:hover {
  transform: translateY(-1px);
}

/* Active link indicator */
.active-link-indicator {
  position: relative;
}

.active-link-indicator::after {
  content: '';
  position: absolute;
  right: -8px;
  top: 50%;
  transform: translateY(-50%);
  width: 3px;
  height: 16px;
  background: #001f3f;
  border-radius: 2px;
}

/* Collapsed state adjustments */
:deep(.collapsed-icon) {
  margin: 0 auto;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
  .sidebar-container {
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    z-index: 1000;
    box-shadow: 0 0 50px rgba(0, 0, 0, 0.5);
  }

  .mobile-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
  }
}
</style>
