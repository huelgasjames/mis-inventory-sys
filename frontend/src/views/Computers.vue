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
      
      <!-- Computers Content -->
      <div class="container-fluid p-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h1 class="h3 mb-0">Computer Management</h1>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" @click="refreshData">
              <i class="bi bi-arrow-clockwise me-2"></i>Refresh
            </button>
            <button class="btn btn-success" @click="showCreateModal">
              <i class="bi bi-plus-circle me-2"></i>Add Computer
            </button>
            <button class="btn btn-info" @click="exportComputers">
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
                    <i class="bi bi-pc-display text-primary fs-4"></i>
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
                    <i class="bi bi-check-circle text-success fs-4"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Working</h6>
                    <h3 class="mb-0">{{ workingComputers }}</h3>
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
                    <h3 class="mb-0">{{ defectiveComputers }}</h3>
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
                    <i class="bi bi-exclamation-triangle text-warning fs-4"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">For Disposal</h6>
                    <h3 class="mb-0">{{ disposalComputers }}</h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Search and Filter -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">
            <div class="row g-3 align-items-end">
              <div class="col-md-4">
                <label class="form-label">Search Computers</label>
                <input type="text" class="form-control" placeholder="Search by name or asset tag..." v-model="searchQuery">
              </div>
              <div class="col-md-3">
                <label class="form-label">Department</label>
                <select class="form-select" v-model="departmentFilter">
                  <option value="">All Departments</option>
                  <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label">Status</label>
                <select class="form-select" v-model="statusFilter">
                  <option value="">All Status</option>
                  <option value="Working">Working</option>
                  <option value="Defective">Defective</option>
                  <option value="For Disposal">For Disposal</option>
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button class="btn btn-outline-secondary w-100" @click="clearFilters">
                  <i class="bi bi-x-circle me-2"></i>Clear
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Computers Table -->
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0">Computer Inventory</h5>
              <div class="d-flex gap-2">
                <input type="text" class="form-control form-control-sm" placeholder="Quick search..." v-model="searchQuery">
                <button class="btn btn-outline-primary btn-sm" @click="showCreateModal">
                  <i class="bi bi-plus-circle me-2"></i>Add Computer
                </button>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Computer Name</th>
                    <th>Asset Tag</th>
                    <th>Department</th>
                    <th>Processor</th>
                    <th>Status</th>
                    <th>Assigned To</th>
                    <th>Location</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="computer in filteredComputers" :key="computer.id">
                    <td>
                      <div class="d-flex align-items-center">
                        <i class="bi bi-pc-display me-2 text-primary"></i>
                        <span class="fw-semibold">{{ computer.computer_name }}</span>
                      </div>
                    </td>
                    <td>
                      <span class="badge bg-primary">{{ computer.asset_tag }}</span>
                    </td>
                    <td>
                      <span v-if="computer.department" class="badge bg-light text-dark">
                        {{ computer.department.name }}
                      </span>
                      <span v-else class="text-muted">-</span>
                    </td>
                    <td>
                      <span v-if="computer.processor" class="badge bg-info">
                        {{ computer.processor.model }}
                      </span>
                      <span v-else class="text-muted">-</span>
                    </td>
                    <td>
                      <span :class="getStatusBadgeClass(computer.status)">
                        {{ computer.status }}
                      </span>
                    </td>
                    <td>
                      <span v-if="computer.assigned_user" class="badge bg-success">
                        {{ computer.assigned_user.name }}
                      </span>
                      <span v-else class="text-muted">Unassigned</span>
                    </td>
                    <td>{{ computer.location || '-' }}</td>
                    <td>
                      <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" @click="viewComputer(computer)">
                          <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-outline-warning" @click="editComputer(computer)">
                          <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-outline-danger" @click="deleteComputer(computer.id)">
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

        <!-- Department Overview -->
        <div class="row g-4 mt-4">
          <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-white border-bottom">
                <h5 class="card-title mb-0">Department Overview</h5>
              </div>
              <div class="card-body">
                <div class="row g-3">
                  <div v-for="dept in departmentStats" :key="dept.id" class="col-md-6">
                    <div class="card border h-100">
                      <div class="card-body text-center">
                        <h6 class="card-title">{{ dept.name }}</h6>
                        <div class="mb-2">
                          <span v-if="dept.category" class="badge" :style="{ backgroundColor: dept.category.color + '20', color: dept.category.color }">
                            {{ dept.category.name }}
                          </span>
                        </div>
                        <div class="row text-center">
                          <div class="col-6">
                            <h4 class="text-primary mb-0">{{ dept.total_computers }}</h4>
                            <small class="text-muted">Total</small>
                          </div>
                          <div class="col-6">
                            <h4 class="text-success mb-0">{{ dept.working }}</h4>
                            <small class="text-muted">Working</small>
                          </div>
                        </div>
                        <div class="row text-center">
                          <div class="col-6">
                            <h4 class="text-danger mb-0">{{ dept.defective }}</h4>
                            <small class="text-muted">Defective</small>
                          </div>
                          <div class="col-6">
                            <h4 class="text-warning mb-0">{{ dept.disposal }}</h4>
                            <small class="text-muted">Disposal</small>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Component Overview -->
          <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-white border-bottom">
                <h5 class="card-title mb-0">Component Distribution</h5>
              </div>
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-md-6">
                    <div class="card border h-100">
                      <div class="card-body text-center">
                        <h6 class="card-title">Processors</h6>
                        <div class="mb-2">
                          <i class="bi bi-cpu text-primary fs-3"></i>
                        </div>
                        <h4 class="text-primary mb-0">{{ componentStats.processors }}</h4>
                        <small class="text-muted">Total</small>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="card border h-100">
                      <div class="card-body text-center">
                        <h6 class="card-title">RAM</h6>
                        <div class="mb-2">
                          <i class="bi bi-memory text-info fs-3"></i>
                        </div>
                        <h4 class="text-info mb-0">{{ componentStats.ram }}</h4>
                        <small class="text-muted">Total</small>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="card border h-100">
                      <div class="card-body text-center">
                        <h6 class="card-title">Storage</h6>
                        <div class="mb-2">
                          <i class="bi bi-hdd text-success fs-3"></i>
                        </div>
                        <h4 class="text-success mb-0">{{ componentStats.storage }}</h4>
                        <small class="text-muted">Total</small>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="card border h-100">
                      <div class="card-body text-center">
                        <h6 class="card-title">Video Cards</h6>
                        <div class="mb-2">
                          <i class="bi bi-gpu text-warning fs-3"></i>
                        </div>
                        <h4 class="text-warning mb-0">{{ componentStats.video_cards }}</h4>
                        <small class="text-muted">Total</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Create Computer Modal -->
  <div class="modal fade" id="createComputerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Computer</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="createComputer">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Computer Name *</label>
                <input type="text" class="form-control" v-model="newComputer.computer_name" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Asset Tag *</label>
                <input type="text" class="form-control" v-model="newComputer.asset_tag" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Serial Number</label>
                <input type="text" class="form-control" v-model="newComputer.serial_number">
              </div>
              <div class="col-md-6">
                <label class="form-label">Department *</label>
                <select class="form-select" v-model="newComputer.department_id" required>
                  <option value="">Select Department</option>
                  <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Processor</label>
                <select class="form-select" v-model="newComputer.processor_id">
                  <option value="">Select Processor</option>
                  <option v-for="cpu in components.processors" :key="cpu.id" :value="cpu.id">{{ cpu.model }}</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Motherboard</label>
                <select class="form-select" v-model="newComputer.motherboard_id">
                  <option value="">Select Motherboard</option>
                  <option v-for="mb in components.motherboards" :key="mb.id" :value="mb.id">{{ mb.model }}</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">RAM</label>
                <select class="form-select" v-model="newComputer.ram_id">
                  <option value="">Select RAM</option>
                  <option v-for="ram in components.rams" :key="ram.id" :value="ram.id">{{ ram.capacity }}</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Storage</label>
                <select class="form-select" v-model="newComputer.storage_id">
                  <option value="">Select Storage</option>
                  <option v-for="storage in components.storages" :key="storage.id" :value="storage.id">{{ storage.capacity }}</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Video Card</label>
                <select class="form-select" v-model="newComputer.video_card_id">
                  <option value="">Select Video Card</option>
                  <option v-for="gpu in components.video_cards" :key="gpu.id" :value="gpu.id">{{ gpu.model }}</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Status *</label>
                <select class="form-select" v-model="newComputer.status" required>
                  <option value="Working">Working</option>
                  <option value="Defective">Defective</option>
                  <option value="For Disposal">For Disposal</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Assigned To</label>
                <select class="form-select" v-model="newComputer.assigned_to">
                  <option value="">Unassigned</option>
                  <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                </select>
              </div>
              <div class="col-md-12">
                <label class="form-label">Location</label>
                <input type="text" class="form-control" v-model="newComputer.location" placeholder="Room/Office">
              </div>
              <div class="col-md-12">
                <label class="form-label">Description</label>
                <textarea class="form-control" v-model="newComputer.description" rows="3"></textarea>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" @click="createComputer">Add Computer</button>
        </div>
      </div>
    </div>
  </div>

  <!-- View Computer Modal -->
  <div class="modal fade" id="viewComputerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Computer Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" v-if="selectedComputer">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Computer Name</label>
              <div class="form-control bg-light">{{ selectedComputer.computer_name }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Asset Tag</label>
              <div class="form-control bg-light">{{ selectedComputer.asset_tag }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Serial Number</label>
              <div class="form-control bg-light">{{ selectedComputer.serial_number || 'N/A' }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Department</label>
              <div class="form-control bg-light">
                <span v-if="selectedComputer.department" class="badge bg-light text-dark">
                  {{ selectedComputer.department.name }}
                </span>
                <span v-else class="text-muted">No Department</span>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Processor</label>
              <div class="form-control bg-light">
                <span v-if="selectedComputer.processor" class="badge bg-info">
                  {{ selectedComputer.processor.model }}
                </span>
                <span v-else class="text-muted">No Processor</span>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Motherboard</label>
              <div class="form-control bg-light">
                <span v-if="selectedComputer.motherboard" class="badge bg-info">
                  {{ selectedComputer.motherboard.model }}
                </span>
                <span v-else class="text-muted">No Motherboard</span>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">RAM</label>
              <div class="form-control bg-light">
                <span v-if="selectedComputer.ram" class="badge bg-info">
                  {{ selectedComputer.ram.capacity }}
                </span>
                <span v-else class="text-muted">No RAM</span>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Storage</label>
              <div class="form-control bg-light">
                <span v-if="selectedComputer.storage" class="badge bg-info">
                  {{ selectedComputer.storage.capacity }}
                </span>
                <span v-else class="text-muted">No Storage</span>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Video Card</label>
              <div class="form-control bg-light">
                <span v-if="selectedComputer.video_card" class="badge bg-info">
                  {{ selectedComputer.video_card.model }}
                </span>
                <span v-else class="text-muted">No Video Card</span>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Status</label>
              <div class="form-control bg-light">
                <span :class="getStatusBadgeClass(selectedComputer.status)">
                  {{ selectedComputer.status }}
                </span>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Assigned To</label>
              <div class="form-control bg-light">
                <span v-if="selectedComputer.assigned_user" class="badge bg-success">
                  {{ selectedComputer.assigned_user.name }}
                </span>
                <span v-else class="text-muted">Unassigned</span>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Location</label>
              <div class="form-control bg-light">{{ selectedComputer.location || 'Not specified' }}</div>
            </div>
            <div class="col-12">
              <label class="form-label">Description</label>
              <div class="form-control bg-light" style="min-height: 80px;">
                {{ selectedComputer.description || 'No description available' }}
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import AppHeader from '@/components/AppHeader.vue'
import AppNav from '@/components/AppNav.vue'
import axios from 'axios'

const isNavCollapsed = ref(false)
const computers = ref([])
const departments = ref([])
const components = ref({})
const users = ref([])

const searchQuery = ref('')
const statusFilter = ref('')
const departmentFilter = ref('')

const selectedComputer = ref(null)

const newComputer = ref({
  computer_name: '',
  asset_tag: '',
  serial_number: '',
  department_id: '',
  processor_id: '',
  motherboard_id: '',
  ram_id: '',
  storage_id: '',
  video_card_id: '',
  status: 'Working',
  assigned_to: '',
  location: '',
  description: ''
})

// Statistics computed properties
const totalComputers = computed(() => filteredComputers.value.length)
const workingComputers = computed(() => filteredComputers.value.filter(c => c.status === 'Working').length)
const defectiveComputers = computed(() => filteredComputers.value.filter(c => c.status === 'Defective').length)
const disposalComputers = computed(() => filteredComputers.value.filter(c => c.status === 'For Disposal').length)

// Department statistics
const departmentStats = computed(() => {
  const stats = {}
  departments.value.forEach(dept => {
    stats[dept.id] = {
      name: dept.name,
      category: dept.category,
      total_computers: 0,
      working: 0,
      defective: 0,
      disposal: 0
    }
  })
  
  filteredComputers.value.forEach(computer => {
    if (computer.department_id) {
      if (!stats[computer.department_id]) {
        stats[computer.department_id] = {
          name: computer.department?.name || 'Unknown',
          category: computer.department?.category,
          total_computers: 0,
          working: 0,
          defective: 0,
          disposal: 0
        }
      }
      
      stats[computer.department_id].total_computers++
      if (computer.status === 'Working') stats[computer.department_id].working++
      if (computer.status === 'Defective') stats[computer.department_id].defective++
      if (computer.status === 'For Disposal') stats[computer.department_id].disposal++
    }
  })
  
  return Object.values(stats)
})

// Component statistics
const componentStats = computed(() => {
  return {
    processors: computers.value.filter(c => c.processor_id).length,
    ram: computers.value.filter(c => c.ram_id).length,
    storage: computers.value.filter(c => c.storage_id).length,
    video_cards: computers.value.filter(c => c.video_card_id).length
  }
})

const filteredComputers = computed(() => {
  let filtered = computers.value

  if (searchQuery.value) {
    filtered = filtered.filter(comp => 
      comp.computer_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      comp.asset_tag.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  }

  if (statusFilter.value) {
    filtered = filtered.filter(comp => comp.status === statusFilter.value)
  }

  if (departmentFilter.value) {
    filtered = filtered.filter(comp => comp.department_id == departmentFilter.value)
  }

  return filtered
})

const getStatusBadgeClass = (status) => {
  switch (status) {
    case 'Working':
      return 'badge bg-success'
    case 'Defective':
      return 'badge bg-danger'
    case 'For Disposal':
      return 'badge bg-warning text-dark'
    default:
      return 'badge bg-secondary'
  }
}

const fetchComputers = async () => {
  try {
    console.log('Fetching computers from API...')
    const response = await axios.get('http://localhost:8000/api/computers')
    console.log('Computers API Response:', response.data)
    computers.value = response.data.data || []
    console.log('Computers loaded:', computers.value.length)
  } catch (error) {
    console.error('Error fetching computers:', error)
    console.log('Error details:', error.response?.data || error.message)
    // Fallback data for demo
    computers.value = [
      {
        id: 1,
        computer_name: 'CS Lab Computer 1',
        asset_tag: 'PC001',
        department_id: 1,
        department: { id: 1, name: 'Computer Science', category: { id: 1, name: 'Academic', color: '#0F6F43' } },
        status: 'Working',
        location: 'Lab Room 101',
        assigned_to: 1,
        assigned_user: { id: 1, name: 'John Doe' },
        description: 'Desktop computer for Computer Science Lab',
        processor: { id: 1, model: 'Intel Core i5-10400' },
        motherboard: { id: 1, model: 'ASUS Prime H410M' },
        ram: { id: 1, capacity: '16GB DDR4' },
        storage: { id: 1, capacity: '512GB SSD' },
        video_card: { id: 1, model: 'NVIDIA GTX 1650' }
      },
      {
        id: 2,
        computer_name: 'Admin Office PC',
        asset_tag: 'PC002',
        department_id: 2,
        department: { id: 2, name: 'Administration', category: { id: 2, name: 'Administrative', color: '#004D7A' } },
        status: 'Working',
        location: 'Admin Office',
        assigned_to: 1,
        assigned_user: { id: 1, name: 'Admin User' },
        description: 'Desktop computer for Administration',
        processor: { id: 2, model: 'Intel Core i3-10100' },
        motherboard: { id: 2, model: 'Gigabyte H410M' },
        ram: { id: 2, capacity: '8GB DDR4' },
        storage: { id: 2, capacity: '256GB SSD' },
        video_card: { id: 2, model: 'Integrated' }
      },
      {
        id: 3,
        computer_name: 'Library PC 1',
        asset_tag: 'PC003',
        department_id: 3,
        department: { id: 3, name: 'Library', category: { id: 1, name: 'Academic', color: '#0F6F43' } },
        status: 'Defective',
        location: 'Library Room 201',
        assigned_to: null,
        assigned_user: null,
        description: 'Desktop computer for Library - needs repair',
        processor: null,
        motherboard: null,
        ram: null,
        storage: null,
        video_card: null
      },
      {
        id: 4,
        computer_name: 'Old Server PC',
        asset_tag: 'PC004',
        department_id: 1,
        department: { id: 1, name: 'Computer Science', category: { id: 1, name: 'Academic', color: '#0F6F43' } },
        status: 'For Disposal',
        location: 'Storage Room',
        assigned_to: null,
        assigned_user: null,
        description: 'Old computer ready for disposal',
        processor: null,
        motherboard: null,
        ram: null,
        storage: null,
        video_card: null
      }
    ]
  }
}

const fetchDepartments = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/departments')
    departments.value = response.data.data || []
  } catch (error) {
    console.error('Error fetching departments:', error)
    // Fallback data
    departments.value = [
      { id: 1, name: 'Computer Science', category_id: 1, category: { id: 1, name: 'Academic', color: '#0F6F43' } },
      { id: 2, name: 'Administration', category_id: 2, category: { id: 2, name: 'Administrative', color: '#004D7A' } },
      { id: 3, name: 'Library', category_id: 1, category: { id: 1, name: 'Academic', color: '#0F6F43' } }
    ]
  }
}

const fetchComponents = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/components')
    components.value = response.data.data || {}
  } catch (error) {
    console.error('Error fetching components:', error)
    // Fallback data
    components.value = {
      processors: [
        { id: 1, model: 'Intel Core i5-10400' },
        { id: 2, model: 'Intel Core i3-10100' }
      ],
      motherboards: [
        { id: 1, model: 'ASUS Prime H410M' },
        { id: 2, model: 'Gigabyte H410M' }
      ],
      rams: [
        { id: 1, capacity: '16GB DDR4' },
        { id: 2, capacity: '8GB DDR4' }
      ],
      storages: [
        { id: 1, capacity: '512GB SSD' },
        { id: 2, capacity: '256GB SSD' }
      ],
      video_cards: [
        { id: 1, model: 'NVIDIA GTX 1650' },
        { id: 2, model: 'Integrated' }
      ]
    }
  }
}

