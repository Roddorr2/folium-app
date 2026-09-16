import { describe, it, expect, beforeEach } from 'vitest';
import { useAuth } from '../useAuth';
import type { User } from '../../modules/identity/types/auth.types';

describe('useAuth Composable', () => {
  beforeEach(() => {
    localStorage.clear();
    const { logout } = useAuth();
    logout();
  });

  it('initial state without authenticated user has isAuthenticated=false and userRoleKey=reader', () => {
    const { isAuthenticated, userRoleKey, userRoleLabel, userRoleShortLabel, userShortName, userInitials } = useAuth();

    expect(isAuthenticated.value).toBe(false);
    expect(userRoleKey.value).toBe('reader');
    expect(userRoleLabel.value).toBe('Lector');
    expect(userRoleShortLabel.value).toBe('Lector');
    expect(userShortName.value).toBe('');
    expect(userInitials.value).toBe('U');
  });

  it('active session correctly maps UserResource and presentation helpers', () => {
    const { login, isAuthenticated, userRoleKey, userRoleLabel, userRoleShortLabel, userShortName, userInitials } = useAuth();

    const mockUser: User = {
      id: 10,
      name: 'Elena Vásquez Gómez',
      short_name: 'Elena V.',
      initials: 'EV',
      email: 'elena.vasquez@folium.org',
      role_id: 2,
      role: {
        id: 2,
        key: 'cataloger',
        name: 'cataloger',
        display_name: 'Jefe de Catalogación WEMI',
        short_label: 'Catalogador',
        badge_variant: 'emerald',
        description: 'Especialista en metadatos'
      },
      branch_id: 1,
      branch: {
        id: 1,
        name: 'Sede Central',
        city: 'Lima',
        address: 'Av. Abancay'
      }
    };

    login(mockUser, 'test-jwt-token');

    expect(isAuthenticated.value).toBe(true);
    expect(userRoleKey.value).toBe('cataloger');
    expect(userRoleLabel.value).toBe('Jefe de Catalogación WEMI');
    expect(userRoleShortLabel.value).toBe('Catalogador');
    expect(userShortName.value).toBe('Elena V.');
    expect(userInitials.value).toBe('EV');
  });

  it('canAccess allows admin unrestricted access across all permissions', () => {
    const { login, canAccess } = useAuth();

    const adminUser: User = {
      id: 1,
      name: 'Director Admin',
      email: 'admin@folium.org',
      role_id: 1,
      role: {
        id: 1,
        key: 'admin',
        name: 'admin',
        display_name: 'Administrador General',
        short_label: 'Admin',
        badge_variant: 'forest'
      }
    };

    login(adminUser);

    expect(canAccess(['cataloger'])).toBe(true);
    expect(canAccess(['librarian', 'network_librarian'])).toBe(true);
    expect(canAccess([])).toBe(true);
  });

  it('canAccess allows regular role only if present in allowedRoles', () => {
    const { login, canAccess } = useAuth();

    const librarianUser: User = {
      id: 3,
      name: 'Bibliotecario Sede',
      email: 'librarian@folium.org',
      role_id: 3,
      role: {
        id: 3,
        key: 'librarian',
        name: 'librarian',
        display_name: 'Bibliotecario de Sede',
        short_label: 'Bibliotecario',
        badge_variant: 'copper'
      }
    };

    login(librarianUser);

    expect(canAccess(['librarian', 'network_librarian'])).toBe(true);
    expect(canAccess(['cataloger'])).toBe(false);
    expect(canAccess(['admin'])).toBe(false);
  });

  it('persists and clears user and token in localStorage during login and logout', () => {
    const { login, logout } = useAuth();

    const user: User = {
      id: 5,
      name: 'Test Persist',
      email: 'persist@folium.test',
      role_id: 5,
      role: {
        id: 5,
        key: 'reader',
        name: 'reader',
        display_name: 'Lector',
        short_label: 'Lector',
        badge_variant: 'stone'
      }
    };

    login(user, 'secret-auth-token');

    expect(localStorage.getItem('folium_user')).toBe(JSON.stringify(user));
    expect(localStorage.getItem('folium_token')).toBe('secret-auth-token');

    logout();

    expect(localStorage.getItem('folium_user')).toBeNull();
    expect(localStorage.getItem('folium_token')).toBeNull();
  });
});
