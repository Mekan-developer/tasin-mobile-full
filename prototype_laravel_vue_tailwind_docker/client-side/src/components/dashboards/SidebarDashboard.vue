<template>
    <div class="relative">
      <div class="h-[52px] text-2xl bg-gradient-to-br  via-blue-900 from-[#00020ecc] to-deepblue font-bold text-white border-b border-white flex items-center">
        <RouterLink to="/" class="w-full h-full flex items-center ">
          <span v-if="isCollapsed" class="w-full h-full flex justify-center items-center pl-[6px]"> <AppIcon name="Codesandbox" size="20" class="inline-block" /> </span>
          <span v-if="!isCollapsed" class="pl-4 text-[14px] whitespace-nowrap">
            {{ $t('dashboard') }}
          </span>
        </RouterLink>
      </div>
      <nav class="flex flex-col justify-between h-[calc(100vh-52px)] bg-deepblue ">
        <!-- Category -->
          <div class="p-2">
            <nav class="space-y-[6px] overflow-x-hidden overflow-y-auto h-[calc(100vh-52-52-72px)] text-[14px] scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-200 ">


              <SidebarLink @click="setDropdown(false)" to="/home" icon="Home" :label="$t('home')"/>

              <SidebarLink v-if="isSuperAdmin || isOwner"  @click="setDropdown(false)"
              :to="isSuperAdmin ? '/restaurants' : '/restaurants/my'" icon="Building"
              :label="isSuperAdmin ? $t('restaurants') : $t('my_restaurant')"/>

              <SidebarLink @click="setDropdown(false)" to="/icons" icon="List" :label="$t('icons')"/>
              <SidebarLink @click="setDropdown(false)" to="/settings" icon="Settings" :label="$t('settings')"/>
              <SidebarLink @click="setDropdown(false)" to="/tables" icon="Table" :label="$t('tables')"/>

              <SidebarDropdown :label="$t('dropdown')" icon="User">
                <SidebarLink to="/dropdown/page1" icon="CircleSmall" :label="$t('dropdown_1')"/>
                <SidebarLink to="/dropdown/page2" icon="CircleSmall" :label="$t('dropdown_2')"/>
              </SidebarDropdown>

              <SidebarLink
                v-if="isOwner"
                @click="setDropdown(false)"
                to="/users"
                icon="Users"
                :label="$t('users')"
              />


            </nav>
          </div>
          <div class="flex flex-col w-full border-t border-white">
            <!-- change language start -->
            <div class="flex justify-center items-center w-full h-[72px]" >
              <div @click="setCollapse(false)" :class="isCollapsed ? 'cursor-pointer' : 'hidden'"  :title="$t('languages')" class="-mr-2">
                <AppIcon  name="Globe" size="20" class="inline-block " />
              </div>
              <div :class="isCollapsed ? 'hidden' : ''" class="w-full flex items-center">

                  <LanguageSwitcher />


              </div>
            </div>
            <!-- change language end -->

            <LogoutButton variant="sidebar" />
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
    components: { LogoutButton,SidebarLink,SidebarDropdown,LanguageSwitcher },
    data(){
      return {
        loading: false,
        languages: this.$i18n.availableLocales // ["en","ru","tm"]
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
      }
    },
    methods:{
      ...mapActions("ui", ["toggleDropdown"]),
      ...mapActions("ui", ["setDropdown"]),
      ...mapActions("ui", ["setCollapse"]),

      // Или метод для проверки роли (если нужна гибкость)
      userHasRole(role) {
        return this.currentUser?.role === role;
      }
    }
};

</script>
