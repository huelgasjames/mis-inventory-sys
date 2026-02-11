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
      
      <!-- Computers Content -->
      <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h1 class="h3 mb-0">Computer Management</h1>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" @click="refreshData">
              <i class="bi bi-arrow-clockwise me-2"></i>Refresh
            </button>
            <button class="btn btn-primary" @click="showCreateModal">
              <i class="bi bi-plus-circle me-2"></i>Add Computer
            </button>
          </div>
        </div>

        <!-- Search and Filter -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-4">
                <input type="text" class="form-control" placeholder="Search computers..." v-model="searchQuery">
              </div>
              <div class="col-md-3">
                <select class="form-select" v-model="statusFilter">
                  <option value="">All Status</option>
                  <option value="Working">Working</option>
                  <option value="Defective">Defective</option>
                  <option value="For Disposal">For Disposal</option>
                </select>
              </div>
              <div class="col-md-3">
                <select class="form-select" v-model="departmentFilter">
                  <option value="">All Departments</option>
                  <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
                </select>
              </div>
              <div class="col-md-2">
                <button class="btn btn-outline-secondary w-100" @click="clearFilters">
                  <i class="bi bi-x-circle me-2"></i>Clear
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Computers Table -->
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead class="table-light">
                  <tr>
                    <th>Computer Name</th>
                    <th>Asset Tag</th>
                    <th>Department</th>
                    <th>Processor</th>
                    <th>Status</th>
                    <th>Assigned To</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="computer in filteredComputers" :key="computer.id">
                    <td>{{ computer.computer_name }}</td>
                    <td>{{ computer.asset_tag }}</td>
                    <td>{{ computer.department?.name || 'N/A' }}</td>
                    <td>{{ computer.processor?.model || 'N/A' }}</td>
                    <td>
                      <span class="badge" :class="getStatusClass(computer.status)">
                        {{ computer.status }}
                      </span>
                    </td>
                    <td>{{ computer.assigned_user?.name || 'Unassigned' }}</td>
                    <td>
                      <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" @click="viewComputer(computer)">
                          <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-outline-secondary" @click="editComputer(computer)">
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
                <label class="form-label">Computer Name</label>
                <input type="text" class="form-control" v-model="newComputer.computer_name" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Asset Tag</label>
                <input type="text" class="form-control" v-model="newComputer.asset_tag" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Serial Number</label>
                <input type="text" class="form-control" v-model="newComputer.serial_number">
              </div>
              <div class="col-md-6">
                <label class="form-label">Department</label>
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
                <label class="form-label">Status</label>
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
                <input type="text" class="form-control" v-model="newComputer.location">
              </div>
              <div class="col-md-12">
                <label class="form-label">Notes</label>
                <textarea class="form-control" v-model="newComputer.notes" rows="3"></textarea>
              </div>
            </div>
            <div class="mt-3">
              <button type="submit" class="btn btn-primary">Add Computer</button>
            </div>
          </form>
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
              <strong>Computer Name:</strong> {{ selectedComputer.computer_name }}
            </div>
            <div class="col-md-6">
              <strong>Asset Tag:</strong> {{ selectedComputer.asset_tag }}
            </div>
            <div class="col-md-6">
              <strong>Serial Number:</strong> {{ selectedComputer.serial_number || 'N/A' }}
            </div>
            <div class="col-md-6">
              <strong>Department:</strong> {{ selectedComputer.department?.name || 'N/A' }}
            </div>
            <div class="col-md-6">
              <strong>Processor:</strong> {{ selectedComputer.processor?.model || 'N/A' }}
            </div>
            <div class="col-md-6">
              <strong>Motherboard:</strong> {{ selectedComputer.motherboard?.model || 'N/A' }}
            </div>
            <div class="col-md-6">
              <strong>RAM:</strong> {{ selectedComputer.ram?.capacity || 'N/A' }}
            </div>
            <div class="col-md-6">
              <strong>Storage:</strong> {{ selectedComputer.storage?.capacity || 'N/A' }}
            </div>
            <div class="col-md-6">
              <strong>Video Card:</strong> {{ selectedComputer.video_card?.model || 'N/A' }}
            </div>
            <div class="col-md-6">
              <strong>Status:</strong> 
              <span class="badge" :class="getStatusClass(selectedComputer.status)">
                {{ selectedComputer.status }}
              </span>
            </div>
            <div class="col-md-6">
              <strong>Assigned To:</strong> {{ selectedComputer.assigned_user?.name || 'Unassigned' }}
            </div>
            <div class="col-md-6">
              <strong>Location:</strong> {{ selectedComputer.location || 'N/A' }}
            </div>
            <div class="col-12">
              <strong>Notes:</strong> {{ selectedComputer.notes || 'N/A' }}
            </div>
          </div>
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
  notes: ''
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

const getStatusClass = (status) => {
  switch (status) {
    case 'Working':
      return 'bg-success'
    case 'Defective':
      return 'bg-danger'
    case 'For Disposal':
      return 'bg-warning'
    default:
      return 'bg-secondary'
  }
}

const fetchComputers = async () => {
  try {
    const response = await fetch('http://localhost:8000/api/computers')
    const data = await response.json()
    if (data.success) {
      computers.value = data.data
    }
  } catch (error) {
    console.error('Error fetching computers:', error)
  }
}

const fetchDepartments = async () => {
  try {
    const response = await fetch('http://localhost:8000/api/departments')
    const data = await response.json()
    if (data.success) {
      departments.value = data.data
    }
  } catch (error) {
    console.error('Error fetching departments:', error)
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

const fetchUsers = async () => {
  try {
    const response = await fetch('http://localhost:8000/api/users')
    const data = await response.json()
    if (data.success) {
      users.value = data.data
    }
  } catch (error) {
    console.error('Error fetching users:', error)
  }
}

const createComputer = async () => {
  try {
    const response = await fetch('http://localhost:8000/api/computers/create', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(newComputer.value)
    })
    
    const data = await response.json()
    if (data.success) {
      alert('Computer added successfully!')
      bootstrap.Modal.getInstance(document.getElementById('createComputerModal')).hide()
      resetComputerForm()
      fetchComputers()
    } else {
      alert('Error adding computer: ' + data.message)
    }
  } catch (error) {
    console.error('Error adding computer:', error)
    alert('Error adding computer')
  }
}

const viewComputer = (computer) => {
  selectedComputer.value = computer
  const modal = new bootstrap.Modal(document.getElementById('viewComputerModal'))
  modal.show()
}

const editComputer = (computer) => {
  console.log('Edit computer:', computer)
}

const deleteComputer = async (id) => {
  if (confirm('Are you sure you want to delete this computer?')) {
    try {
      const response = await fetch(`http://localhost:8000/api/computers/${id}/delete`, {
        method: 'DELETE'
      })
      
      const data = await response.json()
      if (data.success) {
        alert('Computer deleted successfully!')
        fetchComputers()
      } else {
        alert('Error deleting computer: ' + data.message)
      }
    } catch (error) {
      console.error('Error deleting computer:', error)
      alert('Error deleting computer')
    }
  }
}

const resetComputerForm = () => {
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
    notes: ''
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

const refreshData = () => {
  fetchComputers()
  fetchDepartments()
  fetchComponents()
  fetchUsers()
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
