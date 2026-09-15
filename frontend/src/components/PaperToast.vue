<template>
  <Teleport to="body">
    <div 
      class="fixed bottom-6 right-6 z-50 flex flex-col gap-3 max-w-md w-full pointer-events-none px-4"
      role="status"
      aria-live="polite"
      aria-atomic="false"
    >
      <TransitionGroup name="toast">
        <div 
          v-for="toast in activeToasts" 
          :key="toast.id"
          class="pointer-events-auto bg-[#F9F6F0] p-4 rounded-xl border shadow-paper-lg flex items-start gap-3 relative overflow-hidden group transition-all"
          :class="toastBorderClass(toast.type)"
        >
          <!-- Left Accent Stripe -->
          <div class="absolute left-0 top-0 bottom-0 w-1.5" :class="toastStripeClass(toast.type)"></div>

          <!-- Icon Stamp -->
          <div 
            class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 border"
            :class="toastIconBoxClass(toast.type)"
          >
            <component :is="getToastIcon(toast.type)" class="w-4 h-4" />
          </div>

          <!-- Content -->
          <div class="space-y-1 flex-1 text-xs">
            <div class="flex items-center justify-between">
              <span class="font-mono text-[9px] uppercase tracking-wider font-semibold text-[#7D8D81]">
                {{ toast.timestamp }} · {{ toast.tag }}
              </span>
              <button 
                type="button"
                @click="handleDismiss(toast.id)"
                class="text-[#7D8D81] hover:text-[#1C241E] p-1 rounded-md hover:bg-[#EBE5D8]/60 transition-colors"
                aria-label="Cerrar notificación"
              >
                <X class="w-3.5 h-3.5" />
              </button>
            </div>

            <h5 class="font-bold text-sm font-serif text-[#1C241E] leading-tight">
              {{ toast.title }}
            </h5>

            <p class="text-xs text-[#4A584E] leading-snug">
              {{ toast.message }}
            </p>
          </div>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<script setup>
import { computed } from 'vue';
import { X, Bell, Truck, CheckCircle2, Bookmark, AlertTriangle, AlertCircle, Info } from 'lucide-vue-next';
import { useToast } from '../composables/useToast';

const props = defineProps({
  toasts: { type: Array, default: null }
});

const emit = defineEmits(['dismiss']);

const { toasts: globalToasts, dismiss: globalDismiss } = useToast();

const activeToasts = computed(() => {
  return props.toasts ?? globalToasts.value;
});

const handleDismiss = (id) => {
  if (props.toasts) {
    emit('dismiss', id);
  } else {
    globalDismiss(id);
  }
};

const getToastIcon = (type) => {
  switch (type) {
    case 'error': return AlertCircle;
    case 'warning': return AlertTriangle;
    case 'ill': return Truck;
    case 'success': return CheckCircle2;
    case 'reserve': return Bookmark;
    case 'info': return Info;
    default: return Bell;
  }
};

const toastStripeClass = (type) => {
  switch (type) {
    case 'error':
    case 'warning':
      return 'bg-[#8C433E]';
    case 'ill':
      return 'bg-[#B87333]';
    case 'success':
      return 'bg-[#2D5A3F]';
    case 'reserve':
      return 'bg-[#6B5B95]';
    case 'info':
    default:
      return 'bg-[#4A584E]';
  }
};

const toastIconBoxClass = (type) => {
  switch (type) {
    case 'error':
    case 'warning':
      return 'bg-[#F9EDED] text-[#8C433E] border-[#8C433E]/30';
    case 'ill':
      return 'bg-[#FBF3EB] text-[#B87333] border-[#B87333]/30';
    case 'success':
      return 'bg-[#E9F2EB] text-[#2D5A3F] border-[#2D5A3F]/30';
    case 'reserve':
      return 'bg-[#F4F0F7] text-[#6B5B95] border-[#6B5B95]/30';
    case 'info':
    default:
      return 'bg-[#FAF7F0] text-[#4A584E] border-[#E3DAC9]';
  }
};

const toastBorderClass = (type) => {
  switch (type) {
    case 'error':
    case 'warning':
      return 'border-[#8C433E]/30';
    case 'ill':
      return 'border-[#B87333]/40';
    case 'success':
      return 'border-[#2D5A3F]/30';
    case 'reserve':
      return 'border-[#6B5B95]/30';
    default:
      return 'border-[#E3DAC9]';
  }
};
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.toast-enter-from {
  opacity: 0;
  transform: translateY(20px) scale(0.95);
}
.toast-leave-to {
  opacity: 0;
  transform: translateX(100px);
}
</style>
