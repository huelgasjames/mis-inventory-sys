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
      
      <!-- Component Content -->
      <div class="container-fluid p-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h1 class="h3 mb-0" style="color: black;">Component Manager</h1>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" @click="refreshData">
              <i class="bi bi-arrow-clockwise me-2"></i>Refresh
            </button>
          </div>
        </div>

        <!-- Component Type Tabs -->
        <div class="mb-4">
          <ul class="nav nav-tabs">
            <li class="nav-item">
              <a class="nav-link" :class="{ 'active': activeComponentType === 'processors' }" href="#" @click.prevent="setComponentType('processors')">
                <i class="bi bi-cpu me-2"></i>Processors
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" :class="{ 'active': activeComponentType === 'motherboards' }" href="#" @click.prevent="setComponentType('motherboards')">
                <i class="bi bi-motherboard me-2"></i>Motherboards
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" :class="{ 'active': activeComponentType === 'video_cards' }" href="#" @click.prevent="setComponentType('video_cards')">
                <i class="bi bi-gpu-card me-2"></i>Video Cards
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" :class="{ 'active': activeComponentType === 'dvd_roms' }" href="#" @click.prevent="setComponentType('dvd_roms')">
                <i class="bi bi-disc me-2"></i>DVD ROMs
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" :class="{ 'active': activeComponentType === 'psus' }" href="#" @click.prevent="setComponentType('psus')">
                <i class="bi bi-lightning-charge me-2"></i>PSUs
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" :class="{ 'active': activeComponentType === 'rams' }" href="#" @click.prevent="setComponentType('rams')">
                <i class="bi bi-memory me-2"></i>RAM
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" :class="{ 'active': activeComponentType === 'storages' }" href="#" @click.prevent="setComponentType('storages')">
                <i class="bi bi-hdd me-2"></i>Storage
              </a>
            </li>
          </ul>
        </div>

        <!-- Current Component Type Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h4 class="mb-0" style="color: black;">{{ componentTitle }} Management</h4>
          <button class="btn btn-primary" @click="showCreateComponentModal">
            <i class="bi bi-plus-circle me-2"></i>Add {{ componentTitle }}
          </button>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
          <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                    <i :class="componentIcon" class="text-primary fs-4"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Total {{ componentTitle }}</h6>
                    <h3 class="mb-0">{{ components.length }}</h3>
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

        <!-- Components Table -->
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0">{{ componentTitle }} Inventory</h5>
              <div class="d-flex gap-2">
                <select class="form-select form-select-sm" v-model="statusFilter" @change="filterComponents">
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
                    <th>{{ componentType === 'rams' ? 'Capacity' : componentType === 'storages' ? 'Capacity' : componentType === 'psus' ? 'Wattage' : componentType === 'dvd_roms' ? 'Type' : 'Model' }}</th>
                    <th>Status</th>
                    <th>Created Date</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="component in filteredComponents" :key="component.id">
                    <td>{{ component.id }}</td>
                    <td>
                      <div class="d-flex align-items-center">
                        <i :class="componentIcon" class="me-2 text-primary"></i>
                        <span class="fw-semibold">{{ getComponentDisplayValue(component) }}</span>
                      </div>
                    </td>
                    <td>
                      <span :class="getStatusBadgeClass(component.status)">
                        {{ component.status }}
                      </span>
                    </td>
                    <td>{{ formatDate(component.created_at) }}</td>
                    <td>
                      <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" @click="viewComponent(component)">
                          <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-outline-warning" @click="editComponent(component)">
                          <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-outline-danger" @click="deleteComponent(component)">
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

  <!-- Create Component Modal -->
  <div class="modal fade" id="createComponentModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add {{ componentTitle }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="createComponent">
            <div class="mb-3">
              <label class="form-label">{{ componentType === 'rams' ? 'Capacity' : componentType === 'storages' ? 'Capacity' : componentType === 'psus' ? 'Wattage' : componentType === 'dvd_roms' ? 'Type' : 'Model' }} *</label>
              <select 
                v-if="componentType === 'rams'" 
                class="form-select" 
                v-model="newComponent.value" 
                required
              >
                <option value="">Select Capacity</option>
                <option value="4GB">4GB</option>
                <option value="8GB">8GB</option>
                <option value="16GB">16GB</option>
                <option value="32GB">32GB</option>
              </select>
              <select 
                v-else-if="componentType === 'psus'" 
                class="form-select" 
                v-model="newComponent.value" 
                required
              >
                <option value="">Select Wattage</option>
                <option value="350">350W</option>
                <option value="450">450W</option>
                <option value="550">550W</option>
                <option value="650">650W</option>
                <option value="750">750W</option>
                <option value="850">850W</option>
                <option value="1000">1000W</option>
              </select>
              <select 
                v-else-if="componentType === 'dvd_roms'" 
                class="form-select" 
                v-model="newComponent.value" 
                required
              >
                <option value="">Select Type</option>
                <option value="CD-ROM">CD-ROM</option>
                <option value="DVD-ROM">DVD-ROM</option>
                <option value="DVD-RW">DVD-RW</option>
                <option value="Blu-ray">Blu-ray</option>
                <option value="Blu-ray RW">Blu-ray RW</option>
              </select>
              <input 
                v-else 
                type="text" 
                class="form-control" 
                v-model="newComponent.value" 
                :placeholder="getPlaceholder()"
                required
              >
            </div>
            <div class="mb-3">
              <label class="form-label">Status *</label>
              <select class="form-select" v-model="newComponent.status" required>
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
          <button type="button" class="btn btn-primary" @click="createComponent">Add {{ componentTitle }}</button>
        </div>
      </div>
    </div>
  </div>

  <!-- View Component Modal -->
  <div class="modal fade" id="viewComponentModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ componentTitle }} Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" v-if="selectedComponent">
          <div class="mb-3">
            <label class="form-label">ID</label>
            <div class="form-control bg-light">{{ selectedComponent.id }}</div>
          </div>
          <div class="mb-3">
            <label class="form-label">{{ componentType === 'rams' ? 'Capacity' : componentType === 'storages' ? 'Capacity' : componentType === 'psus' ? 'Wattage' : componentType === 'dvd_roms' ? 'Type' : 'Model' }}</label>
            <div class="form-control bg-light">{{ getComponentDisplayValue(selectedComponent) }}</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <div class="form-control bg-light">
              <span :class="getStatusBadgeClass(selectedComponent.status)">
                {{ selectedComponent.status }}
              </span>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Created Date</label>
            <div class="form-control bg-light">{{ formatDate(selectedComponent.created_at) }}</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Component Modal -->
  <div class="modal fade" id="editComponentModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit {{ componentTitle }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="updateComponent">
            <div class="mb-3">
              <label class="form-label">{{ componentType === 'rams' ? 'Capacity' : componentType === 'storages' ? 'Capacity' : componentType === 'psus' ? 'Wattage' : componentType === 'dvd_roms' ? 'Type' : 'Model' }} *</label>
              <select 
                v-if="componentType === 'rams'" 
                class="form-select" 
                v-model="editComponentData.value" 
                required
              >
                <option value="">Select Capacity</option>
                <option value="4GB">4GB</option>
                <option value="8GB">8GB</option>
                <option value="16GB">16GB</option>
                <option value="32GB">32GB</option>
              </select>
              <select 
                v-else-if="componentType === 'psus'" 
                class="form-select" 
                v-model="editComponentData.value" 
                required
              >
                <option value="">Select Wattage</option>
                <option value="350">350W</option>
                <option value="450">450W</option>
                <option value="550">550W</option>
                <option value="650">650W</option>
                <option value="750">750W</option>
                <option value="850">850W</option>
                <option value="1000">1000W</option>
              </select>
              <select 
                v-else-if="componentType === 'dvd_roms'" 
                class="form-select" 
                v-model="editComponentData.value" 
                required
              >
                <option value="">Select Type</option>
                <option value="CD-ROM">CD-ROM</option>
                <option value="DVD-ROM">DVD-ROM</option>
                <option value="DVD-RW">DVD-RW</option>
                <option value="Blu-ray">Blu-ray</option>
                <option value="Blu-ray RW">Blu-ray RW</option>
              </select>
              <input 
                v-else 
                type="text" 
                class="form-control" 
                v-model="editComponentData.value" 
                :placeholder="getPlaceholder()"
                required
              >
            </div>
            <div class="mb-3">
              <label class="form-label">Status *</label>
              <select class="form-select" v-model="editComponentData.status" required>
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
          <button type="button" class="btn btn-primary" @click="updateComponent">Update {{ componentTitle }}</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import AppNav from '@/components/AppNav.vue'
