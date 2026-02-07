<template>
  <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-midnight">
    <table class="w-full text-sm text-left">
      <thead class="bg-gray-50 dark:bg-midnight/50 text-gray-600 dark:text-gray-400 uppercase">
        <tr>
          <th class="w-10 px-4 py-3"></th>
          <slot name="header"></slot>
        </tr>
      </thead>
      <draggable
        v-model="internalItems"
        tag="tbody"
        :item-key="itemKey"
        handle=".drag-handle"
        @end="onDragEnd"
        class="divide-y divide-gray-200 dark:divide-midnight"
      >
        <template #item="{ element }">
          <tr
            class="bg-white dark:bg-deepblue hover:bg-gray-50 dark:hover:bg-fogactive/20 transition-colors"
          >
            <td class="px-4 py-3">
              <span
                class="drag-handle cursor-grab active:cursor-grabbing p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 inline-flex"
                title="Перетащите для изменения порядка"
              >
                <AppIcon name="GripVertical" size="18" />
              </span>
            </td>
            <slot name="row" :item="element" :index="internalItems.indexOf(element)"></slot>
          </tr>
        </template>
      </draggable>
    </table>
  </div>
</template>

<script>
import draggable from 'vuedraggable'
import AppIcon from '@/components/icons/AppIcon.vue'

/** Универсальная таблица с drag-and-drop для сортировки. */
export default {
  name: 'DraggableTable',
  components: { draggable, AppIcon },
  props: {
    items: { type: Array, default: () => [] },
    itemKey: { type: String, default: 'id' }
  },
  emits: ['update:items', 'reorder'],
  computed: {
    internalItems: {
      get() {
        return [...this.items]
      },
      set(val) {
        this.$emit('update:items', val)
      }
    }
  },
  methods: {
    onDragEnd() {
      this.$emit('reorder', [...this.internalItems])
    }
  }
}
</script>
