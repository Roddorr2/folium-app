import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import ConfirmModal from '../ConfirmModal.vue';

describe('ConfirmModal Component - WAI-ARIA and Event Emissions', () => {
  it('renders with role="dialog" and aria-modal="true" when isOpen=true', () => {
    const wrapper = mount(ConfirmModal, {
      props: {
        isOpen: true,
        title: '¿Confirmar Acción Crítica?',
        message: 'Esta acción modificará los registros del sistema.',
        confirmLabel: 'Aceptar',
        cancelLabel: 'Cancelar',
        isDestructive: true
      },
      global: {
        stubs: {
          Teleport: true
        }
      }
    });

    const dialog = wrapper.find('[role="dialog"]');
    expect(dialog.exists()).toBe(true);
    expect(dialog.attributes('aria-modal')).toBe('true');
    expect(dialog.attributes('aria-labelledby')).toBeDefined();
    expect(dialog.attributes('aria-describedby')).toBeDefined();
    expect(wrapper.text()).toContain('¿Confirmar Acción Crítica?');
    expect(wrapper.text()).toContain('Esta acción modificará los registros del sistema.');
  });

  it('emits cancel event when cancel button is clicked', async () => {
    const wrapper = mount(ConfirmModal, {
      props: {
        isOpen: true,
        title: '¿Deseas continuar?',
        message: 'Confirmación requerida.',
        cancelLabel: 'Cancelar Operación'
      },
      global: {
        stubs: {
          Teleport: true
        }
      }
    });

    const cancelButton = wrapper.findAll('button').find(b => b.text().includes('Cancelar Operación'));
    expect(cancelButton).toBeDefined();

    await cancelButton?.trigger('click');

    expect(wrapper.emitted('cancel')).toBeTruthy();
    expect(wrapper.emitted('cancel')?.length).toBe(1);
  });

  it('emits confirm event when confirmation button is clicked', async () => {
    const wrapper = mount(ConfirmModal, {
      props: {
        isOpen: true,
        title: '¿Eliminar elemento?',
        message: 'No se podrá deshacer.',
        confirmLabel: 'Eliminar Definitivamente',
        isDestructive: true
      },
      global: {
        stubs: {
          Teleport: true
        }
      }
    });

    const confirmButton = wrapper.findAll('button').find(b => b.text().includes('Eliminar Definitivamente'));
    expect(confirmButton).toBeDefined();

    await confirmButton?.trigger('click');

    expect(wrapper.emitted('confirm')).toBeTruthy();
    expect(wrapper.emitted('confirm')?.length).toBe(1);
  });

  it('emits cancel event when pressing Escape key (Focus Trap Integration)', async () => {
    const wrapper = mount(ConfirmModal, {
      props: {
        isOpen: true,
        title: '¿Cerrar con Escape?',
        message: 'Prueba de teclado accesibilidad.'
      },
      global: {
        stubs: {
          Teleport: true
        }
      }
    });

    window.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape' }));

    expect(wrapper.emitted('cancel')).toBeTruthy();
  });
});
