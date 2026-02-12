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
      
      <!-- RAM Content -->
      <div class="container-fluid p-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h1 class="h3 mb-0">RAM Management</h1>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" @click="refreshData">
              <i class="bi bi-arrow-clockwise me-2"></i>Refresh
            </button>
            <button class="btn btn-primary" @click="showCreateRAMModal">
              <i class="bi bi-plus-circle me-2"></i>Add RAM
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
                    <i class="bi bi-memory text-primary fs-4"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Total RAM</h6>
                    <h3 class="mb-0">{{ rams.length }}</h3>
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

        <!-- RAM Table -->
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0">RAM Inventory</h5>
              <div class="d-flex gap-2">
                <select class="form-select form-select-sm" v-model="statusFilter" @change="filterRAM">
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
                    <th>Capacity</th>
                    <th>Type</th>
                    <th>Speed</th>
                    <th>Status</th>
                    <th>Created Date</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="ram in filteredRAM" :key="ram.id">
                    <td>{{ ram.id }}</td>
                    <td>
                      <div class="d-flex align-items-center">
                        <i class="bi bi-memory me-2 text-primary"></i>
                        <span class="fw-semibold">{{ ram.capacity }}</span>
                      </div>
                    </td>
                    <td>{{ ram.type || 'DDR4' }}</td>
                    <td>{{ ram.speed || '3200MHz' }}</td>
                    <td>
                      <span :class="getStatusBadgeClass(ram.status)">
                        {{ ram.status }}
                      </span>
                    </td>
                    <td>{{ formatDate(ram.created_at) }}</td>
                    <td>
                      <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" @click="viewRAM(ram)">
                          <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-outline-warning" @click="editRAM(ram)">
                          <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-outline-danger" @click="deleteRAM(ram)">
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

  <!-- Create RAM Modal -->
  <div class="modal fade" id="createRAMModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add RAM</h5>
          <button type="button" class="btn-close" @click="hideCreateRAMModal"></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="createRAM">
            <div class="mb-3">
              <label class="form-label">Capacity *</label>
              <select class="form-select" v-model="newRAM.capacity" required>
                <option value="">Select Capacity</option>
                <option value="4GB">4GB</option>
                <option value="8GB">8GB</option>
                <option value="16GB">16GB</option>
                <option value="32GB">32GB</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Type</label>
              <select class="form-select" v-model="newRAM.type">
                <option value="">Select Type</option>
                <option value="DDR3">DDR3</option>
                <option value="DDR4">DDR4</option>
                <option value="DDR5">DDR5</option>
                <option value="LPDDR4">LPDDR4</option>
                <option value="LPDDR5">LPDDR5</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Speed</label>
              <input type="text" class="form-control" v-model="newRAM.speed" placeholder="e.g., 3200MHz">
            </div>
            <div class="mb-3">
              <label class="form-label">Status *</label>
              <select class="form-select" v-model="newRAM.status" required>
                <option value="">Select Status</option>
                <option value="Available">Available</option>
                <option value="In Use">In Use</option>
                <option value="Defective">Defective</option>
              </select>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="hideCreateRAMModal">Cancel</button>
          <button type="button" class="btn btn-primary" @click="createRAM">Add RAM</button>
        </div>
      </div>
    </div>
  </div>

  <!-- View RAM Modal -->
  <div class="modal fade" id="viewRAMModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">RAM Details</h5>
          <button type="button" class="btn-close" @click="hideViewRAMModal"></button>
        </div>
        <div class="modal-body" v-if="selectedRAM">
          <div class="mb-3">
            <label class="form-label">ID</label>
            <div class="form-control bg-light">{{ selectedRAM.id }}</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Capacity</label>
            <div class="form-control bg-light">{{ selectedRAM.capacity }}</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Type</label>
            <div class="form-control bg-light">{{ selectedRAM.type || 'DDR4' }}</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Speed</label>
            <div class="form-control bg-light">{{ selectedRAM.speed || '3200MHz' }}</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <div class="form-control bg-light">
              <span :class="getStatusBadgeClass(selectedRAM.status)">
                {{ selectedRAM.status }}
              </span>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Created Date</label>
            <div class="form-control bg-light">{{ formatDate(selectedRAM.created_at) }}</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="hideViewRAMModal">Close</button>
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
  name: 'RAM',
  components: {
    AppNav,
    AppHeader
  },
  setup() {
    const isNavCollapsed = ref(false)
    const rams = ref([])
    const statusFilter = ref('')
    const selectedRAM = ref(null)
    
    const newRAM = ref({
      capacity: '16GB',
      type: 'DDR4',
      speed: '3200MHz',
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

    const filteredRAM = computed(() => {
      if (!statusFilter.value) return rams.value
      return rams.value.filter(r => r.status === statusFilter.value)
    })

    const availableCount = computed(() => rams.value.filter(r => r.status === 'Available').length)
    const inUseCount = computed(() => rams.value.filter(r => r.status === 'In Use').length)
    const defectiveCount = computed(() => rams.value.filter(r => r.status === 'Defective').length)

    const fetchRAM = async () => {
      try {
        const response = await axios.get('http://localhost:8000/api/components')
        rams.value = response.data.data.rams || []
      } catch (error) {
        console.error('Error fetching RAM:', error)
        // Fallback data for demo
        rams.value = [
          { id: 1, capacity: '16GB DDR4', status: 'Available', created_at: '2024-01-01' },
          { id: 2, capacity: '8GB DDR4', status: 'In Use', created_at: '2024-01-02' },
          { id: 3, capacity: '32GB DDR4', status: 'Defective', created_at: '2024-01-03' }
        ]
      }
    }

    const refreshData = async () => {
      await fetchRAM()
    }

    const showCreateRAMModal = () => {
      const modal = document.getElementById('createRAMModal')
      if (modal) {
        modal.classList.add('show')
        modal.style.display = 'block'
        document.body.classList.add('modal-open')
        
        // Create backdrop
        const backdrop = document.createElement('div')
        backdrop.className = 'modal-backdrop fade show'
        backdrop.id = 'ram-modal-backdrop'
        document.body.appendChild(backdrop)
      }
    }

    const createRAM = async () => {
      try {
        const response = await axios.post('http://localhost:8000/api/components/rams', newRAM.value)
        
        if (response.data.success) {
          hideCreateRAMModal()
          await refreshData()
          
          // Reset form
          newRAM.value = {
            capacity: '16GB',
            type: 'DDR4',
            speed: '3200MHz',
            status: 'Available'
          }
          
          alert('RAM added successfully!')
        }
      } catch (error) {
        console.error('Error creating RAM:', error)
        alert('Error adding RAM: ' + (error.response?.data?.message || error.message))
      }
    }

    const hideCreateRAMModal = () => {
      const modal = document.getElementById('createRAMModal')
      const backdrop = document.getElementById('ram-modal-backdrop')
      
      if (modal) {
        modal.classList.remove('show')
        modal.style.display = 'none'
        document.body.classList.remove('modal-open')
      }
      
      if (backdrop) {
        backdrop.remove()
      }
    }

    const viewRAM = (ram) => {
      selectedRAM.value = ram
      const modal = document.getElementById('viewRAMModal')
      if (modal) {
        modal.classList.add('show')
        modal.style.display = 'block'
        document.body.classList.add('modal-open')
        
        // Create backdrop
        const backdrop = document.createElement('div')
        backdrop.className = 'modal-backdrop fade show'
        backdrop.id = 'view-ram-modal-backdrop'
        document.body.appendChild(backdrop)
      }
    }

    const hideViewRAMModal = () => {
      const modal = document.getElementById('viewRAMModal')
      const backdrop = document.getElementById('view-ram-modal-backdrop')
      
      if (modal) {
        modal.classList.remove('show')
        modal.style.display = 'none'
        document.body.classList.remove('modal-open')
      }
      
      if (backdrop) {
        backdrop.remove()
      }
    }

    const editRAM = (ram) => {
      // For now, just show the RAM details
      viewRAM(ram)
    }

    const deleteRAM = async (ram) => {
      if (!confirm('Are you sure you want to delete this RAM?')) return
      
      try {
        const response = await axios.delete(`http://localhost:8000/api/components/rams/${ram.id}`)
        
        if (response.data.success) {
          await refreshData()
          alert('RAM deleted successfully!')
        }
      } catch (error) {
        console.error('Error deleting RAM:', error)
        alert('Error deleting RAM: ' + (error.response?.data?.message || error.message))
      }
    }

    const filterRAM = () => {
      // This is handled by the computed property
    }

    onMounted(() => {
      fetchRAM()
    })

    return {
      isNavCollapsed,
      rams,
      statusFilter,
      selectedRAM,
      newRAM,
      filteredRAM,
      availableCount,
      inUseCount,
      defectiveCount,
      toggleNav,
      openProfile,
      openSettings,
      getStatusBadgeClass,
      formatDate,
      refreshData,
      showCreateRAMModal,
      hideCreateRAMModal,
      createRAM,
      viewRAM,
      hideViewRAMModal,
      editRAM,
      deleteRAM,
      filterRAM
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
