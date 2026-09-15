<template>
  <div class="bg-[#F7F4EC] border border-[#E2DAC8] rounded-2xl py-5 px-6 shadow-[0_4px_16px_rgba(28,36,30,0.03)]">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 divide-y md:divide-y-0 md:divide-x divide-[#E2DAC8]">
      <!-- Metric 1: Títulos Registrados -->
      <div class="space-y-2 pr-2">
        <div class="flex items-center gap-2">
          <div class="w-7 h-7 rounded-lg bg-[#EAE4D7]/60 text-[#2D5A3F] flex items-center justify-center shrink-0">
            <BookOpen class="w-4 h-4 stroke-[1.75]" />
          </div>
          <span class="text-xs font-semibold text-[#334237]">Títulos Registrados</span>
        </div>
        <div v-if="isLoading" class="h-10 w-20 bg-[#E8E2D5] rounded animate-pulse my-1"></div>
        <p v-else class="font-serif font-bold text-4xl text-[#152219] tracking-tight">
          {{ titlesValue }}
        </p>
        <p class="text-xs text-[#7D8D81]">Títulos únicos catalogados</p>
      </div>

      <!-- Metric 2: Ediciones & Formatos -->
      <div class="space-y-2 pt-4 md:pt-0 md:pl-6 pr-2">
        <div class="flex items-center gap-2">
          <div class="w-7 h-7 rounded-lg bg-[#F2E4DE] text-[#9E4E36] flex items-center justify-center shrink-0">
            <BookCopy class="w-4 h-4 stroke-[1.75]" />
          </div>
          <span class="text-xs font-semibold text-[#334237]">Ediciones &amp; Formatos</span>
        </div>
        <div v-if="isLoading" class="h-10 w-20 bg-[#E8E2D5] rounded animate-pulse my-1"></div>
        <p v-else class="font-serif font-bold text-4xl text-[#7D3B28] tracking-tight">
          {{ editionsValue }}
        </p>
        <p class="text-xs text-[#7D8D81]">Físicos, bolsillo y digitales</p>
      </div>

      <!-- Metric 3: Ejemplares en Red -->
      <div class="space-y-2 pt-4 md:pt-0 md:pl-6 pr-2">
        <div class="flex items-center gap-2">
          <div class="w-7 h-7 rounded-lg bg-[#EAE4D7]/60 text-[#2D5A3F] flex items-center justify-center shrink-0">
            <Building2 class="w-4 h-4 stroke-[1.75]" />
          </div>
          <span class="text-xs font-semibold text-[#334237]">Ejemplares en Red</span>
        </div>
        <div v-if="isLoading" class="h-10 w-20 bg-[#E8E2D5] rounded animate-pulse my-1"></div>
        <p v-else class="font-serif font-bold text-4xl text-[#2D5A3F] tracking-tight">
          {{ totalItemsValue }}
        </p>
        <p class="text-xs text-[#7D8D81]">Disponibles en {{ branchesValue }} sedes</p>
      </div>

      <!-- Metric 4: Préstamos Intersede / En Tránsito -->
      <div class="space-y-2 pt-4 md:pt-0 md:pl-6">
        <div class="flex items-center gap-2">
          <div 
            :class="[
              'w-7 h-7 rounded-lg flex items-center justify-center shrink-0 transition-colors',
              inTransitValue > 0 ? 'bg-[#F2E4DE] text-[#9E4E36]' : 'bg-[#EAE4D7]/60 text-[#7D8D81]'
            ]"
          >
            <Truck class="w-4 h-4 stroke-[1.75]" />
          </div>
          <span class="text-xs font-semibold text-[#334237]">Préstamos Intersede</span>
        </div>
        <div v-if="isLoading" class="h-10 w-20 bg-[#E8E2D5] rounded animate-pulse my-1"></div>
        <p 
          v-else
          :class="[
            'font-serif font-bold text-4xl tracking-tight',
            inTransitValue > 0 ? 'text-[#9E4E36]' : 'text-[#152219]'
          ]"
        >
          {{ inTransitValue }}
        </p>
        <p 
          :class="[
            'text-xs font-medium',
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
