<template>
  <div class="min-h-screen w-full bg-[#F9F6F0] text-[#1C241E] flex flex-col font-sans selection:bg-[#2D5A3F] selection:text-[#F9F6F0]">
    <!-- Header with Authentication & Branch Switcher -->
    <CatalogHeader 
      :branches="branches"
      :current-branch-id="currentBranchId"
      :active-portal="activePortal"
      :current-user="currentUser"
      @change-branch="handleChangeBranch"
      @navigate="handleNavigate"
      @open-login="isLoginModalOpen = true"
      @logout="handleLogout"
    />

    <!-- Main Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Portal 1: Vista de Clientes / Lectores -->
      <ReaderPortal 
        v-if="activePortal === 'reader'"
        :works="works"
        :branches="branches"
        :current-branch-id="currentBranchId"
        :search-query="searchQuery"
        :selected-branch="selectedBranch"
        :selected-status="selectedStatus"
        :active-subjects="activeSubjects"
        :ill-transfers="illTransfers"
        :is-loading="isLoadingWorks"
        @update:searchQuery="searchQuery = $event"
        @update:selectedBranch="selectedBranch = $event"
        @update:selectedStatus="selectedStatus = $event"
        @toggle-subject="toggleSubjectFilter"
        @clear-filters="clearAllFilters"
        @open-work-detail="openWorkDetail"
        @open-ill-modal="openIllModal"
        @notify-availability="handleNotifyAvailability"
      />

      <!-- Portal 2: Vista de Empleados / Personal (Protegida por Autenticación) -->
      <StaffPortal 
        v-else-if="activePortal === 'staff'"
        :works="works"
        :is-loading="isLoadingWorks"
        @openNewWorkModal="isNewWorkModalOpen = true"
        @open-edit-modal="handleOpenEditModal"
        @delete-work="handleDeleteWork"
        @processLoan="handleProcessLoan"
        @processReturn="handleProcessReturn"
      />
    </main>

    <!-- Footer -->
    <footer class="border-t border-[#E3DAC9] bg-[#F3EFE6] py-6 mt-12">
      <div class="max-w-7xl mx-auto px-4 text-center text-xs text-[#4A584E]">
        Folium &copy; {{ new Date().getFullYear() }} — Sistema Integrado de Gestión Bibliotecaria Red Multi-Sede.
      </div>
    </footer>

    <!-- Staff Login Modal -->
    <LoginModal 
      :is-open="isLoginModalOpen"
      @close="isLoginModalOpen = false"
      @login-success="handleLoginSuccess"
    />

    <!-- WEMI Work Detail Modal -->
    <WemiWorkDetailModal 
      :is-open="isWorkDetailOpen"
      :work="selectedWork"
      :branches="branches"
      :current-branch-id="currentBranchId"
      @close="isWorkDetailOpen = false"
      @request-ill="handleOpenIllFromModal"
    />

    <!-- Interlibrary Loan (ILL) Modal -->
    <IllTransferModal 
      :is-open="isIllModalOpen"
      :target-data="illTargetData"
      :branches="branches"
      :current-branch-id="currentBranchId"
      @close="isIllModalOpen = false"
      @confirm-transfer="handleConfirmIllTransfer"
    />

    <!-- New Work Cataloging Modal for Staff -->
    <NewWorkModal 
      :is-open="isNewWorkModalOpen"
      :branches="branches"
      @close="isNewWorkModalOpen = false"
      @create-work="handleCreateNewWork"
    />

    <!-- Edit Work Cataloging Modal for Staff -->
    <EditWorkModal 
      :is-open="isEditWorkModalOpen"
      :work="editingWork"
      @close="isEditWorkModalOpen = false"
      @update-work="handleUpdateWork"
    />

    <!-- Paper Toast Notifications -->
    <PaperToast />
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { apiService } from './services/api';

import CatalogHeader from './components/CatalogHeader.vue';
import ReaderPortal from './views/ReaderPortal.vue';
import StaffPortal from './views/StaffPortal.vue';
import LoginModal from './components/LoginModal.vue';
import WemiWorkDetailModal from './components/WemiWorkDetailModal.vue';
import IllTransferModal from './components/IllTransferModal.vue';
import NewWorkModal from './components/NewWorkModal.vue';
import EditWorkModal from './components/EditWorkModal.vue';
import PaperToast from './components/PaperToast.vue';

import { useRouter, useRoute } from 'vue-router';
import { useAuth } from './composables/useAuth';
import { useToast } from './composables/useToast';

const router = useRouter();
const route = useRoute();
const { currentUser, login: authLogin, logout: authLogout } = useAuth();
const toast = useToast();

const activePortal = ref('reader');

