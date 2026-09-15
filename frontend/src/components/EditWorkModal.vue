<template>
  <Teleport to="body">
    <div 
      v-if="isOpen" 
      ref="modalRef"
      class="fixed inset-0 z-50 flex items-end md:items-center justify-center p-0 sm:p-4 md:p-6 overflow-hidden bg-[#1C241E]/60 backdrop-blur-sm transition-all motion-reduce:transition-none"
      role="dialog"
      aria-modal="true"
      aria-labelledby="edit-work-modal-title"
      tabindex="-1"
      @click.self="$emit('close')"
    >
      <!-- Bottom Sheet on Mobile (< md) / Centered Modal on Desktop (>= md) -->
      <div class="bg-[#F9F6F0] rounded-t-2xl md:rounded-2xl rounded-b-none md:rounded-b-2xl max-w-2xl w-full max-h-[90dvh] md:max-h-[85vh] flex flex-col border-t-2 md:border border-[#E3DAC9] shadow-paper-lg overflow-hidden animate-in slide-in-from-bottom-6 md:slide-in-from-bottom-0 md:zoom-in-95 duration-200 motion-reduce:animate-none">
        <!-- Drag Handle for Mobile Bottom Sheet -->
        <div class="w-12 h-1.5 bg-[#D8CEBC] rounded-full mx-auto my-1.5 md:hidden shrink-0"></div>

        <!-- Top Laurel Accent Line -->
        <div class="h-2 bg-[#2D5A3F] w-full shrink-0"></div>

        <!-- Editorial Header -->
        <div class="p-4 sm:p-6 pb-4 border-b border-[#E3DAC9] flex items-start justify-between gap-4 bg-[#F3EFE6] shrink-0">
          <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-[#2D5A3F] text-[#F9F6F0] flex items-center justify-center shadow-sm shrink-0">
              <Pencil class="w-5 h-5 stroke-[1.75]" />
            </div>
            <div>
              <h3 id="edit-work-modal-title" class="text-xl sm:text-2xl font-bold font-serif tracking-tight text-[#1C241E]">
                Editar Obra
              </h3>
              <p class="text-xs text-[#4A584E] font-sans font-medium mt-0.5">
                Modificar registro de catálogo #{{ work?.id }}
              </p>
            </div>
          </div>

          <button 
            type="button"
            @click="$emit('close')"
            class="p-2.5 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-lg text-[#7D8D81] hover:text-[#1C241E] hover:bg-[#EAE4D7] transition-all focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]"
            aria-label="Cerrar modal de edición de obra"
            title="Cerrar modal (Esc)"
          >
            <X class="w-5 h-5" />
          </button>
        </div>

        <!-- Form Body with Vue 3 Reactive Validation -->
        <form novalidate @submit.prevent="handleSubmit" class="p-4 sm:p-6 overflow-y-auto space-y-6 text-xs text-[#1C241E] font-sans flex-1">
          <!-- Section Container -->
          <div class="space-y-5 bg-[#F3EFE6] p-4 sm:p-5 rounded-xl border border-[#E3DAC9]">
            <h4 class="font-semibold text-xs uppercase tracking-wider text-[#2D5A3F] font-sans flex items-center gap-2">
              <BookOpen class="w-4 h-4 text-[#2D5A3F]" />
              <span>Datos Generales de la Obra</span>
            </h4>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <!-- Title Field -->
              <div class="space-y-1.5">
                <label for="edit-work-title-input" class="block font-semibold text-xs text-[#1C241E] font-sans">Título de la obra *</label>
                <input 
                  id="edit-work-title-input"
                  v-model="form.title"
                  @blur="touchField('title')"
                  @input="touchField('title')"
                  type="text" 
                  aria-required="true"
                  :aria-invalid="!!(errors.title && touched.title)"
                  :aria-describedby="errors.title && touched.title ? 'edit-work-title-error' : undefined"
                  placeholder="Título de la obra..."
                  :class="[
                    'w-full p-3 rounded-lg bg-[#FBF9F4] border text-base md:text-xs font-serif italic font-semibold text-[#1C241E] transition-all focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]',
                    errors.title && touched.title 
                      ? 'border-[#8C433E] focus:ring-2 focus:ring-[#8C433E]/20' 
                      : 'border-[#E2DCCF] focus:ring-2 focus:ring-[#2D5A3F]/20 focus:border-[#2D5A3F]'
                  ]"
                />
                <span v-if="errors.title && touched.title" id="edit-work-title-error" role="alert" class="text-[#8C433E] text-[11px] font-medium block animate-in fade-in duration-150">
                  {{ errors.title }}
                </span>
              </div>

              <!-- Author Field -->
              <div class="space-y-1.5">
                <label for="edit-work-author-input" class="block font-semibold text-xs text-[#1C241E] font-sans">Autoría / Creador(es) *</label>
                <input 
                  id="edit-work-author-input"
                  v-model="form.author"
                  @blur="touchField('author')"
                  @input="touchField('author')"
                  type="text" 
                  aria-required="true"
                  :aria-invalid="!!(errors.author && touched.author)"
                  :aria-describedby="errors.author && touched.author ? 'edit-work-author-error' : undefined"
                  placeholder="Nombre de los autores..."
                  :class="[
                    'w-full p-3 rounded-lg bg-[#FBF9F4] border text-base md:text-xs font-sans text-[#1C241E] transition-all focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]',
                    errors.author && touched.author 
                      ? 'border-[#8C433E] focus:ring-2 focus:ring-[#8C433E]/20' 
                      : 'border-[#E2DCCF] focus:ring-2 focus:ring-[#2D5A3F]/20 focus:border-[#2D5A3F]'
                  ]"
                />
                <span v-if="errors.author && touched.author" id="edit-work-author-error" role="alert" class="text-[#8C433E] text-[11px] font-medium block animate-in fade-in duration-150">
                  {{ errors.author }}
                </span>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <!-- Dewey Field -->
              <div class="space-y-1.5">
                <label for="edit-work-dewey-input" class="block font-semibold text-xs text-[#1C241E] font-sans">Clasificación Dewey (CDD)</label>
                <input 
                  id="edit-work-dewey-input"
                  v-model="form.dewey"
                  type="text" 
                  placeholder="Ej. 985.02 G21c"
                  class="w-full p-3 rounded-lg bg-[#FBF9F4] border border-[#E2DCCF] focus:ring-2 focus:ring-[#2D5A3F]/20 focus:border-[#2D5A3F] focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F] text-base md:text-xs font-mono text-[#1C241E] transition-all"
                />
              </div>

              <!-- Language Dropdown -->
              <div class="space-y-1.5">
                <label for="edit-work-lang-select" class="block font-semibold text-xs text-[#1C241E] font-sans">Idioma original</label>
                <select 
                  id="edit-work-lang-select"
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
              <label for="edit-work-abstract-input" class="block font-semibold text-xs text-[#1C241E] font-sans">Sinopsis / Resumen *</label>
              <textarea 
                id="edit-work-abstract-input"
                v-model="form.abstract"
                @blur="touchField('abstract')"
                @input="touchField('abstract')"
                rows="4"
                aria-required="true"
                :aria-invalid="!!(errors.abstract && touched.abstract)"
                :aria-describedby="errors.abstract && touched.abstract ? 'edit-work-abstract-error' : undefined"
                placeholder="Descripción detallada del contenido intelectual de la obra..."
                :class="[
                  'w-full p-3 rounded-lg bg-[#FBF9F4] border text-base md:text-xs font-sans text-[#1C241E] leading-relaxed min-h-[110px] resize-y transition-all focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]',
                  errors.abstract && touched.abstract 
                    ? 'border-[#8C433E] focus:ring-2 focus:ring-[#8C433E]/20' 
                    : 'border-[#E2DCCF] focus:ring-2 focus:ring-[#2D5A3F]/20 focus:border-[#2D5A3F]'
                ]"
              ></textarea>
              <span v-if="errors.abstract && touched.abstract" id="edit-work-abstract-error" role="alert" class="text-[#8C433E] text-[11px] font-medium block animate-in fade-in duration-150">
                {{ errors.abstract }}
              </span>
            </div>
          </div>

          <!-- Sticky Footer Actions inside Form Anchored in Thumb Zone -->
          <div class="pt-4 border-t border-[#E3DAC9] bg-[#F9F6F0]/95 backdrop-blur-sm sticky bottom-0 z-20 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <span class="text-[11px] text-[#7D8D81] font-medium hidden sm:inline">
              * Campos requeridos para la actualización
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
                <Check v-else class="w-4 h-4" />
                <span>{{ isSubmitting ? 'Guardando...' : 'Guardar Cambios' }}</span>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted } from 'vue';
