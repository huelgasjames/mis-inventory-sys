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
      
      <!-- PSUs Content -->
      <div class="container-fluid p-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h1 class="h3 mb-0">PSU Management</h1>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" @click="refreshData">
              <i class="bi bi-arrow-clockwise me-2"></i>Refresh
            </button>
            <button class="btn btn-primary" @click="showCreatePSUModal">
              <i class="bi bi-plus-circle me-2"></i>Add PSU
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
                    <i class="bi bi-lightning-charge text-primary fs-4"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Total PSUs</h6>
                    <h3 class="mb-0">{{ psus.length }}</h3>
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

        <!-- PSUs Table -->
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0">PSUs Inventory</h5>
              <div class="d-flex gap-2">
                <select class="form-select form-select-sm" v-model="statusFilter" @change="filterPSUs">
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
                    <th>Wattage</th>
                    <th>Status</th>
                    <th>Created Date</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="psu in filteredPSUs" :key="psu.id">
                    <td>{{ psu.id }}</td>
                    <td>
                      <div class="d-flex align-items-center">
                        <i class="bi bi-lightning-charge me-2 text-primary"></i>
                        <span class="fw-semibold">{{ psu.wattage }}W</span>
                      </div>
                    </td>
                    <td>
                      <span :class="getStatusBadgeClass(psu.status)">
                        {{ psu.status }}
                      </span>
                    </td>
                    <td>{{ formatDate(psu.created_at) }}</td>
                    <td>
                      <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" @click="viewPSU(psu)">
                          <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-outline-warning" @click="editPSU(psu)">
                          <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-outline-danger" @click="deletePSU(psu)">
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

  <!-- Create PSU Modal -->
  <div class="modal fade" id="createPSUModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add PSU</h5>
          <button type="button" class="btn-close" @click="hideCreatePSUModal"></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="createPSU">
            <div class="mb-3">
              <label class="form-label">Wattage *</label>
              <select class="form-select" v-model="newPSU.wattage" required>
                <option value="">Select Wattage</option>
                <option value="350">350W</option>
                <option value="450">450W</option>
                <option value="550">550W</option>
                <option value="650">650W</option>
                <option value="750">750W</option>
                <option value="850">850W</option>
                <option value="1000">1000W</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Status *</label>
              <select class="form-select" v-model="newPSU.status" required>
                <option value="">Select Status</option>
                <option value="Available">Available</option>
                <option value="In Use">In Use</option>
                <option value="Defective">Defective</option>
              </select>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="hideCreatePSUModal">Cancel</button>
          <button type="button" class="btn btn-primary" @click="createPSU">Add PSU</button>
        </div>
      </div>
    </div>
  </div>

  <!-- View PSU Modal -->
  <div class="modal fade" id="viewPSUModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">PSU Details</h5>
          <button type="button" class="btn-close" @click="hideViewPSUModal"></button>
        </div>
        <div class="modal-body" v-if="selectedPSU">
          <div class="mb-3">
            <label class="form-label">ID</label>
            <div class="form-control bg-light">{{ selectedPSU.id }}</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Wattage</label>
            <div class="form-control bg-light">{{ selectedPSU.wattage }}W</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <div class="form-control bg-light">
              <span :class="getStatusBadgeClass(selectedPSU.status)">
                {{ selectedPSU.status }}
              </span>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Created Date</label>
            <div class="form-control bg-light">{{ formatDate(selectedPSU.created_at) }}</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="hideViewPSUModal">Close</button>
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
  name: 'PSUs',
  components: {
    AppNav,
    AppHeader
  },
  setup() {
    const isNavCollapsed = ref(false)
    const psus = ref([])
    const statusFilter = ref('')
    const selectedPSU = ref(null)
    
    const newPSU = ref({
      wattage: '550',
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

    const filteredPSUs = computed(() => {
      if (!statusFilter.value) return psus.value
      return psus.value.filter(p => p.status === statusFilter.value)
    })

    const availableCount = computed(() => psus.value.filter(p => p.status === 'Available').length)
    const inUseCount = computed(() => psus.value.filter(p => p.status === 'In Use').length)
    const defectiveCount = computed(() => psus.value.filter(p => p.status === 'Defective').length)

    const fetchPSUs = async () => {
      try {
        const response = await axios.get('http://localhost:8000/api/components')
        psus.value = response.data.data.psus || []
      } catch (error) {
        console.error('Error fetching PSUs:', error)
        // Fallback data for demo
        psus.value = [
          { id: 1, wattage: 450, status: 'Available', created_at: '2024-01-01' },
          { id: 2, wattage: 650, status: 'In Use', created_at: '2024-01-02' },
          { id: 3, wattage: 350, status: 'Defective', created_at: '2024-01-03' }
        ]
      }
    }

    const refreshData = async () => {
      await fetchPSUs()
    }

    const showCreatePSUModal = () => {
      const modal = document.getElementById('createPSUModal')
      if (modal) {
        modal.classList.add('show')
        modal.style.display = 'block'
        document.body.classList.add('modal-open')
        
        // Create backdrop
        const backdrop = document.createElement('div')
        backdrop.className = 'modal-backdrop fade show'
        backdrop.id = 'psu-modal-backdrop'
        document.body.appendChild(backdrop)
      }
    }

    const createPSU = async () => {
      try {
        const response = await axios.post('http://localhost:8000/api/components/psus', newPSU.value)
        
        if (response.data.success) {
          hideCreatePSUModal()
          await refreshData()
          
          // Reset form
          newPSU.value = {
            wattage: '550',
            status: 'Available'
          }
          
          alert('PSU added successfully!')
        }
      } catch (error) {
        console.error('Error creating PSU:', error)
        alert('Error adding PSU: ' + (error.response?.data?.message || error.message))
      }
    }

    const hideCreatePSUModal = () => {
      const modal = document.getElementById('createPSUModal')
      const backdrop = document.getElementById('psu-modal-backdrop')
      
      if (modal) {
        modal.classList.remove('show')
        modal.style.display = 'none'
        document.body.classList.remove('modal-open')
      }
      
      if (backdrop) {
        backdrop.remove()
      }
    }

    const viewPSU = (psu) => {
      selectedPSU.value = psu
      const modal = document.getElementById('viewPSUModal')
      if (modal) {
        modal.classList.add('show')
        modal.style.display = 'block'
        document.body.classList.add('modal-open')
        
        // Create backdrop
        const backdrop = document.createElement('div')
        backdrop.className = 'modal-backdrop fade show'
        backdrop.id = 'view-psu-modal-backdrop'
        document.body.appendChild(backdrop)
      }
    }

    const hideViewPSUModal = () => {
      const modal = document.getElementById('viewPSUModal')
      const backdrop = document.getElementById('view-psu-modal-backdrop')
      
      if (modal) {
        modal.classList.remove('show')
        modal.style.display = 'none'
        document.body.classList.remove('modal-open')
      }
      
      if (backdrop) {
        backdrop.remove()
      }
    }

    const editPSU = (psu) => {
      // For now, just show the PSU details
      viewPSU(psu)
    }

    const deletePSU = async (psu) => {
      if (!confirm('Are you sure you want to delete this PSU?')) return
      
      try {
        const response = await axios.delete(`http://localhost:8000/api/components/psus/${psu.id}`)
        
        if (response.data.success) {
          await refreshData()
          alert('PSU deleted successfully!')
        }
      } catch (error) {
        console.error('Error deleting PSU:', error)
        alert('Error deleting PSU: ' + (error.response?.data?.message || error.message))
      }
    }

    const filterPSUs = () => {
      // This is handled by the computed property
    }

    onMounted(() => {
      fetchPSUs()
    })

    return {
      isNavCollapsed,
      psus,
      statusFilter,
      selectedPSU,
      newPSU,
      filteredPSUs,
      availableCount,
      inUseCount,
      defectiveCount,
      toggleNav,
      openProfile,
      openSettings,
      getStatusBadgeClass,
      formatDate,
      refreshData,
      showCreatePSUModal,
      hideCreatePSUModal,
      createPSU,
      viewPSU,
      hideViewPSUModal,
      editPSU,
      deletePSU,
      filterPSUs
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
