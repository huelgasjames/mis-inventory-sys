<template>
  <div class="dashboard-layout">
    <!-- Navigation Sidebar -->
    <AppNav :is-collapsed="isNavCollapsed" />
    
    <!-- Main Content Area -->
    <div class="main-content" :class="{ 'collapsed': isNavCollapsed }">
      <!-- Header -->
      <AppHeader 
        :is-collapsed="isNavCollapsed"
        @sidebar-toggle="(collapsed) => isNavCollapsed = collapsed"
        @profile-open="openProfile"
        @settings-open="openSettings"
      />
      
      <!-- DVD ROMs Content -->
      <div class="container-fluid p-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h1 class="h3 mb-0" style="color: black;">DVD ROMs Management</h1>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" @click="refreshData">
              <i class="bi bi-arrow-clockwise me-2"></i>Refresh
            </button>
            <button class="btn btn-primary" @click="showCreateDVDROMModal">
              <i class="bi bi-plus-circle me-2"></i>Add DVD ROM
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
                    <i class="bi bi-disc text-primary fs-4"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Total DVD ROMs</h6>
                    <h3 class="mb-0">{{ dvdRoms.length }}</h3>
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

        <!-- DVD ROMs Table -->
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0">DVD ROMs Inventory</h5>
              <div class="d-flex gap-2">
                <select class="form-select form-select-sm" v-model="statusFilter" @change="filterDVDROMs">
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
                    <th>Type</th>
                    <th>Status</th>
                    <th>Created Date</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="dvdRom in filteredDVDROMs" :key="dvdRom.id">
                    <td>{{ dvdRom.id }}</td>
                    <td>
                      <div class="d-flex align-items-center">
                        <i class="bi bi-disc me-2 text-primary"></i>
                        <span class="fw-semibold">{{ dvdRom.type_field }}</span>
                      </div>
                    </td>
                    <td>
                      <span :class="getStatusBadgeClass(dvdRom.status)">
                        {{ dvdRom.status }}
                      </span>
                    </td>
                    <td>{{ formatDate(dvdRom.created_at) }}</td>
                    <td>
                      <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" @click="viewDVDROM(dvdRom)">
                          <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-outline-warning" @click="editDVDROM(dvdRom)">
                          <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-outline-danger" @click="deleteDVDROM(dvdRom)">
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

  <!-- Create DVD ROM Modal -->
  <div class="modal fade" id="createDVDROMModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add DVD ROM</h5>
          <button type="button" class="btn-close" @click="hideCreateDVDROMModal"></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="createDVDROM">
            <div class="mb-3">
              <label class="form-label">Type *</label>
              <select class="form-select" v-model="newDVDROM.type_field" required>
                <option value="">Select Type</option>
                <option value="CD-ROM">CD-ROM</option>
                <option value="DVD-ROM">DVD-ROM</option>
                <option value="DVD-RW">DVD-RW</option>
                <option value="Blu-ray">Blu-ray</option>
                <option value="Blu-ray RW">Blu-ray RW</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Status *</label>
              <select class="form-select" v-model="newDVDROM.status" required>
                <option value="">Select Status</option>
                <option value="Available">Available</option>
                <option value="In Use">In Use</option>
                <option value="Defective">Defective</option>
              </select>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="hideCreateDVDROMModal">Cancel</button>
          <button type="button" class="btn btn-primary" @click="createDVDROM">Add DVD ROM</button>
        </div>
      </div>
    </div>
  </div>

  <!-- View DVD ROM Modal -->
  <div class="modal fade" id="viewDVDROMModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">DVD ROM Details</h5>
          <button type="button" class="btn-close" @click="hideViewDVDROMModal"></button>
        </div>
        <div class="modal-body" v-if="selectedDVDROM">
          <div class="mb-3">
            <label class="form-label">ID</label>
            <div class="form-control bg-light">{{ selectedDVDROM.id }}</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Type</label>
            <div class="form-control bg-light">{{ selectedDVDROM.type_field }}</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <div class="form-control bg-light">
              <span :class="getStatusBadgeClass(selectedDVDROM.status)">
                {{ selectedDVDROM.status }}
              </span>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Created Date</label>
            <div class="form-control bg-light">{{ formatDate(selectedDVDROM.created_at) }}</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="hideViewDVDROMModal">Close</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed, watch } from 'vue'
import AppNav from '@/components/AppNav.vue'
import AppHeader from '@/components/AppHeader.vue'
import axios from 'axios'

