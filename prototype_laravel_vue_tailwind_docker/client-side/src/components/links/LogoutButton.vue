<template>
  <button
    @click="handleLogout"
    :disabled="loading"
    :class="buttonClass" class="pl-[24px]"
  >
    <slot>
      <span :title="$t('logout')">
        <AppIcon v-if="showIcon" name="LogOut" size="20" />
      </span>
    <span v-if="variant == 'header'">
      {{ loading ? $t('logging_out') : $t('logout') }}
    </span>
     <span v-if="!isCollapsed && variant == 'sidebar'" class="overflow-hidden whitespace-nowrap">
       {{ loading ? $t('logging_out') : $t('logout') }}
    </span>

    </slot>
  </button>
</template>

<script>
import AuthController from "@/controllers/AuthController"
import { mapGetters } from "vuex"
export default {
  props: {
    variant: {
      type: String,
      default: "default" // "header", "sidebar"
    },
    showIcon: {
      type: Boolean,
      default: true
    }
  },
  data() {
    return { loading: false }
  },
  computed: {
    ...mapGetters('ui', ['isCollapsed']),
    buttonClass() {
      if (this.variant === "header") {
        return "flex items-center gap-2 w-full text-left p-3 rounded-[2px] hover:text-deepblue hover:bg-[#cfcfd1] cursor-pointer transition transform active:scale-95 active:shadow-inner disabled:opacity-50 disabled:cursor-not-allowed"
      }
      if (this.variant === "sidebar") {
        return "transition-colors h-[52px] duration-200 ease-in-out flex items-center gap-6 p-4 text-midnight hover:text-white hover:bg-fogactive font-medium bg-fog cursor-pointer active:shadow-inner"
      }
      return "px-4 py-2 rounded bg-gray-200"
    }
  },
  methods: {
    async handleLogout() {
      this.loading = true
      try {
        await AuthController.logout()
      }finally {
        this.loading = false
      }
    }
  }
}
</script>
