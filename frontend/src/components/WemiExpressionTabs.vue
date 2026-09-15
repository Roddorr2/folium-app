<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-2">
        <BookOpen class="w-4 h-4 text-folium-forest" />
        <h4 class="text-xs font-bold uppercase tracking-wider text-folium-moss font-sans">
          Ediciones Registradas
        </h4>
      </div>
      <span class="text-xs text-folium-sage font-sans font-medium">
        {{ expressions.length }} {{ expressions.length === 1 ? 'edición' : 'ediciones' }}
      </span>
    </div>

    <!-- Edition Tabs Header -->
    <div class="flex flex-wrap border-b border-folium-border gap-1">
      <button 
        v-for="(expr, idx) in expressions" 
        :key="expr.id || idx"
        @click="emit('select-expression', idx)"
        :class="[
          'px-4 py-2.5 rounded-t-lg text-xs transition-all flex items-center gap-2 font-medium',
          activeIdx === idx 
            ? 'bg-folium-ivory text-folium-forest border-x border-t border-folium-border border-t-2 border-t-folium-forest font-bold shadow-xs' 
            : 'bg-folium-canvas text-folium-sage border border-folium-border-subtle hover:bg-folium-ivory hover:text-folium-ink'
        ]"
      >
        <FileText class="w-3.5 h-3.5" :class="activeIdx === idx ? 'text-folium-forest' : 'text-folium-sage'" />
        <span class="max-w-[220px] truncate">{{ expr.title || `Edición ${idx + 1}` }}</span>
        <span v-if="expr.language" class="px-2 py-0.5 rounded text-[10px] font-mono bg-folium-terracotta-subtle text-folium-terracotta-deep font-semibold border border-folium-terracotta-light/40">
          {{ expr.language }}
        </span>
      </button>
    </div>

    <!-- Active Edition Details Card (Marfil Puro con Borde Definido) -->
    <div v-if="activeExpression" class="bg-folium-ivory p-4 rounded-b-xl border border-t-0 border-folium-border space-y-2 shadow-2xs">
      <div class="flex flex-wrap items-center justify-between text-xs text-folium-sage gap-2">
        <span v-if="activeExpression.type" class="font-medium text-folium-ink">
          Tipo: <span class="text-folium-forest font-semibold">{{ activeExpression.type }}</span>
        </span>
        <span v-if="activeExpression.revisionYear" class="font-mono text-folium-sage">
          Año de revisión: {{ activeExpression.revisionYear }}
        </span>
      </div>
      <p v-if="activeExpression.description" class="text-xs text-folium-moss leading-relaxed">
        {{ activeExpression.description }}
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { BookOpen, FileText } from 'lucide-vue-next';
import type { Expression } from '../modules/catalog/types';

const props = defineProps<{
  expressions: Expression[];
  activeIdx?: number;
}>();

const emit = defineEmits<{
  (e: 'select-expression', idx: number): void;
}>();

const activeExpression = computed(() => {
  return props.expressions[props.activeIdx ?? 0] || props.expressions[0];
});
</script>
