<template>
  <div class="space-y-3">
    <div class="flex items-center justify-between border-b border-folium-border-subtle pb-2">
      <span class="text-xs font-bold uppercase tracking-wider text-folium-ink font-sans flex items-center gap-1.5">
        <Library class="w-3.5 h-3.5 text-folium-forest" />
        Ejemplares físicos por sede
      </span>
      <span class="text-[11px] font-sans text-folium-sage font-medium">
        {{ items.length }} {{ items.length === 1 ? 'ejemplar registrado' : 'ejemplares registrados' }}
      </span>
    </div>

    <!-- Items List -->
    <div class="space-y-2.5">
      <div 
        v-for="item in items" 
        :key="item.id || item.barcode"
        :class="[
          'p-3.5 rounded-lg border text-xs flex flex-wrap items-center justify-between gap-3 transition-all shadow-2xs',
          getItemBranchId(item) === currentBranchId 
            ? 'bg-folium-ivory border-folium-forest/40 ring-1 ring-folium-forest/20' 
            : 'bg-folium-parchment border-folium-border'
        ]"
      >
        <!-- Branch & Location Details -->
        <div class="space-y-1.5">
          <div class="flex items-center gap-2">
            <span class="font-bold text-folium-ink text-sm flex items-center gap-1.5">
              <MapPin class="w-4 h-4 text-folium-forest shrink-0" />
              {{ getBranchName(item) }}
            </span>
            <span v-if="getItemBranchId(item) === currentBranchId" class="px-2 py-0.5 rounded text-[10px] font-mono uppercase bg-folium-forest text-folium-ivory font-bold shadow-2xs">
              Tu Sede
            </span>
          </div>

          <div class="flex flex-wrap items-center gap-2 text-[11px] font-mono text-folium-sage">
            <span v-if="item.shelfLocation" class="bg-folium-ivory px-2 py-0.5 rounded border border-folium-border text-folium-ink font-medium flex items-center gap-1">
              <MapPin class="w-3 h-3 text-folium-forest" />
              <span>{{ item.shelfLocation }}</span>
            </span>
            <span class="bg-folium-ivory px-2 py-0.5 rounded border border-folium-border text-folium-ink font-semibold">
              Barcode: {{ item.barcode }}
            </span>
          </div>

          <p v-if="item.condition" class="text-[11px] text-folium-sage italic">
            "{{ item.condition }}"
          </p>
        </div>

        <!-- Status Badge & Contextual Action Button -->
        <div class="flex items-center gap-3 ml-auto">
          <!-- Status Badge (Humanized Spanish) -->
          <span 
            :class="[
              'px-2.5 py-1 rounded-full text-xs font-semibold flex items-center gap-1.5 border',
              getStatusBadgeClass(item.status)
            ]"
          >
            <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="getStatusDotClass(item.status)"></span>
            <span>{{ getDisplayStatus(item.status) }}</span>
          </span>

          <!-- Contextual Action Buttons -->
          <!-- 1. Available at Current Branch -> Local Loan -->
          <button 
            v-if="isAvailable(item) && getItemBranchId(item) === currentBranchId"
            @click="emit('borrow-item', item)"
            class="px-3.5 py-1.5 rounded-lg bg-folium-forest hover:bg-folium-forest-hover text-folium-ivory font-bold text-xs transition-all shadow-sm flex items-center gap-1.5"
          >
            <CheckCircle2 class="w-3.5 h-3.5" />
            <span>Préstamo Local</span>
          </button>

          <!-- 2. Available at Another Branch -> Request Interlibrary Loan (ILL) -->
          <button 
            v-else-if="isAvailable(item) && getItemBranchId(item) !== currentBranchId"
            @click="emit('request-ill', item)"
            class="px-3.5 py-1.5 rounded-lg bg-folium-terracotta-subtle hover:bg-folium-terracotta-badge text-folium-terracotta-deep border border-folium-terracotta-light/40 font-bold text-xs transition-all flex items-center gap-1.5 shadow-2xs"
            title="Solicitar traslado de esta sede a tu sede actual"
          >
            <Truck class="w-3.5 h-3.5" />
            <span>Solicitar ILL</span>
          </button>

          <!-- 3. In Transit -> Disabled In Transit Notice -->
          <button 
            v-else-if="isInTransit(item)"
            disabled
            class="px-3.5 py-1.5 rounded-lg bg-folium-amber-subtle text-folium-amber font-semibold text-xs border border-folium-amber/30 opacity-90 flex items-center gap-1.5 cursor-not-allowed"
          >
            <Clock class="w-3.5 h-3.5 text-folium-amber" />
            <span>En Camino</span>
          </button>

          <!-- 4. Borrowed or Reserved -> Reserve Turn -->
          <button 
            v-else
            @click="emit('reserve-item', item)"
            class="px-3.5 py-1.5 rounded-lg bg-folium-terracotta-subtle hover:bg-folium-terracotta-badge text-folium-terracotta-deep border border-folium-terracotta-light/40 font-semibold text-xs transition-all flex items-center gap-1.5"
          >
            <Bookmark class="w-3.5 h-3.5 text-folium-terracotta-deep" />
            <span>Reservar Turno</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Library, MapPin, CheckCircle2, Truck, Clock, Bookmark } from 'lucide-vue-next';
