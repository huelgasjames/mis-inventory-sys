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
      
      <!-- Reports Content -->
      <div class="container-fluid p-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h1 class="h3 mb-0">Reports & Analytics</h1>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" @click="refreshData">
              <i class="bi bi-arrow-clockwise me-2"></i>Refresh
            </button>
            <button class="btn btn-success" @click="exportReport">
              <i class="bi bi-download me-2"></i>Export Report
            </button>
          </div>
        </div>

        <!-- Report Type Selection -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-3">
                <label class="form-label">Report Type</label>
                <select class="form-select" v-model="selectedReportType" @change="loadReportData">
                  <option value="pcs_per_department">PCs per Department</option>
                  <option value="pcs_per_laboratory">PCs per Laboratory</option>
                  <option value="component_usage">Component Usage</option>
                  <option value="repair_history">Repair History</option>
                  <option value="deployment_history">Deployment History</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label">Date Range</label>
                <select class="form-select" v-model="dateRange">
                  <option value="7">Last 7 Days</option>
                  <option value="30">Last 30 Days</option>
                  <option value="90">Last 90 Days</option>
                  <option value="365">Last Year</option>
                  <option value="all">All Time</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label">Format</label>
                <select class="form-select" v-model="exportFormat">
                  <option value="csv">CSV</option>
                  <option value="pdf">PDF</option>
                  <option value="excel">Excel</option>
                </select>
              </div>
              <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-primary w-100" @click="generateReport">
                  <i class="bi bi-file-earmark-bar-graph me-2"></i>Generate Report
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Summary Cards -->
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
                    <h3 class="mb-0">{{ reportStats.total_assets || 0 }}</h3>
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
                    <h3 class="mb-0">{{ reportStats.working || 0 }}</h3>
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
                    <h3 class="mb-0">{{ reportStats.under_repair || 0 }}</h3>
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
                    <h3 class="mb-0">{{ reportStats.defective || 0 }}</h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Report Content -->
        <div class="row g-4">
          <!-- Chart Section -->
          <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-white border-bottom">
                <h5 class="card-title mb-0">{{ getReportTitle() }}</h5>
              </div>
              <div class="card-body">
                <div class="chart-container" style="height: 400px;">
                  <canvas ref="reportChart"></canvas>
                </div>
              </div>
            </div>
          </div>

          <!-- Data Table -->
          <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-white border-bottom">
                <h5 class="card-title mb-0">Detailed Data</h5>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-sm">
                    <thead>
                      <tr>
                        <th>{{ getTableHeader() }}</th>
                        <th>Count</th>
                        <th>%</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(item, index) in reportData" :key="index">
                        <td>{{ item.name || item.department || item.component }}</td>
                        <td>{{ item.count }}</td>
                        <td>{{ calculatePercentage(item.count) }}%</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Additional Reports -->
        <div class="row g-4 mt-2">
          <!-- Recent Activities -->
          <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-white border-bottom">
                <h5 class="card-title mb-0">Recent Activities</h5>
              </div>
              <div class="card-body">
                <div class="list-group list-group-flush">
                  <div v-for="activity in recentActivities" :key="activity.id" 
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

          <!-- Quick Stats -->
          <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-white border-bottom">
                <h5 class="card-title mb-0">Quick Statistics</h5>
              </div>
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-6">
                    <div class="text-center">
                      <h4 class="text-primary">{{ quickStats.deployments }}</h4>
                      <small class="text-muted">Deployments</small>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="text-center">
                      <h4 class="text-success">{{ quickStats.repairs }}</h4>
                      <small class="text-muted">Repairs</small>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="text-center">
                      <h4 class="text-warning">{{ quickStats.transfers }}</h4>
                      <small class="text-muted">Transfers</small>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="text-center">
                      <h4 class="text-info">{{ quickStats.returns }}</h4>
                      <small class="text-muted">Returns</small>
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
</template>

<script>
import { ref, onMounted, nextTick } from 'vue'
import AppNav from '@/components/AppNav.vue'
import AppHeader from '@/components/AppHeader.vue'
import axios from 'axios'

