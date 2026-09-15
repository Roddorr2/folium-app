<template>
  <div class="bg-folium-parchment/60 border border-folium-border rounded-xl p-5 shadow-paper-sm hover:shadow-paper-md transition-all flex flex-col justify-between space-y-4">
    <!-- Header Badge: Nature of Work & Dewey Classification -->
    <div class="flex flex-wrap items-center justify-between gap-2">
      <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-folium-ivory text-folium-ink border border-folium-border flex items-center gap-1.5 shadow-2xs">
        <Bookmark class="w-3 h-3 text-folium-forest shrink-0" />
        <span>{{ work.nature || 'Obra Literaria' }}</span>
      </span>

      <span v-if="work.dewey" class="px-2.5 py-0.5 rounded-md text-[11px] font-mono font-bold bg-folium-terracotta-subtle text-folium-terracotta-deep border border-folium-terracotta-light/60 tracking-tight shrink-0">
        CDD {{ work.dewey }}
      </span>
    </div>

    <!-- Work Info -->
    <div>
      <h3 class="text-xl font-bold font-serif text-folium-ink leading-snug">
        <button 
          type="button" 
          @click="emit('select', work)"
          class="text-left hover:text-folium-forest transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-folium-forest rounded"
          :aria-label="'Ver detalle de obra: ' + work.title"
        >
          {{ work.title }}
        </button>
      </h3>
      <p class="text-xs text-folium-sage font-medium mt-1">
        por <span class="text-folium-ink font-bold">{{ work.author }}</span>
      </p>
    </div>

    <p v-if="work.abstract" class="text-sm text-folium-moss line-clamp-3 leading-relaxed font-sans">
      {{ work.abstract }}
    </p>

    <!-- Branch Availability Grid or Zero Stock Container (RF7.4 / US6-04) -->
    <div class="pt-2 border-t border-folium-border-subtle">
      <!-- If totalAvailable > 0: Render clean breakdown of branches with stock -->
      <div v-if="totalAvailable > 0" class="space-y-2">
        <span class="text-xs font-bold text-folium-ink flex items-center gap-1.5">
          <Building2 class="w-3.5 h-3.5 text-folium-forest" />
          Disponibilidad por sede:
        </span>
        <div class="flex flex-wrap gap-1.5">
          <span 
            v-for="branch in availableBranches" 
            :key="branch.branch_id"
            class="px-2.5 py-1 rounded-lg text-xs font-medium bg-folium-ivory text-folium-ink border border-folium-border flex items-center gap-1.5"
          >
            <span class="w-1.5 h-1.5 rounded-full bg-folium-forest shrink-0"></span>
            <span>{{ branch.branch_name }}: {{ branch.available_copies }}</span>
          </span>
        </div>
      </div>

      <!-- If totalAvailable === 0: Render styled parchment container -->
      <div v-else class="bg-folium-parchment/60 border border-folium-border-subtle rounded-lg p-3.5 my-3 text-center space-y-1">
        <p class="text-xs font-semibold text-folium-terracotta-deep">
          Sin ejemplares físicos disponibles actualmente en red
        </p>
        <p class="text-[11px] text-folium-sage">
          Puedes suscribirte para recibir una notificación automática cuando un ejemplar sea devuelto.
        </p>
      </div>
    </div>

    <!-- Actions (Touch target min-h-[44px]) -->
    <div class="pt-2 flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
      <!-- If stock available: Immediate action button -->
      <button 
        v-if="totalAvailable > 0"
        type="button"
        @click="emit('select', work)"
        :aria-label="'Préstamo inmediato de ' + work.title"
        class="w-full sm:flex-1 py-3 px-4 min-h-[44px] rounded-xl bg-folium-forest hover:bg-folium-forest-hover text-folium-ivory text-xs font-bold transition-all shadow-sm flex items-center justify-center gap-2 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-folium-forest"
      >
        <BookOpen class="w-3.5 h-3.5" />
        <span>Préstamo inmediato</span>
      </button>

      <!-- If zero stock: Reservation / Availability Alert in Terracotta palette -->
      <button 
        v-else
        type="button"
        @click="toggleSubscribe"
        :aria-pressed="isSubscribed"
        :aria-label="(isSubscribed ? 'Alerta activada para ' : 'Suscribirme a alerta de disponibilidad de ') + work.title"
        :class="[
          'w-full sm:flex-1 py-3 px-4 min-h-[44px] rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 focus-visible:outline-2 focus-visible:outline-offset-2',
          isSubscribed
            ? 'bg-[#E9F2EB] text-[#2D5A3F] border border-[#2D5A3F]/30 hover:bg-[#DCEDE0] focus-visible:outline-[#2D5A3F]'
            : 'bg-folium-terracotta-subtle hover:bg-folium-terracotta-badge text-folium-terracotta-deep border border-folium-terracotta-light/60 focus-visible:outline-folium-terracotta'
        ]"
      >
        <Check v-if="isSubscribed" class="w-3.5 h-3.5 text-[#2D5A3F]" />
        <Bell v-else class="w-3.5 h-3.5 text-folium-terracotta-deep" />
        <span>{{ isSubscribed ? 'Alerta de Disponibilidad Activada' : 'Suscribirme a Alerta de Disponibilidad' }}</span>
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { BookOpen, Building2, Bell, Check, Bookmark } from 'lucide-vue-next';
import type { WorkItem } from '../types';

const props = defineProps<{
  work: WorkItem;
}>();

const emit = defineEmits<{
  (e: 'select', work: WorkItem): void;
  (e: 'subscribe-alert', payload: { work: WorkItem; subscribed: boolean }): void;
}>();

const isSubscribed = ref(false);

const toggleSubscribe = () => {
  isSubscribed.value = !isSubscribed.value;
  emit('subscribe-alert', {
    work: props.work,
    subscribed: isSubscribed.value
  });
};

const totalAvailable = computed(() => {
  return props.work.branches_availability?.reduce((acc, b) => acc + b.available_copies, 0) ?? 0;
});

const availableBranches = computed(() => {
  return (props.work.branches_availability ?? []).filter(b => b.available_copies > 0);
});
</script>
