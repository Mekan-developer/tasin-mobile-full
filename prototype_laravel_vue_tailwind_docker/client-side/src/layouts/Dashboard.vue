<template>
  <div class="flex h-screen bg-gray-100 text-gray-100">
    <!-- Sidebar -->
    <aside :class="isCollapsed? 'w-[64px]' :'w-[240px]'"  class=" shadow-lg md:block relative transition-[width] duration-200 ease-in-out">
      <div class="overflow-hidden h-full">
        <sidebar-dashboard :isCollapsed />
      </div>
      <div @click="toggleCollapse" class="absolute left-full flex justify-center  items-center text-[#635999] top-0  h-[52px] aspect-square">
          <AppIcon name="Menu" size="20" class="hover:text-midnight  cursor-pointer" />
      </div>
    </aside>
    <!-- Main content -->
    <div class="flex-1 flex flex-col">
      <!-- Header -->
       <header class="flex items-center justify-end bg-fog shadow px-6">
            <header-dashboard/>
        </header>
      <!-- Content -->
      <main class="flex-1 p-2 overflow-y-auto">
        <div class="relative bg-white overflow-y-auto text-midnight shadow rounded-sm border border-gray-300 w-full p-2 h-full">
            <NotificationComponent v-if="isNotificationVisible"/>
          <router-view/>
        </div>
      </main>
    </div>
  </div>
</template>

<script>
import api from '@/api/http';
import SidebarDashboard from '@/components/dashboards/SidebarDashboard.vue';
import HeaderDashboard from '@/components/dashboards/HeaderDashboard.vue';
import { mapGetters,mapActions } from 'vuex';
import NotificationComponent from '@/components/notifications/NotificationComponent.vue';

export default {
  name: "UserDashboard",
  components: { SidebarDashboard,HeaderDashboard,NotificationComponent },

  data() {
    return {
      userMenuOpen: false,
      loading: false,
      error: '',
    };
  },
  mounted() {
    this.getProfile();
  },

  computed: {
    ...mapGetters('ui', ['isCollapsed']),
    ...mapGetters('notifications', ['isNotificationVisible']),
  },
  methods: {
    async getProfile() {
      try {
        await api.get('/profile')
      } catch (error) {
        console.error('Failed to fetch user profile:', error);
      }
    },

    ...mapActions('ui', ['toggleCollapse']),
  },
};
</script>

<style>
/* Optional: smooth dropdown transition */
</style>
