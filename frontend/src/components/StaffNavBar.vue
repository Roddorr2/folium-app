<template>
  <nav aria-label="Navegación de módulos de personal" class="paper-card p-2 sm:p-3 rounded-2xl border border-[#E3DAC9] flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 shadow-xs">
    <!-- Left Module Selector / Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto max-w-full py-1 w-full md:w-auto flex-1 scrollbar-none" role="tablist" aria-label="Módulos del portal de empleados">
      <!-- Operational Tools -->
      <button 
        type="button"
        role="tab"
        :aria-selected="activeModule === 'cataloging'"
        @click="$emit('select-module', 'cataloging')"
        :class="[activeModule === 'cataloging' ? 'bg-[#2D5A3F] text-[#F9F6F0] font-bold shadow-xs' : 'bg-[#EBE5D8] text-[#4A584E] hover:text-[#1C241E] hover:bg-[#E3DAC9]']"
        class="px-3.5 py-2.5 min-h-[44px] rounded-xl text-xs transition-all flex items-center gap-1.5 shrink-0 whitespace-nowrap focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]"
        aria-label="Módulo de Catalogación Bibliográfica"
      >
        <BookOpen class="w-4 h-4" />
        <span>Catalogación Bibliográfica</span>
      </button>

      <button 
        type="button"
        role="tab"
        :aria-selected="activeModule === 'circulation'"
        @click="$emit('select-module', 'circulation')"
        :class="[activeModule === 'circulation' ? 'bg-[#2D5A3F] text-[#F9F6F0] font-bold shadow-xs' : 'bg-[#EBE5D8] text-[#4A584E] hover:text-[#1C241E] hover:bg-[#E3DAC9]']"
        class="px-3.5 py-2.5 min-h-[44px] rounded-xl text-xs transition-all flex items-center gap-1.5 shrink-0 whitespace-nowrap focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]"
        aria-label="Módulo de Circulación y Préstamos"
      >
        <ArrowRightLeft class="w-4 h-4" />
        <span>Circulación & Préstamos</span>
      </button>

      <button 
        type="button"
        role="tab"
        :aria-selected="activeModule === 'transfers'"
        @click="$emit('select-module', 'transfers')"
        :class="[activeModule === 'transfers' ? 'bg-[#2D5A3F] text-[#F9F6F0] font-bold shadow-xs' : 'bg-[#EBE5D8] text-[#4A584E] hover:text-[#1C241E] hover:bg-[#E3DAC9]']"
        class="px-3.5 py-2.5 min-h-[44px] rounded-xl text-xs transition-all flex items-center gap-1.5 shrink-0 whitespace-nowrap focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]"
        aria-label="Módulo de Transferencias Interbibliotecarias ILL"
      >
        <Truck class="w-4 h-4" />
        <span>Transferencias ILL</span>
      </button>

      <!-- Admin-Only Infrastructure Tools (v-if="userRole === 'admin'") -->
      <button
        v-if="userRole === 'admin'"
        type="button"
        role="tab"
        :aria-selected="activeModule === 'branches'"
        @click="$emit('select-module', 'branches')"
        :class="[activeModule === 'branches' ? 'bg-[#2D5A3F] text-[#F9F6F0] font-bold shadow-xs' : 'bg-[#F3EFE6] text-[#2D5A3F] hover:bg-[#EBE4D5] border border-[#2D5A3F]/20']"
        class="px-3.5 py-2.5 min-h-[44px] rounded-xl text-xs font-semibold transition-all flex items-center gap-1.5 shrink-0 whitespace-nowrap focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]"
        aria-label="Gestión de Red de Sedes (Administrador)"
      >
        <Building2 class="w-4 h-4" />
        <span>Red de Sedes</span>
      </button>

      <button
        v-if="userRole === 'admin'"
        type="button"
        role="tab"
        :aria-selected="activeModule === 'users'"
        @click="$emit('select-module', 'users')"
        :class="[activeModule === 'users' ? 'bg-[#2D5A3F] text-[#F9F6F0] font-bold shadow-xs' : 'bg-[#F3EFE6] text-[#2D5A3F] hover:bg-[#EBE4D5] border border-[#2D5A3F]/20']"
        class="px-3.5 py-2.5 min-h-[44px] rounded-xl text-xs font-semibold transition-all flex items-center gap-1.5 shrink-0 whitespace-nowrap focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]"
        aria-label="Gestión de Usuarios y Roles (Administrador)"
      >
        <Users class="w-4 h-4" />
        <span>Gestión de Usuarios</span>
      </button>
    </div>

    <!-- Role Indicator Chip -->
    <div v-if="currentUser" class="flex items-center justify-end gap-2 shrink-0 pt-2 md:pt-0 border-t md:border-t-0 border-[#E3DAC9]" aria-label="Ficha de usuario autenticado">
      <div class="flex items-center gap-2 bg-[#E9F2EB] px-3 py-1.5 rounded-xl border border-[#2D5A3F]/20 text-xs text-[#2D5A3F] font-medium">
        <div class="w-6 h-6 rounded-full bg-[#2D5A3F] text-[#F9F6F0] flex items-center justify-center font-bold text-[11px] uppercase shrink-0" aria-hidden="true">
          {{ userInitials }}
        </div>
        <div class="flex flex-col">
          <span class="font-bold leading-tight text-[#1C241E] truncate max-w-[110px] text-xs">{{ currentUser.name }}</span>
          <span class="text-[9px] uppercase font-semibold tracking-wider text-[#2D5A3F]">{{ userRoleShortLabel }}</span>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { BookOpen, ArrowRightLeft, Truck, Building2, Users } from 'lucide-vue-next';
import { useAuth } from '../composables/useAuth';

defineProps({
  activeModule: { type: String, default: 'cataloging' }
});

defineEmits(['select-module']);

const { currentUser, userRole, userRoleShortLabel, userInitials } = useAuth();
</script>
