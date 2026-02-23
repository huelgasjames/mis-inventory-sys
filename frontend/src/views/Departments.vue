<template>
  <div class="dashboard-layout">
    <!-- Loading Spinner -->
    <LoadingSpinner :is-visible="isLoading" message="Loading departments..." />

    <!-- Navigation Sidebar -->
    <AppNav :is-collapsed="isNavCollapsed" />
    
    <!-- Main Content Area -->
    <div class="main-content" :class="{ 'collapsed': isNavCollapsed }">
      <!-- Header -->
      <AppHeader 
        :is-collapsed="isNavCollapsed"
        @sidebar-toggle="(collapsed) => {
          console.log('Departments received sidebar-toggle:', collapsed)
          isNavCollapsed = collapsed
        }"
        @profile-open="openProfile"
        @settings-open="openSettings"
      />
      
      <!-- Departments Content -->
      <div class="container-fluid p-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h1 class="h3 mb-0" style="color: black;">Department Management</h1>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" @click="refreshData">
              <i class="bi bi-arrow-clockwise me-2"></i>Refresh
            </button>
            <button class="btn btn-success" @click="showCreateDepartmentModal">
              <i class="bi bi-plus-circle me-2"></i>Add Department
            </button>
            <button class="btn btn-info" @click="exportDepartments">
              <i class="bi bi-download me-2"></i>Export
            </button>
          </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
          <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                    <i class="bi bi-building text-primary fs-4"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Total Departments</h6>
                    <h3 class="mb-0">{{ departments.length }}</h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                    <i class="bi bi-pc-display text-info fs-4"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Total Assets</h6>
                    <h3 class="mb-0">{{ totalAssets }}</h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                    <i class="bi bi-laptop text-warning fs-4"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Total Computers</h6>
                    <h3 class="mb-0">{{ totalComputers }}</h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                    <i class="bi bi-people text-success fs-4"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Total Users</h6>
                    <h3 class="mb-0">{{ totalUsers }}</h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Departments Table -->
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0">Departments Directory</h5>
              <div class="d-flex gap-2">
                <input type="text" class="form-control form-control-sm" placeholder="Quick search..." v-model="searchQuery">
                <button class="btn btn-outline-primary btn-sm" @click="showCreateDepartmentModal">
                  <i class="bi bi-plus-circle me-2"></i>Add Department
                </button>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Assets</th>
                    <th>Computers</th>
                    <th>Users</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="department in filteredDepartments" :key="department.id">
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-3">
                          <i class="bi bi-building text-primary"></i>
                        </div>
                        <div>
                          <div class="fw-semibold">{{ department.name }}</div>
                          <small class="text-muted">ID: {{ department.id }}</small>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span v-if="department.category" class="badge" :style="{ backgroundColor: department.category.color + '20', color: department.category.color }">
                        {{ department.category.name }}
                      </span>
                      <span v-else class="text-muted">-</span>
                    </td>
                    <td>
                      <span class="text-truncate d-inline-block" style="max-width: 200px;">
                        {{ department.description || '-' }}
                      </span>
                    </td>
                    <td>
                      <span class="badge bg-info">{{ department.total_assets || 0 }}</span>
                    </td>
                    <td>
                      <span class="badge bg-warning">{{ department.total_computers || 0 }}</span>
                    </td>
                    <td>
                      <span class="badge bg-primary">{{ department.total_users || 0 }}</span>
                    </td>
                    <td>
                      <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" @click="viewDepartment(department)">
                          <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-outline-warning" @click="editDepartment(department)">
                          <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-outline-danger" @click="deleteDepartment(department)">
                          <i class="bi bi-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Create Department Modal -->
  <div class="modal fade" id="createDepartmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Department</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="createDepartment">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Name *</label>
                <input type="text" class="form-control" v-model="newDepartment.name" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Category</label>
                <select class="form-select" v-model="newDepartment.category_id">
                  <option value="">Select Category</option>
                  <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                  </option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label">Description</label>
                <textarea class="form-control" v-model="newDepartment.description" rows="3"></textarea>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" @click="createDepartment">Add Department</button>
        </div>
      </div>
    </div>
  </div>

  <!-- View Department Modal -->
  <div class="modal fade" id="viewDepartmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Department Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" v-if="selectedDepartment">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Name</label>
              <div class="form-control bg-light">{{ selectedDepartment.name }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Category</label>
              <div class="form-control bg-light">
                <span v-if="selectedDepartment.category" class="badge" :style="{ backgroundColor: selectedDepartment.category.color + '20', color: selectedDepartment.category.color }">
                  {{ selectedDepartment.category.name }}
                </span>
                <span v-else>-</span>
              </div>
            </div>
            <div class="col-12">
              <label class="form-label">Description</label>
              <div class="form-control bg-light" style="min-height: 80px;">
                {{ selectedDepartment.description || 'No description' }}
              </div>
            </div>
            <div class="col-md-4">
              <label class="form-label">Total Assets</label>
              <div class="form-control bg-light">{{ selectedDepartment.total_assets || 0 }}</div>
            </div>
            <div class="col-md-4">
              <label class="form-label">Total Computers</label>
              <div class="form-control bg-light">{{ selectedDepartment.total_computers || 0 }}</div>
            </div>
            <div class="col-md-4">
              <label class="form-label">Total Users</label>
              <div class="form-control bg-light">{{ selectedDepartment.total_users || 0 }}</div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Department Modal -->
  <div class="modal fade" id="editDepartmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Department</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="updateDepartment">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Name *</label>
                <input type="text" class="form-control" v-model="editingDepartment.name" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Category</label>
                <select class="form-select" v-model="editingDepartment.category_id">
                  <option value="">Select Category</option>
                  <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                  </option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label">Description</label>
                <textarea class="form-control" v-model="editingDepartment.description" rows="3"></textarea>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" @click="updateDepartment">Update Department</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, watch, computed } from 'vue'
import { useRouter } from 'vue-router'
import AppHeader from '@/components/AppHeader.vue'
import AppNav from '@/components/AppNav.vue'
import LoadingSpinner from '@/components/LoadingSpinner.vue'
import { useDarkMode } from '@/composables/useDarkMode.js'
import axios from 'axios'
import { Modal } from 'bootstrap'
export default {
  name: 'Departments',
  components: {
    AppNav,
    AppHeader,
    LoadingSpinner
  },
  setup() {
    const router = useRouter()
    const { initDarkMode } = useDarkMode()
    
    const isNavCollapsed = ref(false)
    const isLoading = ref(false)
    const loadingStartTime = ref(null)
    
    // Helper function to ensure minimum loading duration
    const ensureMinimumLoading = async (minDuration = 5000) => {
      if (loadingStartTime.value) {
        const elapsed = Date.now() - loadingStartTime.value
        if (elapsed < minDuration) {
          await new Promise(resolve => setTimeout(resolve, minDuration - elapsed))
        }
      }
    }
    
    // Watch for changes in navigation state
    watch(isNavCollapsed, (newValue, oldValue) => {
      console.log('Departments isNavCollapsed changed from', oldValue, 'to', newValue)
    })
    const departments = ref([])
    const categories = ref([])
    const categoryFilter = ref('')
    const searchQuery = ref('')
    const selectedDepartment = ref(null)
    const editingDepartment = ref({})
    
    const newDepartment = ref({
      name: '',
      description: '',
      category_id: ''
    })

    const toggleNav = () => {
      isNavCollapsed.value = !isNavCollapsed.value
    }

    const openProfile = () => {
      console.log('Profile clicked')
    }

    const openSettings = () => {
      console.log('Settings clicked')
    }

    const filteredDepartments = computed(() => {
      let result = departments.value

      // Apply category filter
      if (categoryFilter.value) {
        result = result.filter(d => d.category?.name === categoryFilter.value)
      }

      // Apply search filter
      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        result = result.filter(dept => 
          dept.name.toLowerCase().includes(query) ||
          dept.description?.toLowerCase().includes(query)
        )
      }

      return result
    })

    const activeCount = computed(() => departments.value.length)
    const totalAssets = computed(() => departments.value.reduce((sum, d) => sum + (d.total_assets || 0), 0))
    const totalComputers = computed(() => departments.value.reduce((sum, d) => sum + (d.total_computers || 0), 0))
    const totalUsers = computed(() => departments.value.reduce((sum, d) => sum + (d.total_users || 0), 0))

    const fetchDepartments = async () => {
      if (!isLoading.value) {
        isLoading.value = true
        loadingStartTime.value = Date.now()
      }
      try {
        console.log('Fetching departments from API...')
        const response = await axios.get('http://localhost:8000/api/departments')
        console.log('Departments API response:', response.data)
        
        if (response.data.success) {
          departments.value = response.data.data || []
          console.log('Departments loaded:', departments.value.length)
        } else {
          console.warn('Departments API returned unsuccessful response')
          departments.value = []
        }
      } catch (error) {
        console.error('Error fetching departments:', error)
        departments.value = []
      } finally {
        await ensureMinimumLoading(5000)
        isLoading.value = false
        loadingStartTime.value = null
      }
    }

    const fetchCategories = async () => {
      if (!isLoading.value) {
        isLoading.value = true
        loadingStartTime.value = Date.now()
      }
      try {
        const response = await axios.get('http://localhost:8000/api/department-categories')
        if (response.data.success) {
          categories.value = response.data.data || []
        } else {
          categories.value = []
        }
      } catch (error) {
        console.error('Error fetching categories:', error)
        categories.value = []
      } finally {
        await ensureMinimumLoading(5000)
        isLoading.value = false
      }
    }

    const refreshData = async () => {
      if (!isLoading.value) {
        isLoading.value = true
        loadingStartTime.value = Date.now()
      }
      
      await Promise.all([fetchDepartments(), fetchCategories()])
      
      await ensureMinimumLoading()
      isLoading.value = false
      loadingStartTime.value = null
    }

    const showCreateDepartmentModal = () => {
      const modal = new Modal(document.getElementById('createDepartmentModal'))
      modal.show()
    }

    const createDepartment = async () => {
      isLoading.value = true
      try {
        const response = await axios.post('http://localhost:8000/api/departments', newDepartment.value)
        
        if (response.data.success) {
          Modal.getInstance(document.getElementById('createDepartmentModal')).hide()
          await refreshData()
          
          // Reset form
          newDepartment.value = {
            name: '',
            description: '',
            category_id: ''
          }
          
          alert('Department created successfully!')
        }
      } catch (error) {
        console.error('Error creating department:', error)
        alert('Error creating department: ' + (error.response?.data?.message || error.message))
      } finally {
        isLoading.value = false
      }
    }

    const viewDepartment = (department) => {
      selectedDepartment.value = department
      const modal = new Modal(document.getElementById('viewDepartmentModal'))
      modal.show()
    }

    const editDepartment = (department) => {
      editingDepartment.value = { ...department }
      const modal = new Modal(document.getElementById('editDepartmentModal'))
      modal.show()
    }

    const updateDepartment = async () => {
      isLoading.value = true
      try {
        const response = await axios.put(`http://localhost:8000/api/departments/${editingDepartment.value.id}`, editingDepartment.value)
        
        if (response.data.success) {
          Modal.getInstance(document.getElementById('editDepartmentModal')).hide()
          await refreshData()
          alert('Department updated successfully!')
        }
      } catch (error) {
        console.error('Error updating department:', error)
        alert('Error updating department: ' + (error.response?.data?.message || error.message))
      } finally {
        isLoading.value = false
      }
    }

    const deleteDepartment = async (department) => {
      if (!confirm('Are you sure you want to delete this department?')) return
      
      isLoading.value = true
      try {
        const response = await axios.delete(`http://localhost:8000/api/departments/${department.id}`)
        
        if (response.data.success) {
          await refreshData()
          alert('Department deleted successfully!')
        }
      } catch (error) {
        console.error('Error deleting department:', error)
        alert('Error deleting department: ' + (error.response?.data?.message || error.message))
      } finally {
        isLoading.value = false
      }
    }

    const filterDepartments = () => {
      // This is handled by the computed property
    }

    const exportDepartments = () => {
      alert('Export feature would generate CSV/PDF report of current departments')
    }

    onMounted(async () => {
      initDarkMode()
      
      if (!isLoading.value) {
        isLoading.value = true
        loadingStartTime.value = Date.now()
      }
      
      await refreshData()
      
      await ensureMinimumLoading()
      isLoading.value = false
      loadingStartTime.value = null
    })

    return {
      isNavCollapsed,
      isLoading,
      departments,
      categories,
      categoryFilter,
      searchQuery,
      selectedDepartment,
      editingDepartment,
      newDepartment,
      filteredDepartments,
      activeCount,
      totalAssets,
      totalComputers,
      totalUsers,
      toggleNav,
      openProfile,
      openSettings,
      refreshData,
      showCreateDepartmentModal,
      createDepartment,
      viewDepartment,
      editDepartment,
      updateDepartment,
      deleteDepartment,
      filterDepartments,
      exportDepartments
    }
  }
}
</script>

