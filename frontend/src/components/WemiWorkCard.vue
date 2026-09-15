<template>
  <div class="bg-[#F3EFE6] rounded-xl p-5 sm:p-6 flex flex-col justify-between space-y-5 border border-[#E2DAC8] hover:border-[#D8CEBC] hover:shadow-paper-md transition-all duration-200 relative group shadow-paper-sm">
    <!-- Top Bar: Nature of Work & Classification Code -->
    <div class="flex flex-wrap items-center justify-between gap-2.5">
      <!-- Nature / Type of Work Badge -->
      <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-[#F9F6F0] text-[#152219] border border-[#E2DAC8] flex items-center gap-1.5 shadow-2xs">
        <Bookmark class="w-3 h-3 text-[#2D5A3F] shrink-0" />
        <span>{{ work.nature || 'Obra Literaria' }}</span>
      </span>

      <!-- Classification Code / Catalog Reference (Terracota Cuero) -->
      <span class="px-2.5 py-0.5 rounded-md text-[11px] font-mono font-bold bg-[#F2E4DE] text-[#7D3B28] border border-[#C47055]/40 tracking-tight shrink-0 shadow-2xs">
        {{ work.dewey ? `CDD ${work.dewey}` : `CDD #${work.id}` }}
      </span>
    </div>

    <!-- Title & Author Attribution -->
    <div class="space-y-1.5">
      <h3 class="text-xl font-bold font-serif text-[#152219] leading-snug">
        <button 
          type="button" 
          @click="$emit('select', work)"
          class="text-left hover:text-[#2D5A3F] transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F] rounded"
          :aria-label="'Ver detalle de obra: ' + work.title"
        >
          {{ work.title }}
        </button>
      </h3>
      <p class="text-xs text-[#66756A] font-medium">
        por <span class="text-[#152219] font-bold">{{ work.author }}</span>
        <span v-if="work.yearRange" class="ml-1 text-[#66756A]">({{ work.yearRange }})</span>
      </p>
    </div>

    <!-- Synopsis -->
    <p class="text-sm text-[#3C4A40] line-clamp-3 leading-relaxed font-sans font-normal">
      {{ work.abstract }}
    </p>

    <!-- Subject Tags (Acento Terracota / Cuero Envejecido Visible) -->
    <div v-if="work.subjects && work.subjects.length > 0" class="flex flex-wrap gap-1.5 pt-1">
      <span 
        v-for="subject in work.subjects" 
        :key="subject"
        class="px-2.5 py-1 rounded-md text-[11px] bg-[#F2E4DE] text-[#7D3B28] border border-[#C47055]/40 font-semibold tracking-wide shadow-2xs"
      >
        {{ subject }}
      </span>
    </div>

    <!-- Multi-Branch Inventory / Availability Breakdown -->
    <div class="pt-3 border-t border-[#E2DAC8] space-y-2">
      <!-- Positive Availability State -->
      <template v-if="availableTotal > 0">
        <div class="flex items-center justify-between text-xs mb-1.5">
          <span class="font-bold text-[#152219] flex items-center gap-1.5">
            <Building2 class="w-3.5 h-3.5 text-[#2D5A3F]" />
            Sedes con stock disponible:
          </span>
        </div>

        <div class="flex flex-wrap gap-1.5">
          <span 
            v-for="branch in availableBranches" 
            :key="branch.id"
            :class="[
              'px-2.5 py-1 rounded-lg text-xs font-medium flex items-center gap-1.5 border transition-all',
              branch.id === currentBranchId 
                ? 'bg-[#E9F2EB] text-[#2D5A3F] border-[#2D5A3F]/40 font-bold' 
                : 'bg-[#F9F6F0] text-[#334237] border-[#E2DAC8]'
            ]"
          >
            <span class="w-1.5 h-1.5 rounded-full bg-[#2D5A3F] shrink-0"></span>
            <span>{{ branch.shortName }} ({{ branch.count }})</span>
          </span>
        </div>
      </template>

      <!-- Zero Stock Empty State (Terracota Warm Parchment Container RF7.4 / US6-04) -->
      <template v-else>
        <div class="bg-folium-parchment/60 border border-folium-border-subtle rounded-lg p-3.5 my-3 text-center space-y-1">
          <p class="text-xs font-semibold text-folium-terracotta-deep flex items-center justify-center gap-1.5">
            <PackageX class="w-4 h-4 text-folium-terracotta shrink-0" />
            Sin ejemplares físicos disponibles actualmente en red
          </p>
          <p class="text-[11px] text-folium-sage">
            Puedes solicitar una reserva o suscribirte a alertas de disponibilidad.
          </p>
        </div>
      </template>
    </div>

    <!-- Action Bar (44px Minimum Touch Targets) -->
    <div class="pt-2 flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
      <button 
        v-if="availableTotal > 0"
        type="button"
        @click="$emit('select', work)"
        :aria-label="'Préstamo inmediato de ' + work.title"
        class="w-full sm:flex-1 py-3 px-4 min-h-[44px] rounded-xl bg-folium-forest hover:bg-folium-forest-hover text-folium-ivory text-xs font-bold tracking-wide transition-all shadow-sm flex items-center justify-center gap-2 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]"
      >
        <BookOpen class="w-3.5 h-3.5" />
        <span>Préstamo inmediato</span>
      </button>

      <button 
        v-else
        type="button"
        @click="toggleNotify"
        :aria-pressed="isSubscribed"
        :aria-label="(isSubscribed ? 'Alerta activada para ' : 'Suscribirme a alerta de disponibilidad de ') + work.title"
        :class="[
          'w-full sm:flex-1 py-3 px-4 min-h-[44px] rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 focus-visible:outline-2 focus-visible:outline-offset-2',
          isSubscribed
            ? 'bg-[#E9F2EB] text-[#2D5A3F] border border-[#2D5A3F]/30 hover:bg-[#DCEDE0] focus-visible:outline-[#2D5A3F]'
            : 'bg-[#F9EDED] hover:bg-[#F2DFDF] text-[#8C433E] border border-[#8C433E]/30 focus-visible:outline-[#8C433E]'
        ]"
      >
        <Check v-if="isSubscribed" class="w-3.5 h-3.5 text-[#2D5A3F]" />
        <Bell v-else class="w-3.5 h-3.5 text-[#8C433E]" />
        <span>{{ isSubscribed ? 'Alerta de Disponibilidad Activada' : 'Suscribirme a Alerta de Disponibilidad' }}</span>
      </button>

      <button 
        type="button"
        @click="$emit('request-ill', work)"
        :disabled="availableTotal === 0"
        :aria-label="'Solicitar préstamo intersede de ' + work.title"
        :class="[
          'py-3 px-3 min-h-[44px] rounded-xl text-xs font-semibold transition-all flex items-center justify-center gap-1.5 border focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#7D3B28]',
          availableTotal > 0 
            ? 'bg-[#F2E4DE] hover:bg-[#EAD5CD] text-[#7D3B28] border-[#C47055]/40' 
            : 'bg-[#EBE5D8] text-[#7D8D81] border-[#E2DAC8] cursor-not-allowed opacity-50'
        ]"
        title="Solicitar préstamo entre sedes"
      >
        <Truck class="w-3.5 h-3.5" />
        <span>Préstamo intersede</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { BookOpen, Building2, Truck, PackageX, Bell, Check, Bookmark } from 'lucide-vue-next';

