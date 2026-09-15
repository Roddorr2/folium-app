<template>
  <div class="space-y-4" role="search" aria-label="Buscador y filtros del catálogo bibliográfico">
    <!-- Main High-Speed Search Input Container -->
    <div class="relative h-14 bg-[#FDFBF7] rounded-2xl border-2 border-[#D8CEBC] focus-within:border-[#2D5A3F] focus-within:ring-4 focus-within:ring-[#2D5A3F]/10 shadow-[0_8px_30px_rgba(28,36,30,0.06)] flex items-center px-2 transition-all">
      <div class="relative flex items-center w-full">
        <Search class="w-5 h-5 text-[#6E7B72] absolute left-3.5 pointer-events-none stroke-[1.75]" />
        <label for="catalog-search-input" class="sr-only">Buscar por título, autoría, materia o signatura</label>
        <input 
          id="catalog-search-input"
          :value="searchQuery" 
          @input="$emit('update:searchQuery', $event.target.value)"
          type="text" 
          aria-label="Buscar en el catálogo"
          placeholder="Buscar por título, autoría, materia o signatura..."
          class="w-full pl-11 pr-32 sm:pr-36 bg-transparent text-[#152219] placeholder-[#7D8D81] focus:outline-none text-base md:text-sm font-sans font-medium"
        />
        
        <div class="absolute right-2 flex items-center gap-2">
          <!-- Clear search query button -->
          <button 
            v-if="searchQuery"
            type="button"
            @click="$emit('update:searchQuery', '')"
            class="p-2 min-w-[36px] min-h-[36px] flex items-center justify-center text-[#7D8D81] hover:text-[#152219] transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F] rounded"
            aria-label="Limpiar término de búsqueda"
            title="Limpiar búsqueda"
          >
            <X class="w-4 h-4" />
          </button>

          <!-- Keyboard Shortcut Hint Badge -->
          <span class="hidden sm:inline-flex items-center text-[10px] font-mono text-[#8C988E] border border-[#D5CCBA] rounded px-1.5 py-0.5 bg-[#F5EFE3]/80 font-medium">
            ⌘K
          </span>

          <!-- Search Trigger Button -->
          <button 
            type="button"
            aria-label="Ejecutar búsqueda en catálogo"
            class="bg-[#2D5A3F] hover:bg-[#1E3E2B] text-[#FAF8F2] px-5 sm:px-7 h-10 min-h-[40px] font-semibold rounded-lg shadow-sm flex items-center justify-center text-xs sm:text-sm transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]"
          >
            <span>Buscar</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Facet Filter Controls & Quick Chips -->
    <div class="flex flex-wrap items-center justify-between gap-3 text-xs">
      <div class="flex flex-wrap items-center gap-2.5">
        <!-- Filter by Branch -->
        <div class="flex items-center gap-1.5 bg-[#FAF8F3] px-3 py-2 rounded-lg border border-[#DBD2C0] min-h-[44px]">
          <Building2 class="w-3.5 h-3.5 text-[#2D5A3F] stroke-[1.5] shrink-0" />
          <label for="filter-branch-select" class="text-[#7D8D81] font-medium text-xs">Sede:</label>
          <select 
            id="filter-branch-select"
            :value="selectedBranch" 
            @change="$emit('update:selectedBranch', $event.target.value)"
            aria-label="Filtrar catálogo por sede de la red"
            class="bg-transparent text-[#1C241E] font-medium focus:outline-none cursor-pointer text-base md:text-xs"
          >
            <option value="all">Todas las Sedes</option>
            <option v-for="branch in branches" :key="branch.id" :value="branch.id">
              {{ branch.name }}
            </option>
          </select>
        </div>

        <!-- Filter by Availability Status -->
        <div class="flex items-center gap-1.5 bg-[#FAF8F3] px-3 py-2 rounded-lg border border-[#DBD2C0] min-h-[44px]">
          <CheckCircle2 class="w-3.5 h-3.5 text-[#3E6B48] stroke-[1.5] shrink-0" />
          <label for="filter-status-select" class="text-[#7D8D81] font-medium text-xs">Estado:</label>
          <select 
            id="filter-status-select"
            :value="selectedStatus" 
            @change="$emit('update:selectedStatus', $event.target.value)"
            aria-label="Filtrar catálogo por estado de disponibilidad"
            class="bg-transparent text-[#1C241E] font-medium focus:outline-none cursor-pointer text-base md:text-xs"
          >
            <option value="all">Todos los Estados</option>
            <option value="Disponible">Disponible</option>
            <option value="En Tránsito (ILL)">En Tránsito (ILL)</option>
            <option value="Prestado">Prestado</option>
            <option value="Reservado">Reservado</option>
          </select>
        </div>

        <!-- Subject Quick Filter Chips (Terracota Cuero Envejecido) -->
        <div class="hidden lg:flex items-center gap-1.5" role="group" aria-label="Filtro rápido por materia">
          <Tag class="w-3.5 h-3.5 text-[#9E4E36] stroke-[1.75] ml-1" />
          <span class="text-[#3C4A40] font-semibold mr-0.5">Materias:</span>
          <button 
            v-for="subject in subjects" 
            :key="subject"
            @click="$emit('toggle-subject', subject)"
            :aria-pressed="activeSubjects.includes(subject)"
            :aria-label="'Filtrar por materia ' + subject"
            :class="[
              'px-2.5 py-1 rounded-md text-[11px] font-semibold transition-all border shadow-2xs',
              activeSubjects.includes(subject) 
                ? 'bg-[#9E4E36] text-[#F9F6F0] border-[#7D3B28]' 
                : 'bg-[#F2E4DE] text-[#7D3B28] border-[#C47055]/40 hover:bg-[#E8D5CE]'
            ]"
          >
            {{ subject }}
          </button>
        </div>
      </div>

      <!-- Result Counter & Clear Action -->
      <div class="flex items-center gap-3 ml-auto">
        <!-- Visually Hidden Screen Reader Live Region Announcer (WCAG 2.1 AA) -->
        <div class="sr-only" aria-live="polite" aria-atomic="true">
          {{ filteredCount }} obras encontradas para la búsqueda actual
        </div>

        <span class="text-[#7D8D81] font-mono text-[11px]" aria-live="polite">
          Mostrando <strong class="text-[#1C241E]">{{ filteredCount }}</strong> de <strong class="text-[#1C241E]">{{ totalCount }}</strong> títulos catalogados
        </span>
        <button 
          v-if="hasActiveFilters"
          type="button"
          @click="$emit('clear-filters')"
          aria-label="Limpiar todos los filtros activos"
          class="text-xs text-[#8C433E] hover:underline font-medium flex items-center gap-1 transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#8C433E] rounded px-1"
        >
          <RotateCcw class="w-3 h-3" />
          <span>Limpiar filtros</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Search, X, Building2, CheckCircle2, Tag, RotateCcw } from 'lucide-vue-next';

defineProps({
  searchQuery: { type: String, default: '' },
  selectedBranch: { type: [String, Number], default: 'all' },
  selectedStatus: { type: String, default: 'all' },
  branches: { type: Array, required: true },
  subjects: { type: Array, default: () => ['Botánica Neotropical', 'Taxonomía', 'Historia Natural', 'Realismo Mágico'] },
  activeSubjects: { type: Array, default: () => [] },
  totalCount: { type: Number, default: 0 },
  filteredCount: { type: Number, default: 0 },
  hasActiveFilters: { type: Boolean, default: false }
});

defineEmits(['update:searchQuery', 'update:selectedBranch', 'update:selectedStatus', 'toggle-subject', 'clear-filters']);
</script>
