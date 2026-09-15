/**
 * Folium SIGB — REST API Client Service
 * Encapsulates HTTP requests: GET, POST, PUT, PATCH, DELETE, OPTIONS
 */

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api/v1';

async function request(endpoint, options = {}) {
  const url = `${API_BASE_URL}${endpoint}`;
  const token = localStorage.getItem('folium_token');

  const defaultHeaders = {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  };

  if (token) {
    defaultHeaders['Authorization'] = `Bearer ${token}`;
  }

  const config = {
    ...options,
    headers: {
      ...defaultHeaders,
      ...options.headers,
    },
  };

  if (config.body && typeof config.body === 'object' && !(config.body instanceof FormData)) {
    config.body = JSON.stringify(config.body);
  }

  const response = await fetch(url, config);
  
  if (!response.ok) {
    const errorData = await response.json().catch(() => null);
    const error = new Error(errorData?.message || `HTTP ${response.status}`);
    error.status = response.status;
    error.data = errorData;
    throw error;
  }

  return response.json();
}

export const apiService = {
  /**
   * GET /works - Retrieve catalog works from backend database
   */
  async getWorks(params = {}) {
    const queryString = new URLSearchParams(params).toString();
    const endpoint = queryString ? `/works?${queryString}` : '/works';
    return request(endpoint, { method: 'GET' });
  },

  /**
   * GET /works/:id - Retrieve single work with full WEMI structure
   */
  async getWorkById(id) {
    return request(`/works/${id}`, { method: 'GET' });
  },

  /**
   * POST /works - Create a new Work in the database
   */
  async createWork(workData) {
    return request('/works', {
      method: 'POST',
      body: workData,
    });
  },

  /**
   * PUT /works/:id - Update an existing Work in the database
   */
  async updateWork(id, workData) {
    return request(`/works/${id}`, {
      method: 'PUT',
      body: workData,
    });
  },

  /**
   * PATCH /works/:id - Partial update of a Work field
   */
  async patchWork(id, patchData) {
    return request(`/works/${id}`, {
      method: 'PATCH',
      body: patchData,
    });
  },

  /**
   * DELETE /works/:id - Delete a Work from the database
   */
  async deleteWork(id) {
    return request(`/works/${id}`, {
      method: 'DELETE',
    });
  },

  /**
   * OPTIONS /works - CORS preflight check call
   */
  async checkOptions(endpoint = '/works') {
    return request(endpoint, {
      method: 'OPTIONS',
    });
  },

  /**
   * GET /branches - Fetch list of library branches
   */
  async getBranches() {
    return request('/branches', { method: 'GET' });
  },

  /**
   * GET /branches/:id - Fetch single branch details with inventory
   */
  async getBranchById(id) {
    return request(`/branches/${id}`, { method: 'GET' });
  },

  /**
   * POST /branches - Create a new branch
   */
  async createBranch(branchData) {
    return request('/branches', {
      method: 'POST',
      body: branchData,
    });
  },

  /**
   * PUT /branches/:id - Update branch details
   */
  async updateBranch(id, branchData) {
    return request(`/branches/${id}`, {
      method: 'PUT',
      body: branchData,
    });
  },

  /**
   * DELETE /branches/:id - Delete a branch
   */
  async deleteBranch(id) {
    return request(`/branches/${id}`, {
      method: 'DELETE',
    });
  },

  /**
   * GET /languages - Fetch list of active languages
   */
  async getLanguages() {
    return request('/languages', { method: 'GET' });
  },

  /**
   * POST /transfers - Request Interlibrary Loan (ILL) transfer
   */
  async createTransfer(transferData) {
    return request('/transfers', {
      method: 'POST',
      body: transferData,
    });
  },

  /**
   * POST /loans - Create local physical loan
   */
  async createLoan(loanData) {
    return request('/loans', {
      method: 'POST',
      body: loanData,
    });
  },

  /**
   * GET /users - Fetch user list (Admin only)
   */
  async getUsers() {
    return request('/users', { method: 'GET' });
  },

  /**
   * POST /users - Create a new user (Admin only)
   */
  async createUser(userData) {
    return request('/users', {
      method: 'POST',
      body: userData,
    });
  },

  /**
   * PUT /users/:id - Update user details and roles (Admin only)
   */
  async updateUser(id, userData) {
    return request(`/users/${id}`, {
      method: 'PUT',
      body: userData,
    });
  },

  /**
   * DELETE /users/:id - Delete / revoke user (Admin only)
   */
  async deleteUser(id) {
    return request(`/users/${id}`, {
      method: 'DELETE',
    });
  },

  /**
   * GET /roles - Fetch system roles
   */
  async getRoles() {
    return request('/roles', { method: 'GET' });
  }
};
