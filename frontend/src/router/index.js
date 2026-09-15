import { createRouter, createWebHistory } from 'vue-router';
import { useAuth } from '../composables/useAuth';
import ReaderPortal from '../views/ReaderPortal.vue';
import StaffPortal from '../views/StaffPortal.vue';
import Forbidden403 from '../views/Forbidden403.vue';
import BranchesManagementView from '../views/BranchesManagementView.vue';
import UsersManagementView from '../views/UsersManagementView.vue';

const routes = [
  {
    path: '/',
    name: 'ReaderPortal',
    component: ReaderPortal,
    meta: {
      requiresAuth: false,
      title: 'Catálogo OPAC — Folium'
    }
  },
  {
    path: '/staff',
    alias: '/circulation',
    name: 'StaffPortal',
    component: StaffPortal,
    meta: {
      requiresAuth: true,
      allowedRoles: ['admin', 'cataloger', 'librarian', 'network_librarian'],
      title: 'Panel de Gestión — Folium'
    }
  },
  {
    path: '/catalog/manage',
    alias: '/catalog',
    name: 'CatalogManagement',
    component: StaffPortal,
    meta: {
      requiresAuth: true,
      allowedRoles: ['admin', 'cataloger'],
      title: 'Catalogación WEMI — Folium'
    }
  },
  {
    path: '/transfers',
    name: 'NetworkTransfers',
    component: StaffPortal,
    meta: {
      requiresAuth: true,
      allowedRoles: ['admin', 'network_librarian'],
      title: 'Red Multi-Sede ILL — Folium'
    }
  },
  {
    path: '/admin/branches',
    name: 'BranchesManagement',
    component: BranchesManagementView,
    meta: {
      requiresAuth: true,
      allowedRoles: ['admin'],
      title: 'Gestión de Sedes — Folium'
    }
  },
  {
    path: '/admin/users',
    name: 'UsersManagement',
    component: UsersManagementView,
    meta: {
      requiresAuth: true,
      allowedRoles: ['admin'],
      title: 'Gestión de Personal & Permisos — Folium'
    }
  },
  {
    path: '/403',
    name: 'Forbidden403',
    component: Forbidden403,
    meta: {
      requiresAuth: false,
      title: '403 Acceso Denegado — Folium'
    }
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/'
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

/**
 * Global Navigation Guard against Broken Access Control (OWASP A01)
 */
router.beforeEach((to, from, next) => {
  const { isAuthenticated, canAccess } = useAuth();

  // 1. Update Document Title
  if (to.meta.title) {
    document.title = to.meta.title;
  }

  // 2. Check if route requires authentication
  if (to.meta.requiresAuth && !isAuthenticated.value) {
    return next({
      path: '/',
      query: { redirect_to: to.fullPath, error: 'auth_required' }
    });
  }

  // 3. Check Role Permissions (RBAC)
  if (to.meta.allowedRoles && !canAccess(to.meta.allowedRoles)) {
    return next({
      path: '/403',
      query: { attempted_url: to.fullPath }
    });
  }

  next();
});

export default router;
