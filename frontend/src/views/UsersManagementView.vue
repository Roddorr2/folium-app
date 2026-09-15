<template>
  <div class="space-y-6">
    <!-- Header with Back Link and Action -->
    <div class="paper-card p-4 sm:p-6 rounded-2xl border border-[#E3DAC9] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div class="space-y-1">
        <div class="flex items-center gap-2">
          <router-link to="/staff" class="text-xs text-[#2D5A3F] font-semibold hover:underline flex items-center gap-1 min-h-[36px]">
            &larr; Volver a Gestión
          </router-link>
          <span class="text-xs text-[#7D8D81]">•</span>
          <span class="px-2.5 py-0.5 rounded-full bg-[#E9F2EB] text-[#2D5A3F] text-[11px] font-semibold border border-[#2D5A3F]/20">Seguridad & RBAC</span>
        </div>
        <h2 class="text-2xl font-bold font-serif text-[#1C241E] tracking-tight">Gestión de Personal & Roles</h2>
        <p class="text-xs text-[#4A584E]">Asignación de credenciales, roles institucionales y adscripción a sedes bibliotecarias.</p>
      </div>

      <button 
        type="button"
        @click="openCreateModal" 
        class="w-full sm:w-auto px-4 py-3 min-h-[44px] rounded-xl bg-[#2D5A3F] hover:bg-[#224430] text-[#F9F6F0] font-bold text-xs shadow-sm flex items-center justify-center gap-2 transition-all"
        aria-label="Registrar nuevo usuario o personal"
      >
        <UserPlus class="w-4 h-4" />
        <span>+ Registrar Nuevo Usuario</span>
      </button>
    </div>

    <!-- Desktop View: Table (hidden md:block) -->
    <div class="hidden md:block paper-card rounded-2xl overflow-hidden border border-[#E3DAC9] shadow-sm">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-[#E3DAC9] bg-[#EBE5D8]/70 text-xs font-semibold text-[#4A584E] uppercase tracking-wider">
            <th class="p-4">Usuario / Nombre</th>
            <th class="p-4">Correo Institucional</th>
            <th class="p-4">Rol Asignado</th>
            <th class="p-4">Sede Adscrita</th>
            <th class="p-4 text-right">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#E3DAC9] text-xs text-[#1C241E]">
          <!-- Skeleton Loading Rows -->
          <template v-if="isLoadingUsers">
            <tr v-for="n in 5" :key="'user-skeleton-' + n" class="animate-pulse">
              <td class="p-4">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-full bg-[#E8E2D5] shrink-0"></div>
                  <div class="space-y-1.5">
                    <div class="h-4 w-36 bg-[#E8E2D5] rounded"></div>
                    <div class="h-3 w-20 bg-[#EDE7DC] rounded"></div>
                  </div>
                </div>
              </td>
              <td class="p-4">
                <div class="h-4 w-40 bg-[#E8E2D5] rounded"></div>
              </td>
              <td class="p-4">
                <div class="h-5 w-24 bg-[#E8E2D5] rounded-full"></div>
              </td>
              <td class="p-4">
                <div class="h-4 w-28 bg-[#E8E2D5] rounded"></div>
              </td>
              <td class="p-4 text-right">
                <div class="inline-flex gap-2">
                  <div class="w-9 h-9 bg-[#E8E2D5] rounded-xl"></div>
                  <div class="w-9 h-9 bg-[#E8E2D5] rounded-xl"></div>
                </div>
              </td>
            </tr>
          </template>

          <!-- Actual Rows -->
          <template v-else-if="users.length > 0">
            <tr v-for="user in users" :key="user.id" class="hover:bg-[#F9F6F0] transition-colors">
              <td class="p-4">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-full bg-[#2D5A3F] text-[#F9F6F0] font-bold flex items-center justify-center text-xs uppercase shrink-0">
                    {{ user.initials || 'U' }}
                  </div>
                  <div>
                    <div class="flex items-center gap-2">
                      <span class="font-bold font-serif text-sm block text-[#1C241E]">{{ user.name }}</span>
                      <span 
                        v-if="isCurrentUser(user.id)" 
                        class="px-2 py-0.5 rounded-md bg-[#E9F2EB] text-[#2D5A3F] text-[10px] font-bold border border-[#2D5A3F]/20 uppercase tracking-wider"
                      >
                        (Tú)
                      </span>
                    </div>
                    <span v-if="user.dni" class="text-[11px] text-[#6E7B72]">DNI: {{ user.dni }}</span>
                  </div>
                </div>
              </td>
              <td class="p-4 font-mono text-[#2D5A3F] text-xs">{{ user.email }}</td>
              <td class="p-4">
                <span :class="getBadgeClassByVariant(user.role?.badge_variant)" class="px-3 py-1 rounded-full text-[11px] font-semibold">
                  {{ user.role?.display_name || user.role?.name || 'Lector' }}
                </span>
              </td>
              <td class="p-4 text-[#4A584E]">
                <span v-if="user.branch" class="font-medium text-[#1C241E] block">{{ user.branch.name }}</span>
                <span v-else-if="user.role?.key === 'librarian' || user.role?.name === 'librarian'" class="text-[#8C433E] font-bold text-[11px]">Sede Requerida</span>
                <span v-else class="text-[#7D8D81] italic">Nivel Central / Red</span>
              </td>
              <td class="p-4 text-right space-x-2">
                <button 
                  type="button"
                  @click="openEditModal(user)"
                  class="p-2.5 min-w-[44px] min-h-[44px] rounded-xl bg-[#EBE5D8] hover:bg-[#2D5A3F] text-[#1C241E] hover:text-[#F9F6F0] transition-colors border border-[#E3DAC9] inline-flex items-center justify-center"
                  title="Editar permisos de usuario"
                  :aria-label="'Editar permisos de ' + user.name"
                >
                  <Pencil class="w-4 h-4" />
                </button>
                <button 
                  type="button"
                  @click="requestDeleteUser(user)"
                  :disabled="isCurrentUser(user.id)"
                  :aria-disabled="isCurrentUser(user.id)"
                  :title="isCurrentUser(user.id) ? 'No puedes eliminar o desactivar tu propia cuenta en sesión' : 'Revocar credenciales'"
                  :class="[
                    'p-2.5 min-w-[44px] min-h-[44px] rounded-xl transition-colors border inline-flex items-center justify-center',
                    isCurrentUser(user.id) 
                      ? 'bg-[#F2ECE4] text-[#A69B89] border-[#E3DAC9] opacity-30 cursor-not-allowed pointer-events-none' 
                      : 'bg-[#F9EDED] hover:bg-[#8C433E] text-[#8C433E] hover:text-[#F9F6F0] border-[#8C433E]/20'
                  ]"
                  :aria-label="'Revocar credenciales de ' + user.name"
                >
                  <Trash2 class="w-4 h-4" />
                </button>
              </td>
            </tr>
          </template>

          <!-- Empty State Desktop -->
          <tr v-else>
            <td colspan="5" class="p-12 text-center text-[#7D8D81]">
              <UserCheck class="w-10 h-10 mx-auto text-[#7D8D81]/60 stroke-[1.5] mb-2" />
              <p class="font-serif font-bold text-sm text-[#1C241E]">No se encontraron usuarios registrados</p>
              <p class="text-xs text-[#4A584E] mt-1">Registra nuevos miembros del personal o lectores para gestionar credenciales.</p>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Mobile-First Table-to-Card Stacked List (block md:hidden) -->
    <div class="block md:hidden space-y-3">
      <!-- Skeleton Loading Cards -->
      <template v-if="isLoadingUsers">
        <div 
          v-for="n in 3" 
          :key="'user-mobile-skeleton-' + n"
          class="paper-card p-4 rounded-2xl border border-[#E3DAC9] space-y-3 shadow-paper-sm animate-pulse"
        >
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
              <div class="w-8 h-8 rounded-full bg-[#E8E2D5]"></div>
              <div class="space-y-1">
                <div class="h-4 w-28 bg-[#E8E2D5] rounded"></div>
                <div class="h-3 w-36 bg-[#EDE7DC] rounded"></div>
              </div>
            </div>
            <div class="h-5 w-16 bg-[#E8E2D5] rounded-full"></div>
          </div>
          <div class="h-4 w-1/2 bg-[#EDE7DC] rounded"></div>
          <div class="pt-2 border-t border-[#E3DAC9] flex gap-2">
            <div class="flex-1 h-10 bg-[#E8E2D5] rounded-xl"></div>
            <div class="w-20 h-10 bg-[#E8E2D5] rounded-xl"></div>
          </div>
        </div>
      </template>

      <!-- Actual Mobile Cards -->
      <template v-else-if="users.length > 0">
        <div 
          v-for="user in users" 
          :key="'mobile-' + user.id"
          class="paper-card p-4 rounded-2xl border border-[#E3DAC9] space-y-3 shadow-paper-sm"
        >
          <div class="flex items-center justify-between gap-2 border-b border-[#E3DAC9]/80 pb-2">
            <div class="flex items-center gap-2.5">
              <div class="w-8 h-8 rounded-full bg-[#2D5A3F] text-[#F9F6F0] font-bold flex items-center justify-center text-xs uppercase shrink-0">
                {{ user.initials || 'U' }}
              </div>
              <div>
                <div class="flex items-center gap-1.5 flex-wrap">
                  <h4 class="font-serif font-bold text-sm text-[#1C241E] leading-snug">{{ user.name }}</h4>
                  <span 
                    v-if="isCurrentUser(user.id)" 
                    class="px-1.5 py-0.2 rounded bg-[#E9F2EB] text-[#2D5A3F] text-[10px] font-bold border border-[#2D5A3F]/20"
                  >
                    (Tú)
                  </span>
                </div>
                <span class="font-mono text-[11px] text-[#2D5A3F] block">{{ user.email }}</span>
              </div>
            </div>

            <span :class="getBadgeClassByVariant(user.role?.badge_variant)" class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold shrink-0">
              {{ user.role?.display_name || user.role?.name || 'Lector' }}
            </span>
          </div>

          <div class="flex items-center justify-between text-xs pt-1">
            <span class="text-[#6E7B72]">Sede física:</span>
            <span class="font-medium text-[#1C241E]">
              {{ user.branch ? user.branch.name : (user.role?.key === 'librarian' ? 'Sin Sede (!)' : 'Toda la Red') }}
            </span>
          </div>

          <div class="pt-2 border-t border-[#E3DAC9] flex items-center justify-end gap-2">
            <button 
              type="button"
              @click="openEditModal(user)"
              class="flex-1 py-2.5 px-3 min-h-[44px] rounded-xl bg-[#EBE5D8] hover:bg-[#2D5A3F] text-[#1C241E] hover:text-[#F9F6F0] font-semibold text-xs transition-colors border border-[#E3DAC9] flex items-center justify-center gap-1.5"
            >
              <Pencil class="w-4 h-4" />
              <span>Editar Rol</span>
            </button>
            <button 
              type="button"
              @click="requestDeleteUser(user)"
              :disabled="isCurrentUser(user.id)"
              :aria-disabled="isCurrentUser(user.id)"
              :title="isCurrentUser(user.id) ? 'No puedes eliminar o desactivar tu propia cuenta en sesión' : 'Revocar credenciales'"
              :class="[
                'py-2.5 px-3 min-h-[44px] rounded-xl font-semibold text-xs transition-colors border flex items-center justify-center gap-1.5',
                isCurrentUser(user.id) 
                  ? 'bg-[#F2ECE4] text-[#A69B89] border-[#E3DAC9] opacity-30 cursor-not-allowed pointer-events-none' 
                  : 'bg-[#F9EDED] hover:bg-[#8C433E] text-[#8C433E] hover:text-[#F9F6F0] border-[#8C433E]/20'
              ]"
            >
              <Trash2 class="w-4 h-4" />
              <span>Revocar</span>
            </button>
          </div>
        </div>
      </template>

      <!-- Empty State Mobile -->
      <div v-else class="paper-card p-8 rounded-2xl border border-[#E3DAC9] text-center text-[#7D8D81]">
        <UserCheck class="w-10 h-10 mx-auto text-[#7D8D81]/60 stroke-[1.5] mb-2" />
        <p class="font-serif font-bold text-sm text-[#1C241E]">No se encontraron usuarios registrados</p>
        <p class="text-xs text-[#4A584E] mt-1">Registra nuevos miembros del personal o lectores para gestionar credenciales.</p>
      </div>
    </div>

    <!-- Modal Form (Create / Edit User) -->
    <Teleport to="body">
      <div 
        v-if="isModalOpen" 
        ref="modalRef"
        class="fixed inset-0 z-50 flex items-end md:items-center justify-center p-0 sm:p-4 md:p-6 overflow-hidden bg-[#141A16]/70 backdrop-blur-md"
        role="dialog"
        aria-modal="true"
        aria-labelledby="user-modal-title"
        tabindex="-1"
        @click.self="closeModal"
      >
        <div class="paper-card w-full max-w-lg rounded-t-3xl md:rounded-2xl rounded-b-none md:rounded-b-2xl p-6 sm:p-8 border-t md:border border-[#E3DAC9] shadow-2xl relative space-y-5 max-h-[90dvh] md:max-h-[85vh] flex flex-col overflow-y-auto">
          <!-- Drag Handle -->
          <div class="w-12 h-1.5 bg-[#C5BBAA] rounded-full mx-auto mb-1 md:hidden shrink-0"></div>

          <!-- Header -->
          <div class="flex items-center justify-between border-b border-[#E3DAC9] pb-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-[#2D5A3F] text-[#F9F6F0] flex items-center justify-center font-bold text-lg border border-[#224430] shrink-0">
                <UserCheck class="w-5 h-5" />
              </div>
              <h3 id="user-modal-title" class="text-xl font-bold font-serif text-[#1C241E]">
                {{ editingUser ? 'Editar Permisos de Usuario' : 'Registrar Nuevo Usuario' }}
              </h3>
            </div>
            <button 
              type="button" 
              @click="closeModal" 
              class="p-2 text-[#7D8D81] hover:text-[#1C241E] rounded-xl min-w-[44px] min-h-[44px] flex items-center justify-center focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]"
              aria-label="Cerrar modal de usuario"
            >
              <X class="w-5 h-5" />
            </button>
          </div>

          <!-- Form Error Alert -->
          <div v-if="validationError" role="alert" class="p-3 rounded-xl bg-[#F9EDED] border border-[#8C433E]/30 text-xs text-[#8C433E] font-semibold animate-in fade-in">
            {{ validationError }}
          </div>

          <!-- Form -->
          <form novalidate @submit.prevent="saveUser" class="space-y-4 font-sans">
            <div class="space-y-1.5">
              <label for="user-name" class="block text-xs font-semibold text-[#1C241E]">Nombre Completo *</label>
              <input 
                id="user-name"
                v-model="form.name"
                required
                type="text" 
                placeholder="Ej. María Elena Izquierdo"
                class="w-full px-4 py-3 rounded-xl bg-[#FBF9F4] border border-[#E2DCCF] text-[#1C241E] text-base md:text-sm focus:ring-2 focus:ring-[#2D5A3F]/20 focus:border-[#2D5A3F] outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]"
              />
            </div>

            <div class="space-y-1.5">
              <label for="user-email" class="block text-xs font-semibold text-[#1C241E]">Correo Institucional *</label>
              <input 
                id="user-email"
                v-model="form.email"
                required
                type="email" 
                placeholder="ej. bibliotecario@folium.pe"
                class="w-full px-4 py-3 rounded-xl bg-[#FBF9F4] border border-[#E2DCCF] text-[#1C241E] text-base md:text-sm focus:ring-2 focus:ring-[#2D5A3F]/20 focus:border-[#2D5A3F] outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]"
              />
            </div>

            <div class="space-y-1.5">
              <label for="user-password" class="block text-xs font-semibold text-[#1C241E]">
                {{ editingUser ? 'Contraseña Provisoria (Dejar en blanco para mantener)' : 'Contraseña Provisoria *' }}
              </label>
              <input 
                id="user-password"
                v-model="form.password"
                :required="!editingUser"
                type="password" 
                placeholder="••••••••"
                class="w-full px-4 py-3 rounded-xl bg-[#FBF9F4] border border-[#E2DCCF] text-[#1C241E] text-base md:text-sm focus:ring-2 focus:ring-[#2D5A3F]/20 focus:border-[#2D5A3F] outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]"
              />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <!-- Role Selector -->
              <div class="space-y-1.5">
                <label for="user-role-select" class="block text-xs font-semibold text-[#1C241E]">Rol Institucional *</label>
                <select 
                  id="user-role-select"
                  v-model="form.role_id"
                  required
                  :disabled="isEditingSelf"
                  :class="[
                    'w-full px-4 py-3 rounded-xl border text-[#1C241E] text-base md:text-sm focus:ring-2 focus:ring-[#2D5A3F]/20 focus:border-[#2D5A3F] outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]',
                    isEditingSelf ? 'bg-[#EBE5D8]/50 border-[#D8CEBD] text-[#7D8D81] cursor-not-allowed' : 'bg-[#FBF9F4] border-[#E2DCCF]'
                  ]"
                >
                  <option v-for="role in roles" :key="role.id" :value="role.id">
                    {{ role.display_name || role.name }}
                  </option>
                </select>
                <p v-if="isEditingSelf" class="text-[11px] text-[#8C6D3F] font-medium mt-1">
                  No puedes revocar tus propios privilegios de administración.
                </p>
              </div>

              <!-- Branch Selector (Mandatory for librarian) -->
              <div class="space-y-1.5">
                <label for="user-branch-select" class="block text-xs font-semibold text-[#1C241E]">
                  Sede Asignada
                  <span v-if="isSelectedRoleLibrarian" class="text-[#8C433E] font-bold">* (Obligatorio)</span>
                </label>
                <select 
                  id="user-branch-select"
                  v-model="form.branch_id"
                  :required="isSelectedRoleLibrarian"
                  :class="[
                    'w-full px-4 py-3 rounded-xl bg-[#FBF9F4] border text-[#1C241E] text-base md:text-sm focus:ring-2 outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]',
                    isSelectedRoleLibrarian && !form.branch_id ? 'border-[#8C433E]' : 'border-[#E2DCCF] focus:ring-[#2D5A3F]/20 focus:border-[#2D5A3F]'
                  ]"
                >
                  <option :value="null">-- Seleccionar Sede --</option>
                  <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                    {{ branch.name }}
                  </option>
                </select>
              </div>
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
                <span>{{ editingUser ? 'Guardar Cambios' : 'Registrar Usuario' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

    <!-- Editorial Confirmation Modal for User Revocation -->
    <ConfirmModal 
      :is-open="isConfirmModalOpen"
      title="¿Revocar credenciales y eliminar usuario?"
      :message="`¿Estás seguro de que deseas revocar los accesos institucionales y eliminar la cuenta de ${userToDelete?.name || 'este usuario'}? Esta acción desconectará las sesiones activas de inmediato.`"
      confirm-label="Revocar y Eliminar"
      cancel-label="Cancelar"
      :is-destructive="true"
      :is-submitting="isDeletingUser"
      @confirm="handleConfirmDelete"
      @cancel="isConfirmModalOpen = false"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { UserPlus, Pencil, Trash2, UserCheck, X } from 'lucide-vue-next';
import { apiService } from '../services/api';
import { useAuth } from '../composables/useAuth';
import { useToast } from '../composables/useToast';
import { useFocusTrap } from '../composables/useFocusTrap';
import { getBadgeClassByVariant } from '../modules/identity/utils/design-system';
import ConfirmModal from '../components/ConfirmModal.vue';

const { currentUser } = useAuth();
const toast = useToast();

const isCurrentUser = (targetUserId) => {
  if (!targetUserId || !currentUser.value?.id) return false;
  return String(currentUser.value.id) === String(targetUserId);
};

const users = ref([]);
const roles = ref([]);
const branches = ref([]);
const isModalOpen = ref(false);
const editingUser = ref(null);
const isSubmitting = ref(false);
const validationError = ref('');
const isLoadingUsers = ref(true);

// Destructive Action Confirmation State
const isConfirmModalOpen = ref(false);
const userToDelete = ref(null);
const isDeletingUser = ref(false);

const modalRef = ref(null);
useFocusTrap(modalRef, computed(() => isModalOpen.value), () => closeModal());

const isEditingSelf = computed(() => {
  return editingUser.value ? isCurrentUser(editingUser.value.id) : false;
});

const form = ref({
  name: '',
  email: '',
  password: '',
  role_id: 3,
  branch_id: null,
  dni: ''
});

const isSelectedRoleLibrarian = computed(() => {
  const selected = roles.value.find(r => r.id === Number(form.value.role_id));
  return selected?.key === 'librarian' || selected?.name === 'librarian';
});

const loadInitialData = async () => {
  isLoadingUsers.value = true;
  try {
    const [uRes, rRes, bRes] = await Promise.all([
      apiService.getUsers(),
      apiService.getRoles(),
      apiService.getBranches()
    ]);
    users.value = uRes.data || [];
    roles.value = rRes.data || [];
    branches.value = bRes.data || [];
  } catch {
    toast.error('No se pudieron cargar los datos de personal y sedes.');
  } finally {
    isLoadingUsers.value = false;
  }
};

const openCreateModal = () => {
  editingUser.value = null;
  validationError.value = '';
  form.value = {
    name: '',
    email: '',
    password: '',
    role_id: roles.value[2]?.id || 3,
    branch_id: null,
    dni: ''
  };
  isModalOpen.value = true;
};

const openEditModal = (user) => {
  editingUser.value = user;
  validationError.value = '';
  form.value = {
    name: user.name,
    email: user.email,
    password: '',
    role_id: user.role_id || (user.role?.id) || 3,
    branch_id: user.branch_id || null,
    dni: user.dni || ''
  };
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
  editingUser.value = null;
  validationError.value = '';
};

const saveUser = async () => {
  validationError.value = '';

  // Mandatory branch restriction for desk librarian
  if (isSelectedRoleLibrarian.value && !form.value.branch_id) {
    validationError.value = 'Un Bibliotecario de Sede debe estar asignado obligatoriamente a una sede física.';
    return;
  }

  isSubmitting.value = true;

  try {
    const payload = {
      name: form.value.name.trim(),
      email: form.value.email.trim(),
      role_id: Number(form.value.role_id),
      branch_id: form.value.branch_id ? Number(form.value.branch_id) : null,
      dni: form.value.dni ? form.value.dni.trim() : null
    };

    if (form.value.password) {
      payload.password = form.value.password;
    }

    if (editingUser.value) {
      await apiService.updateUser(editingUser.value.id, payload);
      toast.success(`Datos de ${payload.name} actualizados correctamente.`);
    } else {
      await apiService.createUser(payload);
      toast.success(`Usuario ${payload.name} registrado con éxito.`);
    }

    await loadInitialData();
    closeModal();
  } catch (err) {
    validationError.value = 'No se pudo guardar el usuario. Verifica que el correo no esté duplicado.';
    toast.error('Error al guardar datos de usuario.');
  } finally {
    isSubmitting.value = false;
  }
};

const requestDeleteUser = (user) => {
  if (isCurrentUser(user.id)) {
    toast.warning('Operación no permitida: no puedes revocar o eliminar tu propia cuenta en sesión.');
    return;
  }

  userToDelete.value = user;
  isConfirmModalOpen.value = true;
};

const handleConfirmDelete = async () => {
  if (!userToDelete.value) return;

  isDeletingUser.value = true;
  try {
    await apiService.deleteUser(userToDelete.value.id);
    toast.success(`Credenciales de ${userToDelete.value.name} revocadas exitosamente.`);
    isConfirmModalOpen.value = false;
    userToDelete.value = null;
    await loadInitialData();
  } catch (err) {
    toast.error('Error al revocar el usuario.');
  } finally {
    isDeletingUser.value = false;
  }
};

onMounted(() => {
  loadInitialData();
});
</script>