import type { Branch, Item } from '../modules/catalog/types';

const props = withDefaults(defineProps<{
  items: Item[];
  branches: Branch[];
  currentBranchId?: number;
}>(), {
  currentBranchId: 1
});

const emit = defineEmits<{
  (e: 'request-ill', item: Item): void;
  (e: 'borrow-item', item: Item): void;
  (e: 'reserve-item', item: Item): void;
}>();

// Resolve branch ID from item properties
const getItemBranchId = (item: Item): number => {
  return Number(item.branchId ?? item.branch_id ?? 1);
};

// Resolve branch name from props.branches if missing on item
const getBranchName = (item: Item): string => {
  if (item.branchName) return item.branchName;
  if (item.branch_name) return item.branch_name;
  const branchId = getItemBranchId(item);
  const branch = props.branches?.find(b => Number(b.id) === branchId);
  return branch ? branch.name : `Sede #${branchId}`;
};

// Case-insensitive status checks
const isAvailable = (item: Item): boolean => {
  const s = String(item.status || '').toLowerCase();
  return s === 'available' || s === 'disponible';
};

const isInTransit = (item: Item): boolean => {
  const s = String(item.status || '').toLowerCase();
  return s === 'in_transit' || s.includes('tránsito') || s.includes('transit');
};

const getDisplayStatus = (status: string): string => {
  const s = String(status || '').toLowerCase();
  if (s === 'available' || s === 'disponible') return 'Disponible';
  if (s === 'in_transit' || s.includes('tránsito') || s.includes('transit')) return 'En tránsito';
  if (s === 'borrowed' || s === 'loaned' || s === 'prestado') return 'Prestado';
  if (s === 'reserved' || s === 'reservado') return 'Reservado';
  return status || 'Disponible';
};

const getStatusBadgeClass = (status: string): string => {
  const s = String(status || '').toLowerCase();
  if (s === 'available' || s === 'disponible') {
    return 'bg-folium-forest-subtle text-folium-forest border-folium-forest/30';
  }
  if (s === 'in_transit' || s.includes('tránsito') || s.includes('transit')) {
    return 'bg-folium-amber-subtle text-folium-amber border-folium-amber/30';
  }
  if (s === 'borrowed' || s === 'loaned' || s === 'prestado') {
    return 'bg-folium-crimson-subtle text-folium-crimson border-folium-crimson/30';
  }
  return 'bg-folium-terracotta-subtle text-folium-terracotta-deep border-folium-terracotta-light/40';
};

const getStatusDotClass = (status: string): string => {
  const s = String(status || '').toLowerCase();
  if (s === 'available' || s === 'disponible') return 'bg-folium-forest';
  if (s === 'in_transit' || s.includes('tránsito') || s.includes('transit')) return 'bg-folium-amber';
  if (s === 'borrowed' || s === 'loaned' || s === 'prestado') return 'bg-folium-crimson';
  return 'bg-folium-terracotta-deep';
};
</script>
