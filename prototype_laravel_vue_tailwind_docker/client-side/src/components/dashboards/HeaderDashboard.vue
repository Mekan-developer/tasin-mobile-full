<template>

        <div class="flex items-center justify-center space-x-4">
          <FullscreenToggle />
          <!-- User dropdown -->
          <div class="relative text-[14px]" >
            <button @click="toggleUserMenu" class="flex items-center space-x-2 focus:outline-none h-[52px]">
              <AppIcon name="User" size="20" class=" w-8 h-8 rounded-full text-green-800 bg-gray-400 hover:text-deepblue cursor-pointer"
              />
              <div v-if="currentUser" class="flex flex-col leading-tight">
                <span  class="text-midnight font-medium">{{ currentUser.name }}</span>
                <span class="text-gray-400 text-[12px] lowercase font-medium">{{ currentUser.role }}</span>
              </div>
              <span v-else class="text-midnight font-medium">John Doe</span>
              <AppIcon name="ChevronDown" size="16" :class="{ 'rotate-180': userMenuOpen }" class="w-4 h-4 text-deepblue transition-transform duration-600" />
            </button>
            <div v-if="userMenuOpen" class="fixed inset-0 z-40 " @click="toggleUserMenu"></div>
            <!-- Dropdown menu -->
            <div
                :style="{ maxHeight: (userMenuOpen) ? '500px' : '0', padding: userMenuOpen ? '8px' : '0 8px' }"
              class="absolute z-50 right-0 mt-full text-[13px] transition-all duration-600 overflow-hidden space-y-2 w-48 bg-gradient-to-br  via-blue-900 from-[#00020ecc] to-deepblue  text-white rounded-sm shadow-lg"
            >
              <a href="#" @click="toggleVisiblePopUpLayout" class="flex items-center gap-2 p-3 rounded-[2px] hover:text-deepblue hover:bg-[#cfcfd1] cursor-pointer">
                <AppIcon name="UserCog" size="20" class="hover:text-midnight  cursor-pointer" />
                {{$t('profile')}}
              </a>
              <LogoutButton variant="header" />
            </div>



              <!-- <Transition name="fade"> -->
                <popUpLayout
                  :is-visible-pop-up-layout="isVisiblePopUpLayout" :mounted="mounted"
                  title="Профиль пользователя"
                  @toggle-visible-pop-up-layout="isVisiblePopUpLayout = false" @onAfterLeave="onAfterLeave"
                  >
                  <template #content>
                    <div class="space-y-6 p-4">
                      <!-- Детальная информация -->
                      <div class="grid gap-4">
                        <div class="flex justify-between items-center p-3 bg-white/5 rounded-lg">
                          <span class="text-gray-300">Email:</span>
                          <span class="font-medium">{{ userData.email }}</span>
                        </div>

                        <div class="flex justify-between items-center p-3 bg-white/5 rounded-lg">
                          <span class="text-gray-300">Имя:</span>
                          <span class="font-medium">{{ userData.name }}</span>
                        </div>

                        <div class="flex justify-between items-center p-3 bg-white/5 rounded-lg">
                          <span class="text-gray-300">Роль:</span>
                          <span class="px-3 py-1 bg-blue-500/20 text-blue-300 rounded-full text-sm capitalize">{{ userData.role }}</span>
                        </div>
                      </div>
                    </div>
                  </template>
                </popUpLayout>
              <!-- </Transition> -->

          </div>
        </div>
</template>

<script>
import AuthController from '@/controllers/AuthController';
import { mapState,mapGetters } from 'vuex';
import LogoutButton from '../links/LogoutButton.vue';
import PopUpLayout from '../popUpLayout.vue';
import ProfileController from '@/controllers/ProfileController';
import FullscreenToggle from '@/components/links/FullscreenToggle.vue'
import {popUpLayoutMixins} from "@/mixins/popUpLayoutMixins";

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
  },

  watch: {
    isVisiblePopUpLayout(newValue) {
      if (newValue) {
        this.getProfile()
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
      this.loading = true
      try {
        await AuthController.logout()
        this.$router.push('/login')
      } finally {
        this.loading = false
      }
    },
    async getProfile() {
      try {
         const profile = await ProfileController.getProfile()
         this.userData = profile.data
      } catch (error) {
        console.error('Failed to fetch user profile:', error);
      }
    }
  },
};
</script>
