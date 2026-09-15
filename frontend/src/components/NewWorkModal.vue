<template>
  <Teleport to="body">
    <div 
      v-if="isOpen" 
      ref="modalRef"
      class="fixed inset-0 z-50 flex items-end md:items-center justify-center p-0 sm:p-4 md:p-6 overflow-hidden bg-[#1C241E]/60 backdrop-blur-sm transition-all motion-reduce:transition-none"
      role="dialog"
      aria-modal="true"
      aria-labelledby="new-work-modal-title"
      tabindex="-1"
      @click.self="$emit('close')"
    >
      <!-- Bottom Sheet on Mobile (< md) / Centered Modal on Desktop (>= md) -->
      <div class="bg-[#F9F6F0] rounded-t-2xl md:rounded-2xl rounded-b-none md:rounded-b-2xl max-w-2xl w-full max-h-[90dvh] md:max-h-[85vh] flex flex-col border-t-2 md:border border-[#E3DAC9] shadow-paper-lg overflow-hidden animate-in slide-in-from-bottom-6 md:slide-in-from-bottom-0 md:zoom-in-95 duration-200 motion-reduce:animate-none">
        <!-- Drag Handle for Mobile Bottom Sheet -->
        <div class="w-12 h-1.5 bg-[#D8CEBC] rounded-full mx-auto my-1.5 md:hidden shrink-0"></div>

        <!-- Top Laurel Border Accent -->
        <div class="h-2 bg-[#2D5A3F] w-full shrink-0"></div>

        <!-- Header -->
        <div class="p-4 sm:p-6 pb-4 border-b border-[#E3DAC9] flex items-start justify-between gap-4 bg-[#F3EFE6] shrink-0">
          <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-[#2D5A3F] text-[#F9F6F0] flex items-center justify-center shadow-sm shrink-0">
              <BookPlus class="w-5 h-5 stroke-[1.75]" />
            </div>
            <div>
              <h3 id="new-work-modal-title" class="text-xl sm:text-2xl font-bold font-serif text-[#1C241E]">
                Registrar Nueva Obra
              </h3>
              <p class="text-xs text-[#4A584E] font-sans font-medium">
                Alta de registro bibliográfico y asignación inicial por sedes
              </p>
            </div>
          </div>

          <button 
            type="button"
            @click="$emit('close')"
            class="p-2.5 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-lg text-[#7D8D81] hover:text-[#1C241E] hover:bg-[#EAE4D7] transition-all focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]"
            aria-label="Cerrar modal de registro de obra"
            title="Cerrar modal (Esc)"
          >
            <X class="w-5 h-5" />
          </button>
        </div>

        <!-- Form Content with Reactive Validation -->
        <form novalidate @submit.prevent="handleSubmit" class="p-4 sm:p-6 overflow-y-auto space-y-6 text-xs text-[#1C241E] font-sans flex-1">
          <!-- Work Section -->
          <div class="space-y-4 bg-[#F3EFE6] p-4 sm:p-5 rounded-xl border border-[#E3DAC9]">
            <h4 class="font-semibold text-xs uppercase tracking-wider text-[#2D5A3F] font-sans flex items-center gap-2">
              <BookOpen class="w-4 h-4 text-[#2D5A3F]" />
              <span>1. Datos Generales de la Obra</span>
            </h4>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <!-- Title Field -->
              <div class="space-y-1.5">
                <label for="new-work-title-input" class="block font-semibold text-xs text-[#1C241E] font-sans">Título de la obra *</label>
                <input 
                  id="new-work-title-input"
                  v-model="form.title"
                  @blur="touchField('title')"
                  @input="touchField('title')"
                  type="text" 
                  aria-required="true"
                  :aria-invalid="!!(errors.title && touched.title)"
                  :aria-describedby="errors.title && touched.title ? 'new-work-title-error' : undefined"
                  placeholder="Ej. Comentarios Reales de los Incas..."
                  :class="[
                    'w-full p-3 rounded-lg bg-[#FBF9F4] border text-base md:text-xs font-serif italic font-semibold text-[#1C241E] transition-all focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]',
                    errors.title && touched.title 
                      ? 'border-[#8C433E] focus:ring-2 focus:ring-[#8C433E]/20' 
                      : 'border-[#E2DCCF] focus:ring-2 focus:ring-[#2D5A3F]/20 focus:border-[#2D5A3F]'
                  ]"
                />
                <span v-if="errors.title && touched.title" id="new-work-title-error" role="alert" class="text-[#8C433E] text-[11px] font-medium block animate-in fade-in duration-150">
                  {{ errors.title }}
                </span>
              </div>

              <!-- Author Field -->
              <div class="space-y-1.5">
                <label for="new-work-author-input" class="block font-semibold text-xs text-[#1C241E] font-sans">Autoría / Creador(es) *</label>
                <input 
                  id="new-work-author-input"
                  v-model="form.author"
                  @blur="touchField('author')"
                  @input="touchField('author')"
                  type="text" 
                  aria-required="true"
                  :aria-invalid="!!(errors.author && touched.author)"
                  :aria-describedby="errors.author && touched.author ? 'new-work-author-error' : undefined"
                  placeholder="Ej. Inca Garcilaso de la Vega..."
                  :class="[
                    'w-full p-3 rounded-lg bg-[#FBF9F4] border text-base md:text-xs font-sans text-[#1C241E] transition-all focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]',
                    errors.author && touched.author 
                      ? 'border-[#8C433E] focus:ring-2 focus:ring-[#8C433E]/20' 
                      : 'border-[#E2DCCF] focus:ring-2 focus:ring-[#2D5A3F]/20 focus:border-[#2D5A3F]'
                  ]"
                />
                <span v-if="errors.author && touched.author" id="new-work-author-error" role="alert" class="text-[#8C433E] text-[11px] font-medium block animate-in fade-in duration-150">
                  {{ errors.author }}
                </span>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <!-- Dewey Field -->
              <div class="space-y-1.5">
                <label for="new-work-dewey-input" class="block font-semibold text-xs text-[#1C241E] font-sans font-bold">Clasificación Dewey (CDD)</label>
                <input 
                  id="new-work-dewey-input"
                  v-model="form.dewey"
                  type="text" 
                  placeholder="Ej. 985.02 G21c"
                  class="w-full p-3 rounded-lg bg-[#FBF9F4] border border-[#E2DCCF] focus:ring-2 focus:ring-[#2D5A3F]/20 focus:border-[#2D5A3F] focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F] text-base md:text-xs font-mono text-[#1C241E] transition-all"
                />
              </div>

              <!-- Language Field -->
              <div class="space-y-1.5">
                <label for="new-work-lang-select" class="block font-semibold text-xs text-[#1C241E] font-sans">Idioma original</label>
                <select 
                  id="new-work-lang-select"
                  v-model="form.originalLanguage"
                  class="w-full p-3 min-h-[44px] rounded-lg bg-[#FBF9F4] border border-[#E2DCCF] focus:ring-2 focus:ring-[#2D5A3F]/20 focus:border-[#2D5A3F] focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F] text-base md:text-xs font-sans font-medium text-[#1C241E] transition-all cursor-pointer"
                >
                  <option v-for="lang in availableLanguages" :key="lang.code || lang.native_name" :value="lang.native_name">
                    {{ lang.native_name }} ({{ lang.name }})
                  </option>
                </select>
              </div>
            </div>

            <!-- Synopsis / Abstract Textarea -->
            <div class="space-y-1.5">
              <label for="new-work-abstract-input" class="block font-semibold text-xs text-[#1C241E] font-sans font-bold">Sinopsis / Resumen *</label>
              <textarea 
                id="new-work-abstract-input"
                v-model="form.abstract"
                @blur="touchField('abstract')"
                @input="touchField('abstract')"
                rows="3"
                aria-required="true"
                :aria-invalid="!!(errors.abstract && touched.abstract)"
                :aria-describedby="errors.abstract && touched.abstract ? 'new-work-abstract-error' : undefined"
                placeholder="Descripción detallada del contenido intelectual de la obra..."
                :class="[
                  'w-full p-3 rounded-lg bg-[#FBF9F4] border text-base md:text-xs font-sans text-[#1C241E] leading-relaxed min-h-[90px] resize-y transition-all focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]',
                  errors.abstract && touched.abstract 
                    ? 'border-[#8C433E] focus:ring-2 focus:ring-[#8C433E]/20' 
                    : 'border-[#E2DCCF] focus:ring-2 focus:ring-[#2D5A3F]/20 focus:border-[#2D5A3F]'
                ]"
              ></textarea>
              <span v-if="errors.abstract && touched.abstract" id="new-work-abstract-error" role="alert" class="text-[#8C433E] text-[11px] font-medium block animate-in fade-in duration-150">
                {{ errors.abstract }}
              </span>
            </div>
          </div>

          <!-- Expression & Manifestation Section -->
          <div class="space-y-4 bg-[#F3EFE6] p-4 sm:p-5 rounded-xl border border-[#E3DAC9]">
            <h4 class="font-semibold text-xs uppercase tracking-wider text-[#C27D38] font-sans flex items-center gap-2">
              <Package class="w-4 h-4 text-[#C27D38]" />
              <span>2. Formato y Edición Inicial</span>
            </h4>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="space-y-1.5">
                <label for="new-work-format-select" class="block font-semibold text-xs text-[#1C241E] font-sans">Formato físico</label>
                <select 
                  id="new-work-format-select"
                  v-model="form.format"
                  class="w-full p-3 min-h-[44px] rounded-lg bg-[#FBF9F4] border border-[#E2DCCF] focus:ring-2 focus:ring-[#2D5A3F]/20 focus:border-[#2D5A3F] focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F] text-base md:text-xs font-sans font-medium text-[#1C241E] transition-all cursor-pointer"
                >
                  <option value="Tapa Dura en Lino">Tapa Dura en Lino</option>
                  <option value="Rústica Editorial">Rústica Editorial</option>
                  <option value="Facsímil Ilustrado Botánico">Facsímil Ilustrado Botánico</option>
                  <option value="Facsímil Digital HD">Facsímil Digital HD</option>
                  <option value="Audiolibro Narrado">Audiolibro Narrado</option>
                </select>
              </div>

              <div class="space-y-1.5">
                <label for="new-work-isbn-input" class="block font-semibold text-xs text-[#1C241E] font-sans">Código ISBN</label>
                <input 
                  id="new-work-isbn-input"
                  v-model="form.isbn"
                  @blur="touchField('isbn')"
                  @input="touchField('isbn')"
                  type="text" 
                  :aria-invalid="!!(errors.isbn && touched.isbn)"
                  :aria-describedby="errors.isbn && touched.isbn ? 'new-work-isbn-error' : undefined"
                  placeholder="Ej. 978-612-4000-01-9"
                  :class="[
                    'w-full p-3 rounded-lg bg-[#FBF9F4] border text-base md:text-xs font-mono text-[#1C241E] transition-all focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]',
                    errors.isbn && touched.isbn 
                      ? 'border-[#8C433E] focus:ring-2 focus:ring-[#8C433E]/20' 
                      : 'border-[#E2DCCF] focus:ring-2 focus:ring-[#2D5A3F]/20 focus:border-[#2D5A3F]'
                  ]"
                />
                <span v-if="errors.isbn && touched.isbn" id="new-work-isbn-error" role="alert" class="text-[#8C433E] text-[11px] font-medium block animate-in fade-in duration-150">
                  {{ errors.isbn }}
                </span>
              </div>
            </div>
          </div>

          <!-- Initial Item Inventory Assignation -->
          <div class="space-y-4 bg-[#E9F2EB] p-4 sm:p-5 rounded-xl border border-[#2D5A3F]/30">
            <h4 class="font-semibold text-xs uppercase tracking-wider text-[#3E6B48] font-sans flex items-center gap-2">
              <Building2 class="w-4 h-4 text-[#3E6B48]" />
              <span>3. Ejemplar Físico Inicial (Sede)</span>
            </h4>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="space-y-1.5">
                <label for="new-work-branch-select" class="block font-semibold text-xs text-[#1C241E] font-sans">Sede asignada *</label>
                <select 
                  id="new-work-branch-select"
                  v-model="form.initialBranchId"
                  class="w-full p-3 min-h-[44px] rounded-lg bg-[#FBF9F4] border border-[#E2DCCF] focus:ring-2 focus:ring-[#2D5A3F]/20 focus:border-[#2D5A3F] focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F] text-base md:text-xs font-sans font-medium text-[#1C241E] transition-all cursor-pointer"
                >
                  <option v-for="b in availableBranches" :key="b.id" :value="b.id">
                    {{ b.name }} {{ b.city ? `(${b.city})` : '' }}
                  </option>
                </select>
              </div>

              <div class="space-y-1.5">
                <label for="new-work-shelf-input" class="block font-semibold text-xs text-[#1C241E] font-sans">Ubicación en estante</label>
                <input 
                  id="new-work-shelf-input"
                  v-model="form.shelfLocation"
                  type="text" 
                  placeholder="Ej. BOT-SEC02 · Estante C-12"
                  class="w-full p-3 rounded-lg bg-[#FBF9F4] border border-[#E2DCCF] focus:ring-2 focus:ring-[#2D5A3F]/20 focus:border-[#2D5A3F] focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F] text-base md:text-xs font-mono text-[#1C241E] transition-all"
                />
              </div>
            </div>
          </div>

          <!-- Sticky Footer Actions inside Form Anchored in Thumb Zone -->
          <div class="pt-4 border-t border-[#E3DAC9] bg-[#F9F6F0]/95 backdrop-blur-sm sticky bottom-0 z-20 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <span class="text-[11px] text-[#7D8D81] font-medium hidden sm:inline">
              * Campos requeridos para la catalogación
            </span>
            <div class="flex items-center gap-3 w-full sm:w-auto">
              <button 
                type="button"
                @click="$emit('close')"
                class="flex-1 sm:flex-none px-4 py-3 min-h-[44px] rounded-xl text-[#4A584E] hover:bg-[#EAE4D7] font-sans font-medium text-xs transition-all flex items-center justify-center focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]"
              >
                Cancelar
              </button>
              <button 
                type="submit" 
                :disabled="!isFormValid || isSubmitting"
                class="flex-1 sm:flex-none px-6 py-3 min-h-[44px] rounded-xl bg-[#2D5A3F] hover:bg-[#224430] text-[#F9F6F0] font-sans font-semibold text-xs transition-all shadow-sm flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]"
              >
                <span v-if="isSubmitting" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                <Plus v-else class="w-4 h-4" />
                <span>{{ isSubmitting ? 'Guardando...' : 'Guardar en Catálogo' }}</span>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue';
import { BookPlus, X, BookOpen, Package, Building2, Plus } from 'lucide-vue-next';
import { apiService } from '../services/api';
import { useFocusTrap } from '../composables/useFocusTrap';

const props = defineProps({
  isOpen: { type: Boolean, default: false },
  branches: { type: Array, default: () => [] }
});

const emit = defineEmits(['close', 'create-work']);

const modalRef = ref(null);
useFocusTrap(modalRef, computed(() => props.isOpen), () => emit('close'));

const availableLanguages = ref([]);
const availableBranches = ref([]);
const isSubmitting = ref(false);

const form = ref({
  title: '',
  author: '',
  dewey: '',
  originalLanguage: 'Español',
  abstract: '',
  format: 'Tapa Dura en Lino',
  isbn: '978-612-4000-01-9',
  initialBranchId: 1,
  shelfLocation: 'BOT-NUEVOS · Estante A-01'
});

const touched = reactive({
  title: false,
  author: false,
  abstract: false,
  isbn: false
});

const touchField = (field) => {
  touched[field] = true;
};

const errors = computed(() => {
  const errs = {};
  if (!form.value.title || form.value.title.trim() === '') {
    errs.title = 'El título de la obra es obligatorio.';
  } else if (form.value.title.trim().length < 2) {
    errs.title = 'El título debe tener al menos 2 caracteres.';
  }

  if (!form.value.author || form.value.author.trim() === '') {
    errs.author = 'Debes indicar la autoría o creador(es).';
  }

  if (!form.value.abstract || form.value.abstract.trim() === '') {
    errs.abstract = 'La sinopsis o resumen es requerida.';
  }

  if (form.value.isbn && !/^(97(8|9))?[\d-]{9,17}\d$/i.test(form.value.isbn.trim())) {
    errs.isbn = 'Formato de ISBN no válido (ej. 978-612-4000-01-9).';
  }

  return errs;
});

const isFormValid = computed(() => Object.keys(errors.value).length === 0);

const fetchLanguages = async () => {
  try {
    const response = await apiService.getLanguages();
    if (response && response.data) {
      availableLanguages.value = response.data;
      if (response.data.length > 0 && !form.value.originalLanguage) {
        form.value.originalLanguage = response.data[0].native_name;
      }
    }
  } catch (error) {
    console.error('Error al obtener idiomas de la base de datos:', error);
  }
};

const fetchBranches = async () => {
  if (props.branches && props.branches.length > 0) {
    availableBranches.value = props.branches;
    if (!form.value.initialBranchId) {
      form.value.initialBranchId = props.branches[0].id;
    }
    return;
  }
  try {
    const response = await apiService.getBranches();
    if (response && response.data) {
      availableBranches.value = response.data;
      if (response.data.length > 0 && !form.value.initialBranchId) {
        form.value.initialBranchId = response.data[0].id;
      }
    }
  } catch (error) {
    console.error('Error al obtener sedes de la base de datos:', error);
  }
};

watch(() => props.branches, (newVal) => {
  if (newVal && newVal.length > 0) {
    availableBranches.value = newVal;
    if (!form.value.initialBranchId) {
      form.value.initialBranchId = newVal[0].id;
    }
  }
}, { immediate: true });

onMounted(() => {
  fetchLanguages();
  fetchBranches();
});

const handleSubmit = () => {
  // Mark all fields as touched to trigger errors if invalid
  touched.title = true;
  touched.author = true;
  touched.abstract = true;
  touched.isbn = true;

  if (!isFormValid.value) return;

  const targetBranch = availableBranches.value.find(b => b.id === form.value.initialBranchId) || availableBranches.value[0] || { id: 1, name: 'Sede Lima Central' };

  const newWorkData = {
    id: Date.now(),
    title: form.value.title.trim(),
    author: form.value.author.trim(),
    authors: [form.value.author.trim()],
    dewey: form.value.dewey.trim() || '580.0 GENERAL',
    originalLanguage: form.value.originalLanguage,
    abstract: form.value.abstract.trim(),
    subjects: ['Nuevas Adquisiciones', 'Catálogo Abierto'],
    yearRange: '2026',
    expressions: [
      {
        id: Date.now() + 1,
        title: 'Primera Expresión Catalogada',
        type: 'Texto Impreso',
        language: form.value.originalLanguage,
        revisionYear: 2026,
        description: 'Texto integro en edición estándar de catálogo.',
        manifestations: [
          {
            id: Date.now() + 2,
            format: form.value.format,
            isbn: form.value.isbn.trim(),
            publisher: 'Editorial Folium SIGB',
            publicationYear: 2026,
            dimensions: '24 x 17 cm, 350 pp.',
            notes: 'Catalogación reciente',
            items: [
              {
                id: Date.now() + 3,
                barcode: `FOL-${Math.floor(10000 + Math.random() * 90000)}`,
                branchId: targetBranch.id,
                branchName: targetBranch.name,
                shelfLocation: form.value.shelfLocation.trim(),
                status: 'Disponible',
                condition: 'Ejemplar nuevo'
              }
            ]
          }
        ]
      }
    ]
  };

  emit('create-work', newWorkData);
  emit('close');

  // Reset form
  form.value.title = '';
  form.value.author = '';
  form.value.abstract = '';
  touched.title = false;
  touched.author = false;
  touched.abstract = false;
  touched.isbn = false;
};
</script>
