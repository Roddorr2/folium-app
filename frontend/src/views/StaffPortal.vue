<template>
  <div class="space-y-8">
    <!-- Staff Management Header with StaffNavBar -->
    <StaffNavBar 
      :active-module="activeModule"
      @select-module="handleSelectModule"
    />

    <!-- Module 1: Cataloging (WEMI CRUD) -->
    <section v-if="activeModule === 'cataloging'" class="space-y-6">
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <h3 class="text-lg font-bold font-serif text-[#1C241E]">Módulo de Catalogación Bibliográfica</h3>
        <button @click="$emit('openNewWorkModal')" class="w-full sm:w-auto px-4 py-3 min-h-[44px] rounded-xl bg-[#2D5A3F] hover:bg-[#224430] text-[#F9F6F0] font-bold text-xs shadow-sm flex items-center justify-center gap-2">
          <Plus class="w-4 h-4" />
          <span>Registrar Nueva Obra</span>
        </button>
      </div      <!-- Desktop View: Table (hidden md:block) -->
      <div class="hidden md:block paper-card rounded-xl overflow-hidden border border-[#E3DAC9]">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="border-b border-[#E3DAC9] bg-[#EBE5D8]/60 text-xs font-semibold text-[#4A584E] uppercase tracking-wider">
              <th class="p-4">ID</th>
              <th class="p-4">Título de la Obra</th>
              <th class="p-4">Autor(es)</th>
              <th class="p-4">Disponibles</th>
              <th class="p-4 text-right">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#E3DAC9] text-xs text-[#1C241E]">
            <!-- Skeleton Rows when Loading -->
            <template v-if="isLoading">
              <tr v-for="n in 5" :key="'skeleton-' + n" class="animate-pulse">
                <td class="p-4">
                  <div class="h-4 w-10 bg-[#E8E2D5] rounded"></div>
                </td>
                <td class="p-4">
                  <div class="h-4 w-48 bg-[#E8E2D5] rounded mb-1.5"></div>
                  <div class="h-3 w-28 bg-[#EDE7DC] rounded"></div>
                </td>
                <td class="p-4">
                  <div class="h-4 w-32 bg-[#E8E2D5] rounded"></div>
                </td>
                <td class="p-4">
                  <div class="h-6 w-24 bg-[#E8E2D5] rounded-full"></div>
                </td>
                <td class="p-4 text-right">
                  <div class="inline-flex gap-1.5">
                    <div class="w-9 h-9 bg-[#E8E2D5] rounded-lg"></div>
                    <div class="w-9 h-9 bg-[#E8E2D5] rounded-lg"></div>
                  </div>
                </td>
              </tr>
            </template>

            <!-- Actual Rows -->
            <template v-else-if="works.length > 0">
              <tr v-for="work in works" :key="work.id" class="hover:bg-[#F9F6F0] transition-colors">
                <td class="p-4 font-mono font-bold text-[#2D5A3F]">#{{ work.id }}</td>
                <td class="p-4 font-semibold font-serif text-[#1C241E]">{{ work.title }}</td>
                <td class="p-4 text-[#4A584E]">{{ work.author }}</td>
                <td class="p-4">
                  <span class="px-2.5 py-1 rounded-full text-xs font-semibold badge-laurel">
                    {{ work.availableCount }} ejemplar(es)
                  </span>
                </td>
                <td class="p-4 text-right space-x-1.5">
                  <button 
                    @click="$emit('open-edit-modal', work)"
                    class="p-2.5 min-w-[44px] min-h-[44px] rounded-lg bg-[#EBE5D8] hover:bg-[#2D5A3F] text-[#1C241E] hover:text-[#F9F6F0] transition-colors border border-[#E3DAC9] inline-flex items-center justify-center"
                    :aria-label="'Editar obra ' + work.title"
                    title="Editar obra"
                  >
                    <Pencil class="w-4 h-4" />
                  </button>
                  <button 
                    @click="requestDeleteWork(work)"
                    class="p-2.5 min-w-[44px] min-h-[44px] rounded-lg bg-[#F9EDED] hover:bg-[#8C433E] text-[#8C433E] hover:text-[#F9F6F0] transition-colors border border-[#8C433E]/20 inline-flex items-center justify-center"
                    :aria-label="'Eliminar obra ' + work.title"
                    title="Eliminar obra"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </td>
              </tr>
            </template>

            <!-- Empty State -->
            <tr v-else>
              <td colspan="5" class="p-12 text-center text-[#7D8D81]">
                <BookOpen class="w-10 h-10 mx-auto text-[#7D8D81]/60 stroke-[1.5] mb-2" />
                <p class="font-serif font-bold text-sm text-[#1C241E]">No se encontraron obras registradas</p>
                <p class="text-xs text-[#4A584E] mt-1">Registra una nueva obra para comenzar a construir el catálogo.</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile View: Table-to-Card Morphing (block md:hidden) -->
      <div class="block md:hidden space-y-3">
        <!-- Skeleton Cards when Loading -->
        <template v-if="isLoading">
          <div 
            v-for="n in 3" 
            :key="'mobile-skeleton-' + n"
            class="paper-card p-4 rounded-xl border border-[#E3DAC9] space-y-3 shadow-paper-sm animate-pulse"
          >
            <div class="flex items-center justify-between">
              <div class="h-5 w-12 bg-[#E8E2D5] rounded"></div>
              <div class="h-5 w-24 bg-[#E8E2D5] rounded-full"></div>
            </div>
            <div class="space-y-1.5">
              <div class="h-5 w-3/4 bg-[#E8E2D5] rounded"></div>
              <div class="h-3 w-1/2 bg-[#EDE7DC] rounded"></div>
            </div>
            <div class="pt-2 border-t border-[#E3DAC9] flex gap-2">
              <div class="flex-1 h-10 bg-[#E8E2D5] rounded-xl"></div>
              <div class="flex-1 h-10 bg-[#E8E2D5] rounded-xl"></div>
            </div>
          </div>
        </template>

        <!-- Actual Mobile Cards -->
        <template v-else-if="works.length > 0">
          <div 
            v-for="work in works" 
            :key="'mobile-' + work.id"
            class="paper-card p-4 rounded-xl border border-[#E3DAC9] space-y-3 shadow-paper-sm"
          >
            <div class="flex items-center justify-between gap-2">
              <span class="font-mono text-xs font-bold text-[#2D5A3F] bg-[#E9F2EB] px-2.5 py-0.5 rounded border border-[#2D5A3F]/20">
                #{{ work.id }}
              </span>
              <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold badge-laurel">
                {{ work.availableCount }} ejemplar(es)
              </span>
            </div>

            <div>
              <h4 class="font-serif font-bold text-base text-[#1C241E] leading-snug">
                {{ work.title }}
              </h4>
              <p class="text-xs text-[#4A584E] font-medium mt-0.5">
                por <span class="text-[#1C241E] font-bold">{{ work.author }}</span>
              </p>
            </div>

            <div class="pt-2 border-t border-[#E3DAC9] flex items-center justify-end gap-2">
              <button 
                @click="$emit('open-edit-modal', work)"
                class="flex-1 py-2.5 px-3 min-h-[44px] rounded-xl bg-[#EBE5D8] hover:bg-[#2D5A3F] text-[#1C241E] hover:text-[#F9F6F0] font-semibold text-xs transition-colors border border-[#E3DAC9] flex items-center justify-center gap-1.5"
              >
                <Pencil class="w-4 h-4" />
                <span>Editar</span>
              </button>
              <button 
                @click="requestDeleteWork(work)"
                class="flex-1 py-2.5 px-3 min-h-[44px] rounded-xl bg-[#F9EDED] hover:bg-[#8C433E] text-[#8C433E] hover:text-[#F9F6F0] font-semibold text-xs transition-colors border border-[#8C433E]/20 flex items-center justify-center gap-1.5"
              >
                <Trash2 class="w-4 h-4" />
                <span>Eliminar</span>
              </button>
            </div>
          </div>
        </template>

        <!-- Empty State Mobile -->
        <div v-else class="paper-card p-8 rounded-xl border border-[#E3DAC9] text-center text-[#7D8D81]">
          <BookOpen class="w-10 h-10 mx-auto text-[#7D8D81]/60 stroke-[1.5] mb-2" />
          <p class="font-serif font-bold text-sm text-[#1C241E]">No se encontraron obras registradas</p>
          <p class="text-xs text-[#4A584E] mt-1">Registra una nueva obra para comenzar a construir el catálogo.</p>
        </div>
      </div>
    </section>

    <!-- Module 2: Circulation (Loans/Returns) -->
    <section v-else-if="activeModule === 'circulation'" class="grid grid-cols-1 lg:grid-cols-2 gap-6 font-sans">
      <div class="paper-card p-6 rounded-xl border border-[#E3DAC9] space-y-4">
        <h3 class="text-base font-bold font-serif text-[#1C241E] flex items-center gap-2">
          <ArrowUpRight class="w-4 h-4 text-[#2D5A3F]" />
          <span>Registrar Préstamo Físico</span>
        </h3>
        <p class="text-xs text-[#4A584E]">Escanea o ingresa el código del usuario y el código de barras del ejemplar.</p>

        <form novalidate @submit.prevent="submitLoan" class="space-y-4 pt-2">
          <div class="space-y-1">
            <label class="block text-xs font-semibold text-[#1C241E]">Código de barras del ejemplar *</label>
            <input 
              v-model="loanForm.barcode" 
              @blur="loanTouched.barcode = true"
              @input="loanTouched.barcode = true"
              type="text" 
              placeholder="Ej. FOL-83920-3" 
              :class="[
                'w-full px-4 py-3 rounded-lg bg-[#FBF9F4] border text-[#1C241E] text-base md:text-xs font-mono transition-all focus:outline-none',
                loanErrors.barcode && loanTouched.barcode 
                  ? 'border-[#8C433E] focus:ring-2 focus:ring-[#8C433E]/20' 
                  : 'border-[#E2DCCF] focus:ring-2 focus:ring-[#2D5A3F]/20 focus:border-[#2D5A3F]'
              ]"
            />
            <span v-if="loanErrors.barcode && loanTouched.barcode" class="text-[#8C433E] text-[11px] font-medium block animate-in fade-in duration-150">
              {{ loanErrors.barcode }}
            </span>
          </div>

          <div class="space-y-1">
            <label class="block text-xs font-semibold text-[#1C241E]">Código o DNI del usuario *</label>
            <input 
              v-model="loanForm.userId" 
              @blur="loanTouched.userId = true"
              @input="loanTouched.userId = true"
              type="text" 
              placeholder="Ej. 70123456" 
              :class="[
                'w-full px-4 py-3 rounded-lg bg-[#FBF9F4] border text-[#1C241E] text-base md:text-xs font-mono transition-all focus:outline-none',
                loanErrors.userId && loanTouched.userId 
                  ? 'border-[#8C433E] focus:ring-2 focus:ring-[#8C433E]/20' 
                  : 'border-[#E2DCCF] focus:ring-2 focus:ring-[#2D5A3F]/20 focus:border-[#2D5A3F]'
              ]"
            />
            <span v-if="loanErrors.userId && loanTouched.userId" class="text-[#8C433E] text-[11px] font-medium block animate-in fade-in duration-150">
              {{ loanErrors.userId }}
            </span>
          </div>

          <button 
            type="submit" 
            :disabled="!isLoanValid || isSubmittingLoan"
            class="w-full py-3 min-h-[44px] bg-[#2D5A3F] hover:bg-[#224430] text-[#F9F6F0] font-semibold text-xs rounded-lg shadow-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
          >
            <span v-if="isSubmittingLoan" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
            <span>Procesar Préstamo</span>
          </button>
        </form>
      </div>

      <div class="paper-card p-6 rounded-xl border border-[#E3DAC9] space-y-4">
        <h3 class="text-base font-bold font-serif text-[#1C241E] flex items-center gap-2">
          <ArrowDownLeft class="w-4 h-4 text-[#C27D38]" />
          <span>Recepción de Devoluciones</span>
        </h3>
        <p class="text-xs text-[#4A584E]">Registra el retorno del ejemplar al estante o cola de reservas.</p>

        <form novalidate @submit.prevent="submitReturn" class="space-y-4 pt-2">
          <div class="space-y-1">
            <label class="block text-xs font-semibold text-[#1C241E]">Código de barras a devolver *</label>
            <input 
              v-model="returnForm.barcode" 
              @blur="returnTouched.barcode = true"
              @input="returnTouched.barcode = true"
              type="text" 
              placeholder="Ej. FOL-91023-1" 
              :class="[
                'w-full px-4 py-3 rounded-lg bg-[#FBF9F4] border text-[#1C241E] text-base md:text-xs font-mono transition-all focus:outline-none',
                returnErrors.barcode && returnTouched.barcode 
                  ? 'border-[#8C433E] focus:ring-2 focus:ring-[#8C433E]/20' 
                  : 'border-[#E2DCCF] focus:ring-2 focus:ring-[#2D5A3F]/20 focus:border-[#2D5A3F]'
              ]"
            />
            <span v-if="returnErrors.barcode && returnTouched.barcode" class="text-[#8C433E] text-[11px] font-medium block animate-in fade-in duration-150">
              {{ returnErrors.barcode }}
            </span>
          </div>

          <button 
            type="submit" 
            :disabled="!isReturnValid || isSubmittingReturn"
            class="w-full py-3 min-h-[44px] bg-[#EBE5D8] hover:bg-[#E3DAC9] text-[#1C241E] font-semibold text-xs rounded-lg border border-[#E3DAC9] transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
          >
            <span v-if="isSubmittingReturn" class="w-4 h-4 border-2 border-[#1C241E] border-t-transparent rounded-full animate-spin"></span>
            <span>Confirmar Devolución</span>
          </button>
        </form>
      </div>
    </section>

    <!-- Module 3: Interlibrary Transfers -->
    <section v-else-if="activeModule === 'transfers'" class="space-y-6">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-bold font-serif text-[#1C241E]">Solicitudes de Transferencia Interbibliotecaria (ILL)</h3>
      </div>

      <div class="paper-card rounded-xl p-6 border border-[#E3DAC9] text-xs text-[#4A584E] text-center py-12">
        <p>No hay solicitudes de tránsito pendientes de aprobación en este momento.</p>
      </div>
    </section>

    <!-- Module 4: Network Branches Management -->
    <section v-else-if="activeModule === 'branches'">
      <BranchesManagementView />
    </section>

    <!-- Module 5: Users and Permissions Management -->
    <section v-else-if="activeModule === 'users'">
      <UsersManagementView />
    </section>

    <!-- Confirm Modal for Work Deletion -->
    <ConfirmModal 
      :is-open="isConfirmDeleteOpen"
      title="¿Eliminar obra del catálogo?"
      :message="`¿Estás seguro de que deseas eliminar la obra '${workToDelete?.title}'? Esta acción eliminará permanentemente los metadatos bibliográficos.`"
      confirm-label="Eliminar Obra"
      cancel-label="Cancelar"
      :is-destructive="true"
      @confirm="handleConfirmDeleteWork"
      @cancel="isConfirmDeleteOpen = false"
    />
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { Plus, ArrowUpRight, ArrowDownLeft, Pencil, Trash2, BookOpen } from 'lucide-vue-next';
import StaffNavBar from '../components/StaffNavBar.vue';
import BranchesManagementView from './BranchesManagementView.vue';
import UsersManagementView from './UsersManagementView.vue';
import ConfirmModal from '../components/ConfirmModal.vue';

const props = defineProps({
  works: { type: Array, default: () => [] },
  isLoading: { type: Boolean, default: false }
});

const emit = defineEmits(['openNewWorkModal', 'open-edit-modal', 'delete-work', 'processLoan', 'processReturn']);

const route = useRoute();
const router = useRouter();
const activeModule = ref('cataloging');

const isConfirmDeleteOpen = ref(false);
const workToDelete = ref(null);

const isSubmittingLoan = ref(false);
const isSubmittingReturn = ref(false);

const requestDeleteWork = (work) => {
  workToDelete.value = work;
  isConfirmDeleteOpen.value = true;
};

const handleConfirmDeleteWork = () => {
  if (!workToDelete.value) return;
  emit('delete-work', workToDelete.value.id);
  isConfirmDeleteOpen.value = false;
  workToDelete.value = null;
};

const syncModuleFromRoute = () => {
  if (route.path === '/admin/branches') {
    activeModule.value = 'branches';
  } else if (route.path === '/admin/users') {
    activeModule.value = 'users';
  } else if (route.path === '/transfers') {
    activeModule.value = 'transfers';
  } else if (route.path === '/catalog/manage' || route.path === '/catalog') {
    activeModule.value = 'cataloging';
  } else if (route.path === '/staff' || route.path === '/circulation') {
    activeModule.value = 'circulation';
  }
};

onMounted(() => {
  syncModuleFromRoute();
});

watch(() => route.path, () => {
  syncModuleFromRoute();
});

const handleSelectModule = (mod) => {
  activeModule.value = mod;
  if (mod === 'branches') {
    router.push('/admin/branches');
  } else if (mod === 'users') {
    router.push('/admin/users');
  } else if (mod === 'transfers') {
    router.push('/transfers');
  } else if (mod === 'cataloging') {
    router.push('/catalog/manage');
  } else if (mod === 'circulation') {
    router.push('/staff');
  }
};

const loanForm = ref({
  barcode: '',
  userId: ''
});

const loanTouched = reactive({
  barcode: false,
  userId: false
});

const loanErrors = computed(() => {
  const errs = {};
  if (!loanForm.value.barcode || loanForm.value.barcode.trim() === '') {
    errs.barcode = 'El código de barras del ejemplar es requerido.';
  }
  if (!loanForm.value.userId || loanForm.value.userId.trim() === '') {
    errs.userId = 'El DNI o código del usuario es requerido.';
  }
  return errs;
});

const isLoanValid = computed(() => Object.keys(loanErrors.value).length === 0);

const returnForm = ref({
  barcode: ''
});

const returnTouched = reactive({
  barcode: false
});

const returnErrors = computed(() => {
  const errs = {};
  if (!returnForm.value.barcode || returnForm.value.barcode.trim() === '') {
    errs.barcode = 'El código de barras a devolver es requerido.';
  }
  return errs;
});

const isReturnValid = computed(() => Object.keys(returnErrors.value).length === 0);

const submitLoan = async () => {
  loanTouched.barcode = true;
  loanTouched.userId = true;

  if (!isLoanValid.value) return;

  isSubmittingLoan.value = true;
  try {
    emit('processLoan', { barcode: loanForm.value.barcode, userId: loanForm.value.userId });
    loanForm.value = { barcode: '', userId: '' };
    loanTouched.barcode = false;
    loanTouched.userId = false;
  } finally {
    setTimeout(() => {
      isSubmittingLoan.value = false;
    }, 400);
  }
};

const submitReturn = async () => {
  returnTouched.barcode = true;

  if (!isReturnValid.value) return;

  isSubmittingReturn.value = true;
  try {
    emit('processReturn', { barcode: returnForm.value.barcode });
    returnForm.value = { barcode: '' };
    returnTouched.barcode = false;
  } finally {
    setTimeout(() => {
      isSubmittingReturn.value = false;
    }, 400);
  }
};
</script>
