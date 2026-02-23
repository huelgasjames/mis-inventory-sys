<template>
  <div class="dashboard-layout">
    <!-- Loading Spinner -->
    <LoadingSpinner :is-visible="isLoading" message="Loading dashboard..." />

    <!-- Navigation Sidebar -->
    <AppNav :is-collapsed="isNavCollapsed" />
    
    <!-- Main Content Area -->
    <div class="main-content" :class="{ 'collapsed': isNavCollapsed }">
      <!-- Header -->
      <AppHeader 
        :is-collapsed="isNavCollapsed"
        @sidebar-toggle="(collapsed) => {
          console.log('Dashboard received sidebar-toggle:', collapsed)
          isNavCollapsed = collapsed
        }"
        @profile-open="openProfile"
        @settings-open="openSettings"
      />
      
      <!-- Dashboard Content -->
      <div class="container-fluid p-4">
        <!-- Dashboard Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h1 class="h3 mb-0" style="color: black;">Inventory Dashboard</h1>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" @click="refreshData">
              <i class="bi bi-arrow-clockwise me-2"></i>Refresh
            </button>
            <button class="btn btn-primary" @click="showCreateComputerModal">
              <i class="bi bi-plus-circle me-2"></i>Create Computer
            </button>
          </div>
        </div>

        <!-- Overview Stats Cards -->
        <div class="row g-3 mb-4">
          <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                    <i class="bi bi-pc-display text-primary fs-4"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Total Assets</h6>
                    <h3 class="mb-0">{{ dashboardStats.overview?.total_assets || 0 }}</h3>
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
                    <h3 class="mb-0">{{ dashboardStats.status_breakdown?.available || 0 }}</h3>
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
                    <i class="bi bi-building text-info fs-4"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Deployed</h6>
                    <h3 class="mb-0">{{ dashboardStats.status_breakdown?.deployed || 0 }}</h3>
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
                    <h6 class="text-muted mb-1">Under Repair</h6>
                    <h3 class="mb-0">{{ dashboardStats.status_breakdown?.under_repair || 0 }}</h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Main Dashboard Grid -->
        <div class="row g-4">
          <!-- Component Inventory Section -->
          <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-white border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                  <h5 class="card-title mb-0">Component Inventory</h5>
                  <router-link to="/components" class="btn btn-sm btn-outline-primary">View All</router-link>
                </div>
              </div>
              <div class="card-body">
                <div class="row g-2">
                  <div v-for="(count, component) in componentStats.available_components" :key="component" class="col-6">
                    <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                      <span class="small text-capitalize">{{ component.replace('_', ' ') }}</span>
                      <span class="badge bg-primary">{{ count }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Activities -->
          <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-white border-bottom">
                <h5 class="card-title mb-0">Recent Activities</h5>
              </div>
              <div class="card-body">
                <div class="list-group list-group-flush">
                  <div v-for="activity in dashboardStats.recent_activities" :key="activity.timestamp" 
                       class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <div class="d-flex align-items-center">
                      <div :class="getActivityIconClass(activity.type)" class="rounded-circle p-2 me-3">
                        <i :class="getActivityIcon(activity.type)"></i>
                      </div>
                      <div>
                        <h6 class="mb-1 fw-semibold small">{{ activity.description }}</h6>
                        <small class="text-muted">{{ activity.timestamp }}</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-white border-bottom">
                <h5 class="card-title mb-0">Quick Actions</h5>
              </div>
              <div class="card-body">
                <div class="d-grid gap-2">
                  <router-link to="/components" class="btn btn-outline-primary d-flex align-items-center justify-content-start">
                    <i class="bi bi-cpu me-2"></i>
                    <span>Manage Components</span>
                  </router-link>
                  <router-link to="/computers" class="btn btn-outline-primary d-flex align-items-center justify-content-start">
                    <i class="bi bi-pc-display me-2"></i>
                    <span>Manage Computers</span>
                  </router-link>
                  <router-link to="/department-inventory" class="btn btn-outline-primary d-flex align-items-center justify-content-start">
                    <i class="bi bi-building me-2"></i>
                    <span>Department Inventory</span>
                  </router-link>
                  <router-link to="/reports" class="btn btn-outline-primary d-flex align-items-center justify-content-start">
                    <i class="bi bi-file-earmark-bar-graph me-2"></i>
                    <span>View Reports</span>
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Charts Section -->
        <div class="row g-4 mt-2">
          <!-- Monthly Repairs Chart -->
          <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-white border-bottom">
                <h5 class="card-title mb-0">Monthly Repair Trends</h5>
              </div>
              <div class="card-body">
                <div class="chart-container" style="height: 250px;">
                  <canvas ref="repairChart"></canvas>
                </div>
              </div>
            </div>
          </div>

          <!-- Component Health Chart -->
          <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-white border-bottom">
                <h5 class="card-title mb-0">Component Health Status</h5>
              </div>
              <div class="card-body">
                <div class="chart-container" style="height: 250px;">
                  <canvas ref="healthChart"></canvas>
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
          <h5 class="modal-title">Create Computer Asset</h5>
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
                <label class="form-label">Department</label>
                <select class="form-select" v-model="newComputer.department_id">
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
            </div>
            <div class="mt-3">
              <button type="submit" class="btn btn-primary">Create Computer</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick, watch, computed } from 'vue'