const props = defineProps({
  work: { type: Object, required: true },
  branches: { type: Array, required: true },
  currentBranchId: { type: Number, default: 1 }
});

const emit = defineEmits(['select', 'request-ill', 'notify-availability']);

const isSubscribed = ref(false);

const toggleNotify = () => {
  isSubscribed.value = !isSubscribed.value;
  emit('notify-availability', {
    work: props.work,
    subscribed: isSubscribed.value
  });
};

// Flatten all items across expressions and manifestations
const allItems = computed(() => {
  const items = [];
  props.work.expressions?.forEach(expr => {
    expr.manifestations?.forEach(manif => {
      manif.items?.forEach(item => {
        items.push(item);
      });
    });
  });
  return items;
});

// Calculate total available physical copies
const availableTotal = computed(() => {
  return allItems.value.filter(i => i.status === 'Disponible').length;
});

// Compute only branches that have available physical stock (count > 0)
const availableBranches = computed(() => {
  return props.branches
    .map(branch => {
      const branchItems = allItems.value.filter(i => i.branchId === branch.id);
      const count = branchItems.filter(i => i.status === 'Disponible').length;
      const shortName = branch.name.replace(/^Sede\s+/i, '');
      return {
        id: branch.id,
        name: branch.name,
        shortName,
        count
      };
    })
    .filter(b => b.count > 0);
});
</script>
