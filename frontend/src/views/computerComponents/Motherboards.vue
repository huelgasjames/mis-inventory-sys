<template>
  <div class="dashboard-layout">
    <!-- Navigation Sidebar -->
    <AppNav :is-collapsed="isNavCollapsed" />
    
    <!-- Main Content Area -->
    <div class="main-content" :class="{ 'collapsed': isNavCollapsed }">
      <!-- Header -->
      <AppHeader 
        @menu-toggle="toggleNav"
        @profile-open="openProfile"
        @settings-open="openSettings"
      />
      
      <!-- Motherboards Content -->
      <div class="container-fluid p-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h1 class="h3 mb-0">Motherboards Management</h1>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" @click="refreshData">
              <i class="bi bi-arrow-clockwise me-2"></i>Refresh
            </button>
            <button class="btn btn-primary" @click="showCreateMotherboardModal">
              <i class="bi bi-plus-circle me-2"></i>Add Motherboard
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
                    <i class="bi bi-motherboard text-primary fs-4"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Total Motherboards</h6>
                    <h3 class="mb-0">{{ motherboards.length }}</h3>
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
                    <i class="bi bi-check-circle text-success fs-4"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Available</h6>
                    <h3 class="mb-0">{{ availableCount }}</h3>
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
                    <i class="bi bi-tools text-warning fs-4"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">In Use</h6>
                    <h3 class="mb-0">{{ inUseCount }}</h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                    <i class="bi bi-x-circle text-danger fs-4"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Defective</h6>
                    <h3 class="mb-0">{{ defectiveCount }}</h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Motherboards Table -->
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0">Motherboards Inventory</h5>
              <div class="d-flex gap-2">
                <select class="form-select form-select-sm" v-model="statusFilter" @change="filterMotherboards">
                  <option value="">All Status</option>
                  <option value="Available">Available</option>
                  <option value="In Use">In Use</option>
                  <option value="Defective">Defective</option>
                </select>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Model</th>
                    <th>Status</th>
                    <th>Created Date</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="motherboard in filteredMotherboards" :key="motherboard.id">
                    <td>{{ motherboard.id }}</td>
                    <td>
                      <div class="d-flex align-items-center">
                        <i class="bi bi-motherboard me-2 text-primary"></i>
                        <span class="fw-semibold">{{ motherboard.model }}</span>
                      </div>
                    </td>
                    <td>
                      <span :class="getStatusBadgeClass(motherboard.status)">
                        {{ motherboard.status }}
                      </span>
                    </td>
                    <td>{{ formatDate(motherboard.created_at) }}</td>
                    <td>
                      <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" @click="viewMotherboard(motherboard)">
                          <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-outline-warning" @click="editMotherboard(motherboard)">
                          <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-outline-danger" @click="deleteMotherboard(motherboard)">
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

  <!-- Create Motherboard Modal -->
  <div class="modal fade" id="createMotherboardModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Motherboard</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="createMotherboard">
            <div class="mb-3">
              <label class="form-label">Model *</label>
              <input type="text" class="form-control" v-model="newMotherboard.model" placeholder="e.g., ASUS Prime H410M" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Status *</label>
              <select class="form-select" v-model="newMotherboard.status" required>
                <option value="">Select Status</option>
                <option value="Available">Available</option>
                <option value="In Use">In Use</option>
                <option value="Defective">Defective</option>
              </select>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" @click="createMotherboard">Add Motherboard</button>
        </div>
      </div>
    </div>
  </div>

  <!-- View Motherboard Modal -->
  <div class="modal fade" id="viewMotherboardModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Motherboard Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" v-if="selectedMotherboard">
          <div class="mb-3">
            <label class="form-label">ID</label>
            <div class="form-control bg-light">{{ selectedMotherboard.id }}</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Model</label>
            <div class="form-control bg-light">{{ selectedMotherboard.model }}</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <div class="form-control bg-light">
              <span :class="getStatusBadgeClass(selectedMotherboard.status)">
                {{ selectedMotherboard.status }}
              </span>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Created Date</label>
            <div class="form-control bg-light">{{ formatDate(selectedMotherboard.created_at) }}</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import AppNav from '@/components/AppNav.vue'
import AppHeader from '@/components/AppHeader.vue'
import axios from 'axios'

