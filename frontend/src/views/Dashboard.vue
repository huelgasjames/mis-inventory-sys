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
                      <div class="d-flex align-items-center">
                        <i :class="getComponentIcon(component)" class="me-2"></i>
                        <span class="small text-capitalize">{{ component.replace('_', ' ') }}</span>
                      </div>
                      <div class="d-flex align-items-center gap-1">
                        <span class="badge bg-primary">{{ count }}</span>
                        <span class="small text-muted">/ {{ componentStats.total_components?.[component] || 0 }}</span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="mt-3 pt-3 border-top">
                  <div class="d-flex justify-content-between align-items-center">
                    <span class="small fw-semibold">Total Available</span>
                    <span class="badge bg-success">
                      {{ Object.values(componentStats.available_components || {}).reduce((sum, count) => sum + count, 0) }}
                    </span>
                  </div>
                  <div class="d-flex justify-content-between align-items-center mt-1">
                    <span class="small fw-semibold">Total Components</span>
                    <span class="badge bg-info">
                      {{ Object.values(componentStats.total_components || {}).reduce((sum, count) => sum + count, 0) }}
                    </span>
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
              <button type="submit" class="btn btn-primary" @click="createComputerAsset">Create Computer</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick, watch } from 'vue'
import { useRouter } from 'vue-router'
import AppHeader from '@/components/AppHeader.vue'
import AppNav from '@/components/AppNav.vue'
import LoadingSpinner from '@/components/LoadingSpinner.vue'
import Chart from 'chart.js/auto'
import { useInventoryData } from '@/composables/useInventoryData'

const router = useRouter()
const isNavCollapsed = ref(false)

// Use centralized data fetching
const {
  isLoading,
  dashboardStats,
  componentStats,
  components,
  departments,
  users,
  refreshAllData,
  createComputer
} = useInventoryData()

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


// Create computer
const createComputerAsset = async () => {
  try {
    await createComputer(newComputer.value)
    
    alert('Computer created successfully!')
    bootstrap.Modal.getInstance(document.getElementById('createComputerModal')).hide()
    resetComputerForm()
    // Data is automatically refreshed by the composable
  } catch (error) {
    console.error('Error creating computer:', error)
    alert('Error creating computer: ' + (error.message || 'Unknown error'))
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

// Refresh all data using centralized service
const refreshData = async () => {
  try {
    await refreshAllData({ showLoading: true, minLoadingDuration: 1000 })
    initializeCharts()
  } catch (error) {
    console.error('Error refreshing data:', error)
  }
}

// Get component icon
const getComponentIcon = (component) => {
  switch (component) {
    case 'processors':
      return 'bi bi-cpu text-primary'
    case 'motherboards':
      return 'bi bi-motherboard text-success'
    case 'rams':
      return 'bi bi-memory text-info'
    case 'storages':
      return 'bi bi-hdd text-warning'
    case 'video_cards':
      return 'bi bi-gpu-card text-dark'
    case 'psus':
      return 'bi bi-lightning-charge text-dark'
    case 'dvd_roms':
      return 'bi bi-disc text-secondary'
    default:
      return 'bi bi-box text-muted'
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
  await refreshData()
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
@media (max-width: 1400px) {
  .col-lg-4 {
    margin-bottom: 1.5rem;
  }
}

@media (max-width: 1200px) {
  .col-lg-4 {
    margin-bottom: 1.5rem;
  }
  
  .row.g-4 {
    gap: 1rem;
  }
}

@media (max-width: 992px) {
  .main-content {
    margin-left: 70px;
  }
  
  .main-content.collapsed {
    margin-left: 70px;
  }
  
  .col-lg-3 {
    margin-bottom: 1rem;
  }
  
  .col-lg-6 {
    margin-bottom: 1.5rem;
  }
  
  .card-body {
    padding: 1.25rem !important;
  }
}

@media (max-width: 768px) {
  .main-content {
    margin-left: 0;
  }
  
  .main-content.collapsed {
    margin-left: 0;
  }
  
  .container-fluid {
    padding-left: 1rem !important;
    padding-right: 1rem !important;
  }
  
  .d-flex.justify-content-between {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch !important;
  }
  
  .d-flex.gap-2 {
    flex-wrap: wrap;
    gap: 0.5rem !important;
  }
  
  .btn {
    font-size: 0.9rem;
    padding: 0.5rem 1rem;
    flex: 1;
    min-width: 120px;
  }
  
  .h3 {
    font-size: 1.5rem;
  }
  
  .card-body {
    padding: 1rem !important;
  }
  
  .rounded-circle.p-3 {
    padding: 1.5rem !important;
  }
  
  .fs-4 {
    font-size: 1.25rem !important;
  }
  
  .h3.mb-0 {
    font-size: 1.75rem;
  }
  
  .row.g-3 {
    gap: 1rem;
  }
  
  .row.g-4 {
    gap: 1rem;
  }
}

@media (max-width: 576px) {
  .container-fluid {
    padding-left: 0.75rem !important;
    padding-right: 0.75rem !important;
  }
  
  .col-6 {
    flex: 0 0 100%;
    max-width: 100%;
  }
  
  .btn {
    font-size: 0.85rem;
    padding: 0.4rem 0.8rem;
  }
  
  .small {
    font-size: 0.75rem;
  }
  
  .card-body {
    padding: 0.75rem !important;
  }
  
  .rounded-circle.p-3 {
    padding: 1rem !important;
    margin-right: 0.75rem !important;
  }
  
  .chart-container {
    height: 200px !important;
  }
  
  .list-group-item {
    padding: 0.75rem 0;
  }
  
  .row.g-3 {
    gap: 0.75rem;
  }
  
  .row.g-4 {
    gap: 0.75rem;
  }
}

@media (max-width: 480px) {
  .container-fluid {
    padding-left: 0.5rem !important;
    padding-right: 0.5rem !important;
  }
  
  .h3 {
    font-size: 1.25rem;
  }
  
  .h3.mb-0 {
    font-size: 1.5rem;
  }
  
  .btn {
    font-size: 0.8rem;
    padding: 0.3rem 0.6rem;
  }
  
  .card-body {
    padding: 0.5rem !important;
  }
  
  .rounded-circle.p-3 {
    padding: 0.75rem !important;
    margin-right: 0.5rem !important;
  }
  
  .fs-4 {
    font-size: 1rem !important;
  }
  
  .chart-container {
    height: 150px !important;
  }
  
  .row.g-3 {
    gap: 0.5rem;
  }
  
  .row.g-4 {
    gap: 0.5rem;
  }
  
  .d-grid {
    gap: 0.5rem;
  }
}

/* Landscape mobile optimization */
@media (max-width: 768px) and (orientation: landscape) {
  .container-fluid {
    padding-left: 0.5rem !important;
    padding-right: 0.5rem !important;
  }
  
  .card-body {
    padding: 0.75rem !important;
  }
  
  .rounded-circle.p-3 {
    padding: 0.75rem !important;
  margin-right: 0.5rem !important;
  }
  
  .row.g-3 {
    gap: 0.5rem;
  }
}

/* Custom grid for Quick Actions */
.d-grid {
  display: grid;
  gap: 0.5rem;
  grid-template-columns: 1fr;
}
</style>