import { useRouter } from 'vue-router'
import AppHeader from '@/components/AppHeader.vue'
import AppNav from '@/components/AppNav.vue'
import LoadingSpinner from '@/components/LoadingSpinner.vue'
import Chart from 'chart.js/auto'
import axios from 'axios'

const router = useRouter()
const isNavCollapsed = ref(false)
const isLoading = ref(false)
const loadingStartTime = ref(null)

// Watch for changes in navigation state
watch(isNavCollapsed, (newValue, oldValue) => {
  console.log('isNavCollapsed changed from', oldValue, 'to', newValue)
})

// Data from backend API
const dashboardStats = ref({})
const componentStats = ref({})
const components = ref({})
const departments = ref([])
const users = ref([])

// New computer form
const newComputer = ref({
  computer_name: '',
  department_id: '',
  processor_id: '',
  motherboard_id: '',
  ram_id: '',
  storage_id: '',
})

// Chart references
const repairChart = ref(null)
const healthChart = ref(null)

// Fetch dashboard statistics
const fetchDashboardStats = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/reports/dashboard-stats')
    if (response.data.success) {
      dashboardStats.value = response.data.data
      console.log('Dashboard stats loaded:', dashboardStats.value)
    } else {
      console.error('Error fetching dashboard stats:', response.data.message)
      // Use mock data as fallback
      dashboardStats.value = {
        overview: { total_assets: 0, total_computers: 0, total_departments: 0, total_laboratories: 0 },
        status_breakdown: { available: 0, deployed: 0, under_repair: 0, defective: 0 },
        recent_activities: [],
        monthly_repairs: [],
        component_health: []
      }
    }
  } catch (error) {
    console.error('Error fetching dashboard stats:', error)
    // Use mock data as fallback
    dashboardStats.value = {
      overview: { total_assets: 0, total_computers: 0, total_departments: 0, total_laboratories: 0 },
      status_breakdown: { available: 0, deployed: 0, under_repair: 0, defective: 0 },
      recent_activities: [],
      monthly_repairs: [],
      component_health: []
    }
  }
}
// Fetch component statistics
const fetchComponentStats = async () => {
  try {
    // Use mock data for components
    componentStats.value = {
      total_processors: 85,
      total_memory: 90,
      total_storage: 75,
      total_graphics: 60,
      healthy_percentage: 85
    }
    console.log('Using mock component stats:', componentStats.value)
  } catch (error) {
    console.error('Error setting component stats:', error)
  }
}

// Fetch components
const fetchComponents = async () => {
  try {
    // Use mock data for components list
    components.value = {
      processors: [
        { id: 1, model: 'Intel Core i7-10700' },
        { id: 2, model: 'AMD Ryzen 9 5900X' },
        { id: 3, model: 'Intel Core i5-11600K' },
        { id: 4, model: 'AMD Ryzen 7 5800X' }
      ],
      motherboards: [
        { id: 1, model: 'ASRock X570 Taichi' },
        { id: 2, model: 'ASUS PRIME Z590-A' },
        { id: 3, model: 'MSI MPG X570 GAMING PLUS' },
        { id: 4, model: 'GIGABYTE B550 AORUS MASTER' }
      ],
      rams: [
        { id: 1, capacity: '16GB DDR4' },
        { id: 2, capacity: '32GB DDR4' },
        { id: 3, capacity: '64GB DDR4' },
        { id: 4, capacity: '128GB DDR4' }
      ],
      storages: [
        { id: 1, capacity: '512GB NVMe SSD' },
        { id: 2, capacity: '1TB NVMe SSD' },
        { id: 3, capacity: '2TB NVMe SSD' },
        { id: 4, capacity: '4TB NVMe SSD' }
      ]
    }
    console.log('Using mock components data:', components.value)
  } catch (error) {
    console.error('Error setting components data:', error)
  }
}

