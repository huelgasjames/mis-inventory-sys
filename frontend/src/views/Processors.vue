<template>
  <div class="dashboard-layout">
    <!-- Navigation Sidebar -->
    <AppNav :is-collapsed="isNavCollapsed" />
    
    <!-- Main Content Area -->
    <div class="main-content" :class="{ 'collapsed': isNavCollapsed }">
      <!-- Header -->
      <AppHeader 
        :is-collapsed="isNavCollapsed"
        @sidebar-toggle="(collapsed) => {
          console.log('Processors received sidebar-toggle:', collapsed)
          isNavCollapsed = collapsed
        }"
        @profile-open="openProfile"
        @settings-open="openSettings"
      />
      
      <!-- Processors Content -->
      <div class="container-fluid p-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h1 class="h3 mb-0">Processors Management</h1>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" @click="refreshData">
              <i class="bi bi-arrow-clockwise me-2"></i>Refresh
            </button>
            <button class="btn btn-primary" @click="showCreateProcessorModal">
              <i class="bi bi-plus-circle me-2"></i>Add Processor
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
                    <i class="bi bi-cpu text-primary fs-4"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Total Processors</h6>
                    <h3 class="mb-0">{{ processors.length }}</h3>
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

        <!-- Processors Table -->
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0">Processors Inventory</h5>
              <div class="d-flex gap-2">
                <select class="form-select form-select-sm" v-model="statusFilter" @change="filterProcessors">
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
                  <tr v-for="processor in filteredProcessors" :key="processor.id">
                    <td>{{ processor.id }}</td>
                    <td>
                      <div class="d-flex align-items-center">
                        <i class="bi bi-cpu me-2 text-primary"></i>
                        <span class="fw-semibold">{{ processor.model }}</span>
                      </div>
                    </td>
                    <td>
                      <span :class="getStatusBadgeClass(processor.status)">
                        {{ processor.status }}
                      </span>
                    </td>
                    <td>{{ formatDate(processor.created_at) }}</td>
                    <td>
                      <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" @click="viewProcessor(processor)">
                          <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-outline-warning" @click="editProcessor(processor)">
                          <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-outline-danger" @click="deleteProcessor(processor)">
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

  <!-- Create Processor Modal -->
  <div class="modal fade" id="createProcessorModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Processor</h5>
          <button type="button" class="btn-close" @click="hideCreateProcessorModal"></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="createProcessor">
            <div class="mb-3">
              <label class="form-label">Model *</label>
              <input type="text" class="form-control" v-model="newProcessor.model" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Status *</label>
              <select class="form-select" v-model="newProcessor.status" required>
                <option value="">Select Status</option>
                <option value="Available">Available</option>
                <option value="In Use">In Use</option>
                <option value="Defective">Defective</option>
              </select>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="hideCreateProcessorModal">Cancel</button>
          <button type="button" class="btn btn-primary" @click="createProcessor">Add Processor</button>
        </div>
      </div>
    </div>
  </div>

  <!-- View Processor Modal -->
  <div class="modal fade" id="viewProcessorModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Processor Details</h5>
          <button type="button" class="btn-close" @click="hideViewProcessorModal"></button>
        </div>
        <div class="modal-body" v-if="selectedProcessor">
          <div class="mb-3">
            <label class="form-label">ID</label>
            <div class="form-control bg-light">{{ selectedProcessor.id }}</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Model</label>
            <div class="form-control bg-light">{{ selectedProcessor.model }}</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <div class="form-control bg-light">
              <span :class="getStatusBadgeClass(selectedProcessor.status)">
                {{ selectedProcessor.status }}
              </span>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Created Date</label>
            <div class="form-control bg-light">{{ formatDate(selectedProcessor.created_at) }}</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="hideViewProcessorModal">Close</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, watch, computed } from 'vue'
import AppNav from '@/components/AppNav.vue'
import AppHeader from '@/components/AppHeader.vue'
import axios from 'axios'