export default {
  name: 'DVDROMs',
  components: {
    AppNav,
    AppHeader
  },
  setup() {
    const isNavCollapsed = ref(false)
    const dvdRoms = ref([])
    const statusFilter = ref('')
    const selectedDVDROM = ref(null)
    
    const newDVDROM = ref({
      type_field: 'DVD-RW',
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

    const filteredDVDROMs = computed(() => {
      if (!statusFilter.value) return dvdRoms.value
      return dvdRoms.value.filter(d => d.status === statusFilter.value)
    })

    const availableCount = computed(() => dvdRoms.value.filter(d => d.status === 'Available').length)
    const inUseCount = computed(() => dvdRoms.value.filter(d => d.status === 'In Use').length)
    const defectiveCount = computed(() => dvdRoms.value.filter(d => d.status === 'Defective').length)

    const fetchDVDROMs = async () => {
      try {
        const response = await axios.get('http://localhost:8000/api/components')
        dvdRoms.value = response.data.data.dvd_roms || []
      } catch (error) {
        console.error('Error fetching DVD ROMs:', error)
        // Fallback data for demo
        dvdRoms.value = [
          { id: 1, type_field: 'DVD-RW', status: 'Available', created_at: '2024-01-01' },
          { id: 2, type_field: 'DVD-ROM', status: 'In Use', created_at: '2024-01-02' },
          { id: 3, type_field: 'Blu-ray', status: 'Defective', created_at: '2024-01-03' }
        ]
      }
    }

    const refreshData = async () => {
      await fetchDVDROMs()
    }

    const showCreateDVDROMModal = () => {
      const modal = document.getElementById('createDVDROMModal')
      if (modal) {
        modal.classList.add('show')
        modal.style.display = 'block'
        document.body.classList.add('modal-open')
        
        // Create backdrop
        const backdrop = document.createElement('div')
        backdrop.className = 'modal-backdrop fade show'
        backdrop.id = 'dvdrom-modal-backdrop'
        document.body.appendChild(backdrop)
      }
    }

    const createDVDROM = async () => {
      try {
        const response = await axios.post('http://localhost:8000/api/components/dvd-roms', newDVDROM.value)
        
        if (response.data.success) {
          hideCreateDVDROMModal()
          await refreshData()
          
          // Reset form
          newDVDROM.value = {
            type_field: 'DVD-RW',
            status: 'Available'
          }
          
          alert('DVD ROM added successfully!')
        }
      } catch (error) {
        console.error('Error creating DVD ROM:', error)
        alert('Error adding DVD ROM: ' + (error.response?.data?.message || error.message))
      }
    }

    const hideCreateDVDROMModal = () => {
      const modal = document.getElementById('createDVDROMModal')
      const backdrop = document.getElementById('dvdrom-modal-backdrop')
      
      if (modal) {
        modal.classList.remove('show')
        modal.style.display = 'none'
        document.body.classList.remove('modal-open')
      }
      
      if (backdrop) {
        backdrop.remove()
      }
    }

    const viewDVDROM = (dvdRom) => {
      selectedDVDROM.value = dvdRom
      const modal = document.getElementById('viewDVDROMModal')
      if (modal) {
        modal.classList.add('show')
        modal.style.display = 'block'
        document.body.classList.add('modal-open')
        
        // Create backdrop
        const backdrop = document.createElement('div')
        backdrop.className = 'modal-backdrop fade show'
        backdrop.id = 'view-dvdrom-modal-backdrop'
        document.body.appendChild(backdrop)
      }
    }

    const hideViewDVDROMModal = () => {
      const modal = document.getElementById('viewDVDROMModal')
      const backdrop = document.getElementById('view-dvdrom-modal-backdrop')
      
      if (modal) {
        modal.classList.remove('show')
        modal.style.display = 'none'
        document.body.classList.remove('modal-open')
      }
      
      if (backdrop) {
        backdrop.remove()
      }
    }

    const editDVDROM = (dvdRom) => {
      // For now, just show the DVD ROM details
      viewDVDROM(dvdRom)
    }

    const deleteDVDROM = async (dvdRom) => {
      if (!confirm('Are you sure you want to delete this DVD ROM?')) return
      
      try {
        const response = await axios.delete(`http://localhost:8000/api/components/dvd-roms/${dvdRom.id}`)
        
        if (response.data.success) {
          await refreshData()
          alert('DVD ROM deleted successfully!')
        }
      } catch (error) {
        console.error('Error deleting DVD ROM:', error)
        alert('Error deleting DVD ROM: ' + (error.response?.data?.message || error.message))
      }
    }

    const filterDVDROMs = () => {
      // This is handled by the computed property
    }

    onMounted(() => {
      fetchDVDROMs()
    })

    return {
      isNavCollapsed,
      dvdRoms,
      statusFilter,
      selectedDVDROM,
      newDVDROM,
      filteredDVDROMs,
      availableCount,
      inUseCount,
      defectiveCount,
      toggleNav,
      openProfile,
      openSettings,
      getStatusBadgeClass,
      formatDate,
      refreshData,
      showCreateDVDROMModal,
      hideCreateDVDROMModal,
      createDVDROM,
      viewDVDROM,
      hideViewDVDROMModal,
      editDVDROM,
      deleteDVDROM,
      filterDVDROMs
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