<style scoped>
.dashboard-layout {
  display: flex;
  min-height: 100vh;
  background-color: #f8f9fa;
}

.main-content {
  flex: 1;
  margin-left: 250px;
  transition: margin-left 0.3s ease;
  display: flex;
  flex-direction: column;
}

.main-content.collapsed {
  margin-left: 60px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .main-content {
    margin-left: 60px;
  }
  
  .main-content.collapsed {
    margin-left: 60px;
  }
}

/* Dark mode styles */
:global(.dark-mode) .dashboard-layout {
  background-color: #121212;
}

:global(.dark-mode) .main-content {
  background-color: #121212;
}

:global(.dark-mode) .card {
  background-color: #1e1e1e;
  border-color: #333;
}

:global(.dark-mode) .card-header {
  background-color: #2d2d2d;
  border-color: #333;
  color: #fff;
}

:global(.dark-mode) .card-body {
  background-color: #1e1e1e;
  color: #fff;
}

:global(.dark-mode) .h1,
:global(.dark-mode) .h2,
:global(.dark-mode) .h3,
:global(.dark-mode) .h4,
:global(.dark-mode) .h5,
:global(.dark-mode) .h6 {
  color: #fff !important;
}

:global(.dark-mode) .text-muted {
  color: #b3b3b3 !important;
}