// Fetch departments
const fetchDepartments = async () => {
  try {
    // Use mock data for departments
    departments.value = [
      { id: 1, name: 'Computer Lab' },
      { id: 2, name: 'Library' },
      { id: 3, name: 'Office' },
      { id: 4, name: 'Classroom' }
    ]
    console.log('Using mock departments data:', departments.value)
  } catch (error) {
    console.error('Error setting departments data:', error)
  }
}

// Fetch users
const fetchUsers = async () => {
  try {
    // Use mock data for users
    users.value = [
      { id: 1, name: 'John Doe' },
      { id: 2, name: 'Jane Doe' },
      { id: 3, name: 'Bob Smith' },
      { id: 4, name: 'Alice Johnson' }
    ]
    console.log('Using mock users data:', users.value)
  } catch (error) {
    console.error('Error setting users data:', error)
  }
}

// Create computer
const createComputer = async () => {
  isLoading.value = true
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
      alert('Computer created successfully!')
      bootstrap.Modal.getInstance(document.getElementById('createComputerModal')).hide()
      resetComputerForm()
      refreshData()
    } else {
      alert('Error creating computer: ' + data.message)
    }
  } catch (error) {
    console.error('Error creating computer:', error)
    alert('Error creating computer')
  } finally {
    isLoading.value = false
  }
}

// Reset computer form
const resetComputerForm = () => {
  newComputer.value = {
    computer_name: '',
    department_id: '',
    processor_id: '',
    motherboard_id: '',
    ram_id: '',
    storage_id: '',
  }
}

// Show create computer modal
const showCreateComputerModal = () => {
  const modal = new bootstrap.Modal(document.getElementById('createComputerModal'))
  modal.show()
}

// Refresh all data
const refreshData = async () => {
  if (!isLoading.value) {
    isLoading.value = true
    loadingStartTime.value = Date.now()
  }
  try {
    await Promise.all([
      fetchDashboardStats(),
      fetchComponentStats(),
      fetchComponents(),
      fetchDepartments(),
      fetchUsers()
    ])
  } catch (error) {
    console.error('Error refreshing data:', error)
  } finally {
    isLoading.value = false
    loadingStartTime.value = null
  }
}

// Get activity icon
const getActivityIcon = (type) => {
  switch (type) {
    case 'deployment':
      return 'bi bi-box-arrow-right'
    case 'repair':
      return 'bi bi-tools'
    default:
      return 'bi bi-activity'
  }
}

// Get activity icon class
const getActivityIconClass = (type) => {
  switch (type) {
    case 'deployment':
      return 'bg-primary bg-opacity-10 text-primary'
    case 'repair':
      return 'bg-warning bg-opacity-10 text-warning'
    default:
      return 'bg-info bg-opacity-10 text-info'
  }
}

// Initialize charts
const initializeCharts = () => {
  nextTick(() => {
    // Repair Chart
    if (repairChart.value && dashboardStats.value.monthly_repairs) {
      const ctx = repairChart.value.getContext('2d')
      new Chart(ctx, {
        type: 'line',
        data: {
          labels: dashboardStats.value.monthly_repairs.map(m => m.month),
          datasets: [{
            label: 'Repairs',
            data: dashboardStats.value.monthly_repairs.map(m => m.count),
            borderColor: '#0F6F43',
            backgroundColor: 'rgba(15, 111, 67, 0.1)',
            tension: 0.4
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      })
    }

    // Health Chart
    if (healthChart.value && dashboardStats.value.component_health) {
      const ctx = healthChart.value.getContext('2d')
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: dashboardStats.value.component_health.map(c => c.component),
          datasets: [{
            label: 'Health %',
            data: dashboardStats.value.component_health.map(c => c.health_percentage),
            backgroundColor: dashboardStats.value.component_health.map(c => 
              c.health_percentage > 80 ? '#28a745' : 
              c.health_percentage > 60 ? '#ffc107' : '#dc3545'
            )
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              max: 100
            }
          }
        }
      })
    }
  })
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

const logout = () => {
  localStorage.removeItem('isAuthenticated')
  localStorage.removeItem('user')
  localStorage.removeItem('token')
  router.push('/login')
}

onMounted(async () => {
  if (!isLoading.value) {
    isLoading.value = true
    loadingStartTime.value = Date.now()
  }
  
  await refreshData()
  initializeCharts()
  
  // Ensure minimum loading duration
  if (loadingStartTime.value) {
    const elapsed = Date.now() - loadingStartTime.value
    if (elapsed < 1000) {
      await new Promise(resolve => setTimeout(resolve, 1000 - elapsed))
    }
  }
  
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

/* Custom grid for Quick Actions */
.d-grid {
  display: grid;
  gap: 0.5rem;
  grid-template-columns: 1fr;
}
</style>
