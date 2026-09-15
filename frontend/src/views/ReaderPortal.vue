<template>
  <div class="space-y-10">
    <!-- Editorial Hero Section -->
    <section class="text-center max-w-3xl mx-auto space-y-4 pt-4">
      <h2 class="font-serif font-bold text-4xl md:text-5xl lg:text-6xl text-[#152219] leading-[1.15] tracking-tight">
        El Archivo Vivo de Nuestra Red
        <span class="font-serif italic font-medium text-2xl md:text-3xl text-[#2D5A3F] mt-2 block">Catálogo Colectivo &amp; Fondo Bibliográfico</span>
      </h2>

      <p class="text-[#3C4A40] text-base md:text-lg max-w-2xl mx-auto leading-relaxed mt-4 font-sans">
        Consulta volúmenes, ediciones históricas y ejemplares disponibles para préstamo en sala o traslado intersede entre nuestras cuatro sucursales.
      </p>
    </section>

    <!-- Network Stats Overview -->
    <BranchStatsOverview 
      :is-loading="isLoading"
      :total-works="works.length"
      :total-manifestations="totalManifestationsCount"
      :total-items="totalItemsCount"
      :total-branches="branches.length"
      :active-ill-transfers="illTransfers.length"
    />

    <!-- Lithographic Ornamental Divider -->
    <div class="border-t border-[#DFD6C4] w-full relative my-10 flex items-center justify-center">
      <div class="relative px-4 bg-[#F9F6F0] text-[#2D5A3F] text-sm select-none font-serif flex items-center gap-2">
        <span>❖</span>
      </div>
    </div>

    <!-- Filter & Search Controls -->
    <CatalogFilterBar 
      :search-query="searchQuery"
      :selected-branch="selectedBranch"
      :selected-status="selectedStatus"
      :branches="branches"
      :subjects="availableSubjects"
      :active-subjects="activeSubjects"
      :total-count="works.length"
      :filtered-count="filteredWorks.length"
      :has-active-filters="hasActiveFilters"
      @update:searchQuery="$emit('update:searchQuery', $event)"
      @update:selectedBranch="$emit('update:selectedBranch', $event)"
      @update:selectedStatus="$emit('update:selectedStatus', $event)"
      @toggle-subject="$emit('toggle-subject', $event)"
      @clear-filters="$emit('clear-filters')"
    />

    <!-- Works Grid -->
    <section class="space-y-6">
      <div class="flex items-center justify-between">
        <h3 class="text-xl font-bold font-serif text-[#1C241E] flex items-center gap-2">
          <BookOpen class="w-5 h-5 text-[#2D5A3F]" />
          <span>Obras y Acervo Bibliográfico</span>
        </h3>
      </div>

      <!-- Skeleton Grid when Loading -->
      <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div 
          v-for="n in 6" 
          :key="'reader-skeleton-' + n"
          class="paper-card rounded-2xl border border-[#E3DAC9] p-6 space-y-4 shadow-paper-sm animate-pulse flex flex-col justify-between"
        >
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <div class="h-5 w-20 bg-[#E8E2D5] rounded-md"></div>
              <div class="h-5 w-28 bg-[#E8E2D5] rounded-full"></div>
            </div>
            <div class="space-y-1.5 pt-1">
              <div class="h-6 w-5/6 bg-[#E8E2D5] rounded"></div>
              <div class="h-4 w-1/2 bg-[#EDE7DC] rounded"></div>
            </div>
            <div class="h-12 w-full bg-[#EBE5D8]/70 rounded-lg"></div>
            <div class="flex gap-2 pt-1">
              <div class="h-6 w-20 bg-[#EDE7DC] rounded-md"></div>
              <div class="h-6 w-24 bg-[#EDE7DC] rounded-md"></div>
            </div>
          </div>
          <div class="pt-4 border-t border-[#E3DAC9] flex items-center justify-between gap-2">
            <div class="h-4 w-24 bg-[#E8E2D5] rounded"></div>
            <div class="h-9 w-28 bg-[#E8E2D5] rounded-xl"></div>
          </div>
        </div>
      </div>

      <!-- Actual Works Grid -->
      <div v-else-if="filteredWorks.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <WemiWorkCard 
          v-for="work in filteredWorks"
          :key="work.id"
          :work="work"
          :branches="branches"
          :current-branch-id="currentBranchId"
          @select="$emit('open-work-detail', work)"
          @request-ill="$emit('open-ill-modal', work)"
          @notify-availability="$emit('notify-availability', work)"
        />
      </div>

      <!-- Empty State -->
      <div v-else class="paper-card rounded-xl p-12 text-center space-y-4 max-w-xl mx-auto">
        <SearchX class="w-12 h-12 text-[#7D8D81] mx-auto stroke-[1.5]" />
        <h4 class="text-lg font-bold font-serif text-[#1C241E]">No se encontraron registros</h4>
        <p class="text-xs text-[#4A584E]">
          No existen obras catalogadas en la base de datos que coincidan con la búsqueda.
        </p>
      </div>
    </section>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { Sparkles, BookOpen, SearchX } from 'lucide-vue-next';
import BranchStatsOverview from '../components/BranchStatsOverview.vue';
import CatalogFilterBar from '../components/CatalogFilterBar.vue';
import WemiWorkCard from '../components/WemiWorkCard.vue';

const props = defineProps({
  works: { type: Array, default: () => [] },
  branches: { type: Array, default: () => [] },
  currentBranchId: { type: Number, default: 1 },
  searchQuery: { type: String, default: '' },
  selectedBranch: { type: String, default: 'all' },
  selectedStatus: { type: String, default: 'all' },
  activeSubjects: { type: Array, default: () => [] },
  illTransfers: { type: Array, default: () => [] },
  isLoading: { type: Boolean, default: false }
});

defineEmits([
  'update:searchQuery',
  'update:selectedBranch',
  'update:selectedStatus',
  'toggle-subject',
  'clear-filters',
  'open-work-detail',
  'open-ill-modal',
  'notify-availability'
]);

const availableSubjects = [
  'Literatura Latinoamericana',
  'Realismo Mágico',
  'Botánica Neotropical', 
  'Historia de la Ciencia', 
  'Pensamiento Crítico',
  'Ciencia Ficción / Distopía',
  'Ensayos e Iconografía'
];

const filteredWorks = computed(() => props.works);

const hasActiveFilters = computed(() => {
  return !!props.searchQuery || props.selectedBranch !== 'all' || props.selectedStatus !== 'all' || props.activeSubjects.length > 0;
});

const totalManifestationsCount = computed(() => {
  let count = 0;
  props.works.forEach(w => w.expressions?.forEach(e => count += e.manifestations?.length || 0));
  return count;
});

const totalItemsCount = computed(() => {
  let count = 0;
  props.works.forEach(w => w.expressions?.forEach(e => e.manifestations?.forEach(m => count += m.items?.length || 0)));
  return count;
});
</script>
