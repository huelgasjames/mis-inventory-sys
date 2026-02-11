<template>
  <div class="dashboard-layout">
    <!-- Navigation Sidebar -->
    <AppNav :is-collapsed="isNavCollapsed" />
    
    <!-- Main Content Area -->
    <div class="main-content" :class="{ 'expanded': !isNavCollapsed }">
      <!-- Header -->
      <AppHeader 
        @menu-toggle="toggleNav"
        @profile-open="openProfile"
        @settings-open="openSettings"
      />
      
      <!-- Components Content -->
      <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h1 class="h3 mb-0">Component Management</h1>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" @click="refreshData">
              <i class="bi bi-arrow-clockwise me-2"></i>Refresh
            </button>
            <button class="btn btn-primary" @click="showCreateModal">
              <i class="bi bi-plus-circle me-2"></i>Add Component
            </button>
          </div>
        </div>

        <!-- Component Types Tabs -->
        <ul class="nav nav-tabs mb-4">
          <li class="nav-item">
            <a class="nav-link" :class="{ active: activeTab === 'processors' }" href="#" @click="setActiveTab('processors')">
              <i class="bi bi-cpu me-2"></i>Processors
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" :class="{ active: activeTab === 'motherboards' }" href="#" @click="setActiveTab('motherboards')">
              <i class="bi bi-motherboard me-2"></i>Motherboards
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" :class="{ active: activeTab === 'rams' }" href="#" @click="setActiveTab('rams')">
              <i class="bi bi-memory me-2"></i>RAM
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" :class="{ active: activeTab === 'storage' }" href="#" @click="setActiveTab('storage')">
              <i class="bi bi-hdd me-2"></i>Storage
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" :class="{ active: activeTab === 'video-cards' }" href="#" @click="setActiveTab('video-cards')">
              <i class="bi bi-gpu-card me-2"></i>Video Cards
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" :class="{ active: activeTab === 'dvd-roms' }" href="#" @click="setActiveTab('dvd-roms')">
              <i class="bi bi-disc me-2"></i>DVD ROMs
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" :class="{ active: activeTab === 'psus' }" href="#" @click="setActiveTab('psus')">
              <i class="bi bi-lightning-charge me-2"></i>PSUs
            </a>
          </li>
        </ul>

        <!-- Components Table -->
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead class="table-light">
                  <tr>
                    <th>ID</th>
                    <th v-if="activeTab === 'processors'">Model</th>
                    <th v-if="activeTab === 'motherboards'">Model</th>
                    <th v-if="activeTab === 'rams'">Capacity</th>
                    <th v-if="activeTab === 'storage'">Capacity</th>
                    <th v-if="activeTab === 'video-cards'">Model</th>
                    <th v-if="activeTab === 'dvd-roms'">Type</th>
                    <th v-if="activeTab === 'psus'">Wattage</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="component in currentComponents" :key="component.id">
                    <td>{{ component.id }}</td>
                    <td v-if="activeTab === 'processors'">{{ component.model }}</td>
                    <td v-if="activeTab === 'motherboards'">{{ component.model }}</td>
                    <td v-if="activeTab === 'rams'">{{ component.capacity }}</td>
                    <td v-if="activeTab === 'storage'">{{ component.capacity }}</td>
                    <td v-if="activeTab === 'video-cards'">{{ component.model }}</td>
                    <td v-if="activeTab === 'dvd-roms'">{{ component.type }}</td>
                    <td v-if="activeTab === 'psus'">{{ component.wattage }}W</td>
                    <td>
                      <span class="badge" :class="getStatusClass(component.status)">
                        {{ component.status }}
                      </span>
                    </td>
                    <td>
                      <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" @click="editComponent(component)">
                          <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-outline-danger" @click="deleteComponent(component.id)">
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
          <h5 class="modal-title">Add Component</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="createComponent">
            <div class="mb-3">
              <label class="form-label">Component Type</label>
              <select class="form-select" v-model="newComponent.type" @change="resetComponentForm" required>
                <option value="">Select Type</option>
                <option value="processors">Processor</option>
                <option value="motherboards">Motherboard</option>
                <option value="rams">RAM</option>
                <option value="storage">Storage</option>
                <option value="video-cards">Video Card</option>
                <option value="dvd-roms">DVD ROM</option>
                <option value="psus">PSU</option>
              </select>
            </div>
            
            <!-- Dynamic fields based on component type -->
            <div v-if="newComponent.type === 'processors'" class="mb-3">
              <label class="form-label">Model</label>
              <input type="text" class="form-control" v-model="newComponent.model" required>
            </div>
            
            <div v-if="newComponent.type === 'motherboards'" class="mb-3">
              <label class="form-label">Model</label>
              <input type="text" class="form-control" v-model="newComponent.model" required>
            </div>
            
            <div v-if="newComponent.type === 'rams'" class="mb-3">
              <label class="form-label">Capacity</label>
              <input type="text" class="form-control" v-model="newComponent.capacity" placeholder="e.g., 8GB DDR4" required>
            </div>
            
            <div v-if="newComponent.type === 'storage'" class="mb-3">
              <label class="form-label">Capacity</label>
              <input type="text" class="form-control" v-model="newComponent.capacity" placeholder="e.g., 512GB SSD" required>
            </div>
            
            <div v-if="newComponent.type === 'video-cards'" class="mb-3">
              <label class="form-label">Model</label>
              <input type="text" class="form-control" v-model="newComponent.model" required>
            </div>
            
            <div v-if="newComponent.type === 'dvd-roms'" class="mb-3">
              <label class="form-label">Type</label>
              <input type="text" class="form-control" v-model="newComponent.type_field" placeholder="e.g., DVD-RW" required>
            </div>
            
            <div v-if="newComponent.type === 'psus'" class="mb-3">
              <label class="form-label">Wattage</label>
              <input type="number" class="form-control" v-model="newComponent.wattage" required>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Status</label>
              <select class="form-select" v-model="newComponent.status" required>
                <option value="Available">Available</option>
                <option value="In Use">In Use</option>
                <option value="Defective">Defective</option>
              </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Add Component</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import AppHeader from '@/components/AppHeader.vue'