watch(() => route.path, (newPath) => {
  if (newPath.startsWith('/staff') || newPath.startsWith('/admin') || newPath.startsWith('/catalog') || newPath.startsWith('/transfers')) {
    activePortal.value = 'staff';
  } else if (newPath === '/') {
    activePortal.value = 'reader';
  }
}, { immediate: true });
const branches = ref([]);
const currentBranchId = ref(1);
const works = ref([]);
const searchQuery = ref('');
const selectedBranch = ref('all');
const selectedStatus = ref('all');
const activeSubjects = ref([]);
const illTransfers = ref([]);

// Modals State
const isLoginModalOpen = ref(false);
const isWorkDetailOpen = ref(false);
const selectedWork = ref({});
const isIllModalOpen = ref(false);
const illTargetData = ref(null);
const isNewWorkModalOpen = ref(false);
const isEditWorkModalOpen = ref(false);
const editingWork = ref(null);

const addToast = (opts) => {
  toast.add(opts);
};

const computeAvailableCount = (expressions = []) => {
  let count = 0;
  expressions.forEach(expr => {
    expr.manifestations?.forEach(manif => {
      manif.items?.forEach(item => {
        const s = String(item?.status || '').toLowerCase();
        if (s === 'available' || s === 'disponible') {
          count++;
        }
      });
    });
  });
  return count;
};

const normalizeWork = (w) => {
  const expressions = w.expressions || [];
  return {
    id: w.id,
    title: w.title,
    author: w.author || w.authors?.map(a => a.name ?? a).join(', ') || 'Autor Intelectual',
    abstract: w.abstract || 'Resumen de obra catalogada.',
    originalLanguage: w.originalLanguage || w.original_language || 'Español',
    dewey: w.dewey || null,
    nature: w.nature || 'Obra Literaria',
    subjects: w.subjects?.map(s => s.name ?? s) || ['Literatura'],
    expressions,
    branches: w.branches || [],
    availableCount: typeof w.availableCount === 'number' ? w.availableCount : computeAvailableCount(expressions)
  };
};

const handleNavigate = (portal) => {
  if (portal === 'staff' && !currentUser.value) {
    isLoginModalOpen.value = true;
    return;
  }
  activePortal.value = portal;
  if (portal === 'reader') {
    router.push('/');
  } else if (portal === 'staff') {
    router.push('/staff');
  }
};

const handleLoginSuccess = (payload) => {
  const user = payload.user || payload;
  authLogin(user, payload.token || null);
  activePortal.value = 'staff';
  router.push('/staff');
  addToast({
    title: 'Sesión Iniciada',
    message: `Bienvenido(a), ${user.name}. Has accedido al área de personal.`,
    type: 'success',
    tag: 'Autenticación'
  });
};

const handleLogout = () => {
  authLogout();
  activePortal.value = 'reader';
  router.push('/');
  addToast({
    title: 'Sesión Finalizada',
    message: 'Has salido del área de personal. Retornando al catálogo público.',
    type: 'info',
    tag: 'Autenticación'
  });
};

const handleChangeBranch = (branchId) => {
  currentBranchId.value = branchId;
  const branchObj = branches.value.find(b => b.id === branchId);
  if (branchObj) {
    addToast({
      title: 'Sede Preferida Cambiada',
      message: `Has fijado tu contexto a ${branchObj.name}.`,
      type: 'success',
      tag: 'Sede'
    });
  }
};

const toggleSubjectFilter = (subject) => {
  if (activeSubjects.value.includes(subject)) {
    activeSubjects.value = activeSubjects.value.filter(s => s !== subject);
  } else {
    activeSubjects.value.push(subject);
  }
};

const clearAllFilters = () => {
  searchQuery.value = '';
  selectedBranch.value = 'all';
  selectedStatus.value = 'all';
  activeSubjects.value = [];
};

const openWorkDetail = (work) => {
  selectedWork.value = work;
  isWorkDetailOpen.value = true;
};

const openIllModal = (work) => {
  illTargetData.value = { work };
  isIllModalOpen.value = true;
};

const handleOpenIllFromModal = (data) => {
  illTargetData.value = data;
  isIllModalOpen.value = true;
};

const handleConfirmIllTransfer = (transferPayload) => {
  const trackingCode = `ILL-2026-${Math.floor(1000 + Math.random() * 9000)}`;
  illTransfers.value.unshift({
    id: Date.now(),
    trackingCode,
    workTitle: transferPayload.work?.title || 'Obra Solicitada',
    originBranch: transferPayload.originBranch,
    targetBranch: transferPayload.targetBranch,
    date: 'Justo ahora'
  });

  addToast({
    title: `Préstamo ILL Solicitado (${trackingCode})`,
    message: `Despacho programado hacia la sede seleccionada.`,
    type: 'ill',
    tag: 'Transferencia'
  });

  isIllModalOpen.value = false;
};

