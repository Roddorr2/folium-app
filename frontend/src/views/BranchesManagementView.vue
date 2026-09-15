<template>
  <div class="space-y-6">
    <!-- Header with Back Button and Main Action -->
    <div class="paper-card p-4 sm:p-6 rounded-2xl border border-[#E3DAC9] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div class="space-y-1">
        <div class="flex items-center gap-2">
          <router-link to="/staff" class="text-xs text-[#2D5A3F] font-semibold hover:underline flex items-center gap-1 min-h-[36px]">
            &larr; Volver a Gestión
          </router-link>
          <span class="text-xs text-[#7D8D81]">•</span>
          <span class="px-2.5 py-0.5 rounded-full bg-[#E9F2EB] text-[#2D5A3F] text-[11px] font-semibold border border-[#2D5A3F]/20">Infraestructura Multi-Sede</span>
        </div>
        <h2 class="text-2xl font-bold font-serif text-[#1C241E] tracking-tight">Red de Sedes Bibliotecarias</h2>
        <p class="text-xs text-[#4A584E]">Administración de sucursales físicas, custodia patrimonial e inventario físico.</p>
      </div>

      <button 
        type="button"
        @click="openCreateModal" 
        class="w-full sm:w-auto px-4 py-3 min-h-[44px] rounded-xl bg-[#2D5A3F] hover:bg-[#224430] text-[#F9F6F0] font-bold text-xs shadow-sm flex items-center justify-center gap-2 transition-all"
        aria-label="Registrar nueva sede bibliotecaria"
      >
        <Plus class="w-4 h-4" />
        <span>+ Registrar Nueva Sede</span>
      </button>
    </div>

    <!-- Desktop View: Table (hidden md:block) -->
    <div class="hidden md:block paper-card rounded-2xl overflow-hidden border border-[#E3DAC9] shadow-sm">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-[#E3DAC9] bg-[#EBE5D8]/70 text-xs font-semibold text-[#4A584E] uppercase tracking-wider">
            <th class="p-4">ID / Código</th>
            <th class="p-4">Nombre de la Sede</th>
            <th class="p-4">Ubicación & Dirección</th>
            <th class="p-4">Ejemplares Custodiados</th>
            <th class="p-4 text-center">Estado</th>
            <th class="p-4 text-right">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#E3DAC9] text-xs text-[#1C241E]">
          <!-- Skeleton Loading Rows -->
          <template v-if="isLoadingBranches">
            <tr v-for="n in 4" :key="'branch-skeleton-' + n" class="animate-pulse">
              <td class="p-4">
                <div class="h-4 w-16 bg-[#E8E2D5] rounded"></div>
              </td>
              <td class="p-4">
                <div class="h-4 w-44 bg-[#E8E2D5] rounded mb-1.5"></div>
                <div class="h-3 w-24 bg-[#EDE7DC] rounded"></div>
              </td>
              <td class="p-4">
                <div class="h-4 w-32 bg-[#E8E2D5] rounded mb-1.5"></div>
                <div class="h-3 w-48 bg-[#EDE7DC] rounded"></div>
              </td>
              <td class="p-4">
                <div class="h-6 w-24 bg-[#E8E2D5] rounded-full"></div>
              </td>
              <td class="p-4 text-center">
                <div class="h-5 w-16 bg-[#E8E2D5] rounded-full mx-auto"></div>
              </td>
              <td class="p-4 text-right">
                <div class="w-9 h-9 bg-[#E8E2D5] rounded-xl ml-auto"></div>
              </td>
            </tr>
          </template>

          <!-- Actual Rows -->
          <template v-else-if="branches.length > 0">
            <tr v-for="branch in branches" :key="branch.id" class="hover:bg-[#F9F6F0] transition-colors">
              <td class="p-4 font-mono font-bold text-[#2D5A3F]">FOL-{{ branch.id }}</td>
              <td class="p-4">
                <span class="font-bold font-serif text-sm block text-[#1C241E]">{{ branch.name }}</span>
                <span v-if="branch.phone" class="text-[11px] text-[#6E7B72] font-sans">Tel: {{ branch.phone }}</span>
              </td>
              <td class="p-4 text-[#4A584E]">
                <span class="font-medium text-[#1C241E] block">{{ branch.city || 'Sede Central' }}</span>
                <span class="text-[11px] text-[#6E7B72]">{{ branch.address || 'Sin dirección registrada' }}</span>
              </td>
              <td class="p-4">
                <span class="px-3 py-1 rounded-full text-xs font-semibold badge-laurel">
                  {{ branch.items_count ?? branch.itemsCount ?? 0 }} ejemplares
                </span>
              </td>
              <td class="p-4 text-center">
                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#E9F2EB] text-[#2D5A3F] border border-[#2D5A3F]/20">
                  Activa
                </span>
              </td>
              <td class="p-4 text-right space-x-2">
                <button 
                  type="button"
                  @click="openEditModal(branch)"
                  class="p-2.5 min-w-[44px] min-h-[44px] rounded-xl bg-[#EBE5D8] hover:bg-[#2D5A3F] text-[#1C241E] hover:text-[#F9F6F0] transition-colors border border-[#E3DAC9] inline-flex items-center justify-center"
                  title="Editar datos de sede"
                  :aria-label="'Editar sede ' + branch.name"
                >
                  <Pencil class="w-4 h-4" />
                </button>
              </td>
            </tr>
          </template>

          <!-- Empty State Desktop -->
          <tr v-else>
            <td colspan="6" class="p-12 text-center text-[#7D8D81]">
              <Building2 class="w-10 h-10 mx-auto text-[#7D8D81]/60 stroke-[1.5] mb-2" />
              <p class="font-serif font-bold text-sm text-[#1C241E]">No se encontraron sedes bibliotecarias</p>
              <p class="text-xs text-[#4A584E] mt-1">Registra la primera sede física de la red.</p>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Mobile-First Table-to-Card Stacked List (block md:hidden) -->
    <div class="block md:hidden space-y-3">
      <!-- Skeleton Loading Cards -->
      <template v-if="isLoadingBranches">
        <div 
          v-for="n in 3" 
          :key="'branch-mobile-skeleton-' + n"
          class="paper-card p-4 rounded-2xl border border-[#E3DAC9] space-y-3 shadow-paper-sm animate-pulse"
        >
          <div class="flex items-center justify-between">
            <div class="h-5 w-16 bg-[#E8E2D5] rounded-md"></div>
            <div class="h-5 w-16 bg-[#E8E2D5] rounded-full"></div>
          </div>
          <div class="space-y-1.5">
            <div class="h-5 w-3/4 bg-[#E8E2D5] rounded"></div>
            <div class="h-3 w-1/2 bg-[#EDE7DC] rounded"></div>
          </div>
          <div class="pt-2 border-t border-[#E3DAC9] flex items-center justify-between">
            <div class="h-4 w-28 bg-[#EDE7DC] rounded"></div>
            <div class="h-6 w-20 bg-[#E8E2D5] rounded"></div>
          </div>
        </div>
      </template>

      <!-- Actual Mobile Cards -->
      <template v-else-if="branches.length > 0">
        <div 
          v-for="branch in branches" 
          :key="'mobile-' + branch.id"
          class="paper-card p-4 rounded-2xl border border-[#E3DAC9] space-y-3 shadow-paper-sm"
        >
          <div class="flex items-center justify-between gap-2 border-b border-[#E3DAC9]/80 pb-2">
            <span class="font-mono text-xs font-bold text-[#2D5A3F] bg-[#E9F2EB] px-2.5 py-0.5 rounded-md border border-[#2D5A3F]/20">
              FOL-{{ branch.id }}
            </span>
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#E9F2EB] text-[#2D5A3F] border border-[#2D5A3F]/20">
              Activa
            </span>
          </div>

          <div>
            <h4 class="font-serif font-bold text-base text-[#1C241E] leading-snug">
              {{ branch.name }}
            </h4>
            <p class="text-xs text-[#4A584E] mt-1 flex items-center gap-1">
              <MapPin class="w-3.5 h-3.5 text-[#2D5A3F] shrink-0" />
              <span>{{ branch.city || 'Lima' }} — {{ branch.address || 'Dirección no especificada' }}</span>
            </p>
          </div>

          <div class="flex items-center justify-between text-xs pt-2 border-t border-[#E3DAC9]">
            <span class="text-[#6E7B72] font-medium">Ejemplares custodiados:</span>
            <span class="font-bold text-[#2D5A3F] bg-[#EBE5D8] px-2.5 py-1 rounded-lg">
              {{ branch.items_count ?? branch.itemsCount ?? 0 }} vols.
            </span>
          </div>

          <div class="pt-2">
            <button 
              type="button"
              @click="openEditModal(branch)"
              class="w-full py-2.5 min-h-[44px] rounded-xl bg-[#EBE5D8] hover:bg-[#2D5A3F] text-[#1C241E] hover:text-[#F9F6F0] font-semibold text-xs transition-colors border border-[#E3DAC9] flex items-center justify-center gap-2"
            >
              <Pencil class="w-4 h-4" />
              <span>Editar Metadatos de Sede</span>
            </button>
          </div>
        </div>
      </template>

      <!-- Empty State Mobile -->
      <div v-else class="paper-card p-8 rounded-2xl border border-[#E3DAC9] text-center text-[#7D8D81]">
        <Building2 class="w-10 h-10 mx-auto text-[#7D8D81]/60 stroke-[1.5] mb-2" />
        <p class="font-serif font-bold text-sm text-[#1C241E]">No se encontraron sedes bibliotecarias</p>
        <p class="text-xs text-[#4A584E] mt-1">Registra la primera sede física de la red.</p>
      </div>
    </div>

    <!-- Modal Form (Create / Edit Branch) -->
    <Teleport to="body">
      <div 
        v-if="isModalOpen" 
        ref="modalRef"
        class="fixed inset-0 z-50 flex items-end md:items-center justify-center p-0 sm:p-4 md:p-6 overflow-hidden bg-[#141A16]/70 backdrop-blur-md"
        role="dialog"
        aria-modal="true"
        aria-labelledby="branch-modal-title"
        tabindex="-1"
        @click.self="closeModal"
      >
        <div class="paper-card w-full max-w-lg rounded-t-3xl md:rounded-2xl rounded-b-none md:rounded-b-2xl p-6 sm:p-8 border-t md:border border-[#E3DAC9] shadow-2xl relative space-y-5 max-h-[90dvh] md:max-h-[85vh] flex flex-col overflow-y-auto">
          <!-- Mobile Drag Handle -->
          <div class="w-12 h-1.5 bg-[#C5BBAA] rounded-full mx-auto mb-1 md:hidden shrink-0"></div>

          <!-- Header -->
          <div class="flex items-center justify-between border-b border-[#E3DAC9] pb-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-[#2D5A3F] text-[#F9F6F0] flex items-center justify-center font-bold text-lg border border-[#224430] shrink-0">
                <Building2 class="w-5 h-5" />
              </div>
              <h3 id="branch-modal-title" class="text-xl font-bold font-serif text-[#1C241E]">
                {{ editingBranch ? 'Editar Sede Bibliotecaria' : 'Registrar Nueva Sede' }}
              </h3>
            </div>
            <button 
              type="button" 
              @click="closeModal" 
              class="p-2 text-[#7D8D81] hover:text-[#1C241E] rounded-xl min-w-[44px] min-h-[44px] flex items-center justify-center focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]"
              aria-label="Cerrar modal de gestión de sede"
            >
              <X class="w-5 h-5" />
            </button>
          </div>

          <!-- Form -->
          <form novalidate @submit.prevent="saveBranch" class="space-y-4 font-sans">
            <div class="space-y-1.5">
              <label for="branch-name" class="block text-xs font-semibold text-[#1C241E]">Nombre de la Sede *</label>
              <input 
                id="branch-name"
                v-model="form.name"
                required
                type="text" 
                placeholder="Ej. Sede Lima Central — Biblioteca Nacional"
                class="w-full px-4 py-3 rounded-xl bg-[#FBF9F4] border border-[#E2DCCF] text-[#1C241E] text-base md:text-sm focus:ring-2 focus:ring-[#2D5A3F]/20 focus:border-[#2D5A3F] outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]"
              />
            </div>

            <div class="space-y-1.5">
              <label for="branch-city" class="block text-xs font-semibold text-[#1C241E]">Ciudad / Ubicación</label>
              <input 
                id="branch-city"
                v-model="form.city"
                type="text" 
                placeholder="Ej. Lima, Miraflores, Arequipa"
                class="w-full px-4 py-3 rounded-xl bg-[#FBF9F4] border border-[#E2DCCF] text-[#1C241E] text-base md:text-sm focus:ring-2 focus:ring-[#2D5A3F]/20 focus:border-[#2D5A3F] outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]"
              />
            </div>

            <div class="space-y-1.5">
              <label for="branch-address" class="block text-xs font-semibold text-[#1C241E]">Dirección Física</label>
              <input 
                id="branch-address"
                v-model="form.address"
                type="text" 
                placeholder="Ej. Av. Abancay cdra. 4 s/n"
                class="w-full px-4 py-3 rounded-xl bg-[#FBF9F4] border border-[#E2DCCF] text-[#1C241E] text-base md:text-sm focus:ring-2 focus:ring-[#2D5A3F]/20 focus:border-[#2D5A3F] outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]"
              />
            </div>

            <div class="space-y-1.5">
              <label for="branch-phone" class="block text-xs font-semibold text-[#1C241E]">Teléfono de Contacto</label>
              <input 
                id="branch-phone"
                v-model="form.phone"
                type="text" 
                placeholder="Ej. (01) 514-6000"
                class="w-full px-4 py-3 rounded-xl bg-[#FBF9F4] border border-[#E2DCCF] text-[#1C241E] text-base md:text-sm focus:ring-2 focus:ring-[#2D5A3F]/20 focus:border-[#2D5A3F] outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]"
              />
            </div>

            <div class="pt-4 border-t border-[#E3DAC9] flex items-center justify-end gap-3 sticky bottom-0 bg-[#FAF7F0] p-2">
              <button 
                type="button" 
                @click="closeModal" 
                class="px-4 py-3 min-h-[44px] rounded-xl bg-[#EBE5D8] hover:bg-[#E3DAC9] text-[#1C241E] font-semibold text-xs transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]"
              >
                Cancelar
              </button>
              <button 
                type="submit" 
                :disabled="isSubmitting"
                class="px-5 py-3 min-h-[48px] rounded-xl bg-[#2D5A3F] hover:bg-[#224430] text-[#F9F6F0] font-bold text-xs shadow-md transition-all flex items-center justify-center gap-2 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]"
              >
                <span v-if="isSubmitting" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                <span>{{ editingBranch ? 'Guardar Cambios' : 'Registrar Sede' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Plus, Pencil, Building2, MapPin, X } from 'lucide-vue-next';
import { apiService } from '../services/api';
import { useFocusTrap } from '../composables/useFocusTrap';

const branches = ref([]);
const isModalOpen = ref(false);
const editingBranch = ref(null);
const isSubmitting = ref(false);
const isLoadingBranches = ref(true);

const modalRef = ref(null);
useFocusTrap(modalRef, computed(() => isModalOpen.value), () => closeModal());

const form = ref({
  name: '',
  city: '',
  address: '',
  phone: ''
});

const loadBranches = async () => {
  isLoadingBranches.value = true;
  try {
    const res = await apiService.getBranches();
    branches.value = res.data || [];
  } catch (err) {
    branches.value = [];
  } finally {
    isLoadingBranches.value = false;
  }
};

const openCreateModal = () => {
  editingBranch.value = null;
  form.value = { name: '', city: '', address: '', phone: '' };
  isModalOpen.value = true;
};

const openEditModal = (branch) => {
  editingBranch.value = branch;
  form.value = {
    name: branch.name,
    city: branch.city || '',
    address: branch.address || '',
    phone: branch.phone || ''
  };
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
  editingBranch.value = null;
};

const saveBranch = async () => {
  if (!form.value.name.trim()) return;
  isSubmitting.value = true;

  try {
    if (editingBranch.value) {
      await apiService.updateBranch(editingBranch.value.id, form.value);
    } else {
      await apiService.createBranch(form.value);
    }
    await loadBranches();
    closeModal();
  } catch (err) {
    // Error handling
  } finally {
    isSubmitting.value = false;
  }
};

onMounted(() => {
  loadBranches();
});
</script>
