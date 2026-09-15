<template>
  <Teleport to="body">
    <div 
      v-if="isOpen" 
      ref="modalRef"
      role="dialog"
      aria-modal="true"
      :aria-labelledby="'wemi-work-detail-title-' + (work?.id || 'modal')"
      tabindex="-1"
      class="fixed inset-0 z-50 flex items-end md:items-center justify-center p-0 sm:p-4 md:p-6 overflow-hidden bg-folium-ink/80 backdrop-blur-md focus:outline-none transition-all motion-reduce:transition-none"
      @click.self="emit('close')"
    >
      <!-- Bottom Sheet on Mobile (< md) / Centered Modal on Desktop (>= md) -->
      <div class="bg-folium-ivory rounded-t-2xl md:rounded-2xl rounded-b-none md:rounded-b-2xl max-w-4xl w-full max-h-[90dvh] md:max-h-[85vh] flex flex-col border-t-2 md:border-2 border-folium-border shadow-2xl overflow-hidden relative z-10 animate-in slide-in-from-bottom-6 md:slide-in-from-bottom-0 md:zoom-in-95 duration-200 motion-reduce:animate-none">
        <!-- Drag Handle for Mobile Bottom Sheet -->
        <div class="w-12 h-1.5 bg-folium-border/80 rounded-full mx-auto my-2 md:hidden shrink-0"></div>

        <!-- Top Green Laurel Accent Bar -->
        <div class="h-2 bg-folium-forest w-full shrink-0"></div>

        <!-- Opaque Header with Parchment Contrast -->
        <div class="p-4 sm:p-6 pb-4 border-b border-folium-border flex items-start justify-between gap-4 bg-folium-parchment shrink-0">
          <div class="space-y-2">
            <!-- Classification Badge & Discrete Reference Tag -->
            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
              <span class="px-3 py-1 rounded-md text-xs font-mono font-bold bg-folium-terracotta-subtle text-folium-terracotta-deep border border-folium-terracotta-light/60 shadow-2xs">
                {{ work.dewey ? `CDD ${work.dewey}` : `CDD #${work.id}` }}
              </span>
              <span class="px-2.5 py-1 rounded-md text-xs font-mono font-semibold bg-folium-canvas text-folium-sage border border-folium-border-subtle">
                Ref. #{{ work.id }}
              </span>
            </div>

            <!-- Work Title -->
            <h2 :id="'wemi-work-detail-title-' + (work?.id || 'modal')" class="text-xl sm:text-2xl md:text-3xl font-bold font-serif text-folium-ink leading-snug">
              {{ work.title }}
            </h2>

            <!-- Author Attribution -->
            <p class="text-xs text-folium-sage font-medium">
              por <span class="text-folium-ink font-bold">{{ work.author }}</span>
              <span v-if="authorDetails" class="ml-1 text-folium-sage">({{ authorDetails }})</span>
            </p>
          </div>

          <!-- Close Icon Button (44x44px minimum touch target) -->
          <button 
            type="button"
            @click="emit('close')"
            class="p-2.5 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-xl bg-folium-canvas hover:bg-folium-parchment text-folium-ink transition-colors border border-folium-border shrink-0 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-folium-forest"
            aria-label="Cerrar ficha detallada de obra"
            title="Cerrar ficha (Esc)"
          >
            <X class="w-5 h-5 text-folium-moss" />
          </button>
        </div>

        <!-- Scrollable Modal Body on Canvas Surface -->
        <div class="p-4 sm:p-6 overflow-y-auto space-y-6 flex-1 bg-folium-canvas">
          <!-- Section 1: Sinopsis de la Obra (Contenedor Pergamino Sólido con Borde Visible) -->
          <section class="space-y-3 bg-folium-parchment p-4 sm:p-5 rounded-xl border border-folium-border shadow-paper-sm">
            <div class="flex items-center justify-between gap-2 border-b border-folium-border-subtle pb-2">
              <h3 class="text-base font-bold font-serif text-folium-ink flex items-center gap-2">
                <BookOpen class="w-4 h-4 text-folium-forest" />
                Sinopsis de la Obra
              </h3>
              <!-- Language metadata strictly hidden if empty -->
              <span v-if="work.originalLanguage" class="text-xs font-sans font-medium text-folium-sage">
                Idioma original: <strong class="text-folium-ink">{{ work.originalLanguage }}</strong>
              </span>
            </div>

            <p v-if="work.abstract" class="text-sm text-folium-moss leading-relaxed font-sans pt-1">
              {{ work.abstract }}
            </p>

            <!-- Subject Badges in Terracotta Tone -->
            <div v-if="work.subjects && work.subjects.length > 0" class="flex flex-wrap gap-2 pt-2">
              <span 
                v-for="subject in work.subjects" 
                :key="subject"
                class="px-3 py-1 rounded-md text-xs bg-folium-terracotta-subtle text-folium-terracotta-deep border border-folium-terracotta-light/60 font-semibold flex items-center gap-1.5 shadow-2xs"
              >
                <Tag class="w-3.5 h-3.5 text-folium-terracotta-deep" />
                {{ subject }}
              </span>
            </div>
          </section>

          <!-- Sections 2 & 3 Unified: Ediciones y Formatos Disponibles (Contenedor Pergamino Sólido) -->
          <section class="space-y-5 bg-folium-parchment p-4 sm:p-5 rounded-xl border border-folium-border shadow-paper-sm">
            <div class="flex items-center justify-between border-b border-folium-border-subtle pb-2">
              <h3 class="text-base font-bold font-serif text-folium-ink flex items-center gap-2">
                <Layers class="w-4 h-4 text-folium-forest" />
                Ediciones y Formatos Disponibles
              </h3>
            </div>

            <!-- Edition Selector Tabs -->
            <WemiExpressionTabs 
              :expressions="work.expressions || []"
              :active-idx="activeExpressionIdx"
              @select-expression="(idx: number) => activeExpressionIdx = idx"
            />

            <!-- Formats and Physical Copies Breakdown -->
            <div class="space-y-4 pt-2">
              <div class="flex items-center justify-between">
                <h4 class="text-xs font-bold uppercase tracking-wider text-folium-moss font-sans flex items-center gap-1.5">
                  <Package class="w-4 h-4 text-folium-forest" />
                  Formatos físicos en esta edición
                </h4>
                <span v-if="activeManifestations.length > 0" class="text-xs text-folium-sage font-medium">
                  {{ activeManifestations.length }} {{ activeManifestations.length === 1 ? 'formato' : 'formatos' }}
                </span>
              </div>

              <div v-if="activeManifestations.length > 0" class="space-y-4">
                <WemiManifestationCard 
                  v-for="manif in activeManifestations"
                  :key="manif.id"
                  :manifestation="manif"
                  :branches="branches"
                  :current-branch-id="currentBranchId"
                  @request-ill="(data) => emit('request-ill', { work, ...data })"
                  @borrow-item="(data) => emit('borrow-item', { work, ...data })"
                  @reserve-item="(data) => emit('reserve-item', { work, ...data })"
                />
              </div>

              <!-- Fallback if no manifestations registered for active edition -->
              <div v-else class="p-6 rounded-xl bg-folium-ivory border border-folium-border text-center text-xs text-folium-sage font-medium">
                No hay formatos de catálogo registrados para esta edición.
              </div>
            </div>
          </section>
        </div>

        <!-- Sticky Bottom Footer Anchored in Thumb Zone -->
        <div class="p-4 border-t border-folium-border bg-folium-parchment/95 backdrop-blur-sm sticky bottom-0 z-20 flex items-center justify-end shrink-0">
          <button 
            type="button"
            @click="emit('close')"
            class="w-full sm:w-auto px-6 py-3 min-h-[44px] rounded-xl bg-folium-forest hover:bg-folium-forest-hover text-folium-ivory text-xs font-bold transition-all shadow-sm flex items-center justify-center focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-folium-forest"
          >
            Cerrar Ficha
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { X, BookOpen, Tag, Layers, Package } from 'lucide-vue-next';
import WemiExpressionTabs from './WemiExpressionTabs.vue';
import WemiManifestationCard from './WemiManifestationCard.vue';
import type { Work, Branch } from '../modules/catalog/types';
import { useFocusTrap } from '../composables/useFocusTrap';

const props = withDefaults(defineProps<{
  isOpen?: boolean;
  work?: Work;
  branches: Branch[];
  currentBranchId?: number;
}>(), {
  isOpen: false,
  work: () => ({ id: '', title: '', author: '' }),
  currentBranchId: 1
});

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'request-ill', payload: any): void;
  (e: 'borrow-item', payload: any): void;
  (e: 'reserve-item', payload: any): void;
}>();

const modalRef = ref<HTMLElement | null>(null);
useFocusTrap(modalRef, computed(() => props.isOpen ?? false), () => emit('close'));

const activeExpressionIdx = ref(0);

watch(() => props.work, () => {
  activeExpressionIdx.value = 0;
});

const authorDetails = computed(() => {
  const dates = props.work?.yearRange || props.work?.authorDates;
  if (!dates || dates.trim() === '' || dates === '()') return null;
  return dates.replace(/^\(|\)$/g, '').trim();
});

const activeManifestations = computed(() => {
  if (!props.work?.expressions || props.work.expressions.length === 0) return [];
  const expr = props.work.expressions[activeExpressionIdx.value] || props.work.expressions[0];
  return expr.manifestations || [];
});
</script>
