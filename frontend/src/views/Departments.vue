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
      
      <!-- Departments Content -->
      <div class="container-fluid p-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h1 class="h3 mb-0">Department Management</h1>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" @click="refreshData">
              <i class="bi bi-arrow-clockwise me-2"></i>Refresh
            </button>
            <button class="btn btn-success" @click="showCreateDepartmentModal">
              <i class="bi bi-plus-circle me-2"></i>Add Department
            </button>
            <button class="btn btn-info" @click="exportDepartments">
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
                    <i class="bi bi-building text-primary fs-4"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Total Departments</h6>
                    <h3 class="mb-0">{{ departments.length }}</h3>
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
                    <h3 class="mb-0">{{ activeCount }}</h3>
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
                    <h6 class="text-muted mb-1">Total Assets</h6>
                    <h3 class="mb-0">{{ totalAssets }}</h3>
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
                    <i class="bi bi-people text-warning fs-4"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Total Users</h6>
                    <h3 class="mb-0">{{ totalUsers }}</h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Departments Table -->
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0">Departments Directory</h5>
              <div class="d-flex gap-2">
                <input type="text" class="form-control form-control-sm" placeholder="Quick search..." v-model="searchQuery">
                <button class="btn btn-outline-primary btn-sm" @click="showCreateDepartmentModal">
                  <i class="bi bi-plus-circle me-2"></i>Add Department
                </button>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Assets</th>
                    <th>Users</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="department in filteredDepartments" :key="department.id">
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-3">
                          <i class="bi bi-building text-primary"></i>
                        </div>
                        <div>
                          <div class="fw-semibold">{{ department.name }}</div>
                          <small class="text-muted">ID: {{ department.id }}</small>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span v-if="department.category" class="badge" :style="{ backgroundColor: department.category.color + '20', color: department.category.color }">
                        {{ department.category.name }}
                      </span>
                      <span v-else class="text-muted">-</span>
                    </td>
                    <td>
                      <span class="text-truncate d-inline-block" style="max-width: 200px;">
                        {{ department.description || '-' }}
                      </span>
                    </td>
                    <td>
                      <span class="badge bg-info">{{ department.total_assets || 0 }}</span>
                    </td>
                    <td>
                      <span class="badge bg-primary">{{ department.total_users || 0 }}</span>
                    </td>
                    <td>
                      <span :class="getStatusBadgeClass(department.is_active)">
                        {{ department.is_active ? 'Active' : 'Inactive' }}
                      </span>
                    </td>
                    <td>
                      <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" @click="viewDepartment(department)">
                          <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-outline-warning" @click="editDepartment(department)">
                          <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-outline-danger" @click="deleteDepartment(department)">
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

  <!-- Create Department Modal -->
  <div class="modal fade" id="createDepartmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Department</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="createDepartment">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Name *</label>
                <input type="text" class="form-control" v-model="newDepartment.name" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Category</label>
                <select class="form-select" v-model="newDepartment.category_id">
                  <option value="">Select Category</option>
                  <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                  </option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label">Description</label>
                <textarea class="form-control" v-model="newDepartment.description" rows="3"></textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label">Head of Department</label>
                <input type="text" class="form-control" v-model="newDepartment.head_of_department">
              </div>
              <div class="col-md-6">
                <label class="form-label">Location</label>
                <input type="text" class="form-control" v-model="newDepartment.location">
              </div>
              <div class="col-md-6">
                <label class="form-label">Contact Number</label>
                <input type="tel" class="form-control" v-model="newDepartment.contact_number">
              </div>
              <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" v-model="newDepartment.email">
              </div>
              <div class="col-12">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" v-model="newDepartment.is_active" id="isActive">
                  <label class="form-check-label" for="isActive">
                    Active
                  </label>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" @click="createDepartment">Add Department</button>
        </div>
      </div>
    </div>
  </div>

  <!-- View Department Modal -->
  <div class="modal fade" id="viewDepartmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Department Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" v-if="selectedDepartment">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Name</label>
              <div class="form-control bg-light">{{ selectedDepartment.name }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Category</label>
              <div class="form-control bg-light">
                <span v-if="selectedDepartment.category" class="badge" :style="{ backgroundColor: selectedDepartment.category.color + '20', color: selectedDepartment.category.color }">
                  {{ selectedDepartment.category.name }}
                </span>
                <span v-else>-</span>
              </div>
            </div>
            <div class="col-12">
              <label class="form-label">Description</label>
              <div class="form-control bg-light" style="min-height: 80px;">
                {{ selectedDepartment.description || 'No description' }}
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Head of Department</label>
              <div class="form-control bg-light">{{ selectedDepartment.head_of_department || '-' }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Location</label>
              <div class="form-control bg-light">{{ selectedDepartment.location || '-' }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Contact Number</label>
              <div class="form-control bg-light">{{ selectedDepartment.contact_number || '-' }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <div class="form-control bg-light">{{ selectedDepartment.email || '-' }}</div>
            </div>
            <div class="col-md-4">
              <label class="form-label">Status</label>
              <div class="form-control bg-light">
                <span :class="getStatusBadgeClass(selectedDepartment.is_active)">
                  {{ selectedDepartment.is_active ? 'Active' : 'Inactive' }}
                </span>
              </div>
            </div>
            <div class="col-md-4">
              <label class="form-label">Total Assets</label>
              <div class="form-control bg-light">{{ selectedDepartment.total_assets || 0 }}</div>
            </div>
            <div class="col-md-4">
              <label class="form-label">Total Users</label>
              <div class="form-control bg-light">{{ selectedDepartment.total_users || 0 }}</div>
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

<script>
import { ref, onMounted, computed } from 'vue'
import AppNav from '@/components/AppNav.vue'
import AppHeader from '@/components/AppHeader.vue'
import axios from 'axios'

export default {
  name: 'Departments',
  components: {
    AppNav,
    AppHeader
  },
  setup() {
    const isNavCollapsed = ref(false)
    const departments = ref([])
    const categories = ref([])
    const categoryFilter = ref('')
    const searchQuery = ref('')
    const selectedDepartment = ref(null)
    
    const newDepartment = ref({
      name: '',
      description: '',
      category_id: '',
      head_of_department: '',
      location: '',
      contact_number: '',
      email: '',
      is_active: true
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

    const getStatusBadgeClass = (isActive) => {
      return isActive ? 'badge bg-success' : 'badge bg-secondary'
    }

    const filteredDepartments = computed(() => {
      let result = departments.value

      // Apply category filter
      if (categoryFilter.value) {
        result = result.filter(d => d.category?.name === categoryFilter.value)
      }

      // Apply search filter
      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        result = result.filter(dept => 
          dept.name.toLowerCase().includes(query) ||
          dept.description?.toLowerCase().includes(query) ||
          dept.head_of_department?.toLowerCase().includes(query) ||
          dept.location?.toLowerCase().includes(query)
        )
      }

      return result
    })

    const activeCount = computed(() => departments.value.filter(d => d.is_active).length)
    const totalAssets = computed(() => departments.value.reduce((sum, d) => sum + (d.total_assets || 0), 0))
    const totalUsers = computed(() => departments.value.reduce((sum, d) => sum + (d.total_users || 0), 0))

    const fetchDepartments = async () => {
      try {
        console.log('Fetching departments from API...')
        const response = await axios.get('http://localhost:8000/api/departments')
        console.log('Departments API Response:', response.data)
        departments.value = response.data.data || []
        console.log('Departments loaded:', departments.value.length)
      } catch (error) {
        console.error('Error fetching departments:', error)
        console.log('Error details:', error.response?.data || error.message)
        // Fallback data for demo
        departments.value = [
          {
            id: 1,
            name: 'Computer Science',
            description: 'Computer Science Department',
            category_id: 1,
            category: { name: 'Academic', color: '#0F6F43' },
            head_of_department: 'Dr. Smith',
            location: 'Building A, Room 101',
            contact_number: '123-456-7890',
            email: 'cs@university.edu',
            is_active: true,
            total_assets: 25,
            total_users: 150
          },
          {
            id: 2,
            name: 'Administration',
            description: 'Administration Office',
            category_id: 2,
            category: { name: 'Administrative', color: '#004D7A' },
            head_of_department: 'John Admin',
            location: 'Main Building, Room 201',
            contact_number: '123-456-7891',
            email: 'admin@university.edu',
            is_active: true,
            total_assets: 15,
            total_users: 25
          },
          {
            id: 3,
            name: 'Library',
            description: 'Library Services',
            category_id: 1,
            category: { name: 'Academic', color: '#0F6F43' },
            head_of_department: 'Ms. Johnson',
            location: 'Library Building, Room 101',
            contact_number: '123-456-7892',
            email: 'library@university.edu',
            is_active: true,
            total_assets: 10,
            total_users: 8
          },
          {
            id: 4,
            name: 'Student Services',
            description: 'Student Support Services',
            category_id: 3,
            category: { name: 'Student Services', color: '#FF6B35' },
            head_of_department: 'Ms. Davis',
            location: 'Student Center, Room 301',
            contact_number: '123-456-7893',
            email: 'students@university.edu',
            is_active: true,
            total_assets: 8,
            total_users: 12
          }
        ]
        console.log('Using fallback data, departments loaded:', departments.value.length)
      }
    }

    const fetchCategories = async () => {
      try {
        // This would be a real API call in production
        categories.value = [
          { id: 1, name: 'Academic', color: '#0F6F43' },
          { id: 2, name: 'Administrative', color: '#004D7A' },
          { id: 3, name: 'Technical', color: '#FF6B35' },
          { id: 4, name: 'Student Services', color: '#8B5CF6' },
          { id: 5, name: 'Facilities', color: '#10B981' }
        ]
      } catch (error) {
        console.error('Error fetching categories:', error)
      }
    }

    const refreshData = async () => {
      await Promise.all([fetchDepartments(), fetchCategories()])
    }

    const showCreateDepartmentModal = () => {
      const modal = new bootstrap.Modal(document.getElementById('createDepartmentModal'))
      modal.show()
    }

    const createDepartment = async () => {
      try {
        const response = await axios.post('http://localhost:8000/api/departments', newDepartment.value)
        
        if (response.data.success) {
          bootstrap.Modal.getInstance(document.getElementById('createDepartmentModal')).hide()
          await refreshData()
          
          // Reset form
          newDepartment.value = {
            name: '',
            description: '',
            category_id: '',
            head_of_department: '',
            location: '',
            contact_number: '',
            email: '',
            is_active: true
          }
          
          alert('Department created successfully!')
        }
      } catch (error) {
        console.error('Error creating department:', error)
        alert('Error creating department: ' + (error.response?.data?.message || error.message))
      }
    }

    const viewDepartment = (department) => {
      selectedDepartment.value = department
      const modal = new bootstrap.Modal(document.getElementById('viewDepartmentModal'))
      modal.show()
    }

    const editDepartment = (department) => {
      // For now, just show the department details
      viewDepartment(department)
    }

    const deleteDepartment = async (department) => {
      if (!confirm('Are you sure you want to delete this department?')) return
      
      try {
        const response = await axios.delete(`http://localhost:8000/api/departments/${department.id}`)
        
        if (response.data.success) {
          await refreshData()
          alert('Department deleted successfully!')
        }
      } catch (error) {
        console.error('Error deleting department:', error)
        alert('Error deleting department: ' + (error.response?.data?.message || error.message))
      }
    }

    const filterDepartments = () => {
      // This is handled by the computed property
    }

    const exportDepartments = () => {
      alert('Export feature would generate CSV/PDF report of current departments')
    }

    onMounted(() => {
      refreshData()
    })

    return {
      isNavCollapsed,
      departments,
      categories,
      categoryFilter,
      searchQuery,
      selectedDepartment,
      newDepartment,
      filteredDepartments,
      activeCount,
      totalAssets,
      totalUsers,
      toggleNav,
      openProfile,
      openSettings,
      getStatusBadgeClass,
      refreshData,
      showCreateDepartmentModal,
      createDepartment,
      viewDepartment,
      editDepartment,
      deleteDepartment,
      filterDepartments,
      exportDepartments
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
