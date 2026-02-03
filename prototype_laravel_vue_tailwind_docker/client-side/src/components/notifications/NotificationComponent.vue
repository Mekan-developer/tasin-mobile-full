<template>
  <div v-if="isNotificationVisible" class="fixed z-50 top-4 right-4 max-w-sm w-full">
    <div
      class="relative flex justify-between shadow-lg rounded-md overflow-hidden border"
      :class="wrapperClass"
    >
      <span class="p-4 block w-full" :class="textClass">
        {{ notification.message }}
      </span>

      <span @click="cancelNotify" class="absolute top-2 right-2">
        <AppIcon name="X" size="18" class="cursor-pointer" :class="iconClass"/>
      </span>

      <div class="absolute bottom-0 left-0 w-full h-1" :class="barClass"></div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';

export default {
  name: "NotificationComponent",
  computed: {
    ...mapGetters('notifications', ['isNotificationVisible']),
    ...mapGetters('notifications', ['notification']),
    wrapperClass() {
      if (this.notification.type === 'success') {
        return 'bg-emerald-600/95 border-emerald-500';
      }
      if (this.notification.type === 'error') {
        return 'bg-rose-700/95 border-rose-500';
      }
      return 'bg-slate-800/95 border-slate-600';
    },
    textClass() {
      return 'text-white text-sm';
    },
    iconClass() {
      return 'text-white/80 hover:text-white';
    },
    barClass() {
      if (this.notification.type === 'success') return 'bg-emerald-400';
      if (this.notification.type === 'error') return 'bg-rose-400';
      return 'bg-slate-400';
    }
  },
  methods: {
    ...mapActions('notifications',['cancelNotify']),
  },
};
</script>
