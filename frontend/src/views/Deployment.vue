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
      
      <!-- Deployment Content -->
      <div class="container-fluid p-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h1 class="h3 mb-0">Deployment Management</h1>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" @click="refreshData">
              <i class="bi bi-arrow-clockwise me-2"></i>Refresh
            </button>
            <button class="btn btn-primary" @click="showCreateDeploymentModal">
              <i class="bi bi-plus-circle me-2"></i>Deploy Computer
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
                    <i class="bi bi-box-arrow-right text-primary fs-4"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Total Deployments</h6>
                    <h3 class="mb-0">{{ deploymentStats.total_deployments || 0 }}</h3>
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
                    <h6 class="text-muted mb-1">Deployed</h6>
                    <h3 class="mb-0">{{ deploymentStats.deployed || 0 }}</h3>
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
                    <h6 class="text-muted mb-1">Maintenance</h6>
                    <h3 class="mb-0">{{ deploymentStats.maintenance || 0 }}</h3>
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
                    <i class="bi bi-arrow-left text-info fs-4"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Returned</h6>
                    <h3 class="mb-0">{{ deploymentStats.returned || 0 }}</h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Deployments Table -->
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0">Deployment History</h5>
              <div class="d-flex gap-2">
                <select class="form-select form-select-sm" v-model="statusFilter" @change="filterDeployments">
                  <option value="">All Status</option>
                  <option value="deployed">Deployed</option>
                  <option value="returned">Returned</option>
                  <option value="maintenance">Maintenance</option>
                  <option value="retired">Retired</option>
                </select>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Computer</th>
                    <th>Department</th>
                    <th>User</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Deployment Date</th>
                    <th>Return Date</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="deployment in filteredDeployments" :key="deployment.id">
                    <td>
                      <div class="d-flex align-items-center">
                        <i class="bi bi-pc-display me-2 text-primary"></i>
                        <div>
                          <div class="fw-semibold">{{ deployment.computer?.computer_name }}</div>
                          <small class="text-muted">{{ deployment.computer?.asset_tag }}</small>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span class="badge bg-light text-dark">{{ deployment.department?.name }}</span>
                    </td>
                    <td>
                      <span v-if="deployment.user" class="text-capitalize">{{ deployment.user.name }}</span>
                      <span v-else class="text-muted">Unassigned</span>
                    </td>
                    <td>{{ deployment.location || '-' }}</td>
                    <td>
                      <span :class="getStatusBadgeClass(deployment.status)">
                        {{ deployment.status }}
                      </span>
                    </td>
                    <td>{{ formatDate(deployment.deployment_date) }}</td>
                    <td>{{ deployment.return_date ? formatDate(deployment.return_date) : '-' }}</td>
                    <td>
                      <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" @click="viewDeployment(deployment)">
                          <i class="bi bi-eye"></i>
                        </button>
                        <button v-if="deployment.status === 'deployed'" 
                                class="btn btn-outline-warning" 
                                @click="returnComputer(deployment)">
                          <i class="bi bi-arrow-left"></i>
                        </button>
                        <button class="btn btn-outline-danger" @click="deleteDeployment(deployment)">
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

  <!-- Create Deployment Modal -->
  <div class="modal fade" id="createDeploymentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Deploy Computer</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="createDeployment">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Computer *</label>
                <select class="form-select" v-model="newDeployment.computer_id" required>
                  <option value="">Select Computer</option>
                  <option v-for="computer in availableComputers" :key="computer.id" :value="computer.id">
                    {{ computer.computer_name }} - {{ computer.asset_tag }}
                  </option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Department *</label>
                <select class="form-select" v-model="newDeployment.department_id" required>
                  <option value="">Select Department</option>
                  <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">User</label>
                <select class="form-select" v-model="newDeployment.user_id">
                  <option value="">Select User (Optional)</option>
                  <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Location</label>
                <input type="text" class="form-control" v-model="newDeployment.location" placeholder="Room/Office">
              </div>
              <div class="col-md-6">
                <label class="form-label">Deployment Date *</label>
                <input type="date" class="form-control" v-model="newDeployment.deployment_date" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Deployed By *</label>
                <input type="text" class="form-control" v-model="newDeployment.deployed_by" required>
              </div>
              <div class="col-12">
                <label class="form-label">Notes</label>
                <textarea class="form-control" v-model="newDeployment.notes" rows="3"></textarea>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" @click="createDeployment">Deploy Computer</button>
        </div>
      </div>
    </div>
  </div>

  <!-- View Deployment Modal -->
  <div class="modal fade" id="viewDeploymentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Deployment Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" v-if="selectedDeployment">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Computer</label>
              <div class="form-control bg-light">
                {{ selectedDeployment.computer?.computer_name }} ({{ selectedDeployment.computer?.asset_tag }})
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Department</label>
              <div class="form-control bg-light">{{ selectedDeployment.department?.name }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">User</label>
              <div class="form-control bg-light">
                {{ selectedDeployment.user?.name || 'Unassigned' }}
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Location</label>
              <div class="form-control bg-light">{{ selectedDeployment.location || '-' }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Status</label>
              <div class="form-control bg-light">
                <span :class="getStatusBadgeClass(selectedDeployment.status)">
                  {{ selectedDeployment.status }}
                </span>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Deployed By</label>
              <div class="form-control bg-light">{{ selectedDeployment.deployed_by }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Deployment Date</label>
              <div class="form-control bg-light">{{ formatDate(selectedDeployment.deployment_date) }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Return Date</label>
              <div class="form-control bg-light">
                {{ selectedDeployment.return_date ? formatDate(selectedDeployment.return_date) : '-' }}
              </div>
            </div>
            <div class="col-12">
              <label class="form-label">Notes</label>
              <div class="form-control bg-light" style="min-height: 80px;">
                {{ selectedDeployment.notes || 'No notes' }}
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

<script>
import { ref, onMounted, computed } from 'vue'
import AppNav from '@/components/AppNav.vue'
import AppHeader from '@/components/AppHeader.vue'
import axios from 'axios'

export default {
  name: 'Deployment',
  components: {
    AppNav,
    AppHeader
  },
  setup() {
    const isNavCollapsed = ref(false)
    const deployments = ref([])
    const deploymentStats = ref({})
    const departments = ref([])
    const users = ref([])
    const computers = ref([])
    const statusFilter = ref('')
    const selectedDeployment = ref(null)
    
    const newDeployment = ref({
      computer_id: '',
      department_id: '',
      user_id: '',
      location: '',
      status: 'deployed',
      deployment_date: new Date().toISOString().split('T')[0],
      return_date: '',
      notes: '',
      deployed_by: ''
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
        deployed: 'badge bg-success',
        returned: 'badge bg-info',
        maintenance: 'badge bg-warning',
        retired: 'badge bg-secondary'
      }
      return classes[status] || 'badge bg-secondary'
    }

    const formatDate = (dateString) => {
      if (!dateString) return '-'
      return new Date(dateString).toLocaleDateString()
    }

    const filteredDeployments = computed(() => {
      if (!statusFilter.value) return deployments.value
      return deployments.value.filter(d => d.status === statusFilter.value)
    })

    const availableComputers = computed(() => {
      return computers.value.filter(computer => !computer.is_deployed)
    })

    const fetchDeployments = async () => {
      try {
        const response = await axios.get('http://localhost:8000/api/deployments')
        deployments.value = response.data.data
      } catch (error) {
        console.error('Error fetching deployments:', error)
      }
    }

    const fetchDeploymentStats = async () => {
      try {
        const response = await axios.get('http://localhost:8000/api/deployments/stats')
        deploymentStats.value = response.data.data
      } catch (error) {
        console.error('Error fetching deployment stats:', error)
      }
    }

    const fetchDepartments = async () => {
      try {
        const response = await axios.get('http://localhost:8000/api/departments')
        departments.value = response.data.data
      } catch (error) {
        console.error('Error fetching departments:', error)
      }
    }

    const fetchUsers = async () => {
      try {
        const response = await axios.get('http://localhost:8000/api/users')
        users.value = response.data.data
      } catch (error) {
        console.error('Error fetching users:', error)
      }
    }

    const fetchComputers = async () => {
      try {
        const response = await axios.get('http://localhost:8000/api/computers')
        computers.value = response.data.data
      } catch (error) {
        console.error('Error fetching computers:', error)
      }
    }

    const refreshData = async () => {
      await Promise.all([
        fetchDeployments(),
        fetchDeploymentStats(),
        fetchDepartments(),
        fetchUsers(),
        fetchComputers()
      ])
    }

    const showCreateDeploymentModal = () => {
      const modal = new bootstrap.Modal(document.getElementById('createDeploymentModal'))
      modal.show()
    }

    const createDeployment = async () => {
      try {
        const response = await axios.post('http://localhost:8000/api/deployments', newDeployment.value)
        
        if (response.data.success) {
          bootstrap.Modal.getInstance(document.getElementById('createDeploymentModal')).hide()
          await refreshData()
          
          // Reset form
          newDeployment.value = {
            computer_id: '',
            department_id: '',
            user_id: '',
            location: '',
            status: 'deployed',
            deployment_date: new Date().toISOString().split('T')[0],
            return_date: '',
            notes: '',
            deployed_by: ''
          }
          
          // Show success message
          alert('Computer deployed successfully!')
        }
      } catch (error) {
        console.error('Error creating deployment:', error)
        alert('Error deploying computer: ' + (error.response?.data?.message || error.message))
      }
    }

    const viewDeployment = (deployment) => {
      selectedDeployment.value = deployment
      const modal = new bootstrap.Modal(document.getElementById('viewDeploymentModal'))
      modal.show()
    }

    const returnComputer = async (deployment) => {
      if (!confirm('Are you sure you want to return this computer?')) return
      
      try {
        const returnDate = new Date().toISOString().split('T')[0]
        const response = await axios.post(`http://localhost:8000/api/deployments/${deployment.id}/return`, {
          return_date: returnDate,
          notes: 'Computer returned from deployment'
        })
        
        if (response.data.success) {
          await refreshData()
          alert('Computer returned successfully!')
        }
      } catch (error) {
        console.error('Error returning computer:', error)
        alert('Error returning computer: ' + (error.response?.data?.message || error.message))
      }
    }

    const deleteDeployment = async (deployment) => {
      if (!confirm('Are you sure you want to delete this deployment record?')) return
      
      try {
        const response = await axios.delete(`http://localhost:8000/api/deployments/${deployment.id}`)
        
        if (response.data.success) {
          await refreshData()
          alert('Deployment deleted successfully!')
        }
      } catch (error) {
        console.error('Error deleting deployment:', error)
        alert('Error deleting deployment: ' + (error.response?.data?.message || error.message))
      }
    }

    const filterDeployments = () => {
      // This is handled by the computed property
    }

    onMounted(() => {
      refreshData()
    })

    return {
      isNavCollapsed,
      deployments,
      deploymentStats,
      departments,
      users,
      computers,
      statusFilter,
      selectedDeployment,
      newDeployment,
      filteredDeployments,
      availableComputers,
      toggleNav,
      openProfile,
      openSettings,
      getStatusBadgeClass,
      formatDate,
      refreshData,
      showCreateDeploymentModal,
      createDeployment,
      viewDeployment,
      returnComputer,
      deleteDeployment,
      filterDeployments
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
