import { ref, type Ref } from 'vue';

export type ToastType = 'success' | 'error' | 'warning' | 'info' | 'ill' | 'reserve';

export interface ToastOptions {
  id?: string | number;
  type?: ToastType;
  title?: string;
  message: string;
  tag?: string;
  duration?: number;
}

export interface ToastItem {
  id: string;
  type: ToastType;
  title: string;
  message: string;
  tag: string;
  timestamp: string;
  duration: number;
}

// Global reactive toasts list
const toasts: Ref<ToastItem[]> = ref([]);

export function useToast() {
  const add = (options: ToastOptions | string): string => {
    const opts: ToastOptions = typeof options === 'string' ? { message: options } : options;
    const id = opts.id ? String(opts.id) : `toast-${Date.now()}-${Math.random().toString(36).substring(2, 7)}`;
    const type: ToastType = opts.type || 'info';
    const duration = opts.duration ?? 4000;

    const defaultTitleByType: Record<ToastType, string> = {
      success: 'Operación Exitosa',
      error: 'Error del Sistema',
      warning: 'Advertencia de Seguridad',
      info: 'Notificación',
      ill: 'Préstamo Interbibliotecario',
      reserve: 'Reserva de Ejemplar'
    };

    const defaultTagByType: Record<ToastType, string> = {
      success: 'Confirmación',
      error: 'Seguridad / Error',
      warning: 'Restricción',
      info: 'Información',
      ill: 'Transferencia',
      reserve: 'Disponibilidad'
    };

    const newToast: ToastItem = {
      id,
      type,
      title: opts.title || defaultTitleByType[type],
      message: opts.message,
      tag: opts.tag || defaultTagByType[type],
      timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
      duration
    };

    // Keep max 5 visible toasts at a time
    toasts.value.unshift(newToast);
    if (toasts.value.length > 5) {
      toasts.value.pop();
    }

    if (duration > 0) {
      setTimeout(() => {
        dismiss(id);
      }, duration);
    }

    return id;
  };

  const dismiss = (id: string | number): void => {
    const strId = String(id);
    toasts.value = toasts.value.filter(t => t.id !== strId);
  };

  const clear = (): void => {
    toasts.value = [];
  };

  const success = (message: string, title?: string, tag?: string, duration?: number) =>
    add({ type: 'success', message, title, tag, duration });

  const error = (message: string, title?: string, tag?: string, duration?: number) =>
    add({ type: 'error', message, title, tag, duration });

  const warning = (message: string, title?: string, tag?: string, duration?: number) =>
    add({ type: 'warning', message, title, tag, duration });

  const info = (message: string, title?: string, tag?: string, duration?: number) =>
    add({ type: 'info', message, title, tag, duration });

  return {
    toasts,
    add,
    dismiss,
    clear,
    success,
    error,
    warning,
    info
  };
}
