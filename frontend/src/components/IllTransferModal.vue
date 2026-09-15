<template>
  <Teleport to="body">
    <div 
      v-if="isOpen" 
      ref="modalRef"
      class="fixed inset-0 z-50 flex items-end md:items-center justify-center p-0 sm:p-4 md:p-6 overflow-hidden bg-folium-ink/80 backdrop-blur-md transition-all motion-reduce:transition-none"
      role="dialog"
      aria-modal="true"
      aria-labelledby="ill-modal-title"
      tabindex="-1"
      @click.self="emit('close')"
    >
      <!-- Bottom Sheet on Mobile (< md) / Centered Modal on Desktop (>= md) -->
      <div class="bg-folium-ivory rounded-t-2xl md:rounded-2xl rounded-b-none md:rounded-b-2xl max-w-xl w-full max-h-[90dvh] md:max-h-[85vh] flex flex-col border-t-2 md:border-2 border-folium-border shadow-2xl overflow-hidden relative z-10 animate-in slide-in-from-bottom-6 md:slide-in-from-bottom-0 md:zoom-in-95 duration-200 motion-reduce:animate-none">
        <!-- Drag Handle for Mobile Bottom Sheet -->
        <div class="w-12 h-1.5 bg-folium-border/80 rounded-full mx-auto my-2 md:hidden shrink-0"></div>

        <!-- Top Terracotta Accent Bar -->
        <div class="h-2 bg-folium-terracotta w-full shrink-0"></div>

        <!-- Header (Terracotta Soft Tone) -->
        <div class="p-4 sm:p-6 pb-4 border-b border-folium-border flex items-start justify-between gap-4 bg-folium-terracotta-subtle shrink-0">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-folium-ivory border border-folium-terracotta-light/40 text-folium-terracotta-deep flex items-center justify-center shadow-2xs shrink-0">
              <Truck class="w-5 h-5" />
            </div>
            <div class="space-y-0.5">
              <h3 id="ill-modal-title" class="text-lg sm:text-xl font-bold font-serif text-folium-ink">
                Solicitud de Traslado Intersede
              </h3>
              <p class="text-xs text-folium-sage font-medium">
                Solicita el envío de este ejemplar a tu biblioteca habitual para retiro en sala o préstamo a domicilio.
              </p>
            </div>
          </div>

          <button 
            type="button"
            @click="emit('close')"
            class="p-2.5 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-xl bg-folium-ivory hover:bg-folium-parchment text-folium-ink transition-colors border border-folium-border-subtle shrink-0 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-folium-terracotta"
            aria-label="Cerrar modal de traslado intersede"
            title="Cerrar modal (Esc)"
          >
            <X class="w-5 h-5 text-folium-moss" />
          </button>
        </div>

        <!-- Form Body -->
        <div class="p-4 sm:p-6 overflow-y-auto space-y-5 text-xs text-folium-ink bg-folium-canvas flex-1">
          <!-- Book & Item Summary Card -->
          <div class="bg-folium-parchment p-4 rounded-xl border border-folium-border space-y-2.5 shadow-paper-sm">
            <div class="flex flex-wrap items-center justify-between gap-2">
              <span class="font-mono text-xs font-bold text-folium-terracotta-deep bg-folium-terracotta-subtle px-2.5 py-1 rounded border border-folium-terracotta-light/40 shadow-2xs">
                Ejemplar: {{ barcodeDisplay }}
              </span>
              <span class="font-mono text-xs text-folium-sage font-medium">
                Signatura: {{ shelfLocationDisplay }}
              </span>
            </div>

            <h4 class="text-base sm:text-lg font-bold font-serif text-folium-ink leading-snug">
              {{ workTitleDisplay }}
            </h4>

            <p class="text-xs text-folium-sage font-medium">
              Formato: <strong class="text-folium-ink font-semibold">{{ formatDisplay }}</strong>
            </p>
          </div>

          <!-- Transit Ribbon (Origin ➔ Destination) -->
          <div class="p-4 rounded-xl bg-folium-parchment border border-folium-border space-y-2 shadow-2xs">
            <span class="text-[10px] font-mono uppercase tracking-wider text-folium-sage font-bold block mb-1">
              Ruta de Traslado entre Sedes
            </span>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
              <!-- Origin Branch (Left Card) -->
              <div class="flex-1 p-3 rounded-lg bg-folium-ivory border border-folium-border space-y-1">
                <span class="text-[10px] uppercase font-semibold text-folium-sage tracking-wider block">
                  Sede Origen
                </span>
                <div class="flex items-center gap-1.5 font-bold text-xs text-folium-ink">
                  <MapPin class="w-4 h-4 text-folium-terracotta shrink-0" />
                  <span class="truncate">{{ resolvedOriginBranchName }}</span>
                </div>
              </div>

              <!-- Central Transit Arrow Connector -->
              <div class="p-2 rounded-full bg-folium-terracotta-subtle text-folium-terracotta-deep border border-folium-terracotta-light/40 shrink-0 self-center">
                <ArrowRight class="w-4 h-4 rotate-90 sm:rotate-0" />
              </div>

              <!-- Destination Branch (Right Card - Highlighted in Laurel Forest) -->
              <div class="flex-1 p-3 rounded-lg bg-folium-forest-subtle border border-folium-forest/30 space-y-1">
                <span class="text-[10px] uppercase font-semibold text-folium-forest tracking-wider block">
                  Sede Destino / Retiro
                </span>
                <div class="flex items-center gap-1.5 font-bold text-xs text-folium-forest">
                  <Building2 class="w-4 h-4 text-folium-forest shrink-0" />
                  <span class="truncate">{{ resolvedDestinationBranchName }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Sobriety Estimated Time Container -->
          <div class="flex items-start gap-3 p-3.5 rounded-xl bg-folium-parchment border border-folium-border text-folium-moss">
            <Clock class="w-5 h-5 text-folium-terracotta shrink-0 mt-0.5" />
            <div class="space-y-0.5">
              <p class="font-bold text-folium-ink text-xs leading-snug">
                Tiempo estimado de traslado: 24 a 48 horas laborables
              </p>
              <p class="text-xs text-folium-sage font-medium leading-relaxed">
                Te notificaremos por correo y en el sistema apenas el ejemplar arribe a tu sede.
              </p>
            </div>
          </div>

          <!-- Notes / Motivation Textarea (16px font on mobile to prevent iOS Safari auto-zoom) -->
          <div class="space-y-1.5">
            <label for="ill-note-textarea" class="block font-semibold text-xs text-folium-ink">
              Motivo o Nota (Opcional):
            </label>
            <textarea 
              id="ill-note-textarea"
              v-model="transferNote"
              rows="3"
              aria-label="Motivo o indicaciones adicionales para el traslado"
              placeholder="Opcional: agrega indicaciones o comentarios para el personal de circulación..."
              class="w-full p-3 rounded-lg bg-folium-ivory border border-folium-border focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-folium-terracotta text-base md:text-xs text-folium-ink placeholder:text-folium-muted transition-all"
            ></textarea>
          </div>
        </div>

        <!-- Sticky Footer Anchored in Thumb Zone -->
        <div class="p-4 border-t border-folium-border bg-folium-parchment/95 backdrop-blur-sm sticky bottom-0 z-20 flex flex-col-reverse sm:flex-row items-center justify-end gap-3 shrink-0">
          <button 
            type="button"
            @click="emit('close')"
            class="w-full sm:w-auto px-4 py-3 min-h-[44px] rounded-xl bg-folium-ivory hover:bg-folium-canvas text-folium-ink font-medium text-xs border border-folium-border transition-all flex items-center justify-center focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-folium-terracotta"
          >
            Cancelar
          </button>
          <button 
            type="button"
            :disabled="isSubmitting"
            @click="handleConfirm"
            class="w-full sm:w-auto px-6 py-3 min-h-[44px] rounded-xl bg-folium-terracotta hover:bg-folium-terracotta-deep text-folium-ivory font-medium text-xs transition-all shadow-sm flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-folium-terracotta"
          >
            <span v-if="isSubmitting" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
            <Truck v-else class="w-4 h-4" />
            <span>{{ isSubmitting ? 'Procesando...' : 'Confirmar Solicitud de Traslado' }}</span>
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { Truck, X, MapPin, Building2, Clock, ArrowRight } from 'lucide-vue-next';
import type { Branch } from '../modules/catalog/types';
import type { CatalogItem } from '../modules/transfers/types';
import { useFocusTrap } from '../composables/useFocusTrap';

const props = withDefaults(defineProps<{
  isOpen?: boolean;
  item?: CatalogItem | any;
  originBranch?: Branch | any;
  destinationBranch?: Branch | any;
  targetData?: any;
  itemData?: any;
  workData?: any;
  manifestationData?: any;
  branches?: Branch[];
  currentBranchId?: number;
  targetBranchId?: number;
}>(), {
  isOpen: false,
  branches: () => [],
  currentBranchId: 1
});

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'confirm', notes: string): void;
  (e: 'confirm-transfer', payload: any): void;
}>();