import AppNav from '@/components/AppNav.vue'

const isNavCollapsed = ref(false)
const activeTab = ref('processors')
const components = ref({})

const newComponent = ref({
  type: '',
  model: '',
  capacity: '',
  type_field: '',
  wattage: '',
  status: 'Available'
})

const currentComponents = computed(() => {
  return components.value[activeTab.value] || []
})

const setActiveTab = (tab) => {
  activeTab.value = tab
}

const getStatusClass = (status) => {
  switch (status) {
    case 'Available':
      return 'bg-success'
    case 'In Use':
      return 'bg-primary'
    case 'Defective':
      return 'bg-danger'
    default:
      return 'bg-secondary'
  }
}

const fetchComponents = async () => {
  try {
    const response = await fetch('http://localhost:8000/api/components')
    const data = await response.json()
    if (data.success) {
      components.value = data.data
    }
  } catch (error) {
    console.error('Error fetching components:', error)
  }
}

const createComponent = async () => {
  try {
    const response = await fetch('http://localhost:8000/api/components/create', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(newComponent.value)
    })
    
    const data = await response.json()
    if (data.success) {
      alert('Component added successfully!')
      bootstrap.Modal.getInstance(document.getElementById('createComponentModal')).hide()
      resetComponentForm()
      fetchComponents()
    } else {
      alert('Error adding component: ' + data.message)
    }
  } catch (error) {
    console.error('Error adding component:', error)
    alert('Error adding component')
  }
}

const editComponent = (component) => {
  console.log('Edit component:', component)
}

const deleteComponent = async (id) => {
  if (confirm('Are you sure you want to delete this component?')) {
    try {
      const response = await fetch(`http://localhost:8000/api/components/${id}/delete`, {
        method: 'DELETE'
      })
      
      const data = await response.json()
      if (data.success) {
        alert('Component deleted successfully!')
        fetchComponents()
      } else {
        alert('Error deleting component: ' + data.message)
      }
    } catch (error) {
      console.error('Error deleting component:', error)
      alert('Error deleting component')
    }
  }
}

const resetComponentForm = () => {
  newComponent.value = {
    type: newComponent.value.type,
    model: '',
    capacity: '',
    type_field: '',
    wattage: '',
    status: 'Available'
  }
}

const showCreateModal = () => {
  const modal = new bootstrap.Modal(document.getElementById('createComponentModal'))
  modal.show()
}

const refreshData = () => {
  fetchComponents()
}

const toggleNav = () => {
  isNavCollapsed.value = !isNavCollapsed.value
}

const openProfile = () => {
  console.log('Opening profile')
}

const openSettings = () => {
  console.log('Opening settings')
}

onMounted(() => {
  fetchComponents()
})
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

.main-content.expanded {
  margin-left: 70px;
}

@media (max-width: 768px) {
  .main-content {
    margin-left: 70px;
  }
  
  .main-content.expanded {
    margin-left: 70px;
  }
}
</style>
