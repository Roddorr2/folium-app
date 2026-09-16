import { describe, it, expect, beforeEach } from 'vitest';
import { useToast } from '../useToast';

describe('useToast Composable', () => {
  beforeEach(() => {
    const { clear } = useToast();
    clear();
  });

  it('queues a notification with id, default tag, title, and timestamp', () => {
    const { success, toasts } = useToast();

    const id = success('Ejemplar reservado exitosamente con código #442');

    expect(id).toBeDefined();
    expect(toasts.value.length).toBe(1);

    const item = toasts.value[0];
    expect(item.id).toBe(id);
    expect(item.type).toBe('success');
    expect(item.title).toBe('Operación Exitosa');
    expect(item.tag).toBe('Confirmación');
    expect(item.message).toBe('Ejemplar reservado exitosamente con código #442');
    expect(item.timestamp).toBeDefined();
  });

  it('enforces maximum queue limit of 5 simultaneous toasts', () => {
    const { info, toasts } = useToast();

    for (let i = 1; i <= 8; i++) {
      info(`Mensaje en cola número ${i}`, undefined, undefined, 0);
    }

    expect(toasts.value.length).toBe(5);
    // unshift puts latest at index 0, so index 0 is msg 8 and index 4 is msg 4
    expect(toasts.value[0].message).toBe('Mensaje en cola número 8');
    expect(toasts.value[4].message).toBe('Mensaje en cola número 4');
  });

  it('dismiss removes specified toast by id', () => {
    const { warning, dismiss, toasts } = useToast();

    const id1 = warning('Alerta 1', undefined, undefined, 0);
    const id2 = warning('Alerta 2', undefined, undefined, 0);

    expect(toasts.value.length).toBe(2);

    dismiss(id1);

    expect(toasts.value.length).toBe(1);
    expect(toasts.value[0].id).toBe(id2);
  });
});