const modalRef = ref<HTMLElement | null>(null);
useFocusTrap(modalRef, computed(() => props.isOpen ?? false), () => emit('close'));

const transferNote = ref('');
const isSubmitting = ref(false);

// Computed helper resolution for item barcode
const barcodeDisplay = computed(() => {
  return props.item?.barcode 
    || props.itemData?.barcode 
    || props.targetData?.item?.barcode 
    || 'FOL-83920-3';
});

// Computed helper resolution for shelf location / signatura
const shelfLocationDisplay = computed(() => {
  return props.item?.shelfLocation 
    || props.itemData?.shelfLocation 
    || props.targetData?.item?.shelfLocation 
    || 'BOT-M01 · Estante B-04';
});

// Computed helper resolution for work title
const workTitleDisplay = computed(() => {
  return props.workData?.title 
    || props.targetData?.work?.title 
    || props.item?.title 
    || 'Flora de la Real Expedición Botánica';
});

// Computed helper resolution for manifestation format
const formatDisplay = computed(() => {
  return props.manifestationData?.format 
    || props.targetData?.manifestation?.format 
    || props.item?.format 
    || 'Tapa Dura en Lino';
});

// Resolved Origin Branch Name
const resolvedOriginBranchName = computed(() => {
  if (props.originBranch?.name) return props.originBranch.name;
  if (props.itemData?.branchName) return props.itemData.branchName;
  
  const originId = props.item?.branchId 
    || props.item?.branch_id 
    || props.itemData?.branchId 
    || props.targetData?.item?.branchId;
    
  if (originId && props.branches) {
    const found = props.branches.find(b => Number(b.id) === Number(originId));
    if (found) return found.name;
  }
  return 'Sede Cusco';
});

// Resolved Destination Branch Name
const resolvedDestinationBranchName = computed(() => {
  if (props.destinationBranch?.name) return props.destinationBranch.name;
  
  const destId = props.targetBranchId 
    || props.currentBranchId 
    || 1;
    
  if (props.branches) {
    const found = props.branches.find(b => Number(b.id) === Number(destId));
    if (found) return found.name;
  }
  return 'Sede Lima Central';
});

const handleConfirm = () => {
  const trackingCode = `ILL-2026-${Math.floor(1000 + Math.random() * 9000)}`;
  const payload = {
    trackingCode,
    item: props.item || props.itemData || props.targetData?.item,
    work: props.workData || props.targetData?.work,
    originBranch: resolvedOriginBranchName.value,
    targetBranch: resolvedDestinationBranchName.value,
    note: transferNote.value
  };

  emit('confirm', transferNote.value);
  emit('confirm-transfer', payload);
  transferNote.value = '';
};
</script>
