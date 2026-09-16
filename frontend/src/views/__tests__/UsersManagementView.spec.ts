import { describe, it, expect, vi, beforeEach } from 'vitest';
import { mount, flushPromises } from '@vue/test-utils';
import UsersManagementView from '../UsersManagementView.vue';
import { useAuth } from '../../composables/useAuth';
import { apiService } from '../../services/api';

vi.mock('../../services/api', () => ({
  apiService: {
    getUsers: vi.fn(),
    getRoles: vi.fn(),
    getBranches: vi.fn(),
    createUser: vi.fn(),
    updateUser: vi.fn(),
    deleteUser: vi.fn(),
  }
}));

describe('UsersManagementView - Self-Action Prevention', () => {
  const currentAdmin = {
    id: 1,
    name: 'Directora Administradora',
    short_name: 'Directora A.',
    initials: 'DA',
    email: 'admin@folium.org',
    role_id: 1,
    role: {
      id: 1,
      key: 'admin',
      name: 'admin',
      display_name: 'Administrador General',
      short_label: 'Admin',
      badge_variant: 'forest'
    },
    branch_id: 1,
    branch: { id: 1, name: 'Sede Central' }
  };

  const otherUser = {
    id: 2,
    name: 'Carlos Bibliotecario',
    short_name: 'Carlos B.',
    initials: 'CB',
    email: 'carlos@folium.org',
    role_id: 3,
    role: {
      id: 3,
      key: 'librarian',
      name: 'librarian',
      display_name: 'Bibliotecario de Sede',
      short_label: 'Bibliotecario',
      badge_variant: 'copper'
    },
    branch_id: 1,
    branch: { id: 1, name: 'Sede Central' }
  };

  beforeEach(() => {
    vi.clearAllMocks();
    const { login } = useAuth();
    login(currentAdmin as any);

    vi.mocked(apiService.getUsers).mockResolvedValue({ status: 'success', data: [currentAdmin, otherUser] } as any);
    vi.mocked(apiService.getRoles).mockResolvedValue({ status: 'success', data: [currentAdmin.role, otherUser.role] } as any);
    vi.mocked(apiService.getBranches).mockResolvedValue({ status: 'success', data: [currentAdmin.branch] } as any);
  });

  it('disables delete button for current active session user with aria-disabled and lock classes', async () => {
    const wrapper = mount(UsersManagementView, {
      global: {
        stubs: {
          Teleport: true,
          RouterLink: {
            template: '<a><slot /></a>'
          }
        }
      }
    });

    await flushPromises();

    // Find table rows in desktop view
    const rows = wrapper.findAll('tbody tr');
    expect(rows.length).toBe(2);

    // Row 1 is currentAdmin (id: 1)
    const selfRow = rows[0];
    expect(selfRow.text()).toContain('Directora Administradora');
    expect(selfRow.text()).toContain('(Tú)');

    const selfDeleteBtn = selfRow.find('button[aria-label^="Revocar credenciales"]');
    expect(selfDeleteBtn.exists()).toBe(true);
    expect(selfDeleteBtn.attributes('disabled')).toBeDefined();
    expect(selfDeleteBtn.attributes('aria-disabled')).toBe('true');
    expect(selfDeleteBtn.classes()).toContain('opacity-30');
    expect(selfDeleteBtn.classes()).toContain('pointer-events-none');
    expect(selfDeleteBtn.classes()).toContain('cursor-not-allowed');

    // Row 2 is otherUser (id: 2)
    const otherRow = rows[1];
    expect(otherRow.text()).toContain('Carlos Bibliotecario');

    const otherDeleteBtn = otherRow.find('button[aria-label^="Revocar credenciales"]');
    expect(otherDeleteBtn.exists()).toBe(true);
    expect(otherDeleteBtn.attributes('disabled')).toBeUndefined();
    expect(otherDeleteBtn.attributes('aria-disabled')).toBe('false');
    expect(otherDeleteBtn.classes()).not.toContain('opacity-30');
    expect(otherDeleteBtn.classes()).not.toContain('pointer-events-none');
  });
});
