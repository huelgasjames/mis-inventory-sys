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
                  <option value="true">Active</option>
                  <option value="false">Inactive</option>
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
              <table class="table table-hover" v-if="filteredLabs.length > 0">
                <thead>
                  <tr>
                    <th>Laboratory Name</th>
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
                        <span class="fw-semibold">{{ lab.lab_name }}</span>
                      </div>
                    </td>
                    <td>
                      <span class="badge bg-info">{{ lab.location }}</span>
                    </td>
                    <td>
                      <span class="badge bg-primary">{{ lab.current_computer_count || 0 }}</span>
                    </td>
                    <td>
                      <span class="badge bg-success">{{ lab.capacity || 0 }}</span>
                    </td>
                    <td>
                      <span :class="getStatusBadgeClass(lab.is_active)">
                        {{ lab.is_active ? 'Active' : 'Inactive' }}
                      </span>
                    </td>
                    <td>
                      <span v-if="lab.headOfLab" class="badge bg-warning text-dark">
                        {{ lab.headOfLab.name }}
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
              <div v-else class="text-center py-5">
                <div class="mb-4">
                  <i class="bi bi-flask text-muted" style="font-size: 4rem;"></i>
                </div>
                <h5 class="text-muted">No laboratories found</h5>
                <p class="text-muted">
                  {{ searchQuery || statusFilter || departmentFilter 
                    ? 'Try adjusting your search or filters' 
                    : 'Get started by adding your first laboratory' }}
                </p>
                <button 
                  v-if="!searchQuery && !statusFilter && !departmentFilter" 
                  class="btn btn-primary"
                  @click="showCreateLabModal"
                >
                  <i class="bi bi-plus-circle me-2"></i>Add Laboratory
                </button>
              </div>
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
                <input type="text" class="form-control" v-model="newLab.lab_name" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Lab Code *</label>
                <input type="text" class="form-control" v-model="newLab.lab_code" placeholder="e.g., LAB-001" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Location *</label>
                <select class="form-select" v-model="newLab.location" required>
                  <option value="">Select Location</option>
                  <option value="BCH">BCH</option>
                  <option value="UC">UC</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Room ID/Number *</label>
                <input type="text" class="form-control" v-model="newLab.building" placeholder="e.g., Room 101, Lab-A1" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Floor</label>
                <input type="text" class="form-control" v-model="newLab.floor">
              </div>
              <div class="col-md-6">
                <label class="form-label">Capacity *</label>
                <input type="number" class="form-control" v-model="newLab.capacity" required min="1">
              </div>
              <div class="col-md-6">
                <label class="form-label">Department</label>
                <select class="form-select" v-model="newLab.department_id">
                  <option value="">Select Department</option>
                  <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Head of Lab</label>
                <select class="form-select" v-model="newLab.head_of_lab">
                  <option value="">Select User</option>
                  <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Contact Number *</label>
                <input type="text" class="form-control" v-model="newLab.contact_number" placeholder="Enter contact number" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Email *</label>
                <input type="email" class="form-control" v-model="newLab.email" placeholder="Enter email address" required>
              </div>
              <div class="col-md-12">
                <label class="form-label">Status</label>
                <select class="form-select" v-model="newLab.is_active">
                  <option :value="true">Active</option>
                  <option :value="false">Inactive</option>
                </select>
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
              <div class="form-control bg-light">{{ selectedLab.lab_name }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Location</label>
              <div class="form-control bg-light">{{ selectedLab.location }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Room ID/Number</label>
              <div class="form-control bg-light">{{ selectedLab.building }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Floor</label>
              <div class="form-control bg-light">{{ selectedLab.floor || 'N/A' }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Capacity</label>
              <div class="form-control bg-light">{{ selectedLab.capacity || 0 }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Status</label>
              <div class="form-control bg-light">
                <span :class="getStatusBadgeClass(selectedLab.is_active)">
                  {{ selectedLab.is_active ? 'Active' : 'Inactive' }}
                </span>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Computers</label>
              <div class="form-control bg-light">{{ selectedLab.current_computer_count || 0 }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Department</label>
              <div class="form-control bg-light">
                <span v-if="selectedLab.department" class="badge bg-info">
                  {{ selectedLab.department.name }}
                </span>
                <span v-else class="text-muted">Unassigned</span>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Head of Lab</label>
              <div class="form-control bg-light">
                <span v-if="selectedLab.headOfLab" class="badge bg-warning text-dark">
                  {{ selectedLab.headOfLab.name }}
                </span>
                <span v-else class="text-muted">Unassigned</span>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Contact Number</label>
              <div class="form-control bg-light">{{ selectedLab.contact_number || 'N/A' }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <div class="form-control bg-light">{{ selectedLab.email || 'N/A' }}</div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Laboratory Modal -->
  <div class="modal fade" id="editLabModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Laboratory</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="updateLab" v-if="selectedLab">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Laboratory Name *</label>
                <input type="text" class="form-control" v-model="selectedLab.lab_name" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Location *</label>
                <input type="text" class="form-control" v-model="selectedLab.location" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Room ID/Number *</label>
                <input type="text" class="form-control" v-model="selectedLab.building" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Floor</label>
                <input type="text" class="form-control" v-model="selectedLab.floor">
              </div>
              <div class="col-md-6">
                <label class="form-label">Capacity *</label>
                <input type="number" class="form-control" v-model="selectedLab.capacity" required min="1">
              </div>
              <div class="col-md-6">
                <label class="form-label">Department</label>
                <select class="form-select" v-model="selectedLab.department_id">
                  <option value="">Select Department</option>
                  <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Head of Lab</label>
                <select class="form-select" v-model="selectedLab.head_of_lab">
                  <option value="">Select User</option>
                  <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Contact Number *</label>
                <input type="text" class="form-control" v-model="selectedLab.contact_number" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Email *</label>
                <input type="email" class="form-control" v-model="selectedLab.email" required>
              </div>
              <div class="col-md-12">
                <label class="form-label">Status</label>
                <select class="form-select" v-model="selectedLab.is_active">
                  <option :value="true">Active</option>
                  <option :value="false">Inactive</option>
                </select>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" @click="updateLab">Update Laboratory</button>
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
import { Modal } from 'bootstrap'

const isNavCollapsed = ref(false)
const laboratories = ref([])
const departments = ref([])
const users = ref([])

const searchQuery = ref('')
const departmentFilter = ref('')
const statusFilter = ref('')

const selectedLab = ref(null)

const newLab = ref({
  lab_name: '',
  lab_code: '',
  location: '',
  building: '',
  floor: '',
  capacity: '',
  department_id: '',
  head_of_lab: '',
  contact_number: '',
  email: '',
  is_active: true
})

const filteredLabs = computed(() => {
  let result = laboratories.value

  // Apply department filter
  if (departmentFilter.value) {
    result = result.filter(lab => lab.department_id == departmentFilter.value)
  }

  // Apply status filter
  if (statusFilter.value !== '') {
    result = result.filter(lab => lab.is_active === (statusFilter.value === 'true'))
  }

  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(lab => 
      lab.lab_name.toLowerCase().includes(query) ||
      lab.location.toLowerCase().includes(query) ||
      lab.building?.toLowerCase().includes(query) ||
      lab.floor?.toLowerCase().includes(query) ||
      lab.contact_number?.toLowerCase().includes(query) ||
      lab.email?.toLowerCase().includes(query)
    )
  }

  return result
})

// Statistics computed properties
const totalLabs = computed(() => filteredLabs.value.length)
const activeLabs = computed(() => filteredLabs.value.filter(l => l.is_active === true).length)
const maintenanceLabs = computed(() => filteredLabs.value.filter(l => l.is_active === false).length)
const totalComputers = computed(() => filteredLabs.value.reduce((sum, l) => sum + (l.current_computer_count || 0), 0))

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
      if (lab.is_active === true) stats[lab.department_id].active++
      if (lab.is_active === false) stats[lab.department_id].maintenance++
      stats[lab.department_id].total_computers += lab.current_computer_count || 0
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

const getStatusBadgeClass = (isActive) => {
  if (isActive === true) {
    return 'badge bg-success'
  } else {
    return 'badge bg-danger'
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
  }
}

const fetchDepartments = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/departments')
    departments.value = response.data.data || []
  } catch (error) {
    console.error('Error fetching departments:', error)
  }
}

const fetchUsers = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/users')
    users.value = response.data.data || []
  } catch (error) {
    console.error('Error fetching users:', error)
  }
}

const createLab = async () => {
  try {
    // Prepare data with proper null handling
    const labData = {
      ...newLab.value,
      department_id: newLab.value.department_id || null,
      head_of_lab: newLab.value.head_of_lab || null
    }
    
    const response = await axios.post('http://localhost:8000/api/laboratories', labData)
    
    if (response.data.success) {
      Modal.getInstance(document.getElementById('createLabModal')).hide()
      await refreshData()
      
      // Reset form
      newLab.value = {
        lab_name: '',
        lab_code: '',
        location: '',
        building: '',
        floor: '',
        capacity: '',
        department_id: '',
        head_of_lab: '',
        contact_number: '',
        email: '',
        is_active: true
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
  const modal = new Modal(document.getElementById('viewLabModal'))
  modal.show()
}

const editLab = (lab) => {
  selectedLab.value = { 
    ...lab,
    department_id: lab.department_id === 0 ? null : lab.department_id,
    head_of_lab: lab.head_of_lab?.id || null
  }
  const modal = new Modal(document.getElementById('editLabModal'))
  modal.show()
}

const updateLab = async () => {
  try {
    // Prepare data with proper null handling
    const labData = {
      ...selectedLab.value,
      department_id: selectedLab.value.department_id || null,
      head_of_lab: selectedLab.value.head_of_lab || null
    }
    
    const response = await axios.put(`http://localhost:8000/api/laboratories/${selectedLab.value.id}`, labData)
    
    if (response.data.success) {
      Modal.getInstance(document.getElementById('editLabModal')).hide()
      await refreshData()
      alert('Laboratory updated successfully!')
    }
  } catch (error) {
    console.error('Error updating laboratory:', error)
    alert('Error updating laboratory: ' + (error.response?.data?.message || error.message))
  }
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
  const modal = new Modal(document.getElementById('createLabModal'))
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
