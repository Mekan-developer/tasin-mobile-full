<template>
  <div>
    <div class="flex flex-col gap-2 text-[14px]">
      <!-- Табы -->
      <div class="p-1 pb-0 bg-white text-black w-full flex justify-between gap-1 border-b border-gray-300">
        <!-- Для super-admin -->
        <RouterLink
          v-if="isSuperAdmin"
          to="/restaurants"
          class="rounded-t-md py-4 px-6 cursor-pointer justify-center items-center flex flex-1 relative"
          :class="$route.path === '/restaurants' ? 'bg-deepblue text-white shadow-lg' : 'bg-gray-100 hover:bg-fog text-midnight'">
          <AppIcon name="Store" size="16" class="mr-2" />
          <span class="font-semibold text-sm">Restaurants</span>
        </RouterLink>

        <!-- Для owner -->
        <RouterLink
          v-if="isOwner"
          to="/restaurants/my"
          class="rounded-t-md py-4 px-6 cursor-pointer justify-center items-center flex flex-1 relative"
          :class="$route.path === '/restaurants/my' ? 'bg-deepblue text-white shadow-lg' : 'bg-gray-100 hover:bg-fog text-midnight'">
          <AppIcon name="Store" size="16" class="mr-2" />
          <span class="font-semibold text-sm">My Restaurant</span>
        </RouterLink>
      </div>

      <router-view/>
    </div>
  </div>
</template>

<script>
  import { RouterLink } from 'vue-router';
  import AppIcon from '@/components/icons/AppIcon.vue';
  import { mapState } from 'vuex';

  export default {
    name: "RestaurantsLayout",
    components: {
      RouterLink,
      AppIcon,
    },
    computed: {
      ...mapState("user", ["currentUser"]), // ← Исправить

      isSuperAdmin() {
        return this.currentUser?.role === 'super-admin';
      },
      isOwner() {
        return this.currentUser?.role === 'owner';
      }
    }
  };
  </script>
