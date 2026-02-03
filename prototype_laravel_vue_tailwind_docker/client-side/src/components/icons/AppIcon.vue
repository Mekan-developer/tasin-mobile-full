<template>
  <component
    v-if="iconComponent"
    :is="iconComponent"
    v-bind="iconProps"
    class="w-[18px] h-[18px] ml-[1.5px] mr-2 items-center"
  />
</template>

<script>
import * as icons from "lucide-vue-next"

export default {
  name: "AppIcon",
  props: {
    name: { type: String, required: true },
    size: { type: [String, Number], default: 24 },
    color: { type: String, default: "currentColor" },
  },
  computed: {
    iconComponent() {
      if (!this.name) return null; // если имя пустое
      const component = icons[this.name];
      return component ? component : icons.HelpCircle;

    },
    iconProps() {
      return {
        size: this.size,
        color: this.color,
      }
    },
  },
}
</script>