export default {
  name: 'Reports',
  components: {
    AppNav,
    AppHeader
  },
  setup() {
    const isNavCollapsed = ref(false)
    const selectedReportType = ref('pcs_per_department')
    const dateRange = ref('30')
    const exportFormat = ref('csv')
    const reportData = ref([])
    const reportStats = ref({})
    const recentActivities = ref([])
    const quickStats = ref({
      deployments: 0,
      repairs: 0,
      transfers: 0,
      returns: 0
    })
    const reportChart = ref(null)
    let chartInstance = null

    const toggleNav = () => {
      isNavCollapsed.value = !isNavCollapsed.value
    }

    const openProfile = () => {
      console.log('Profile clicked')
    }

    const openSettings = () => {
      console.log('Settings clicked')
    }

    const getReportTitle = () => {
      const titles = {
        pcs_per_department: 'PCs per Department',
        pcs_per_laboratory: 'PCs per Laboratory',
        component_usage: 'Component Usage Statistics',
        repair_history: 'Repair History',
        deployment_history: 'Deployment History'
      }
      return titles[selectedReportType.value] || 'Report'
    }

    const getTableHeader = () => {
      const headers = {
        pcs_per_department: 'Department',
        pcs_per_laboratory: 'Laboratory',
        component_usage: 'Component',
        repair_history: 'Item',
        deployment_history: 'Computer'
      }
      return headers[selectedReportType.value] || 'Name'
    }

    const getActivityIcon = (type) => {
      const icons = {
        deployment: 'bi bi-box-arrow-right',
        repair: 'bi bi-tools',
        transfer: 'bi bi-arrow-left-right',
        return: 'bi bi-arrow-left'
      }
      return icons[type] || 'bi bi-info-circle'
    }

    const getActivityIconClass = (type) => {
      const classes = {
        deployment: 'bg-primary bg-opacity-10 text-primary',
        repair: 'bg-warning bg-opacity-10 text-warning',
        transfer: 'bg-info bg-opacity-10 text-info',
        return: 'bg-success bg-opacity-10 text-success'
      }
      return classes[type] || 'bg-secondary bg-opacity-10 text-secondary'
    }

    const calculatePercentage = (count) => {
      const total = reportData.value.reduce((sum, item) => sum + item.count, 0)
      return total > 0 ? ((count / total) * 100).toFixed(1) : 0
    }

    const fetchReportData = async () => {
      try {
        const response = await axios.get(`http://localhost:8000/api/reports/${selectedReportType.value}`)
        reportData.value = response.data.data || []
      } catch (error) {
        console.error('Error fetching report data:', error)
        // Fallback data
        reportData.value = [
          { name: 'Computer Science', count: 25 },
          { name: 'Administration', count: 15 },
          { name: 'Library', count: 10 },
          { name: 'Engineering', count: 20 }
        ]
      }
    }

    const fetchReportStats = async () => {
      try {
        const response = await axios.get('http://localhost:8000/api/reports/dashboard-stats')
        reportStats.value = response.data.data?.overview || {}
      } catch (error) {
        console.error('Error fetching report stats:', error)
        // Fallback data
        reportStats.value = {
          total_assets: 70,
          working: 50,
          under_repair: 10,
          defective: 10
        }
      }
    }

    const fetchRecentActivities = async () => {
      try {
        const response = await axios.get('http://localhost:8000/api/reports/dashboard-stats')
        recentActivities.value = response.data.data?.recent_activities || []
      } catch (error) {
        console.error('Error fetching recent activities:', error)
        // Fallback data
        recentActivities.value = [
          { id: 1, type: 'deployment', description: 'PC001 deployed to Computer Science', timestamp: '2024-01-15 10:30 AM' },
          { id: 2, type: 'repair', description: 'PC002 sent for repair', timestamp: '2024-01-15 09:15 AM' },
          { id: 3, type: 'transfer', description: 'PC003 transferred to Library', timestamp: '2024-01-14 03:45 PM' }
        ]
      }
    }

    const loadReportData = async () => {
      await fetchReportData()
      await nextTick()
      createChart()
    }

    const createChart = () => {
      if (!reportChart.value) return

      // Destroy existing chart if it exists
      if (chartInstance) {
        chartInstance.destroy()
      }

      const ctx = reportChart.value.getContext('2d')
      
      // Simple chart implementation (in production, you'd use Chart.js)
      const chartData = {
        labels: reportData.value.map(item => item.name || item.department || item.component),
        datasets: [{
          label: getReportTitle(),
          data: reportData.value.map(item => item.count),
          backgroundColor: [
            '#0F6F43',
            '#004D7A',
            '#FF6B35',
            '#8B5CF6',
            '#10B981',
            '#F59E0B'
          ],
          borderWidth: 1
        }]
      }

      // This is a placeholder - in production you'd use Chart.js
      console.log('Chart data:', chartData)
    }

    const refreshData = async () => {
      await Promise.all([
        fetchReportData(),
        fetchReportStats(),
        fetchRecentActivities()
      ])
      createChart()
    }

    const generateReport = async () => {
      try {
        const response = await axios.post('http://localhost:8000/api/reports/export', {
          type: selectedReportType.value,
          date_range: dateRange.value,
          format: exportFormat.value
        })
        
        if (response.data.success) {
          alert('Report generated successfully!')
        }
      } catch (error) {
        console.error('Error generating report:', error)
        alert('Error generating report: ' + (error.response?.data?.message || error.message))
      }
    }

    const exportReport = () => {
      generateReport()
    }

    onMounted(() => {
      refreshData()
      
      // Set quick stats
      quickStats.value = {
        deployments: 45,
        repairs: 12,
        transfers: 8,
        returns: 15
      }
    })

    return {
      isNavCollapsed,
      selectedReportType,
      dateRange,
      exportFormat,
      reportData,
      reportStats,
      recentActivities,
      quickStats,
      reportChart,
      toggleNav,
      openProfile,
      openSettings,
      getReportTitle,
      getTableHeader,
      getActivityIcon,
      getActivityIconClass,
      calculatePercentage,
      refreshData,
      loadReportData,
      generateReport,
      exportReport
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

.chart-container {
  position: relative;
  height: 400px;
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