:global(.dark-mode) .btn-outline-primary {
  border-color: #0F6F43;
  color: #0F6F43;
}

:global(.dark-mode) .btn-outline-primary:hover {
  background-color: #0F6F43;
  border-color: #0F6F43;
  color: #fff;
}

:global(.dark-mode) .btn-success {
  background-color: #198754;
  border-color: #198754;
}

:global(.dark-mode) .btn-success:hover {
  background-color: #157347;
  border-color: #157347;
}

:global(.dark-mode) .btn-info {
  background-color: #0c8599;
  border-color: #0c8599;
}

:global(.dark-mode) .btn-info:hover {
  background-color: #0a6b7c;
  border-color: #0a6b7c;
}

:global(.dark-mode) .table {
  color: #fff;
}

:global(.dark-mode) .table thead th {
  background-color: #2d2d2d;
  border-color: #333;
  color: #fff;
}

:global(.dark-mode) .table tbody td {
  background-color: #1e1e1e;
  border-color: #333;
  color: #fff;
}

:global(.dark-mode) .table tbody tr:hover td {
  background-color: #2d2d2d;
}

:global(.dark-mode) .form-control {
  background-color: #2d2d2d;
  border-color: #444;
  color: #fff;
}

:global(.dark-mode) .form-control:focus {
  background-color: #2d2d2d;
  border-color: #0F6F43;
  color: #fff;
  box-shadow: 0 0 0 0.25rem rgba(15, 111, 67, 0.25);
}

