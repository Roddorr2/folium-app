<template>
  <div class="min-h-[80vh] flex items-center justify-center p-4 sm:p-6 bg-folium-canvas">
    <div class="bg-folium-ivory rounded-2xl max-w-lg w-full p-8 border border-folium-border shadow-paper-md text-center space-y-6 animate-in fade-in zoom-in-95 duration-200">
      
      <!-- Top Terracotta Shield Icon -->
      <div class="w-16 h-16 rounded-2xl bg-folium-terracotta-subtle text-folium-terracotta-deep border border-folium-terracotta-light/40 flex items-center justify-center mx-auto shadow-2xs">
        <ShieldAlert class="w-8 h-8"/>
      </div>

      <!-- Heading -->
      <div class="space-y-2">
        <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-folium-terracotta-subtle text-folium-terracotta-deep border border-folium-terracotta-light/40 shadow-2xs">
          CÓDIGO 403 · ACCESO DENEGADO
        </span>
        <h1 class="text-3xl font-bold font-serif text-folium-ink pt-2">
          Área Restringida
        </h1>
        <p class="text-xs text-folium-sage font-medium max-w-sm mx-auto leading-relaxed">
          Tu perfil actual (<strong class="text-folium-ink">{{ humanRole }}</strong>) no cuenta con los permisos necesarios para realizar operaciones en esta sección.
        </p>
      </div>

      <!-- Explanation Box -->
      <div class="p-4 rounded-xl bg-folium-parchment border border-folium-border text-left text-xs text-folium-moss space-y-1">
        <p class="font-bold text-folium-ink flex items-center gap-1.5">
          <Lock class="w-3.5 h-3.5 text-folium-terracotta"/>
          Políticas de Seguridad Bibliotecaria
        </p>
        <p class="text-[11px] text-folium-sage leading-relaxed">
          Las funciones de catalogación, circulación y gestión de transferencias entre sedes están reservadas al personal autorizado de la red.
        </p>
      </div>

      <!-- Action Button -->
      <div class="pt-2">
        <router-link 
          to="/" 
          class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-folium-forest hover:bg-folium-forest-hover text-folium-ivory text-xs font-bold transition-all shadow-sm"
        >
          <ArrowLeft class="w-4 h-4"/>
          <span>Volver al Catálogo Principal</span>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { ShieldAlert, Lock, ArrowLeft } from 'lucide-vue-next';
import { useAuth } from '../composables/useAuth';

const { userRole } = useAuth();

// Mapeo editorial de roles técnicos a nombres legibles
const ROLE_NAMES: Record<string, string> = {
  admin: 'Administrador de Plataforma',
  cataloger: 'Catalogador',
  librarian: 'Bibliotecario de Sede',
  network_librarian: 'Bibliotecario de Red',
  reader: 'Lector / Usuario OPAC',
};

const humanRole = computed(() => {
  if (!userRole.value) return 'Visitante no autenticado';
  return ROLE_NAMES[userRole.value] || userRole.value;
});
</script>
