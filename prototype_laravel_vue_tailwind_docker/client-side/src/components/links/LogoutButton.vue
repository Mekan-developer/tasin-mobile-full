<template>
  <button
    @click="handleLogout"
    :disabled="loading"
    :class="[
      'logout-button group flex items-center gap-3 transition-all duration-200',
      buttonClass
    ]"
  >
    <!-- Icon -->
    <div class="relative flex items-center justify-center w-6 h-6">
      <AppIcon
        v-if="showIcon"
        name="LogOut"
        size="20"
        :class="iconClass"
      />
      <span
        v-if="loading"
        class="absolute inset-0 flex items-center justify-center"
      >
        <svg
          class="animate-spin h-4 w-4 text-current"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
        >
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </span>
    </div>

    <!-- Text -->
    <span
      v-if="variant === 'header' || (!isCollapsed && variant === 'sidebar')"
      class="logout-text flex-1 text-left truncate"
    >
      {{ loading ? $t('logging_out') : $t('logout') }}
    </span>
  </button>
</template>

<script>
import AuthController from "@/controllers/AuthController"
import { mapGetters } from "vuex"
export default {
  props: {
    variant: {
      type: String,
      default: "default",
      validator: (value) => ["default", "header", "sidebar"].includes(value)
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
      const classes = {
        'header': "px-4 py-2.5 rounded-lg hover:bg-fog hover:text-deepblue bg-transparent text-gray-700",
        'sidebar': "w-full px-4 py-3 rounded-lg bg-fog/30 hover:bg-fogactive text-gray-300 hover:text-white",
        'default': "px-4 py-2 rounded bg-gray-200 hover:bg-gray-300"
      }
      return classes[this.variant] || classes.default
    },

    iconClass() {
      if (this.loading) return 'opacity-0'
      return this.variant === 'sidebar'
        ? 'text-gray-300 group-hover:text-white transition-colors'
        : 'text-current'
    }
  },
  methods: {
    async handleLogout() {
      this.loading = true
      try {
        await AuthController.logout()
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
.logout-button {
  position: relative;
}

.logout-button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.logout-button:not(:disabled):active {
  transform: scale(0.98);
}

/* Sidebar variant specific styles */
.logout-button.sidebar {
  border: 1px solid rgba(207, 207, 209, 0.2);
}

.logout-button.sidebar:hover {
  border-color: rgba(71, 89, 129, 0.5);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.logout-button.sidebar:active {
  box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
}
</style>
