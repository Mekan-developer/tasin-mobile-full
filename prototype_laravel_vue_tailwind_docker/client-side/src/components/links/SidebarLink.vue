<template>
  <RouterLink
    :to="to"
    active-class="bg-fog text-white"
    class="items-center flex py-[13px] h-[47px] rounded-[6px] hover:text-[#fff] hover:bg-fog cursor-pointer transition-colors duration-300"
  >
    <span class="flex items-center pl-[13px]">
      <span :title="label">
        <AppIcon :name="icon" size="20" class="inline-block " />
      </span>
      <span v-if="!isCollapsed" class="whitespace-nowrap">{{ label }}</span>
    </span>
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
    to: { type: String, required: true },      // маршрут
    icon: { type: String, required: true },    // название иконки
    label: { type: String, required: true },   // текст ссылки
  },
}
</script>