const handleNotifyAvailability = (payload) => {
  const work = payload.work || payload;
  const isSubscribed = payload.subscribed !== undefined ? payload.subscribed : true;

  if (isSubscribed) {
    addToast({
      title: 'Alerta de Disponibilidad Activada',
      message: `Te notificaremos por correo apenas existan ejemplares disponibles de "${work.title}".`,
      type: 'success',
      tag: 'Disponibilidad'
    });
  } else {
    addToast({
      title: 'Alerta Cancelada',
      message: `Has cancelado la suscripción de disponibilidad para "${work.title}".`,
      type: 'info',
      tag: 'Disponibilidad'
    });
  }
};

const isLoadingWorks = ref(true);

const loadWorksFromApi = async () => {
  isLoadingWorks.value = true;
  try {
    const res = await apiService.getWorks();
    if (res.data) {
      works.value = res.data.map(normalizeWork);
    } else {
      works.value = [];
    }

    const branchRes = await apiService.getBranches();
    if (branchRes.data) {
      branches.value = branchRes.data;
    }
  } catch {
    works.value = [];
    branches.value = [];
  } finally {
    isLoadingWorks.value = false;
  }
};

const handleCreateNewWork = async (newWorkData) => {
  try {
    const res = await apiService.createWork(newWorkData);
    if (res.data) {
      works.value.unshift(normalizeWork(res.data));
      addToast({
        title: 'Obra Catalogada',
        message: `"${newWorkData.title}" ha sido registrada.`,
        type: 'success',
        tag: 'Catalogación'
      });
    }
  } catch {
    addToast({
      title: 'Error de Catalogación',
      message: 'No fue posible registrar la obra en la base de datos.',
      type: 'error',
      tag: 'Catalogación'
    });
  } finally {
    isNewWorkModalOpen.value = false;
  }
};

const handleOpenEditModal = (work) => {
  editingWork.value = work;
  isEditWorkModalOpen.value = true;
};

const handleUpdateWork = async ({ id, updatedData }) => {
  try {
    const res = await apiService.updateWork(id, updatedData);
    const idx = works.value.findIndex(w => w.id === id);
    if (idx !== -1) {
      if (res?.data) {
        works.value[idx] = normalizeWork(res.data);
      } else {
        works.value[idx].title = updatedData.title;
        works.value[idx].author = updatedData.author;
        works.value[idx].dewey = updatedData.dewey;
        works.value[idx].abstract = updatedData.abstract;
        works.value[idx].originalLanguage = updatedData.original_language;
      }
    }
    addToast({
      title: 'Obra Actualizada',
      message: `Los cambios para "${updatedData.title}" fueron guardados correctamente.`,
      type: 'success',
      tag: 'Catalogación'
    });
  } catch {
    addToast({
      title: 'Error al Guardar',
      message: 'No se pudieron actualizar los cambios en la base de datos.',
      type: 'error',
      tag: 'Catalogación'
    });
  } finally {
    isEditWorkModalOpen.value = false;
  }
};

const handleDeleteWork = async (workId) => {
  try {
    await apiService.deleteWork(workId);
    works.value = works.value.filter(w => w.id !== workId);
    addToast({
      title: 'Obra Eliminada',
      message: `La obra #${workId} ha sido eliminada del catálogo.`,
      type: 'info',
      tag: 'Catalogación'
    });
  } catch {
    addToast({
      title: 'Error al Eliminar',
      message: 'No se pudo eliminar la obra seleccionada.',
      type: 'error',
      tag: 'Catalogación'
    });
  }
};

const handleProcessLoan = async ({ barcode, userId }) => {
  try {
    await apiService.createLoan({
      item_barcode: barcode,
      user_identifier: userId,
      branch_id: currentBranchId.value
    });
    addToast({
      title: 'Préstamo Registrado',
      message: `El ejemplar ${barcode} ha sido prestado exitosamente.`,
      type: 'success',
      tag: 'Circulación'
    });
    await loadWorksFromApi();
  } catch (err) {
    addToast({
      title: 'Error de Circulación',
      message: err.data?.message || err.message || 'No fue posible registrar el préstamo.',
      type: 'error',
      tag: 'Circulación'
    });
  }
};

const handleProcessReturn = async ({ barcode }) => {
  try {
    addToast({
      title: 'Devolución Confirmada',
      message: `El ejemplar ${barcode} ha sido recibido e ingresado al estante.`,
      type: 'success',
      tag: 'Circulación'
    });
    await loadWorksFromApi();
  } catch (err) {
    addToast({
      title: 'Error al Devolver',
      message: err.data?.message || err.message || 'No fue posible confirmar la devolución.',
      type: 'error',
      tag: 'Circulación'
    });
  }
};

onMounted(() => {
  loadWorksFromApi();
});
</script>
