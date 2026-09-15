<template>
  <div class="bg-folium-ivory rounded-xl p-5 border border-folium-border space-y-4 shadow-paper-sm">
    <!-- Top Header: Format & ISBN Monospace Badge -->
    <div class="flex flex-wrap items-center justify-between gap-2 pb-3 border-b border-folium-border-subtle">
      <div class="flex items-center gap-2.5">
        <span class="p-2 rounded-lg bg-folium-terracotta-subtle text-folium-terracotta-deep border border-folium-terracotta-light/40">
          <Book class="w-4 h-4 text-folium-terracotta-deep" />
        </span>
        <div>
          <h5 class="text-sm font-bold text-folium-ink font-serif">
            {{ manifestation.format || 'Formato impreso' }}
          </h5>
          <p v-if="publisherInfo" class="text-[11px] text-folium-sage font-medium">
            {{ publisherInfo }}
          </p>
        </div>
      </div>

      <div v-if="manifestation.isbn" class="flex items-center gap-2">
        <span class="text-[10px] font-mono text-folium-sage">ISBN:</span>
        <span class="px-2.5 py-1 rounded bg-folium-parchment font-mono text-xs font-semibold text-folium-ink border border-folium-border shadow-2xs">
          {{ manifestation.isbn }}
        </span>
      </div>
    </div>

    <!-- Technical Metadata Row (Only rendered if fields exist) -->
    <div v-if="manifestation.dimensions || manifestation.notes" class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-folium-sage">
      <div v-if="manifestation.dimensions" class="flex items-center gap-2">
        <Ruler class="w-3.5 h-3.5 text-folium-sage shrink-0" />
        <span>Dimensiones: <strong class="text-folium-ink font-medium">{{ manifestation.dimensions }}</strong></span>
      </div>
      <div v-if="manifestation.notes" class="flex items-center gap-2">
        <Info class="w-3.5 h-3.5 text-folium-sage shrink-0" />
        <span class="truncate">Detalles: <strong class="text-folium-ink font-medium">{{ manifestation.notes }}</strong></span>
      </div>
    </div>

    <!-- Embedded Branch Inventory Section -->
    <div class="pt-2">
      <BranchItemInventory 
        :items="manifestation.items || []"
        :branches="branches"
        :current-branch-id="currentBranchId"
        @request-ill="(item) => emit('request-ill', { manifestation, item })"
        @borrow-item="(item) => emit('borrow-item', { manifestation, item })"
        @reserve-item="(item) => emit('reserve-item', { manifestation, item })"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Book, Ruler, Info } from 'lucide-vue-next';
import BranchItemInventory from './BranchItemInventory.vue';
import type { Manifestation, Branch, Item } from '../modules/catalog/types';

const props = defineProps<{
  manifestation: Manifestation;
  branches: Branch[];
  currentBranchId?: number;
}>();

const emit = defineEmits<{
  (e: 'request-ill', payload: { manifestation: Manifestation; item: Item }): void;
  (e: 'borrow-item', payload: { manifestation: Manifestation; item: Item }): void;
  (e: 'reserve-item', payload: { manifestation: Manifestation; item: Item }): void;
}>();

const publisherInfo = computed(() => {
  const parts = [];
  if (props.manifestation.publisher) parts.push(props.manifestation.publisher);
  if (props.manifestation.publicationYear) parts.push(String(props.manifestation.publicationYear));
  return parts.join(' · ');
});
</script>
