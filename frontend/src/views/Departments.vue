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
          <h1 class="h3 mb-0" style="color: black;">Department Categories</h1>
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
                    <h6 class="text-muted mb-1">Total Categories</h6>
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
              <h5 class="card-title mb-0">Department Categories Directory</h5>
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
                        <button class="btn btn-outline-danger" @click="deleteDepartmentRecord(department)">
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
          <button type="button" class="btn btn-primary" @click="createDepartmentRecord">Add Department</button>
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
          <button type="button" class="btn btn-primary" @click="updateDepartmentRecord">Update Department</button>
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
import { Modal } from 'bootstrap'
import { useInventoryData } from '@/composables/useInventoryData'
export default {
  name: 'Departments',
  components: {
    AppNav,
    AppHeader,
    LoadingSpinner
  },
  setup() {
    const router = useRouter()
    
    const isNavCollapsed = ref(false)
    
    // Use centralized data fetching
    const {
      isLoading,
      departments,
      categories,
      refreshAllData,
      createDepartment,
      updateDepartment,
      deleteDepartment
    } = useInventoryData()
    
    // Watch for changes in navigation state
    watch(isNavCollapsed, (newValue, oldValue) => {
      console.log('Departments isNavCollapsed changed from', oldValue, 'to', newValue)
    })
    
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

    // Refresh data using centralized service
    const refreshData = async () => {
      try {
        await refreshAllData({ showLoading: true, minLoadingDuration: 1000 })
      } catch (error) {
        console.error('Error refreshing data:', error)
      }
    }

    const showCreateDepartmentModal = () => {
      const modal = new Modal(document.getElementById('createDepartmentModal'))
      modal.show()
    }

    const createDepartmentRecord = async () => {
      try {
        await createDepartment(newDepartment.value)
        
        Modal.getInstance(document.getElementById('createDepartmentModal')).hide()
        
        // Reset form
        newDepartment.value = {
          name: '',
          description: '',
          category_id: ''
        }
        
        alert('Department created successfully!')
        // Data is automatically refreshed by the composable
      } catch (error) {
        console.error('Error creating department:', error)
        alert('Error creating department: ' + (error.message || 'Unknown error'))
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

    const updateDepartmentRecord = async () => {
      try {
        await updateDepartment(editingDepartment.value.id, editingDepartment.value)
        
        Modal.getInstance(document.getElementById('editDepartmentModal')).hide()
        alert('Department updated successfully!')
        // Data is automatically refreshed by the composable
      } catch (error) {
        console.error('Error updating department:', error)
        alert('Error updating department: ' + (error.message || 'Unknown error'))
      }
    }

    const deleteDepartmentRecord = async (department) => {
      if (!confirm('Are you sure you want to delete this department?')) return
      
      try {
        await deleteDepartment(department.id)
        alert('Department deleted successfully!')
        // Data is automatically refreshed by the composable
      } catch (error) {
        console.error('Error deleting department:', error)
        alert('Error deleting department: ' + (error.message || 'Unknown error'))
      }
    }

    const filterDepartments = () => {
      // This is handled by the computed property
    }

    const exportDepartments = () => {
      alert('Export feature would generate CSV/PDF report of current departments')
    }

    onMounted(async () => {
      await refreshData()
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
      createDepartmentRecord,
      viewDepartment,
      editDepartment,
      updateDepartmentRecord,
      deleteDepartmentRecord,
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

</style>
