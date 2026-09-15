import { watch, onUnmounted, nextTick, type Ref } from 'vue';

const FOCUSABLE_SELECTOR = 'button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

export function useFocusTrap(
  containerRef: Ref<HTMLElement | null>,
  isOpen: Ref<boolean>,
  onClose?: () => void
) {
  let previouslyFocusedElement: HTMLElement | null = null;

  const getFocusableElements = (): HTMLElement[] => {
    if (!containerRef.value) return [];
    return (Array.from(containerRef.value.querySelectorAll(FOCUSABLE_SELECTOR)) as HTMLElement[])
      .filter(el => el.offsetWidth > 0 || el.offsetHeight > 0 || el === document.activeElement);
  };

  const handleKeyDown = (e: KeyboardEvent): void => {
    if (!isOpen.value || !containerRef.value) return;

    if (e.key === 'Escape') {
      e.preventDefault();
      if (typeof onClose === 'function') {
        onClose();
      }
      return;
    }

    if (e.key === 'Tab') {
      const focusables = getFocusableElements();
      if (focusables.length === 0) {
        e.preventDefault();
        containerRef.value.focus();
        return;
      }

      const firstEl = focusables[0];
      const lastEl = focusables[focusables.length - 1];

      if (e.shiftKey) {
        if (document.activeElement === firstEl || document.activeElement === containerRef.value) {
          e.preventDefault();
          lastEl.focus();
        }
      } else {
        if (document.activeElement === lastEl) {
          e.preventDefault();
          firstEl.focus();
        }
      }
    }
  };

  const activateTrap = async (): Promise<void> => {
    previouslyFocusedElement = document.activeElement as HTMLElement | null;
    window.addEventListener('keydown', handleKeyDown);

    await nextTick();

    if (containerRef.value) {
      const focusables = getFocusableElements();
      if (focusables.length > 0) {
        focusables[0].focus();
      } else {
        containerRef.value.focus();
      }
    }
  };

  const deactivateTrap = (): void => {
    window.removeEventListener('keydown', handleKeyDown);

    if (previouslyFocusedElement && typeof previouslyFocusedElement.focus === 'function') {
      try {
        previouslyFocusedElement.focus();
      } catch {
        // Element might be unmounted
      }
      previouslyFocusedElement = null;
    }
  };

  watch(isOpen, (newVal) => {
    if (newVal) {
      activateTrap();
    } else {
      deactivateTrap();
    }
  }, { immediate: true });

  onUnmounted(() => {
    deactivateTrap();
  });

  return {
    activateTrap,
    deactivateTrap
  };
}
