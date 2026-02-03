<template>
  <teleport to='body'>
    <div  v-if="mounted" class="fixed inset-0 z-50">
      <!-- Фон: без анимации -->
      <div
          class="absolute inset-0 bg-black/50 backdrop-blur-sm"
          style="transition:none; z-index:0;">
      </div>

      <!-- Модалка: только она в Transition -->
      <Transition name="modal" appear @after-leave="$emit('onAfterLeave')">
        <div @click="$emit('toggleVisiblePopUpLayout')" v-show="isVisiblePopUpLayout"
            class="absolute inset-0 flex items-center justify-center px-4 sm:px-6"
            style="z-index:1;">
          <div @click.stop
            class="flex flex-col overflow-hidden bg-gradient-to-br from-[#181c367c] to-[#181c3694] text-white
                  w-full max-w-2xl max-h-[90vh] rounded-sm shadow-2xl border border-gray-600/30
                  transition-transform duration-300">
            <!-- Header -->
            <div class="flex justify-between items-center h-14 bg-gradient-to-br  via-blue-900 from-[#00020ecc] to-deepblue px-4">
              <h3 class="text-md font-semibold">{{ title }}</h3>
              <button @click="$emit('toggleVisiblePopUpLayout')" class="flex items-center justify-center w-[30px] h-[30px] hover:bg-white/10 pl-[4px] rounded-full transition-colors">
                <AppIcon name="X" size="24" color="#fff" />
              </button>
            </div>
            <!-- Content -->
            <div class="flex-1 p-6 overflow-auto scroll-custom">
              <slot name="content" />
            </div>
          </div>
        </div>
      </Transition>
    </div>
  </teleport>
</template>

<script>

export default {
  name: "popUpLayout",
  emits:["toggleVisiblePopUpLayout", "onAfterLeave"],
  props: {
    isVisiblePopUpLayout: {
      type: Boolean,
      default: false,
    },
    mounted: {
      type: Boolean,
      default: false
    },
    title: {
      type: String,
      default: "Title popup layout",
    },
  },

  mounted() {
    document.addEventListener('keydown', this.handleEscape);
  },

  beforeUnmount() {
    document.removeEventListener('keydown', this.handleEscape);
  },

  methods: {
    handleEscape(event) {
      if (event.key === 'Escape' && this.isVisiblePopUpLayout) {
        this.$emit('toggleVisiblePopUpLayout');
      }
    }
  }

  };

</script>

<style scoped>

/* Анимация только для внешней обёртки (легкий фейд) */
.modal-enter-active, .modal-leave-active { transition: opacity .2s ease, transform .2s ease; }
.modal-enter-from,  .modal-leave-to { opacity: 0; transform: scale(.98); }

/* Анимация появления */
.fade-enter-active {
  transition: all 0.3s ease-out;
}

.fade-leave-active {
  transition: all 0.2s ease-in;
}

.fade-enter-from {
  opacity: 0;
  transform: scale(0.9);
}

.fade-leave-to {
  opacity: 0;
  transform: scale(1.1);
}

/* Кастомный скроллбар */
.scroll-custom::-webkit-scrollbar {
  width: 6px;
}

.scroll-custom::-webkit-scrollbar-thumb {
  background: rgba(255,255,255,0.3);
  border-radius: 3px;
}
</style>
