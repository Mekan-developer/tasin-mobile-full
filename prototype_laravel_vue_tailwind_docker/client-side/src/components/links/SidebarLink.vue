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
/* Светлая тема по умолчанию */
.sidebar-link {
  background: transparent;
  color: #6b7280;
  position: relative;
}

.sidebar-link:hover {
  background: rgba(209, 213, 219, 0.5);
  color: #111827;
  transform: translateX(2px);
}

.sidebar-link:hover .sidebar-icon {
  color: #111827;
}

.sidebar-link:hover .sidebar-label {
  color: #111827;
}

.active-sidebar-link {
  background: linear-gradient(90deg, rgba(147, 197, 253, 0.3), rgba(147, 197, 253, 0.15));
  color: #1e40af;
  border-right: 3px solid #3b82f6;
}

.active-sidebar-link .sidebar-icon {
  color: #1e40af;
}

.active-sidebar-link .sidebar-label {
  color: #1e40af;
  font-weight: 600;
}

/* Тёмная тема */
:global(.dark) .sidebar-link {
  color: #cfcfd1;
}

:global(.dark) .sidebar-link:hover {
  background: rgba(71, 89, 129, 0.3);
  color: white;
}

:global(.dark) .sidebar-link:hover .sidebar-icon,
:global(.dark) .sidebar-link:hover .sidebar-label {
  color: white;
}

:global(.dark) .active-sidebar-link {
  background: linear-gradient(90deg, rgba(71, 89, 129, 0.4), rgba(71, 89, 129, 0.2));
  color: white;
  border-right-color: #001f3f;
}

:global(.dark) .active-sidebar-link .sidebar-icon,
:global(.dark) .active-sidebar-link .sidebar-label {
  color: white;
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
