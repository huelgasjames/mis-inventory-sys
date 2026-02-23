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
      
      <!-- Storage Content -->
      <div class="container-fluid p-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h1 class="h3 mb-0" style="color: black;">Storage Management</h1>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" @click="refreshData">
              <i class="bi bi-arrow-clockwise me-2"></i>Refresh
            </button>
            <button class="btn btn-primary" @click="showCreateStorageModal">
              <i class="bi bi-plus-circle me-2"></i>Add Storage
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
                    <i class="bi bi-hdd text-primary fs-4"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Total Storage</h6>
                    <h3 class="mb-0">{{ storages.length }}</h3>
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

        <!-- Storage Table -->
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0">Storage Inventory</h5>
              <div class="d-flex gap-2">
                <select class="form-select form-select-sm" v-model="statusFilter" @change="filterStorage">
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
                    <th>Status</th>
                    <th>Created Date</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="storage in filteredStorage" :key="storage.id">
                    <td>{{ storage.id }}</td>
                    <td>
                      <div class="d-flex align-items-center">
                        <i class="bi bi-hdd me-2 text-primary"></i>
                        <span class="fw-semibold">{{ storage.capacity }}</span>
                      </div>
                    </td>
                    <td>
                      <span :class="getStatusBadgeClass(storage.status)">
                        {{ storage.status }}
                      </span>
                    </td>
                    <td>{{ formatDate(storage.created_at) }}</td>
                    <td>
                      <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" @click="viewStorage(storage)">
                          <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-outline-warning" @click="editStorage(storage)">
                          <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-outline-danger" @click="deleteStorage(storage)">
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

  <!-- Create Storage Modal -->
  <div class="modal fade" id="createStorageModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Storage</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="createStorage">
            <div class="mb-3">
              <label class="form-label">Capacity *</label>
              <input type="text" class="form-control" v-model="newStorage.capacity" placeholder="e.g., 512GB SSD" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Status *</label>
              <select class="form-select" v-model="newStorage.status" required>
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
          <button type="button" class="btn btn-primary" @click="createStorage">Add Storage</button>
        </div>
      </div>
    </div>
  </div>

  <!-- View Storage Modal -->
  <div class="modal fade" id="viewStorageModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Storage Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" v-if="selectedStorage">
          <div class="mb-3">
            <label class="form-label">ID</label>
            <div class="form-control bg-light">{{ selectedStorage.id }}</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Capacity</label>
            <div class="form-control bg-light">{{ selectedStorage.capacity }}</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <div class="form-control bg-light">
              <span :class="getStatusBadgeClass(selectedStorage.status)">
                {{ selectedStorage.status }}
              </span>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Created Date</label>
            <div class="form-control bg-light">{{ formatDate(selectedStorage.created_at) }}</div>
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
import { ref, onMounted, computed, watch } from 'vue'
import AppNav from '@/components/AppNav.vue'
import AppHeader from '@/components/AppHeader.vue'
import axios from 'axios'

export default {
  name: 'Storage',
  components: {
    AppNav,
    AppHeader
  },
  setup() {
    const isNavCollapsed = ref(false)
    const storages = ref([])
    const statusFilter = ref('')
    const selectedStorage = ref(null)
    
    const newStorage = ref({
      capacity: '',
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

    const filteredStorage = computed(() => {
      if (!statusFilter.value) return storages.value
      return storages.value.filter(s => s.status === statusFilter.value)
    })

    const availableCount = computed(() => storages.value.filter(s => s.status === 'Available').length)
    const inUseCount = computed(() => storages.value.filter(s => s.status === 'In Use').length)
    const defectiveCount = computed(() => storages.value.filter(s => s.status === 'Defective').length)

    const fetchStorage = async () => {
      try {
        const response = await axios.get('http://localhost:8000/api/components')
        storages.value = response.data.data.storages || []
      } catch (error) {
        console.error('Error fetching storage:', error)
        // Fallback data for demo
        storages.value = [
          { id: 1, capacity: '512GB SSD', status: 'Available', created_at: '2024-01-01' },
          { id: 2, capacity: '1TB HDD', status: 'In Use', created_at: '2024-01-02' },
          { id: 3, capacity: '256GB SSD', status: 'Defective', created_at: '2024-01-03' }
        ]
      }
    }

    const refreshData = async () => {
      await fetchStorage()
    }

    const showCreateStorageModal = () => {
      const modal = new bootstrap.Modal(document.getElementById('createStorageModal'))
      modal.show()
    }

    const createStorage = async () => {
      try {
        const response = await axios.post('http://localhost:8000/api/components/storages', newStorage.value)
        
        if (response.data.success) {
          bootstrap.Modal.getInstance(document.getElementById('createStorageModal')).hide()
          await refreshData()
          
          // Reset form
          newStorage.value = {
            capacity: '',
            status: 'Available'
          }
          
          alert('Storage added successfully!')
        }
      } catch (error) {
        console.error('Error creating storage:', error)
        alert('Error adding storage: ' + (error.response?.data?.message || error.message))
      }
    }

    const viewStorage = (storage) => {
      selectedStorage.value = storage
      const modal = new bootstrap.Modal(document.getElementById('viewStorageModal'))
      modal.show()
    }

    const editStorage = (storage) => {
      // For now, just show the storage details
      viewStorage(storage)
    }

    const deleteStorage = async (storage) => {
      if (!confirm('Are you sure you want to delete this storage?')) return
      
      try {
        const response = await axios.delete(`http://localhost:8000/api/components/storages/${storage.id}`)
        
        if (response.data.success) {
          await refreshData()
          alert('Storage deleted successfully!')
        }
      } catch (error) {
        console.error('Error deleting storage:', error)
        alert('Error deleting storage: ' + (error.response?.data?.message || error.message))
      }
    }

    const filterStorage = () => {
      // This is handled by the computed property
    }

    onMounted(() => {
      fetchStorage()
    })

    return {
      isNavCollapsed,
      storages,
      statusFilter,
      selectedStorage,
      newStorage,
      filteredStorage,
      availableCount,
      inUseCount,
      defectiveCount,
      toggleNav,
      openProfile,
      openSettings,
      getStatusBadgeClass,
      formatDate,
      refreshData,
      showCreateStorageModal,
      createStorage,
      viewStorage,
      editStorage,
      deleteStorage,
      filterStorage
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
