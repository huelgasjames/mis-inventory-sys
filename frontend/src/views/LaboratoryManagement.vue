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
      
      <!-- Laboratory Management Content -->
      <div class="container-fluid p-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h1 class="h3 mb-0">Laboratory Management</h1>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" @click="refreshData">
              <i class="bi bi-arrow-clockwise me-2"></i>Refresh
            </button>
            <button class="btn btn-success" @click="showCreateLabModal">
              <i class="bi bi-plus-circle me-2"></i>Add Laboratory
            </button>
            <button class="btn btn-info" @click="exportLabs">
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
                    <i class="bi bi-flask text-primary fs-4"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Total Laboratories</h6>
                    <h3 class="mb-0">{{ totalLabs }}</h3>
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
                    <h6 class="text-muted mb-1">Active</h6>
                    <h3 class="mb-0">{{ activeLabs }}</h3>
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
                    <h6 class="text-muted mb-1">Maintenance</h6>
                    <h3 class="mb-0">{{ maintenanceLabs }}</h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                    <i class="bi bi-pc-display text-info fs-4"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Total Computers</h6>
                    <h3 class="mb-0">{{ totalComputers }}</h3>
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
                <label class="form-label">Search Laboratories</label>
                <input type="text" class="form-control" placeholder="Search by name or location..." v-model="searchQuery">
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
                  <option value="Active">Active</option>
                  <option value="Maintenance">Maintenance</option>
                  <option value="Closed">Closed</option>
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

        <!-- Laboratories Table -->
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0">Laboratory Directory</h5>
              <div class="d-flex gap-2">
                <input type="text" class="form-control form-control-sm" placeholder="Quick search..." v-model="searchQuery">
                <button class="btn btn-outline-primary btn-sm" @click="showCreateLabModal">
                  <i class="bi bi-plus-circle me-2"></i>Add Laboratory
                </button>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Laboratory Name</th>
                    <th>Department</th>
                    <th>Location</th>
                    <th>Computers</th>
                    <th>Capacity</th>
                    <th>Status</th>
                    <th>In Charge</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="lab in filteredLabs" :key="lab.id">
                    <td>
                      <div class="d-flex align-items-center">
                        <i class="bi bi-flask me-2 text-primary"></i>
                        <span class="fw-semibold">{{ lab.name }}</span>
                      </div>
                    </td>
                    <td>
                      <span v-if="lab.department" class="badge bg-light text-dark">
                        {{ lab.department.name }}
                      </span>
                      <span v-else class="text-muted">-</span>
                    </td>
                    <td>
                      <span class="badge bg-info">{{ lab.location }}</span>
                    </td>
                    <td>
                      <span class="badge bg-primary">{{ lab.computer_count || 0 }}</span>
                    </td>
                    <td>
                      <span class="badge bg-success">{{ lab.capacity || 0 }}</span>
                    </td>
                    <td>
                      <span :class="getStatusBadgeClass(lab.status)">
                        {{ lab.status }}
                      </span>
                    </td>
                    <td>
                      <span v-if="lab.in_charge" class="badge bg-warning text-dark">
                        {{ lab.in_charge }}
                      </span>
                      <span v-else class="text-muted">Unassigned</span>
                    </td>
                    <td>
                      <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" @click="viewLab(lab)">
                          <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-outline-warning" @click="editLab(lab)">
                          <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-outline-danger" @click="deleteLab(lab.id)">
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
                            <h4 class="text-primary mb-0">{{ dept.total_labs }}</h4>
                            <small class="text-muted">Total Labs</small>
                          </div>
                          <div class="col-6">
                            <h4 class="text-success mb-0">{{ dept.active }}</h4>
                            <small class="text-muted">Active</small>
                          </div>
                        </div>
                        <div class="row text-center">
                          <div class="col-6">
                            <h4 class="text-warning mb-0">{{ dept.maintenance }}</h4>
                            <small class="text-muted">Maintenance</small>
                          </div>
                          <div class="col-6">
                            <h4 class="text-info mb-0">{{ dept.total_computers }}</h4>
                            <small class="text-muted">Computers</small>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Laboratory Capacity Overview -->
          <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-white border-bottom">
                <h5 class="card-title mb-0">Capacity Overview</h5>
              </div>
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-md-6">
                    <div class="card border h-100">
                      <div class="card-body text-center">
                        <h6 class="card-title">Small Labs</h6>
                        <div class="mb-2">
                          <i class="bi bi-person text-primary fs-3"></i>
                        </div>
                        <h4 class="text-primary mb-0">{{ capacityStats.small }}</h4>
                        <small class="text-muted">≤ 20 Capacity</small>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="card border h-100">
                      <div class="card-body text-center">
                        <h6 class="card-title">Medium Labs</h6>
                        <div class="mb-2">
                          <i class="bi bi-people text-info fs-3"></i>
                        </div>
                        <h4 class="text-info mb-0">{{ capacityStats.medium }}</h4>
                        <small class="text-muted">21-40 Capacity</small>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="card border h-100">
                      <div class="card-body text-center">
                        <h6 class="card-title">Large Labs</h6>
                        <div class="mb-2">
                          <i class="bi bi-building text-success fs-3"></i>
                        </div>
                        <h4 class="text-success mb-0">{{ capacityStats.large }}</h4>
                        <small class="text-muted">41-60 Capacity</small>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="card border h-100">
                      <div class="card-body text-center">
                        <h6 class="card-title">Extra Large</h6>
                        <div class="mb-2">
                          <i class="bi bi-building-fill text-warning fs-3"></i>
                        </div>
                        <h4 class="text-warning mb-0">{{ capacityStats.extra_large }}</h4>
                        <small class="text-muted">> 60 Capacity</small>
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

  <!-- Create Laboratory Modal -->
  <div class="modal fade" id="createLabModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Laboratory</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="createLab">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Laboratory Name *</label>
                <input type="text" class="form-control" v-model="newLab.name" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Department *</label>
                <select class="form-select" v-model="newLab.department_id" required>
                  <option value="">Select Department</option>
                  <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Location *</label>
                <input type="text" class="form-control" v-model="newLab.location" required placeholder="Building, Room">
              </div>
              <div class="col-md-6">
                <label class="form-label">Capacity *</label>
                <input type="number" class="form-control" v-model="newLab.capacity" required min="1">
              </div>
              <div class="col-md-6">
                <label class="form-label">Status *</label>
                <select class="form-select" v-model="newLab.status" required>
                  <option value="Active">Active</option>
                  <option value="Maintenance">Maintenance</option>
                  <option value="Closed">Closed</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">In Charge</label>
                <select class="form-select" v-model="newLab.in_charge_user_id">
                  <option value="">Select User</option>
                  <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                </select>
              </div>
              <div class="col-md-12">
                <label class="form-label">Description</label>
                <textarea class="form-control" v-model="newLab.description" rows="3"></textarea>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" @click="createLab">Add Laboratory</button>
        </div>
      </div>
    </div>
  </div>

  <!-- View Laboratory Modal -->
  <div class="modal fade" id="viewLabModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Laboratory Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" v-if="selectedLab">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Laboratory Name</label>
              <div class="form-control bg-light">{{ selectedLab.name }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Department</label>
              <div class="form-control bg-light">
                <span v-if="selectedLab.department" class="badge bg-light text-dark">
                  {{ selectedLab.department.name }}
                </span>
                <span v-else class="text-muted">No Department</span>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Location</label>
              <div class="form-control bg-light">{{ selectedLab.location }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Capacity</label>
              <div class="form-control bg-light">{{ selectedLab.capacity || 0 }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Status</label>
              <div class="form-control bg-light">
                <span :class="getStatusBadgeClass(selectedLab.status)">
                  {{ selectedLab.status }}
                </span>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Computers</label>
              <div class="form-control bg-light">{{ selectedLab.computer_count || 0 }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">In Charge</label>
              <div class="form-control bg-light">
                <span v-if="selectedLab.in_charge" class="badge bg-warning text-dark">
                  {{ selectedLab.in_charge }}
                </span>
                <span v-else class="text-muted">Unassigned</span>
              </div>
            </div>
            <div class="col-12">
              <label class="form-label">Description</label>
              <div class="form-control bg-light" style="min-height: 80px;">
                {{ selectedLab.description || 'No description available' }}
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
const laboratories = ref([])
const departments = ref([])
const users = ref([])

const searchQuery = ref('')
const departmentFilter = ref('')
const statusFilter = ref('')

const selectedLab = ref(null)

const newLab = ref({
  name: '',
  department_id: '',
  location: '',
  capacity: '',
  status: 'Active',
  in_charge_user_id: '',
  description: ''
})

// Statistics computed properties
const totalLabs = computed(() => filteredLabs.value.length)
const activeLabs = computed(() => filteredLabs.value.filter(l => l.status === 'Active').length)
const maintenanceLabs = computed(() => filteredLabs.value.filter(l => l.status === 'Maintenance').length)
const totalComputers = computed(() => filteredLabs.value.reduce((sum, l) => sum + (l.computer_count || 0), 0))

// Department statistics
const departmentStats = computed(() => {
  const stats = {}
  departments.value.forEach(dept => {
    stats[dept.id] = {
      name: dept.name,
      category: dept.category,
      total_labs: 0,
      active: 0,
      maintenance: 0,
      total_computers: 0
    }
  })
  
  filteredLabs.value.forEach(lab => {
    if (lab.department_id) {
      if (!stats[lab.department_id]) {
        stats[lab.department_id] = {
          name: lab.department?.name || 'Unknown',
          category: lab.department?.category,
          total_labs: 0,
          active: 0,
          maintenance: 0,
          total_computers: 0
        }
      }
      
      stats[lab.department_id].total_labs++
      if (lab.status === 'Active') stats[lab.department_id].active++
      if (lab.status === 'Maintenance') stats[lab.department_id].maintenance++
      stats[lab.department_id].total_computers += lab.computer_count || 0
    }
  })
  
  return Object.values(stats)
})

// Capacity statistics
const capacityStats = computed(() => {
  const stats = {
    small: 0,
    medium: 0,
    large: 0,
    extra_large: 0
  }
  
  filteredLabs.value.forEach(lab => {
    const capacity = lab.capacity || 0
    if (capacity <= 20) stats.small++
    else if (capacity <= 40) stats.medium++
    else if (capacity <= 60) stats.large++
    else stats.extra_large++
  })
  
  return stats
})

const filteredLabs = computed(() => {
  let result = laboratories.value

  // Apply department filter
  if (departmentFilter.value) {
    result = result.filter(lab => lab.department_id == departmentFilter.value)
  }

  // Apply status filter
  if (statusFilter.value) {
    result = result.filter(lab => lab.status === statusFilter.value)
  }

  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(lab => 
      lab.name.toLowerCase().includes(query) ||
      lab.location.toLowerCase().includes(query) ||
      lab.description?.toLowerCase().includes(query) ||
      lab.in_charge?.toLowerCase().includes(query)
    )
  }

  return result
})

const getStatusBadgeClass = (status) => {
  switch (status) {
    case 'Active':
      return 'badge bg-success'
    case 'Maintenance':
      return 'badge bg-warning text-dark'
    case 'Closed':
      return 'badge bg-danger'
    default:
      return 'badge bg-secondary'
  }
}

const fetchLaboratories = async () => {
  try {
    console.log('Fetching laboratories from API...')
    const response = await axios.get('http://localhost:8000/api/laboratories')
    console.log('Laboratories API Response:', response.data)
    laboratories.value = response.data.data || []
    console.log('Laboratories loaded:', laboratories.value.length)
  } catch (error) {
    console.error('Error fetching laboratories:', error)
    console.log('Error details:', error.response?.data || error.message)
    // Fallback data for demo
    laboratories.value = [
      {
        id: 1,
        name: 'Computer Science Lab 1',
        department_id: 1,
        department: { id: 1, name: 'Computer Science', category: { id: 1, name: 'Academic', color: '#0F6F43' } },
        location: 'Building A, Room 101',
        capacity: 30,
        status: 'Active',
        computer_count: 25,
        in_charge_user_id: 1,
        in_charge: 'Dr. Smith',
        description: 'Main computer science laboratory for programming courses'
      },
      {
        id: 2,
        name: 'Computer Science Lab 2',
        department_id: 1,
        department: { id: 1, name: 'Computer Science', category: { id: 1, name: 'Academic', color: '#0F6F43' } },
        location: 'Building A, Room 102',
        capacity: 25,
        status: 'Active',
        computer_count: 20,
        in_charge_user_id: 2,
        in_charge: 'Prof. Johnson',
        description: 'Secondary computer science laboratory'
      },
      {
        id: 3,
        name: 'Network Lab',
        department_id: 1,
        department: { id: 1, name: 'Computer Science', category: { id: 1, name: 'Academic', color: '#0F6F43' } },
        location: 'Building B, Room 201',
        capacity: 20,
        status: 'Maintenance',
        computer_count: 15,
        in_charge_user_id: null,
        in_charge: null,
        description: 'Network configuration and testing laboratory'
      },
      {
        id: 4,
        name: 'Electronics Lab',
        department_id: 2,
        department: { id: 2, name: 'Electronics', category: { id: 2, name: 'Technical', color: '#FF6B35' } },
        location: 'Building C, Room 301',
        capacity: 35,
        status: 'Active',
        computer_count: 10,
        in_charge_user_id: 3,
        in_charge: 'Mr. Davis',
        description: 'Electronics circuit design and testing laboratory'
      }
    ]
    console.log('Using fallback data, laboratories loaded:', laboratories.value.length)
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
      { id: 2, name: 'Electronics', category_id: 2, category: { id: 2, name: 'Technical', color: '#FF6B35' } },
      { id: 3, name: 'Physics', category_id: 1, category: { id: 1, name: 'Academic', color: '#0F6F43' } }
    ]
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
      { id: 1, name: 'Dr. Smith', email: 'smith@university.edu' },
      { id: 2, name: 'Prof. Johnson', email: 'johnson@university.edu' },
      { id: 3, name: 'Mr. Davis', email: 'davis@university.edu' }
    ]
  }
}

const createLab = async () => {
  try {
    const response = await axios.post('http://localhost:8000/api/laboratories', newLab.value)
    
    if (response.data.success) {
      bootstrap.Modal.getInstance(document.getElementById('createLabModal')).hide()
      await refreshData()
      
      // Reset form
      newLab.value = {
        name: '',
        department_id: '',
        location: '',
        capacity: '',
        status: 'Active',
        in_charge_user_id: '',
        description: ''
      }
      
      alert('Laboratory created successfully!')
    }
  } catch (error) {
    console.error('Error creating laboratory:', error)
    alert('Error creating laboratory: ' + (error.response?.data?.message || error.message))
  }
}

const viewLab = (lab) => {
  selectedLab.value = lab
  const modal = new bootstrap.Modal(document.getElementById('viewLabModal'))
  modal.show()
}

const editLab = (lab) => {
  console.log('Edit laboratory:', lab)
  // TODO: Implement edit functionality
}

const deleteLab = async (id) => {
  if (!confirm('Are you sure you want to delete this laboratory?')) return
  
  try {
    const response = await axios.delete(`http://localhost:8000/api/laboratories/${id}`)
    
    if (response.data.success) {
      await refreshData()
      alert('Laboratory deleted successfully!')
    }
  } catch (error) {
    console.error('Error deleting laboratory:', error)
    alert('Error deleting laboratory: ' + (error.response?.data?.message || error.message))
  }
}

const clearFilters = () => {
  searchQuery.value = ''
  departmentFilter.value = ''
  statusFilter.value = ''
}

const showCreateLabModal = () => {
  const modal = new bootstrap.Modal(document.getElementById('createLabModal'))
  modal.show()
}

const refreshData = async () => {
  await Promise.all([fetchLaboratories(), fetchDepartments(), fetchUsers()])
}

const exportLabs = () => {
  alert('Export feature would generate CSV/PDF report of current laboratories')
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