import AppHeader from '@/components/AppHeader.vue'
import axios from 'axios'
import { Modal } from 'bootstrap'

export default {
  name: 'ComponentManager',
  components: {
    AppNav,
    AppHeader
  },
  setup() {
    const route = useRoute()
    const isNavCollapsed = ref(false)
    const activeComponentType = ref('processors')
    const components = ref([])
    const statusFilter = ref('')
    const selectedComponent = ref(null)
    
    const newComponent = ref({
      value: '',
      status: 'Available'
    })
    
    const editingComponent = ref(null)
    const editComponentData = ref({
      value: '',
      status: 'Available'
    })

    const componentType = computed(() => {
      return activeComponentType.value
    })

    const setComponentType = (type) => {
      activeComponentType.value = type
      fetchComponents()
    }

    const componentTitle = computed(() => {
      const titles = {
        processors: 'Processors',
        motherboards: 'Motherboards',
        rams: 'RAM',
        storages: 'Storage',
        video_cards: 'Video Cards',
        psus: 'PSUs',
        dvd_roms: 'DVD ROMs'
      }
      return titles[componentType.value] || 'Components'
    })

    const componentIcon = computed(() => {
      const icons = {
        processors: 'bi bi-cpu',
        motherboards: 'bi bi-motherboard',
        rams: 'bi bi-memory',
        storages: 'bi bi-hdd',
        video_cards: 'bi bi-gpu-card',
        psus: 'bi bi-lightning-charge',
        dvd_roms: 'bi bi-disc'
      }
      return icons[componentType.value] || 'bi bi-cpu'
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

    const getComponentDisplayValue = (component) => {
      if (componentType.value === 'rams' || componentType.value === 'storages') {
        return component.capacity || component.model || '-'
      }
      if (componentType.value === 'psus') {
        return component.wattage ? `${component.wattage}W` : component.model || '-'
      }
      if (componentType.value === 'dvd_roms') {
        return component.type_field || component.model || '-'
      }
      return component.model || '-'
    }

    const getPlaceholder = () => {
      const placeholders = {
        processors: 'e.g., Intel Core i5-10400',
        motherboards: 'e.g., ASUS Prime H410M',
        rams: 'e.g., 16GB DDR4',
        storages: 'e.g., 512GB SSD',
        video_cards: 'e.g., NVIDIA GTX 1650',
        psus: 'e.g., 450',
        dvd_roms: 'e.g., DVD-RW'
      }
      return placeholders[componentType.value] || 'Enter value'
    }

    const filteredComponents = computed(() => {
      if (!statusFilter.value) return components.value
      return components.value.filter(c => c.status === statusFilter.value)
    })

    const availableCount = computed(() => components.value.filter(c => c.status === 'Available').length)
    const inUseCount = computed(() => components.value.filter(c => c.status === 'In Use').length)
    const defectiveCount = computed(() => components.value.filter(c => c.status === 'Defective').length)

    const fetchComponents = async () => {
      try {
        const response = await axios.get('http://localhost:8000/api/components')
        components.value = response.data.data[componentType.value] || []
      } catch (error) {
        console.error('Error fetching components:', error)
      }
    }

    const refreshData = async () => {
      await fetchComponents()
    }

    const showCreateComponentModal = () => {
      const modal = new Modal(document.getElementById('createComponentModal'))
      modal.show()
    }

    const createComponent = async () => {
      try {
        const endpoint = `http://localhost:8000/api/components/${componentType.value.replace('_', '-')}`
        const payload = componentType.value === 'psus' 
          ? { wattage: newComponent.value.value, status: newComponent.value.status }
          : componentType.value === 'rams' || componentType.value === 'storages'
          ? { capacity: newComponent.value.value, status: newComponent.value.status }
          : componentType.value === 'dvd_roms'
          ? { type_field: newComponent.value.value, status: newComponent.value.status }
          : { model: newComponent.value.value, status: newComponent.value.status }

        const response = await axios.post(endpoint, payload)
        
        if (response.data.success) {
          Modal.getInstance(document.getElementById('createComponentModal')).hide()
          await refreshData()
          
          // Reset form
          newComponent.value = {
            value: '',
            status: 'Available'
          }
          
          alert(`${componentTitle.value} added successfully!`)
        }
      } catch (error) {
        console.error('Error creating component:', error)
        alert(`Error adding ${componentTitle.value}: ` + (error.response?.data?.message || error.message))
      }
    }

    const viewComponent = (component) => {
      selectedComponent.value = component
      const modal = new Modal(document.getElementById('viewComponentModal'))
      modal.show()
    }

    const editComponent = (component) => {
      editingComponent.value = component
      editComponentData.value = {
        value: getComponentDisplayValue(component),
        status: component.status
      }
      const modal = new Modal(document.getElementById('editComponentModal'))
      modal.show()
    }

    const updateComponent = async () => {
      try {
        const endpoint = `http://localhost:8000/api/components/${componentType.value.replace('_', '-')}/${editingComponent.value.id}`
        const payload = componentType.value === 'psus' 
          ? { wattage: editComponentData.value.value, status: editComponentData.value.status }
          : componentType.value === 'rams' || componentType.value === 'storages'
          ? { capacity: editComponentData.value.value, status: editComponentData.value.status }
          : componentType.value === 'dvd_roms'
          ? { type_field: editComponentData.value.value, status: editComponentData.value.status }
          : { model: editComponentData.value.value, status: editComponentData.value.status }

        const response = await axios.put(endpoint, payload)
        
        if (response.data.success) {
          Modal.getInstance(document.getElementById('editComponentModal')).hide()
          await refreshData()
          
          // Reset form
          editingComponent.value = null
          editComponentData.value = {
            value: '',
            status: 'Available'
          }
          
          alert(`${componentTitle.value.slice(0, -1)} updated successfully!`)
        }
      } catch (error) {
        console.error('Error updating component:', error)
        alert(`Error updating ${componentTitle.value.slice(0, -1)}: ` + (error.response?.data?.message || error.message))
      }
    }

    const deleteComponent = async (component) => {
      if (!confirm(`Are you sure you want to delete this ${componentTitle.value.slice(0, -1)}?`)) return
      
      try {
        const response = await axios.delete(`http://localhost:8000/api/components/${componentType.value.replace('_', '-')}/${component.id}`)
        
        if (response.data.success) {
          await refreshData()
          alert(`${componentTitle.value.slice(0, -1)} deleted successfully!`)
        }
      } catch (error) {
        console.error('Error deleting component:', error)
        alert(`Error deleting ${componentTitle.value.slice(0, -1)}: ` + (error.response?.data?.message || error.message))
      }
    }

    const filterComponents = () => {
      // This is handled by the computed property
    }

    onMounted(() => {
      fetchComponents()
    })

    return {
      isNavCollapsed,
      components,
      statusFilter,
      selectedComponent,
      newComponent,
      editingComponent,
      editComponentData,
      componentType,
      componentTitle,
      componentIcon,
      filteredComponents,
      availableCount,
      inUseCount,
      defectiveCount,
      toggleNav,
      openProfile,
      openSettings,
      getStatusBadgeClass,
      formatDate,
      getComponentDisplayValue,
      getPlaceholder,
      refreshData,
      showCreateComponentModal,
      createComponent,
      updateComponent,
      viewComponent,
      editComponent,
      deleteComponent,
      filterComponents,
      activeComponentType,
      setComponentType
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