export default {
  name: 'Motherboards',
  components: {
    AppNav,
    AppHeader
  },
  setup() {
    const isNavCollapsed = ref(false)
    const motherboards = ref([])
    const statusFilter = ref('')
    const selectedMotherboard = ref(null)
    
    const newMotherboard = ref({
      model: '',
      status: 'Available'
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

    const getStatusBadgeClass = (status) => {
      const classes = {
        'Available': 'badge bg-success',
        'In Use': 'badge bg-warning',
        'Defective': 'badge bg-danger'
      }
      return classes[status] || 'badge bg-secondary'
    }

    const formatDate = (dateString) => {
      if (!dateString) return '-'
      return new Date(dateString).toLocaleDateString()
    }

    const filteredMotherboards = computed(() => {
      if (!statusFilter.value) return motherboards.value
      return motherboards.value.filter(m => m.status === statusFilter.value)
    })

    const availableCount = computed(() => motherboards.value.filter(m => m.status === 'Available').length)
    const inUseCount = computed(() => motherboards.value.filter(m => m.status === 'In Use').length)
    const defectiveCount = computed(() => motherboards.value.filter(m => m.status === 'Defective').length)

    const fetchMotherboards = async () => {
      try {
        const response = await axios.get('http://localhost:8000/api/components')
        motherboards.value = response.data.data.motherboards || []
      } catch (error) {
        console.error('Error fetching motherboards:', error)
        // Fallback data for demo
        motherboards.value = [
          { id: 1, model: 'ASUS Prime H410M', status: 'Available', created_at: '2024-01-01' },
          { id: 2, model: 'Gigabyte H410M', status: 'In Use', created_at: '2024-01-02' },
          { id: 3, model: 'MSI H410M', status: 'Defective', created_at: '2024-01-03' }
        ]
      }
    }

    const refreshData = async () => {
      await fetchMotherboards()
    }

    const showCreateMotherboardModal = () => {
      const modal = new bootstrap.Modal(document.getElementById('createMotherboardModal'))
      modal.show()
    }

    const createMotherboard = async () => {
      try {
        const response = await axios.post('http://localhost:8000/api/components/motherboards', newMotherboard.value)
        
        if (response.data.success) {
          bootstrap.Modal.getInstance(document.getElementById('createMotherboardModal')).hide()
          await refreshData()
          
          // Reset form
          newMotherboard.value = {
            model: '',
            status: 'Available'
          }
          
          alert('Motherboard added successfully!')
        }
      } catch (error) {
        console.error('Error creating motherboard:', error)
        alert('Error adding motherboard: ' + (error.response?.data?.message || error.message))
      }
    }

    const viewMotherboard = (motherboard) => {
      selectedMotherboard.value = motherboard
      const modal = new bootstrap.Modal(document.getElementById('viewMotherboardModal'))
      modal.show()
    }

    const editMotherboard = (motherboard) => {
      // For now, just show the motherboard details
      viewMotherboard(motherboard)
    }

    const deleteMotherboard = async (motherboard) => {
      if (!confirm('Are you sure you want to delete this motherboard?')) return
      
      try {
        const response = await axios.delete(`http://localhost:8000/api/components/motherboards/${motherboard.id}`)
        
        if (response.data.success) {
          await refreshData()
          alert('Motherboard deleted successfully!')
        }
      } catch (error) {
        console.error('Error deleting motherboard:', error)
        alert('Error deleting motherboard: ' + (error.response?.data?.message || error.message))
      }
    }

    const filterMotherboards = () => {
      // This is handled by the computed property
    }

    onMounted(() => {
      fetchMotherboards()
    })

    return {
      isNavCollapsed,
      motherboards,
      statusFilter,
      selectedMotherboard,
      newMotherboard,
      filteredMotherboards,
      availableCount,
      inUseCount,
      defectiveCount,
      toggleNav,
      openProfile,
      openSettings,
      getStatusBadgeClass,
      formatDate,
      refreshData,
      showCreateMotherboardModal,
      createMotherboard,
      viewMotherboard,
      editMotherboard,
      deleteMotherboard,
      filterMotherboards
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
  margin-left: 70px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .main-content {
    margin-left: 70px;
  }
  
  .main-content.collapsed {
    margin-left: 70px;
  }
}
</style>
