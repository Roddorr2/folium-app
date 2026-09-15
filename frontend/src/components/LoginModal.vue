<template>
  <Teleport to="body">
    <div 
      v-if="isOpen" 
      ref="modalRef"
      class="fixed inset-0 z-50 flex items-end md:items-center justify-center p-0 sm:p-4 md:p-6 overflow-hidden bg-[#141A16]/70 backdrop-blur-md transition-all motion-reduce:transition-none"
      role="dialog"
      aria-modal="true"
      aria-labelledby="login-modal-title"
      tabindex="-1"
      @click.self="$emit('close')"
    >
      <div class="paper-card w-full max-w-md rounded-t-3xl md:rounded-2xl rounded-b-none md:rounded-b-2xl p-6 sm:p-8 border-t md:border border-[#E3DAC9] shadow-2xl relative space-y-6 animate-in slide-in-from-bottom-full md:slide-in-from-bottom-0 md:zoom-in-95 duration-300 ease-out max-h-[90dvh] md:max-h-[85vh] flex flex-col overflow-y-auto">
        <!-- Drag Handle for Mobile Bottom Sheet -->
        <div class="w-12 h-1.5 bg-[#C5BBAA] rounded-full mx-auto mb-1 md:hidden shrink-0"></div>
        
        <!-- Modal Header -->
        <div class="flex items-start justify-between border-b border-[#E3DAC9]/80 pb-4">
          <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-[#2D5A3F] text-[#F9F6F0] flex items-center justify-center font-bold text-lg border border-[#224430] shrink-0 shadow-sm">
              <Lock class="w-5 h-5" />
            </div>
            <div>
              <h3 id="login-modal-title" class="text-xl font-bold font-serif text-[#1C241E] tracking-tight">Acceso de Personal</h3>
              <p class="text-xs text-[#4A584E] font-sans mt-0.5">Ingresa tus credenciales para acceder al área de empleados.</p>
            </div>
          </div>
          <button 
            type="button"
            @click="$emit('close')" 
            class="text-[#7D8D81] hover:text-[#1C241E] hover:bg-[#EBE5D8]/50 p-2 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-xl transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]"
            aria-label="Cerrar modal de inicio de sesión"
          >
            <X class="w-5 h-5" />
          </button>
        </div>

        <!-- Form Error Alert -->
        <div v-if="errorMessage" role="alert" class="p-3.5 rounded-xl bg-[#F9EDED] border border-[#8C433E]/30 text-xs text-[#8C433E] font-medium animate-in fade-in duration-150">
          {{ errorMessage }}
        </div>

        <!-- Login Form with Vue 3 Reactive Validation -->
        <form novalidate @submit.prevent="handleSubmit" class="space-y-4 font-sans">
          <div class="space-y-1.5">
            <label for="login-email" class="block text-xs font-semibold text-[#1C241E]">Correo electrónico *</label>
            <input 
              id="login-email"
              v-model="email" 
              @blur="touchField('email')"
              @input="touchField('email')"
              type="email" 
              aria-required="true"
              :aria-invalid="!!(errors.email && touched.email)"
              :aria-describedby="errors.email && touched.email ? 'login-email-error' : undefined"
              aria-label="Correo electrónico de usuario"
              placeholder="ej. bibliotecario@folium.pe" 
              :class="[
                'w-full px-4 py-3 rounded-xl bg-[#FBF9F4] border text-[#1C241E] text-base md:text-sm transition-all focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]',
                errors.email && touched.email 
                  ? 'border-[#8C433E] focus:ring-2 focus:ring-[#8C433E]/20' 
                  : 'border-[#E2DCCF] focus:ring-2 focus:ring-[#2D5A3F]/20 focus:border-[#2D5A3F]'
              ]"
            />
            <span v-if="errors.email && touched.email" id="login-email-error" role="alert" class="text-[#8C433E] text-[11px] font-medium block animate-in fade-in duration-150">
              {{ errors.email }}
            </span>
          </div>

          <div class="space-y-1.5">
            <label for="login-password" class="block text-xs font-semibold text-[#1C241E]">Contraseña *</label>
            <input 
              id="login-password"
              v-model="password" 
              @blur="touchField('password')"
              @input="touchField('password')"
              type="password" 
              aria-required="true"
              :aria-invalid="!!(errors.password && touched.password)"
              :aria-describedby="errors.password && touched.password ? 'login-password-error' : undefined"
              aria-label="Contraseña de usuario"
              placeholder="••••••••" 
              :class="[
                'w-full px-4 py-3 rounded-xl bg-[#FBF9F4] border text-[#1C241E] text-base md:text-sm transition-all focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F]',
                errors.password && touched.password 
                  ? 'border-[#8C433E] focus:ring-2 focus:ring-[#8C433E]/20' 
                  : 'border-[#E2DCCF] focus:ring-2 focus:ring-[#2D5A3F]/20 focus:border-[#2D5A3F]'
              ]"
            />
            <span v-if="errors.password && touched.password" id="login-password-error" role="alert" class="text-[#8C433E] text-[11px] font-medium block animate-in fade-in duration-150">
              {{ errors.password }}
            </span>
          </div>

          <button 
            type="submit" 
            :disabled="isSubmitting || !isFormValid"
            aria-label="Iniciar sesión de personal"
            class="w-full py-3.5 min-h-[48px] bg-[#2D5A3F] hover:bg-[#224430] text-[#F9F6F0] font-bold text-sm rounded-xl shadow-md transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2D5A3F] active:scale-[0.99] mt-2"
          >
            <span v-if="isSubmitting" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
            <span>Iniciar Sesión de Personal</span>
          </button>
        </form>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { Lock, X } from 'lucide-vue-next';
import { useFocusTrap } from '../composables/useFocusTrap';

const props = defineProps({
  isOpen: { type: Boolean, default: false }
});

const emit = defineEmits(['close', 'login-success']);

const modalRef = ref(null);
useFocusTrap(modalRef, computed(() => props.isOpen), () => emit('close'));

const email = ref('');
const password = ref('');
const isSubmitting = ref(false);
const errorMessage = ref('');

const touched = reactive({
  email: false,
  password: false
});

const touchField = (field) => {
  touched[field] = true;
};

const errors = computed(() => {
  const errs = {};
  if (!email.value || email.value.trim() === '') {
    errs.email = 'El correo electrónico es obligatorio.';
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
    errs.email = 'Ingresa un formato de correo electrónico válido.';
  }

  if (!password.value || password.value === '') {
    errs.password = 'La contraseña es obligatoria.';
  }

  return errs;
});

const isFormValid = computed(() => Object.keys(errors.value).length === 0);

const handleSubmit = async () => {
  touched.email = true;
  touched.password = true;

  if (!isFormValid.value) return;

  isSubmitting.value = true;
  errorMessage.value = '';

  try {
    const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api/v1';
    const response = await fetch(`${apiBaseUrl}/auth/login`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        email: email.value.trim(),
        password: password.value
      })
    });

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.message || 'Credenciales incorrectas');
    }

    emit('login-success', data);
    emit('close');
  } catch (err) {
    errorMessage.value = 'Credenciales no válidas. Verifica el usuario e intenta de nuevo.';
  } finally {
    isSubmitting.value = false;
  }
};
</script>
