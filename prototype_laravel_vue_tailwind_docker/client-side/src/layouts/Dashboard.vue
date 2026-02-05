<template>
  <div class="flex h-screen bg-gradient-to-br from-gray-900 to-midnight text-gray-100">
    <!-- Sidebar -->
    <aside
      :class="[
        'relative border-r border-deepblue/50 transition-all duration-300',
        isCollapsed
          ? 'w-[70px]'
          : 'w-[260px]'
      ]"
      style="background: linear-gradient(180deg, #00020e 0%, #181c36 100%)"
    >
      <!-- Sidebar Logo/Header -->
      <div class="p-4 border-b border-deepblue">
        <div v-if="!isCollapsed" class="flex items-center gap-3">
          <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
            <AppIcon name="Layout" size="20" class="text-white" />
          </div>
          <div>
            <h2 class="text-lg font-bold text-white tracking-tight">Tasin Mobile</h2>
            <p class="text-xs text-fog">Панель управления</p>
          </div>
        </div>
        <div v-else class="flex justify-center">
          <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
            <AppIcon name="Layout" size="20" class="text-white" />
          </div>
        </div>
      </div>

      <!-- Sidebar Content -->
      <div class="overflow-y-auto h-[calc(100%-70px)] py-4 scrollbar-thin scrollbar-thumb-fogactive scrollbar-track-deepblue">
        <sidebar-dashboard :isCollapsed="isCollapsed" />
      </div>

      <!-- Collapse Toggle Button -->
      <div
        @click="toggleCollapse"
        class="absolute -right-3 top-6 flex justify-center items-center bg-deepblue text-gray-300 hover:bg-fogactive hover:text-white cursor-pointer rounded-full shadow-lg hover:shadow-xl active:scale-95 border border-deepblue"
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
      <header class="flex items-center justify-between bg-deepblue shadow-lg border-b border-midnight px-4 md:px-6 py-4">
        <!-- Breadcrumb/Title Area -->
        <div class="flex items-center gap-4">
          <!-- Mobile Menu Toggle -->
          <button
            @click="toggleCollapse"
            class="md:hidden p-2 rounded-lg hover:bg-fogactive"
          >
            <AppIcon name="Menu" size="20" class="text-gray-300" />
          </button>

          <div>
            <h1 class="text-xl font-semibold text-white tracking-tight">
              {{ currentRouteName }}
            </h1>
            <p class="text-sm text-fog mt-0.5">Добро пожаловать в панель управления</p>
          </div>
        </div>

        <!-- Header Actions -->
        <div class="flex items-center gap-4">
          <!-- Search Button -->
          <button class="p-2.5 rounded-lg hover:bg-fogactive group">
            <AppIcon name="Search" size="20" class="text-gray-300 group-hover:text-white" />
          </button>

          <!-- Notifications -->
          <button class="relative p-2.5 rounded-lg hover:bg-fogactive group">
            <AppIcon name="Bell" size="20" class="text-gray-300 group-hover:text-white" />
            <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full animate-pulse ring-2 ring-deepblue"></span>
          </button>

          <!-- Theme Toggle -->
          <button class="p-2.5 rounded-lg hover:bg-fogactive group">
            <AppIcon name="Sun" size="20" class="text-gray-300 group-hover:text-yellow-400" />
          </button>

          <!-- Header Dashboard Component -->
          <div class="ml-2">
            <header-dashboard />
          </div>
        </div>
      </header>

      <!-- Content Area -->
      <main class="flex-1 overflow-hidden p-4 md:p-6">
        <div class="relative bg-deepblue overflow-hidden text-gray-100 shadow-xl rounded-xl border border-midnight w-full h-full p-4 md:p-6">
          <!-- Notification -->
          <div v-if="isNotificationVisible" class="mb-6 notification-slide">
            <NotificationComponent />
          </div>

          <!-- Page Content -->
          <div class="h-[calc(100%-2rem)] overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-fogactive scrollbar-track-deepblue">
            <!-- Router View для реального контента -->
            <router-view />
          </div>
        </div>

        <!-- Footer -->
        <div class="mt-6 text-center">
          <p class="text-xs text-fog">
            © 2024 Tasin Mobile Project •
            <span class="text-cyan-400 font-medium">v1.0.0</span> •
            <span class="text-fog/50 ml-2">{{ new Date().getFullYear() }}</span>
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
    ...mapGetters('ui', ['isCollapsed']),
    ...mapGetters('notifications', ['isNotificationVisible']),
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
        'reports': 'Отчеты'
      };
      this.currentRouteName = nameMap[routeName] || routeName || 'Панель управления';
    },

    ...mapActions('ui', ['toggleCollapse']),
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

/* Стили для ссылок сайдбара - СДЕЛАТЬ ИХ ВИДИМЫМИ */
.sidebar-link {
  @apply transition-all duration-200;
  color: #cfcfd1 !important; /* Ваш fog цвет для текста */
}

.sidebar-link:hover {
  background-color: #475981af !important; /* Ваш fogactive */
  color: white !important;
  border-right: 3px solid #001f3f; /* Ваш activeColor */
}

.sidebar-link.active {
  background: linear-gradient(to right, #475981af, #475981af/50) !important;
  color: white !important;
  font-weight: 600;
  border-right: 3px solid #001f3f; /* Ваш activeColor */
}

/* Иконки в сайдбаре */
.sidebar-link .icon {
  color: #cfcfd1 !important; /* fog цвет для иконок */
}

.sidebar-link:hover .icon {
  color: white !important;
}

.sidebar-link.active .icon {
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

/* Улучшить видимость текста на темном фоне */
.text-fog {
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
