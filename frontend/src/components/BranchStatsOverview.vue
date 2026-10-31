<template>
  <div class="bg-[#F7F4EC] border border-[#E2DAC8] rounded-2xl shadow-[0_4px_16px_rgba(28,36,30,0.03)] overflow-hidden">
    <div class="grid grid-cols-2 md:grid-cols-4">
      <!-- Metric 1: Títulos Registrados -->
      <div class="p-4 sm:p-5 lg:p-6 border-r border-b md:border-b-0 border-[#E2DAC8] flex flex-col justify-between space-y-2">
        <div class="flex items-center gap-2 min-h-[32px]">
          <div class="w-7 h-7 rounded-lg bg-[#EAE4D7]/60 text-[#2D5A3F] flex items-center justify-center shrink-0">
            <BookOpen class="w-4 h-4 stroke-[1.75]" />
          </div>
          <span class="text-xs sm:text-xs font-semibold text-[#334237] leading-snug">Títulos Registrados</span>
        </div>
        <div v-if="isLoading" class="h-9 w-20 bg-[#E8E2D5] rounded animate-pulse my-1"></div>
        <p v-else class="font-serif font-bold text-3xl sm:text-4xl text-[#152219] tracking-tight my-0.5">
          {{ titlesValue }}
        </p>
        <p class="text-[11px] sm:text-xs text-[#7D8D81] leading-tight">Títulos únicos catalogados</p>
      </div>

      <!-- Metric 2: Ediciones & Formatos -->
      <div class="p-4 sm:p-5 lg:p-6 border-b md:border-b-0 md:border-r border-[#E2DAC8] flex flex-col justify-between space-y-2">
        <div class="flex items-center gap-2 min-h-[32px]">
          <div class="w-7 h-7 rounded-lg bg-[#F2E4DE] text-[#9E4E36] flex items-center justify-center shrink-0">
            <BookCopy class="w-4 h-4 stroke-[1.75]" />
          </div>
          <span class="text-xs sm:text-xs font-semibold text-[#334237] leading-snug">Ediciones &amp; Formatos</span>
        </div>
        <div v-if="isLoading" class="h-9 w-20 bg-[#E8E2D5] rounded animate-pulse my-1"></div>
        <p v-else class="font-serif font-bold text-3xl sm:text-4xl text-[#7D3B28] tracking-tight my-0.5">
          {{ editionsValue }}
        </p>
        <p class="text-[11px] sm:text-xs text-[#7D8D81] leading-tight">Físicos, bolsillo y digitales</p>
      </div>

      <!-- Metric 3: Ejemplares en Red -->
      <div class="p-4 sm:p-5 lg:p-6 border-r md:border-r border-[#E2DAC8] flex flex-col justify-between space-y-2">
        <div class="flex items-center gap-2 min-h-[32px]">
          <div class="w-7 h-7 rounded-lg bg-[#EAE4D7]/60 text-[#2D5A3F] flex items-center justify-center shrink-0">
            <Building2 class="w-4 h-4 stroke-[1.75]" />
          </div>
          <span class="text-xs sm:text-xs font-semibold text-[#334237] leading-snug">Ejemplares en Red</span>
        </div>
        <div v-if="isLoading" class="h-9 w-20 bg-[#E8E2D5] rounded animate-pulse my-1"></div>
        <p v-else class="font-serif font-bold text-3xl sm:text-4xl text-[#2D5A3F] tracking-tight my-0.5">
          {{ totalItemsValue }}
        </p>
        <p class="text-[11px] sm:text-xs text-[#7D8D81] leading-tight">Disponibles en {{ branchesValue }} sedes</p>
      </div>

      <!-- Metric 4: Préstamos Intersede / En Tránsito -->
      <div class="p-4 sm:p-5 lg:p-6 flex flex-col justify-between space-y-2">
        <div class="flex items-center gap-2 min-h-[32px]">
          <div 
            :class="[
              'w-7 h-7 rounded-lg flex items-center justify-center shrink-0 transition-colors',
              inTransitValue > 0 ? 'bg-[#F2E4DE] text-[#9E4E36]' : 'bg-[#EAE4D7]/60 text-[#7D8D81]'
            ]"
          >
            <Truck class="w-4 h-4 stroke-[1.75]" />
          </div>
          <span class="text-xs sm:text-xs font-semibold text-[#334237] leading-snug">Préstamos Intersede</span>
        </div>
        <div v-if="isLoading" class="h-9 w-20 bg-[#E8E2D5] rounded animate-pulse my-1"></div>
        <p 
          v-else
          :class="[
            'font-serif font-bold text-3xl sm:text-4xl tracking-tight my-0.5',
            inTransitValue > 0 ? 'text-[#9E4E36]' : 'text-[#152219]'
          ]"
        >
          {{ inTransitValue }}
        </p>
        <p 
          :class="[
            'text-[11px] sm:text-xs font-medium leading-tight',
            inTransitValue > 0 ? 'text-[#9E4E36]' : 'text-[#7D8D81]'
          ]"
        >
          {{ inTransitValue > 0 ? 'En traslado entre sucursales' : 'Sin traslados activos' }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { BookOpen, BookCopy, Building2, Truck } from 'lucide-vue-next';

const props = defineProps({
  isLoading: { type: Boolean, default: false },
  // Props with primary naming or aliases
  titlesCount: { type: Number, default: undefined },
  editionsCount: { type: Number, default: undefined },
  totalItemsCount: { type: Number, default: undefined },
  inTransitCount: { type: Number, default: undefined },
  branchesCount: { type: Number, default: undefined },

  // Compatibility legacy props
  totalWorks: { type: Number, default: undefined },
  totalManifestations: { type: Number, default: undefined },
  totalItems: { type: Number, default: undefined },
  totalBranches: { type: Number, default: undefined },
  activeIllTransfers: { type: Number, default: undefined }
});

const titlesValue = computed(() => props.titlesCount ?? props.totalWorks ?? 0);
const editionsValue = computed(() => props.editionsCount ?? props.totalManifestations ?? 0);
const totalItemsValue = computed(() => props.totalItemsCount ?? props.totalItems ?? 0);
const branchesValue = computed(() => props.branchesCount ?? props.totalBranches ?? 4);
const inTransitValue = computed(() => props.inTransitCount ?? props.activeIllTransfers ?? 0);
</script>
