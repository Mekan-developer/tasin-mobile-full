<template>
  <RouterLink
    :to="to"
    active-class="active-sidebar-link"
    class="sidebar-link group flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200"
  >
    <!-- Icon Container -->
    <div class="relative flex items-center justify-center w-6 h-6">
      <AppIcon
        :name="icon"
        size="20"
        class="sidebar-icon transition-colors duration-200"
      />
    </div>

    <!-- Label -->
    <span
      v-if="!isCollapsed"
      class="sidebar-label flex-1 min-w-0 text-sm font-medium truncate transition-colors duration-200"
    >
      {{ label }}
    </span>

    <!-- Active Indicator -->
    <div
      v-if="!isCollapsed && $route.path === to"
      class="absolute left-0 top-1/2 transform -translate-y-1/2 w-1 h-6 bg-activeColor rounded-r-full"
    ></div>
  </RouterLink>
</template>

<script>
import { mapGetters } from 'vuex';
export default {
  name: "SidebarLink",
  computed: {
    ...mapGetters('ui', ['isCollapsed']),
  },
  props: {
    to: { type: String, required: true },
    icon: { type: String, required: true },
    label: { type: String, required: true },
  },
}
</script>

<style scoped>
.sidebar-link {
  background: transparent;
  color: #cfcfd1;
  position: relative;
}

.sidebar-link:hover {
  background: rgba(71, 89, 129, 0.3);
  color: white;
  transform: translateX(2px);
}

.sidebar-link:hover .sidebar-icon {
  color: white;
}

.sidebar-link:hover .sidebar-label {
  color: white;
}

.active-sidebar-link {
  background: linear-gradient(90deg, rgba(71, 89, 129, 0.4), rgba(71, 89, 129, 0.2));
  color: white;
  border-right: 3px solid #001f3f;
}

.active-sidebar-link .sidebar-icon {
  color: white;
}

.active-sidebar-link .sidebar-label {
  color: white;
  font-weight: 600;
}

/* Collapsed state */
.sidebar-link:has(+ .sidebar-label:empty) {
  padding: 12px;
  justify-content: center;
}

.sidebar-link:has(+ .sidebar-label:empty):hover {
  transform: scale(1.05);
}
</style>
