<template>
  <Teleport to="body">
    <div 
      v-if="isOpen" 
      ref="modalRef"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#141A16]/75 backdrop-blur-sm"
      role="dialog"
      aria-modal="true"
      :aria-labelledby="titleId"
      :aria-describedby="descId"
      tabindex="-1"
      @click.self="handleBackdropClick"
    >
      <div 
        class="paper-card w-full max-w-md rounded-2xl p-6 sm:p-7 border border-[#E3DAC9] shadow-2xl space-y-5 animate-in fade-in zoom-in-95 duration-200"
      >
        <!-- Header with Alert Stamp -->
        <div class="flex items-start gap-4">
          <div 
            :class="[
              'w-11 h-11 rounded-xl flex items-center justify-center shrink-0 border',
              isDestructive 
                ? 'bg-[#F9EDED] text-[#8C433E] border-[#8C433E]/30' 
                : 'bg-[#FAF0E6] text-[#C27D38] border-[#C27D38]/30'
            ]"
          >
            <AlertTriangle class="w-5 h-5" />
          </div>
          <div class="space-y-1 flex-1">
            <h3 :id="titleId" class="text-lg font-bold font-serif text-[#1C241E] tracking-tight leading-snug">
              {{ title }}
            </h3>
            <p :id="descId" class="text-xs text-[#4A584E] leading-relaxed">
              {{ message }}
            </p>
          </div>
        </div>

        <!-- Optional Additional Context Slot -->
        <slot name="context"></slot>

        <!-- Actions -->
        <div class="pt-3 border-t border-[#E3DAC9] flex items-center justify-end gap-2.5">
          <button 
            type="button" 
            :disabled="isSubmitting"
            @click="$emit('cancel')" 
            class="px-4 py-2.5 min-h-[44px] rounded-xl bg-[#EBE5D8] hover:bg-[#E3DAC9] text-[#1C241E] font-semibold text-xs transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]"
          >
            {{ cancelLabel }}
          </button>
          <button 
            type="button" 
            :disabled="isSubmitting"
            @click="$emit('confirm')" 
            :class="[
              'px-5 py-2.5 min-h-[44px] rounded-xl font-bold text-xs shadow-sm transition-all flex items-center justify-center gap-2 focus-visible:outline-2 focus-visible:outline-offset-2',
              isDestructive 
                ? 'bg-[#8C433E] hover:bg-[#6F3430] text-[#F9F6F0] focus-visible:outline-[#8C433E]' 
                : 'bg-[#2D5A3F] hover:bg-[#224430] text-[#F9F6F0] focus-visible:outline-[#2D5A3F]',
              isSubmitting ? 'opacity-60 cursor-not-allowed' : ''
            ]"
          >
            <span v-if="isSubmitting" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
            <span>{{ confirmLabel }}</span>
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed } from 'vue';
import { AlertTriangle } from 'lucide-vue-next';
import { useFocusTrap } from '../composables/useFocusTrap';

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    required: true
  },
  message: {
    type: String,
    required: true
  },
  confirmLabel: {
    type: String,
    default: 'Confirmar Acción'
  },
  cancelLabel: {
    type: String,
    default: 'Cancelar'
  },
  isDestructive: {
    type: Boolean,
    default: true
  },
  isSubmitting: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['confirm', 'cancel', 'close']);

const modalRef = ref(null);
const titleId = `confirm-modal-title-${Math.random().toString(36).substring(2, 7)}`;
const descId = `confirm-modal-desc-${Math.random().toString(36).substring(2, 7)}`;

useFocusTrap(modalRef, computed(() => props.isOpen), () => {
  if (!props.isSubmitting) {
    emit('cancel');
  }
});

const handleBackdropClick = () => {
  if (!props.isSubmitting) {
    emit('cancel');
  }
};
</script>