export default {
  name: 'Processors',
  components: {
    AppNav,
    AppHeader
  },
  setup() {
    const isNavCollapsed = ref(false)
    
    // Watch for changes in navigation state
    watch(isNavCollapsed, (newValue, oldValue) => {
      console.log('Processors isNavCollapsed changed from', oldValue, 'to', newValue)
    })
    
    const processors = ref([])
    const statusFilter = ref('')
    const selectedProcessor = ref(null)
    
    const newProcessor = ref({
      model: 'Intel Core i5-12400',
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

    const filteredProcessors = computed(() => {
      if (!statusFilter.value) return processors.value
      return processors.value.filter(p => p.status === statusFilter.value)
    })

    const availableCount = computed(() => processors.value.filter(p => p.status === 'Available').length)
    const inUseCount = computed(() => processors.value.filter(p => p.status === 'In Use').length)
    const defectiveCount = computed(() => processors.value.filter(p => p.status === 'Defective').length)

    const fetchProcessors = async () => {
      try {
        const response = await axios.get('http://localhost:8000/api/components')
        processors.value = response.data.data.processors || []
      } catch (error) {
        console.error('Error fetching processors:', error)
      }
    }

    const refreshData = async () => {
      await fetchProcessors()
    }

    const showCreateProcessorModal = () => {
      const modal = document.getElementById('createProcessorModal')
      if (modal) {
        modal.classList.add('show')
        modal.style.display = 'block'
        document.body.classList.add('modal-open')
        
        // Create backdrop
        const backdrop = document.createElement('div')
        backdrop.className = 'modal-backdrop fade show'
        backdrop.id = 'processor-modal-backdrop'
        document.body.appendChild(backdrop)
      }
    }

    const createProcessor = async () => {
      try {
        const response = await axios.post('http://localhost:8000/api/components/processors', newProcessor.value)
        
        if (response.data.success) {
          hideCreateProcessorModal()
          await refreshData()
          
          // Reset form
          newProcessor.value = {
            model: 'Intel Core i5-12400',
            status: 'Available'
          }
          
          alert('Processor added successfully!')
        }
      } catch (error) {
        console.error('Error creating Processor:', error)
        alert('Error adding Processor: ' + (error.response?.data?.message || error.message))
      }
    }

    const hideCreateProcessorModal = () => {
      const modal = document.getElementById('createProcessorModal')
      const backdrop = document.getElementById('processor-modal-backdrop')
      
      if (modal) {
        modal.classList.remove('show')
        modal.style.display = 'none'
        document.body.classList.remove('modal-open')
      }
      
      if (backdrop) {
        backdrop.remove()
      }
    }

    const viewProcessor = (processor) => {
      selectedProcessor.value = processor
      const modal = document.getElementById('viewProcessorModal')
      if (modal) {
        modal.classList.add('show')
        modal.style.display = 'block'
        document.body.classList.add('modal-open')
        
        // Create backdrop
        const backdrop = document.createElement('div')
        backdrop.className = 'modal-backdrop fade show'
        backdrop.id = 'view-processor-modal-backdrop'
        document.body.appendChild(backdrop)
      }
    }

    const hideViewProcessorModal = () => {
      const modal = document.getElementById('viewProcessorModal')
      const backdrop = document.getElementById('view-processor-modal-backdrop')
      
      if (modal) {
        modal.classList.remove('show')
        modal.style.display = 'none'
        document.body.classList.remove('modal-open')
      }
      
      if (backdrop) {
        backdrop.remove()
      }
    }

    const editProcessor = (processor) => {
      // For now, just show the processor details
      viewProcessor(processor)
    }

    const deleteProcessor = async (processor) => {
      if (!confirm('Are you sure you want to delete this processor?')) return
      
      try {
        const response = await axios.delete(`http://localhost:8000/api/components/processors/${processor.id}`)
        
        if (response.data.success) {
          await refreshData()
          alert('Processor deleted successfully!')
        }
      } catch (error) {
        console.error('Error deleting processor:', error)
        alert('Error deleting processor: ' + (error.response?.data?.message || error.message))
      }
    }

    const filterProcessors = () => {
      // This is handled by the computed property
    }

    onMounted(() => {
      fetchProcessors()
    })

    return {
      isNavCollapsed,
      processors,
      statusFilter,
      selectedProcessor,
      newProcessor,
      filteredProcessors,
      availableCount,
      inUseCount,
      defectiveCount,
      toggleNav,
      openProfile,
      openSettings,
      getStatusBadgeClass,
      formatDate,
      refreshData,
      showCreateProcessorModal,
      hideCreateProcessorModal,
      createProcessor,
      viewProcessor,
      hideViewProcessorModal,
      editProcessor,
      deleteProcessor,
      filterProcessors
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
