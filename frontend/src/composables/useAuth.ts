import { ref, computed, type ComputedRef, type Ref } from 'vue';
import type { User, RoleKey } from '../modules/identity/types/auth.types';

// Reactive global state for current authenticated user
const currentUser: Ref<User | null> = ref(
  JSON.parse(localStorage.getItem('folium_user') || 'null')
);

export function useAuth() {
  const isAuthenticated: ComputedRef<boolean> = computed(() => currentUser.value !== null);

  const userRoleKey: ComputedRef<RoleKey | string> = computed(() => {
    if (!currentUser.value) return 'reader';
    if (typeof currentUser.value.role === 'string') return currentUser.value.role;
    return currentUser.value.role?.key || currentUser.value.role?.name || 'reader';
  });

  const userRoleLabel: ComputedRef<string> = computed(() => {
    if (!currentUser.value) return 'Lector';
    return currentUser.value.role?.display_name || 'Lector';
  });

  const userRoleShortLabel: ComputedRef<string> = computed(() => {
    if (!currentUser.value) return 'Lector';
    return currentUser.value.role?.short_label || 'Lector';
  });

  const userShortName: ComputedRef<string> = computed(() => {
    if (!currentUser.value) return '';
    return currentUser.value.short_name || currentUser.value.name || '';
  });

  const userInitials: ComputedRef<string> = computed(() => {
    if (!currentUser.value) return 'U';
    return currentUser.value.initials || 'U';
  });

  const userBranchId: ComputedRef<number | null> = computed(() => {
    return currentUser.value?.branch_id ?? null;
  });

  /**
   * Check if current user has permission based on allowed roles array or single role string.
   */
  const canAccess = (allowedRoles?: (RoleKey | string)[] | RoleKey | string): boolean => {
    if (!allowedRoles) return true;
    const rolesArray = Array.isArray(allowedRoles) ? allowedRoles : [allowedRoles];
    if (rolesArray.length === 0) return true;
    if (!isAuthenticated.value) return false;

    // Admin has full access across all domains
    if (userRoleKey.value === 'admin') return true;

    return rolesArray.includes(userRoleKey.value);
  };

  const login = (userData: User, token: string | null = null): void => {
    currentUser.value = userData;
    localStorage.setItem('folium_user', JSON.stringify(userData));
    if (token) {
      localStorage.setItem('folium_token', token);
    }
  };

  const logout = (): void => {
    currentUser.value = null;
    localStorage.removeItem('folium_user');
    localStorage.removeItem('folium_token');
  };

  return {
    currentUser,
    isAuthenticated,
    userRole: userRoleKey,
    userRoleKey,
    userRoleLabel,
    userRoleShortLabel,
    userShortName,
    userInitials,
    userBranchId,
    canAccess,
    login,
    logout
  };
}
