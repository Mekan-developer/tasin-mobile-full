<template>
  <div class="flex h-screen bg-gray-50 text-gray-900 dark:bg-gradient-to-br dark:from-gray-900 dark:to-midnight dark:text-gray-100 transition-colors duration-300">
    <!-- Sidebar -->
    <aside
      :class="[
        'relative border-r transition-all duration-300',
        'bg-white border-gray-200 dark:bg-[linear-gradient(180deg,#00020e_0%,#181c36_100%)] dark:border-deepblue/50',
        isCollapsed ? 'w-[70px]' : 'w-[260px]'
      ]"
    >
      <!-- Sidebar Logo/Header -->
      <div class="p-4 border-b border-gray-200 dark:border-deepblue">
        <div v-if="!isCollapsed" class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-lg overflow-hidden">
            <img :src="logoSrc" alt="Logo" class="w-10 h-10 object-contain">
          </div>
          <div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-white tracking-tight">Tasin Mobile</h2>
            <p class="text-xs text-gray-500 dark:text-fog">Панель управления</p>
          </div>
        </div>
        <div v-else class="flex justify-center">
          <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-lg overflow-hidden">
            <img :src="logoSrc" alt="Logo" class="w-10 h-10 object-contain">
          </div>
        </div>
      </div>

      <!-- Sidebar Content -->
      <div class="overflow-y-auto h-[calc(100%-70px)] py-4 scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-fogactive scrollbar-track-gray-100 dark:scrollbar-track-deepblue">
        <sidebar-dashboard :isCollapsed="isCollapsed" />
      </div>

      <!-- Collapse Toggle Button -->
      <div
        @click="toggleCollapse"
        class="absolute -right-3 top-6 flex justify-center items-center bg-gray-200 dark:bg-deepblue text-gray-600 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-fogactive hover:text-gray-900 dark:hover:text-white cursor-pointer rounded-full shadow-lg hover:shadow-xl active:scale-95 border border-gray-300 dark:border-deepblue"
        style="width: 28px; height: 28px;"
      >
        <AppIcon
          :name="isCollapsed ? 'ChevronRight' : 'ChevronLeft'"
          size="14"
        />
      </div>
    </aside>

    <!-- Main content -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Header -->
      <header class="flex items-center justify-between bg-white dark:bg-deepblue shadow-lg border-b border-gray-200 dark:border-midnight px-4 md:px-6 py-4 transition-colors duration-300">
        <!-- Breadcrumb/Title Area -->
        <div class="flex items-center gap-4">
          <!-- Mobile Menu Toggle -->
          <button
            @click="toggleCollapse"
            class="md:hidden p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-fogactive"
          >
            <AppIcon name="Menu" size="20" class="text-gray-600 dark:text-gray-300" />
          </button>

          <div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white tracking-tight">
              {{ currentRouteName }}
            </h1>
            <p class="text-sm text-gray-500 dark:text-fog mt-0.5">Добро пожаловать в панель управления</p>
          </div>
        </div>

        <!-- Header Actions -->
        <div class="flex items-center gap-4">
          <!-- Search Button -->
          <button class="p-2.5 rounded-lg hover:bg-gray-200 dark:hover:bg-fogactive group">
            <AppIcon name="Search" size="20" class="text-gray-600 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white" />
          </button>

          <!-- Notifications -->
          <button class="relative p-2.5 rounded-lg hover:bg-gray-200 dark:hover:bg-fogactive group">
            <AppIcon name="Bell" size="20" class="text-gray-600 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white" />
            <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full animate-pulse ring-2 ring-white dark:ring-deepblue"></span>
          </button>

          <!-- Theme Toggle: Sun = светлая тема, Moon = тёмная -->
          <button
            @click="toggleTheme"
            class="p-2.5 rounded-lg hover:bg-gray-200 dark:hover:bg-fogactive group"
            :title="isDarkTheme ? 'Включить светлую тему' : 'Включить тёмную тему'"
          >
            <AppIcon :name="isDarkTheme ? 'Sun' : 'Moon'" size="20" class="text-yellow-500 dark:text-gray-300 group-hover:text-yellow-500 dark:group-hover:text-yellow-400" />
          </button>

          <!-- Header Dashboard Component -->
          <div class="ml-2">
            <header-dashboard />
          </div>
        </div>
      </header>

      <!-- Content Area -->
      <main class="flex-1 overflow-hidden p-4 md:p-6">
        <div class="relative bg-white dark:bg-deepblue overflow-hidden text-gray-900 dark:text-gray-100 shadow-xl rounded-xl border border-gray-200 dark:border-midnight w-full h-full p-4 md:p-6 transition-colors duration-300">
          <!-- Notification -->
          <div v-if="isNotificationVisible" class="mb-6 notification-slide">
            <NotificationComponent />
          </div>

          <!-- Page Content -->
          <div class="h-[calc(100%-2rem)] overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-fogactive scrollbar-track-gray-100 dark:scrollbar-track-deepblue">
            <!-- Router View для реального контента -->
            <router-view />
          </div>
        </div>

        <!-- Footer -->
        <div class="mt-6 text-center">
          <p class="text-xs text-gray-500 dark:text-fog">
            © 2024 Tasin Mobile Project •
            <span class="text-cyan-400 font-medium">v1.0.0</span> •
            <span class="text-gray-400 dark:text-fog/50 ml-2">{{ new Date().getFullYear() }}</span>
          </p>
        </div>
      </main>
    </div>
  </div>
