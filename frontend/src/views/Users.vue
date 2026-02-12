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
      
      <!-- Users Content -->
      <div class="container-fluid p-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h1 class="h3 mb-0">User Management</h1>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" @click="refreshData">
              <i class="bi bi-arrow-clockwise me-2"></i>Refresh
            </button>
            <button class="btn btn-primary" @click="showCreateUserModal">
              <i class="bi bi-plus-circle me-2"></i>Add User
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
                    <i class="bi bi-people text-primary fs-4"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Total Users</h6>
                    <h3 class="mb-0">{{ users.length }}</h3>
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
                    <i class="bi bi-shield-check text-success fs-4"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Admin Users</h6>
                    <h3 class="mb-0">{{ adminCount }}</h3>
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
                    <h6 class="text-muted mb-1">With Assets</h6>
                    <h3 class="mb-0">{{ usersWithAssetsCount }}</h3>
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
                    <i class="bi bi-building text-warning fs-4"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Departments</h6>
                    <h3 class="mb-0">{{ departmentCount }}</h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Users Table -->
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0">Users Directory</h5>
              <div class="d-flex gap-2">
                <select class="form-select form-select-sm" v-model="roleFilter" @change="filterUsers">
                  <option value="">All Roles</option>
                  <option value="admin">Admin</option>
                  <option value="staff">Staff</option>
                  <option value="student">Student</option>
                </select>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Department</th>
                    <th>Assigned Assets</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="user in filteredUsers" :key="user.id">
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-3">
                          <i class="bi bi-person text-primary"></i>
                        </div>
                        <div>
                          <div class="fw-semibold">{{ user.name }}</div>
                          <small class="text-muted">ID: {{ user.id }}</small>
                        </div>
                      </div>
                    </td>
                    <td>{{ user.email }}</td>
                    <td>
                      <span :class="getRoleBadgeClass(user.role)">
                        {{ user.role }}
                      </span>
                    </td>
                    <td>
                      <span v-if="user.department" class="badge bg-light text-dark">
                        {{ user.department.name }}
                      </span>
                      <span v-else class="text-muted">-</span>
                    </td>
                    <td>
                      <span class="badge bg-info">{{ user.assigned_assets_count || 0 }}</span>
                    </td>
                    <td>
                      <span :class="getStatusBadgeClass(user.is_active)">
                        {{ user.is_active ? 'Active' : 'Inactive' }}
                      </span>
                    </td>
                    <td>
                      <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" @click="viewUser(user)">
                          <i class="bi bi-eye"></i>
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

  <!-- Create User Modal -->
  <div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add User</h5>
          <button type="button" class="btn-close" @click="hideCreateUserModal"></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="createUser">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Name *</label>
                <input type="text" class="form-control" v-model="newUser.name" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Email *</label>
                <input type="email" class="form-control" v-model="newUser.email" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Password *</label>
                <input type="password" class="form-control" v-model="newUser.password" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Role *</label>
                <select class="form-select" v-model="newUser.role" required>
                  <option value="">Select Role</option>
                  <option value="admin">Admin</option>
                  <option value="faculty">Faculty</option>
                  <option value="staff">Staff</option>
                  <option value="student">Student</option>
                </select>
              </div>
              <div class="col-md-12">
                <label class="form-label">Address *</label>
                <textarea class="form-control" v-model="newUser.address" rows="2" required placeholder="Enter complete address"></textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label">Birthdate *</label>
                <input type="date" class="form-control" v-model="newUser.birthdate" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Contact Person</label>
                <input type="text" class="form-control" v-model="newUser.contact_person" placeholder="Name of contact person">
              </div>
              <div class="col-md-6">
                <label class="form-label">Contact Number</label>
                <input type="text" class="form-control" v-model="newUser.contact_number" placeholder="Phone number">
              </div>
              <div class="col-md-6">
                <label class="form-label">Department</label>
                <select class="form-select" v-model="newUser.department_id">
                  <option value="">Select Department</option>
                  <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                    {{ dept.name }}
                  </option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Status</label>
                <select class="form-select" v-model="newUser.is_active">
                  <option :value="true">Active</option>
                  <option :value="false">Inactive</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Assigned Assets</label>
                <input type="number" class="form-control" v-model="newUser.assigned_assets_count" min="0" placeholder="Number of assigned assets">
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="hideCreateUserModal">Cancel</button>
          <button type="button" class="btn btn-primary" @click="createUser">Add User</button>
        </div>
      </div>
    </div>
  </div>

  <!-- View User Modal -->
  <div class="modal fade" id="viewUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">User Details</h5>
          <button type="button" class="btn-close" @click="hideViewUserModal"></button>
        </div>
        <div class="modal-body" v-if="selectedUser">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Name</label>
              <div class="form-control bg-light">{{ selectedUser.name }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <div class="form-control bg-light">{{ selectedUser.email }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Role</label>
              <div class="form-control bg-light">
                <span :class="getRoleBadgeClass(selectedUser.role)">
                  {{ selectedUser.role }}
                </span>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Address</label>
              <div class="form-control bg-light" style="white-space: pre-wrap;">{{ selectedUser.address || 'N/A' }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Birthdate</label>
              <div class="form-control bg-light">{{ formatDate(selectedUser.birthdate) }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Contact Person</label>
              <div class="form-control bg-light">{{ selectedUser.contact_person || 'N/A' }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Contact Number</label>
              <div class="form-control bg-light">{{ selectedUser.contact_number || 'N/A' }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Department</label>
              <div class="form-control bg-light">
                {{ selectedUser.department?.name || 'No Department' }}
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Status</label>
              <div class="form-control bg-light">
                <span :class="getStatusBadgeClass(selectedUser.is_active)">
                  {{ selectedUser.is_active ? 'Active' : 'Inactive' }}
                </span>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Assigned Assets</label>
              <div class="form-control bg-light">{{ selectedUser.assigned_assets_count || 0 }}</div>
            </div>
            <div class="col-12">
              <label class="form-label">Created Date</label>
              <div class="form-control bg-light">{{ formatDate(selectedUser.created_at) }}</div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="hideViewUserModal">Close</button>
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
  name: 'Users',
  components: {
    AppNav,
    AppHeader
  },
  setup() {
    console.log('Users component setup started')
    const isNavCollapsed = ref(false)
    const users = ref([])
    const departments = ref([])
    const roleFilter = ref('')
    const selectedUser = ref(null)
    
    const newUser = ref({
      name: '',
      email: '',
      password: '',
      role: '',
      address: '',
      birthdate: '',
      contact_person: '',
      contact_number: '',
      department_id: '',
      is_active: true,
      assigned_assets_count: 0
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

    const getRoleBadgeClass = (role) => {
      const classes = {
        admin: 'badge bg-danger',
        faculty: 'badge bg-warning',
        staff: 'badge bg-primary',
        student: 'badge bg-success'
      }
      return classes[role] || 'badge bg-secondary'
    }

    const getStatusBadgeClass = (isActive) => {
      return isActive ? 'badge bg-success' : 'badge bg-secondary'
    }

    const formatDate = (dateString) => {
      if (!dateString) return '-'
      return new Date(dateString).toLocaleDateString()
    }

    const filteredUsers = computed(() => {
      if (!roleFilter.value) return users.value
      return users.value.filter(u => u.role === roleFilter.value)
    })

    const adminCount = computed(() => users.value.filter(u => u.role === 'admin').length)
    const usersWithAssetsCount = computed(() => users.value.filter(u => u.assigned_assets_count > 0).length)
    const departmentCount = computed(() => new Set(users.value.filter(u => u.department).map(u => u.department.name)).size)

    const fetchUsers = async () => {
      try {
        const response = await axios.get('http://localhost:8000/api/users')
        users.value = response.data.data || []
      } catch (error) {
        console.error('Error fetching users:', error)
        // Fallback data for demo
        users.value = [
          {
            id: 1,
            name: 'Admin User',
            email: 'admin@mis.com',
            role: 'admin',
            department: null,
            assigned_assets_count: 0,
            is_active: true
          },
          {
            id: 2,
            name: 'John Doe',
            email: 'john@mis.com',
            role: 'staff',
            department_id: 1,
            department: { name: 'Computer Science' },
            is_active: true,
            assigned_assets_count: 2,
            created_at: '2024-01-02'
          },
          {
            id: 3,
            name: 'Jane Smith',
            email: 'jane@mis.com',
            role: 'student',
            department_id: 1,
            department: { name: 'Library' },
            is_active: true,
            assigned_assets_count: 1,
            created_at: '2024-01-03'
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
        departments.value = []
      }
    }

    const refreshData = async () => {
      await Promise.all([fetchUsers(), fetchDepartments()])
    }

    const showCreateUserModal = () => {
      const modal = document.getElementById('createUserModal')
      if (modal) {
        modal.classList.add('show')
        modal.style.display = 'block'
        document.body.classList.add('modal-open')
        
        // Create backdrop
        const backdrop = document.createElement('div')
        backdrop.className = 'modal-backdrop fade show'
        backdrop.id = 'modal-backdrop'
        document.body.appendChild(backdrop)
      }
    }

    const hideCreateUserModal = () => {
      const modal = document.getElementById('createUserModal')
      const backdrop = document.getElementById('modal-backdrop')
      
      if (modal) {
        modal.classList.remove('show')
        modal.style.display = 'none'
        document.body.classList.remove('modal-open')
      }
      
      if (backdrop) {
        backdrop.remove()
      }
    }

    const createUser = async () => {
      try {
        const response = await axios.post('http://localhost:8000/api/users', newUser.value)
        
        if (response.data.success) {
          hideCreateUserModal()
          await refreshData()
          
          // Reset form
          newUser.value = {
            name: '',
            email: '',
            password: '',
            role: '',
            address: '',
            birthdate: '',
            contact_person: '',
            contact_number: '',
            department_id: '',
            is_active: true,
            assigned_assets_count: 0
          }
          
          alert('User created successfully!')
        }
      } catch (error) {
        console.error('Error creating user:', error)
        alert('Error creating user: ' + (error.response?.data?.message || error.message))
      }
    }

    const viewUser = (user) => {
      selectedUser.value = user
      const modal = document.getElementById('viewUserModal')
      if (modal) {
        modal.classList.add('show')
        modal.style.display = 'block'
        document.body.classList.add('modal-open')
        
        // Create backdrop
        const backdrop = document.createElement('div')
        backdrop.className = 'modal-backdrop fade show'
        backdrop.id = 'view-modal-backdrop'
        document.body.appendChild(backdrop)
      }
    }

    const hideViewUserModal = () => {
      const modal = document.getElementById('viewUserModal')
      const backdrop = document.getElementById('view-modal-backdrop')
      
      if (modal) {
        modal.classList.remove('show')
        modal.style.display = 'none'
        document.body.classList.remove('modal-open')
      }
      
      if (backdrop) {
        backdrop.remove()
      }
    }

    const filterUsers = () => {
      // This is handled by the computed property
    }

    onMounted(() => {
      console.log('Users component mounted')
      refreshData()
    })

    return {
      isNavCollapsed,
      users,
      departments,
      roleFilter,
      selectedUser,
      newUser,
      filteredUsers,
      adminCount,
      usersWithAssetsCount,
      departmentCount,
      toggleNav,
      openProfile,
      openSettings,
      getRoleBadgeClass,
      getStatusBadgeClass,
      formatDate,
      refreshData,
      showCreateUserModal,
      hideCreateUserModal,
      createUser,
      viewUser,
      hideViewUserModal,
      filterUsers
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