const fetchUsers = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/users')
    users.value = response.data.data || []
  } catch (error) {
    console.error('Error fetching users:', error)
    // Fallback data
    users.value = [
      { id: 1, name: 'John Doe', email: 'john@example.com' },
      { id: 2, name: 'Jane Smith', email: 'jane@example.com' },
      { id: 3, name: 'Bob Wilson', email: 'bob@example.com' }
    ]
  }
}

const createComputer = async () => {
  try {
    const response = await axios.post('http://localhost:8000/api/computers', newComputer.value)
    
    if (response.data.success) {
      bootstrap.Modal.getInstance(document.getElementById('createComputerModal')).hide()
      await refreshData()
      
      // Reset form
      newComputer.value = {
        computer_name: '',
        asset_tag: '',
        serial_number: '',
        department_id: '',
        processor_id: '',
        motherboard_id: '',
        ram_id: '',
        storage_id: '',
        video_card_id: '',
        status: 'Working',
        assigned_to: '',
        location: '',
        description: ''
      }
      
      alert('Computer created successfully!')
    }
  } catch (error) {
    console.error('Error creating computer:', error)
    alert('Error creating computer: ' + (error.response?.data?.message || error.message))
  }
}

const viewComputer = (computer) => {
  selectedComputer.value = computer
  const modal = new bootstrap.Modal(document.getElementById('viewComputerModal'))
  modal.show()
}

const editComputer = (computer) => {
  console.log('Edit computer:', computer)
  // TODO: Implement edit functionality
}

const deleteComputer = async (id) => {
  if (!confirm('Are you sure you want to delete this computer?')) return
  
  try {
    const response = await axios.delete(`http://localhost:8000/api/computers/${id}`)
    
    if (response.data.success) {
      await refreshData()
      alert('Computer deleted successfully!')
    }
  } catch (error) {
    console.error('Error deleting computer:', error)
    alert('Error deleting computer: ' + (error.response?.data?.message || error.message))
  }
}

const clearFilters = () => {
  searchQuery.value = ''
  statusFilter.value = ''
  departmentFilter.value = ''
}

const showCreateModal = () => {
  const modal = new bootstrap.Modal(document.getElementById('createComputerModal'))
  modal.show()
}

const refreshData = async () => {
  await Promise.all([fetchComputers(), fetchDepartments(), fetchComponents(), fetchUsers()])
}

const exportComputers = () => {
  alert('Export feature would generate CSV/PDF report of current computers')
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
  refreshData()
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

.progress {
  background-color: #e9ecef;
  border-radius: 0.25rem;
}

.progress-bar {
  background-color: #198754;
  border-radius: 0.25rem;
}
</style>
