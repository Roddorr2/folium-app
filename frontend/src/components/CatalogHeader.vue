<template>
  <header class="sticky top-0 z-40 w-full bg-[#FAF7F0]/95 backdrop-blur-md border-b border-[#E8E1D1] px-2.5 sm:px-6 py-2 sm:py-3 shadow-xs font-sans">
    <div class="max-w-7xl mx-auto flex items-center justify-between gap-1.5 sm:gap-4">
      
      <!-- Zona Izquierda: Marca -->
      <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">
        <button 
          type="button" 
          @click="$emit('navigate', 'reader')"
          class="flex items-center gap-2 sm:gap-2.5 text-left focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F] rounded-xl p-0.5 transition-all"
          aria-label="Ir al Portal de Lectores de Folium"
        >
          <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-[#EBE5D8] border border-[#D8CEBC] flex items-center justify-center shadow-xs shrink-0">
            <Feather class="w-4 h-4 sm:w-5 sm:h-5 text-[#2D5A3F] stroke-[1.75]" />
          </div>
          <div class="min-w-0">
            <h1 class="font-serif font-bold text-sm sm:text-xl tracking-tight text-[#152219] leading-none">
              Folium
            </h1>
            <p class="hidden xs:block text-[8px] sm:text-[10px] tracking-[0.12em] sm:tracking-[0.2em] font-semibold text-[#66756A] uppercase mt-0.5 truncate">
              Archivo Bibliotecario
            </p>
          </div>
        </button>
      </div>

      <!-- Zona Central: Toggle de Portales (Lectores / Personal) - Visible en Desktop/Tablet -->
      <div v-if="currentUser" class="hidden md:flex items-center justify-center flex-1">
        <nav 
          aria-label="Navegación principal de portales" 
          class="inline-flex items-center justify-center gap-0.5 bg-[#EAE4D7]/80 p-0.5 rounded-lg border border-[#E2DAC8] text-xs font-medium shrink-0"
        >
          <button 
            type="button"
            @click="$emit('navigate', 'reader')"
            :aria-current="activePortal === 'reader' ? 'page' : undefined"
            :class="activePortal === 'reader' ? 'bg-[#F9F6F0] text-[#2D5A3F] font-bold shadow-xs border border-[#E2DAC8]/80' : 'text-[#5A695E] hover:text-[#1C241E]'"
            class="px-3 py-1.5 min-h-[32px] rounded-md transition-all flex items-center gap-1.5 whitespace-nowrap focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]"
            aria-label="Portal de Lectores"
          >
            <BookOpen class="w-3.5 h-3.5 text-[#2D5A3F]" />
            <span>Lectores</span>
          </button>
          <button 
            type="button"
            @click="$emit('navigate', 'staff')"
            :aria-current="activePortal === 'staff' ? 'page' : undefined"
            :class="activePortal === 'staff' ? 'bg-[#F9F6F0] text-[#2D5A3F] font-bold shadow-xs border border-[#E2DAC8]/80' : 'text-[#5A695E] hover:text-[#1C241E]'"
            class="px-3 py-1.5 min-h-[32px] rounded-md transition-all flex items-center gap-1.5 whitespace-nowrap focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]"
            aria-label="Portal de Empleados"
          >
            <UserCheck class="w-3.5 h-3.5 text-[#B87333]" />
            <span>Personal</span>
          </button>
        </nav>
      </div>

      <!-- Zona Derecha: Contexto y Sesión -->
      <div class="flex items-center gap-1 sm:gap-3 shrink-0 min-w-0">
        <!-- Selector de Sucursal (Compacto y responsivo con ancho inteligente) -->
        <div class="flex items-center bg-[#FAF7F0] rounded-lg border border-[#E2DAC8] px-1.5 sm:px-2.5 py-1 sm:py-1.5 text-xs text-[#1C241E] shadow-xs max-w-[100px] xs:max-w-[125px] sm:max-w-none shrink-0">
          <MapPin class="w-3.5 h-3.5 text-[#2D5A3F] mr-1 sm:mr-1.5 shrink-0 stroke-[1.5]" />
          <div class="flex flex-col min-w-0">
            <label for="header-branch-select" class="hidden sm:block text-[8px] uppercase tracking-wider text-[#7D8D81] font-semibold leading-tight">Sucursal</label>
            <select 
              id="header-branch-select"
              :value="currentBranchId" 
              @change="$emit('change-branch', Number($event.target.value))"
              aria-label="Sucursal habitual de preferencia"
              class="bg-transparent font-semibold text-[#1C241E] focus:outline-none cursor-pointer text-[10px] sm:text-xs truncate max-w-[65px] xs:max-w-[85px] sm:max-w-none pr-0.5"
            >
              <option v-for="branch in branches" :key="branch.id" :value="branch.id" class="bg-[#F9F6F0] text-[#1C241E]">
                {{ branch.name }}
              </option>
            </select>
          </div>
        </div>

        <!-- Auth Action: Logged In Staff Pill vs Discrete Login Link -->
        <div v-if="currentUser" class="flex items-center gap-1 sm:gap-2 shrink-0">
          <!-- Píldora de Usuario con Tratamiento Editorial Compacto (Visible en tablet/desktop) -->
          <div 
            class="hidden sm:flex items-center gap-1.5 sm:gap-2 px-2.5 sm:px-3 py-1.5 rounded-full bg-[#E9F2EB] border border-[#2D5A3F]/20 text-[#2D5A3F]"
            :title="currentUser.name + ' (' + formattedUserRole + ')'"
          >
            <UserCheck class="w-3.5 h-3.5 sm:w-4 sm:h-4 shrink-0" />
            <span class="text-xs font-semibold hidden md:inline whitespace-nowrap">
              {{ userShortLabel }}
            </span>
          </div>

          <!-- Botón Salir (Con icono y texto, nunca se desborda) -->
          <button 
            type="button"
            @click="$emit('logout')"
            class="px-2 sm:px-3 py-1 sm:py-1.5 rounded-lg sm:rounded-xl bg-[#F5EFEB] hover:bg-[#EBE4D5] text-[#8C433E] text-xs font-bold border border-[#8C433E]/20 transition-all min-h-[30px] sm:min-h-[34px] flex items-center justify-center gap-1 shrink-0 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#8C433E]"
            aria-label="Cerrar sesión de personal"
            title="Cerrar sesión de personal"
          >
            <LogOut class="w-3.5 h-3.5 shrink-0" />
            <span>Salir</span>
          </button>
        </div>

        <!-- Discrete Staff Login Link -->
        <button 
          v-else
          type="button"
          @click="$emit('open-login')"
          class="text-[#6E7B72] hover:text-[#1C241E] text-xs font-sans underline-offset-4 hover:underline transition-colors flex items-center gap-1 min-h-[32px] sm:min-h-[36px] px-1.5 sm:px-2 shrink-0 whitespace-nowrap"
          aria-label="Acceder al área de personal"
        >
          <span class="hidden sm:inline">Acceso </span><span>Personal</span>
        </button>
      </div>

    </div>

    <!-- Mobile Sub-bar: Solo si la pantalla es < md y hay usuario autenticado -->
    <div v-if="currentUser" class="flex md:hidden justify-center pt-2 pb-0.5 border-t border-[#E8E1D1]/60 mt-2">
      <nav 
        aria-label="Navegación móvil de portales" 
        class="inline-flex items-center justify-center gap-0.5 bg-[#EAE4D7]/80 p-0.5 rounded-lg border border-[#E2DAC8] text-xs font-medium w-full max-w-xs"
      >
        <button 
          type="button"
          @click="$emit('navigate', 'reader')"
          :aria-current="activePortal === 'reader' ? 'page' : undefined"
          :class="activePortal === 'reader' ? 'bg-[#F9F6F0] text-[#2D5A3F] font-bold shadow-xs border border-[#E2DAC8]/80' : 'text-[#5A695E] hover:text-[#1C241E]'"
          class="flex-1 justify-center py-1.5 min-h-[34px] rounded-md transition-all flex items-center gap-1.5 text-xs whitespace-nowrap focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]"
          aria-label="Portal de Lectores"
        >
          <BookOpen class="w-3.5 h-3.5 text-[#2D5A3F]" />
          <span>Lectores</span>
        </button>
        <button 
          type="button"
          @click="$emit('navigate', 'staff')"
          :aria-current="activePortal === 'staff' ? 'page' : undefined"
          :class="activePortal === 'staff' ? 'bg-[#F9F6F0] text-[#2D5A3F] font-bold shadow-xs border border-[#E2DAC8]/80' : 'text-[#5A695E] hover:text-[#1C241E]'"
          class="flex-1 justify-center py-1.5 min-h-[34px] rounded-md transition-all flex items-center gap-1.5 text-xs whitespace-nowrap focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]"
          aria-label="Portal de Empleados"
        >
          <UserCheck class="w-3.5 h-3.5 text-[#B87333]" />
          <span>Personal</span>
        </button>
      </nav>
    </div>
  </header>
</template>

<script setup>
import { computed } from 'vue';
import { Feather, MapPin, BookOpen, UserCheck, LogOut } from 'lucide-vue-next';

const props = defineProps({
  branches: { type: Array, required: true },
  currentBranchId: { type: Number, required: true },
  activePortal: { type: String, default: 'reader' },
  currentUser: { type: Object, default: null }
});

defineEmits(['change-branch', 'navigate', 'open-login', 'logout']);

const userShortLabel = computed(() => {
  if (!props.currentUser) return '';
  return props.currentUser.short_name || props.currentUser.name || '';
});

const formattedUserRole = computed(() => {
  if (!props.currentUser) return '';
  return props.currentUser.role?.display_name || props.currentUser.role?.name || 'Lector';
});
</script>
