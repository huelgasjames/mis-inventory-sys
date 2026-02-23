<template>
  <div class="dashboard-layout">
    <!-- Loading Spinner -->
    <LoadingSpinner :is-visible="isLoading" message="Loading components..." />

    <!-- Navigation Sidebar -->
    <AppNav :is-collapsed="isNavCollapsed" />
    
    <!-- Main Content Area -->
    <div class="main-content" :class="{ 'collapsed': isNavCollapsed }">
      <!-- Header -->
      <AppHeader 
        :is-collapsed="isNavCollapsed"
        @sidebar-toggle="(collapsed) => {
          console.log('Components received sidebar-toggle:', collapsed)
          isNavCollapsed = collapsed
        }"
        @profile-open="openProfile"
        @settings-open="openSettings"
      />
      
      <!-- Components Content -->
      <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h1 class="h3 mb-0" style="color: black;">Component Management</h1>
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
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body p-0">
            <ul class="nav nav-tabs">
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
          </div>
        </div>

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
import { ref, onMounted, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import AppHeader from '@/components/AppHeader.vue'
import AppNav from '@/components/AppNav.vue'
import LoadingSpinner from '@/components/LoadingSpinner.vue'
import { useDarkMode } from '@/composables/useDarkMode.js'
import { Modal } from 'bootstrap'

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
  console.log('Components isNavCollapsed changed from', oldValue, 'to', newValue)
})
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
  if (!isLoading.value) {
    isLoading.value = true
    loadingStartTime.value = Date.now()
  }
  try {
    const response = await fetch('http://localhost:8000/api/components')
    const data = await response.json()
    if (data.success) {
      components.value = data.data
    }
  } catch (error) {
    console.error('Error fetching components:', error)
  } finally {
    await ensureMinimumLoading()
    isLoading.value = false
    loadingStartTime.value = null
  }
}

const createComponent = async () => {
  isLoading.value = true
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
      Modal.getInstance(document.getElementById('createComponentModal')).hide()
      resetComponentForm()
      fetchComponents()
    } else {
      alert('Error adding component: ' + data.message)
    }
  } catch (error) {
    console.error('Error adding component:', error)
    alert('Error adding component')
  } finally {
    isLoading.value = false
  }
}

const editComponent = (component) => {
  console.log('Edit component:', component)
}

const deleteComponent = async (id) => {
  if (confirm('Are you sure you want to delete this component?')) {
    isLoading.value = true
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
    } finally {
      isLoading.value = false
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
  const modal = new Modal(document.getElementById('createComponentModal'))
  modal.show()
}

const refreshData = async () => {
  if (!isLoading.value) {
    isLoading.value = true
    loadingStartTime.value = Date.now()
  }
  
  await fetchComponents()
  
  await ensureMinimumLoading()
  isLoading.value = false
  loadingStartTime.value = null
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

onMounted(async () => {
  initDarkMode()
  
  if (!isLoading.value) {
    isLoading.value = true
    loadingStartTime.value = Date.now()
  }
  
  await fetchComponents()
  
  await ensureMinimumLoading()
  isLoading.value = false
  loadingStartTime.value = null
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
  min-height: 100vh;
}

.main-content.collapsed {
  margin-left: 60px;
}

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

:global(.dark-mode) .nav-tabs {
  border-color: #333;
}

:global(.dark-mode) .nav-tabs .nav-link {
  background-color: #2d2d2d;
  border-color: #333;
  color: #b3b3b3;
}

:global(.dark-mode) .nav-tabs .nav-link:hover {
  background-color: #3d3d3d;
  border-color: #444;
  color: #fff;
}

:global(.dark-mode) .nav-tabs .nav-link.active {
  background-color: #1e1e1e;
  border-color: #333;
  color: #0F6F43;
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

:global(.dark-mode) .btn-primary {
  background-color: #0F6F43;
  border-color: #0F6F43;
}

:global(.dark-mode) .btn-primary:hover {
  background-color: #0d5a37;
  border-color: #0d5a37;
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

:global(.dark-mode) .badge.bg-success {
  background-color: #198754 !important;
}

:global(.dark-mode) .badge.bg-danger {
  background-color: #dc3545 !important;
}

:global(.dark-mode) .badge.bg-secondary {
  background-color: #6c757d !important;
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
