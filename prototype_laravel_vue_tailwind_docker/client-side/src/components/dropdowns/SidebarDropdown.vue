<template>
  <div>
    <!-- Заголовок dropdown -->
    <div
      @click="toggleDropdown"
      :class="[
        'items-center flex py-[13px] hover:text-[#fff] hover:bg-[#687d838a] cursor-pointer transition-colors',
        isDashboardActive?'bg-fog text-white':'',  isDropdownOpen ? 'rounded-t-[6px]' : 'rounded-[6px]'
      ]"
    >
      <span class="flex justify-between w-full items-center pl-[13px]">
        <div class="whitespace-nowrap flex items-center">
          <span :title="label">
            <AppIcon :name="icon" size="20" class="inline-block mr-2" />
          </span>
          <span v-if="!isCollapsed">{{ label }}</span>
        </div>
        <AppIcon
          name="ChevronDown"
          size="20"
          :class="{
            'rotate-180': isDropdownOpen,
            'bg-deepblue': isDropdownOpen
          }"
          class="inline-block mr-2 hover:bg-gray-700 rounded-md transition-transform duration-600"
        />
      </span>
    </div>

    <!-- Контент dropdown -->
    <section
      class="flex flex-col space-y-1 bg-gray-800 rounded-b-sm text-[12px] overflow-hidden transition-all duration-600"
      :style="{
        maxHeight: isDropdownOpen ? '500px' : '0',
        padding: isDropdownOpen ? '8px' : '0 8px'
      }"
    >
      <slot />
    </section>
  </div>
</template>

<script>
import { useRoute } from 'vue-router';
import { mapActions, mapGetters } from 'vuex';

export default {
  name: "SidebarDropdown",
   setup() {
    const route = useRoute()
    return { route }
  },
  props: {
    label: { type: String, required: true },  // название dropdown
    icon: { type: String, default: "User" },  // иконка
  },
   computed: {
        ...mapGetters("ui", ["isDropdownOpen", "isCollapsed"]),
        isDashboardActive() {
        // проверяем путь
        return this.route.path.startsWith("/dropdown/page1") ||
                this.route.path.startsWith("/dropdown/page2")
        }
    },

  methods: {
    ...mapActions("ui", ["toggleDropdown"]),
  }
}
</script>