:global(.dark-mode) .form-select {
  background-color: #2d2d2d;
  border-color: #444;
  color: #fff;
}

:global(.dark-mode) .form-select:focus {
  background-color: #2d2d2d;
  border-color: #0F6F43;
  color: #fff;
  box-shadow: 0 0 0 0.25rem rgba(15, 111, 67, 0.25);
}

:global(.dark-mode) .modal-content {
  background-color: #1e1e1e;
  color: #fff;
}

:global(.dark-mode) .modal-header {
  background-color: #2d2d2d;
  border-color: #333;
}

:global(.dark-mode) .modal-body {
  background-color: #1e1e1e;
}

:global(.dark-mode) .modal-footer {
  background-color: #2d2d2d;
  border-color: #333;
}

:global(.dark-mode) .badge {
  background-color: #0F6F43;
}

:global(.dark-mode) .dropdown-menu {
  background-color: #1e1e1e;
  border-color: #333;
}

:global(.dark-mode) .dropdown-item {
  color: #fff;
}

:global(.dark-mode) .dropdown-item:hover {
  background-color: #2d2d2d;
  color: #fff;
}

:global(.dark-mode) .pagination .page-link {
  background-color: #1e1e1e;
  border-color: #333;
  color: #fff;
}

:global(.dark-mode) .pagination .page-link:hover {
  background-color: #2d2d2d;
  border-color: #444;
  color: #fff;
}

:global(.dark-mode) .pagination .page-item.active .page-link {
  background-color: #0F6F43;
  border-color: #0F6F43;
}
</style>