import { Pencil, X, BookOpen, Check } from 'lucide-vue-next';
import { apiService } from '../services/api';
import { useFocusTrap } from '../composables/useFocusTrap';

const props = defineProps({
  isOpen: { type: Boolean, default: false },
  work: { type: Object, default: () => ({}) }
});

const emit = defineEmits(['close', 'update-work']);

const modalRef = ref(null);
useFocusTrap(modalRef, computed(() => props.isOpen), () => emit('close'));

const availableLanguages = ref([]);
const isSubmitting = ref(false);

const form = ref({
  title: '',
  author: '',
  dewey: '',
  originalLanguage: 'Español',
  abstract: ''
});

const touched = reactive({
  title: false,
  author: false,
  abstract: false
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

  return errs;
});

const isFormValid = computed(() => Object.keys(errors.value).length === 0);

const fetchLanguages = async () => {
  try {
    const response = await apiService.getLanguages();
    if (response && response.data) {
      availableLanguages.value = response.data;
    }
  } catch (error) {
    console.error('Error al cargar idiomas de la base de datos:', error);
  }
};

watch(() => props.work, (newWork) => {
  if (newWork) {
    form.value.title = newWork.title || '';
    form.value.author = newWork.author || '';
    form.value.dewey = newWork.dewey || '';
    form.value.originalLanguage = newWork.originalLanguage || 'Español';
    form.value.abstract = newWork.abstract || '';
    touched.title = false;
    touched.author = false;
    touched.abstract = false;
  }
}, { immediate: true });

onMounted(() => {
  fetchLanguages();
});

const handleSubmit = () => {
  touched.title = true;
  touched.author = true;
  touched.abstract = true;

  if (!isFormValid.value) return;

  emit('update-work', {
    id: props.work.id,
    updatedData: {
      title: form.value.title.trim(),
      author: form.value.author.trim(),
      dewey: form.value.dewey.trim(),
      original_language: form.value.originalLanguage,
      abstract: form.value.abstract.trim()
    }
  });

  emit('close');
};
</script>