</template>

<script>
import api from '@/api/http';
import SidebarDashboard from '@/components/dashboards/SidebarDashboard.vue';
import HeaderDashboard from '@/components/dashboards/HeaderDashboard.vue';
import { mapGetters, mapActions } from 'vuex';
import NotificationComponent from '@/components/notifications/NotificationComponent.vue';
import logoDark from '@/assets/logo/dark.png';
import logoLight from '@/assets/logo/light.png';

export default {
  name: "UserDashboard",
  components: {
    SidebarDashboard,
    HeaderDashboard,
    NotificationComponent
  },

  data() {
    return {
      loading: false,
      currentRouteName: 'Панель управления',
      userInitials: 'МР'
    };
  },

  mounted() {
    this.getProfile();
    this.updateRouteName();
    this.$watch(
      () => this.$route,
      () => {
        this.updateRouteName();
      }
    );
  },

  computed: {
    ...mapGetters('ui', ['isCollapsed', 'isDarkTheme']),
    ...mapGetters('notifications', ['isNotificationVisible']),
    /** Логотип в зависимости от темы */
    logoSrc() {
      return this.isDarkTheme ? logoDark : logoLight;
    },
  },

  methods: {
    async getProfile() {
      try {
        this.loading = true;
        const response = await api.get('/profile');
        if (response.data.name) {
          this.userInitials = response.data.name
            .split(' ')
            .map(word => word[0])
            .join('')
            .toUpperCase()
            .slice(0, 2);
        }
      } catch (error) {
        console.error('Failed to fetch user profile:', error);
      } finally {
        this.loading = false;
      }
    },

    updateRouteName() {
      const routeName = this.$route.name;
      const nameMap = {
        'dashboard': 'Панель управления',
        'profile': 'Мой профиль',
        'settings': 'Настройки',
        'analytics': 'Аналитика',
        'users': 'Пользователи',
        'reports': 'Отчеты',
        'Categories': 'Управление категориями',
        'Products': 'Управление товарами'
      };
      this.currentRouteName = nameMap[routeName] || routeName || 'Панель управления';
    },

    ...mapActions('ui', ['toggleCollapse', 'toggleTheme']),
  },
};
</script>

<style>
@reference "../assets/app.css";

@layer components {
  .btn-primary {
    @apply bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-medium py-2 px-4 rounded-lg transition-all duration-200;
  }
}

/* Стили для ссылок сайдбара (светлая тема) */
.sidebar-link {
  @apply transition-all duration-200;
  color: #6b7280 !important;
}

.sidebar-link:hover {
  background-color: rgba(209, 213, 219, 0.5) !important;
  color: #111827 !important;
}

.sidebar-link.active-sidebar-link {
  background: linear-gradient(90deg, rgba(147, 197, 253, 0.3), rgba(147, 197, 253, 0.15)) !important;
  color: #1e40af !important;
  font-weight: 600;
  border-right: 3px solid #3b82f6;
}

/* Тёмная тема для сайдбара */
.dark .sidebar-link {
  color: #cfcfd1 !important;
}

.dark .sidebar-link:hover {
  background-color: #475981af !important;
  color: white !important;
  border-right: 3px solid #001f3f;
}

.dark .sidebar-link.active-sidebar-link {
  background: linear-gradient(to right, #475981af, #475981af / 50) !important;
  color: white !important;
  border-right-color: #001f3f;
}

.sidebar-link .sidebar-icon {
  color: inherit !important;
}

.dark .sidebar-link:hover .sidebar-icon,
.dark .sidebar-link.active-sidebar-link .sidebar-icon {
  color: white !important;
}

/* Анимация для уведомлений */
.notification-slide {
  animation: slideDown 0.3s ease;
}

@keyframes slideDown {
  from {
    transform: translateY(-10px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

/* Цвет fog: для светлой темы — серый, для тёмной — полупрозрачный белый */
.text-fog {
  color: #6b7280;
}

.dark .text-fog {
  color: #cfcfd188 !important;
}

.text-white {
  color: white !important;
}

/* Градиенты для элементов */
.gradient-border {
  border-image: linear-gradient(45deg, #00020e, #181c36) 1;
}

/* Стили для кнопок */
.btn-primary {
  @apply bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-medium py-2 px-4 rounded-lg transition-all duration-200;
}

/* Мобильная оптимизация */
@media (max-width: 768px) {
  aside {
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    z-index: 50;
    box-shadow: 0 0 50px rgba(0, 0, 0, 0.3);
  }

  .mobile-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 2, 14, 0.7);
    z-index: 40;
  }
}
</style>
