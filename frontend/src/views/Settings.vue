<template>
  <div class="dashboard-layout">
    <!-- Navigation Sidebar -->
    <AppNav :is-collapsed="isNavCollapsed" />
    
    <!-- Main Content Area -->
    <div class="main-content" :class="{ 'collapsed': isNavCollapsed }">
      <!-- Header -->
      <AppHeader 
        :is-collapsed="isNavCollapsed"
        @sidebar-toggle="(collapsed) => isNavCollapsed = collapsed"
        @profile-open="openProfile"
        @settings-open="openSettings"
      />
      
      <!-- Settings Content -->
      <div class="container-fluid p-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h1 class="h3 mb-0">System Settings</h1>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" @click="resetSettings">
              <i class="bi bi-arrow-clockwise me-2"></i>Reset to Default
            </button>
            <button class="btn btn-success" @click="saveSettings">
              <i class="bi bi-check-circle me-2"></i>Save Changes
            </button>
          </div>
        </div>

        <!-- Settings Tabs -->
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white border-bottom">
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#general" type="button">
                  <i class="bi bi-gear me-2"></i>General
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#notifications" type="button">
                  <i class="bi bi-bell me-2"></i>Notifications
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#security" type="button">
                  <i class="bi bi-shield-check me-2"></i>Security
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#backup" type="button">
                  <i class="bi bi-cloud-download me-2"></i>Backup
                </button>
              </li>
            </ul>
          </div>
          <div class="card-body">
            <div class="tab-content">
              <!-- General Settings -->
              <div class="tab-pane fade show active" id="general">
                <div class="row g-4">
                  <div class="col-md-6">
                    <h5 class="mb-3">System Information</h5>
                    <div class="mb-3">
                      <label class="form-label">System Name</label>
                      <input type="text" class="form-control" v-model="settings.systemName">
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Organization</label>
                      <input type="text" class="form-control" v-model="settings.organization">
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Admin Email</label>
                      <input type="email" class="form-control" v-model="settings.adminEmail">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <h5 class="mb-3">Display Settings</h5>
                    <div class="mb-3">
                      <label class="form-label">Default Theme</label>
                      <select class="form-select" v-model="settings.defaultTheme">
                        <option value="light">Light</option>
                        <option value="auto">Auto</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Items per Page</label>
                      <select class="form-select" v-model="settings.itemsPerPage">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Date Format</label>
                      <select class="form-select" v-model="settings.dateFormat">
                        <option value="MM/DD/YYYY">MM/DD/YYYY</option>
                        <option value="DD/MM/YYYY">DD/MM/YYYY</option>
                        <option value="YYYY-MM-DD">YYYY-MM-DD</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Notifications Settings -->
              <div class="tab-pane fade" id="notifications">
                <div class="row g-4">
                  <div class="col-md-6">
                    <h5 class="mb-3">Email Notifications</h5>
                    <div class="form-check form-switch mb-3">
                      <input class="form-check-input" type="checkbox" v-model="settings.emailNotifications" id="emailNotif">
                      <label class="form-check-label" for="emailNotif">
                        Enable Email Notifications
                      </label>
                    </div>
                    <div class="form-check form-switch mb-3">
                      <input class="form-check-input" type="checkbox" v-model="settings.lowStockAlerts" id="lowStock">
                      <label class="form-check-label" for="lowStock">
                        Low Stock Alerts
                      </label>
                    </div>
                    <div class="form-check form-switch mb-3">
                      <input class="form-check-input" type="checkbox" v-model="settings.maintenanceAlerts" id="maintenance">
                      <label class="form-check-label" for="maintenance">
                        Maintenance Alerts
                      </label>
                    </div>
                    <div class="form-check form-switch mb-3">
                      <input class="form-check-input" type="checkbox" v-model="settings.deploymentAlerts" id="deployment">
                      <label class="form-check-label" for="deployment">
                        Deployment Alerts
                      </label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <h5 class="mb-3">System Notifications</h5>
                    <div class="form-check form-switch mb-3">
                      <input class="form-check-input" type="checkbox" v-model="settings.systemAlerts" id="systemAlerts">
                      <label class="form-check-label" for="systemAlerts">
                        System Alerts
                      </label>
                    </div>
                    <div class="form-check form-switch mb-3">
                      <input class="form-check-input" type="checkbox" v-model="settings.userActivity" id="userActivity">
                      <label class="form-check-label" for="userActivity">
                        User Activity Tracking
                      </label>
                    </div>
                    <div class="form-check form-switch mb-3">
                      <input class="form-check-input" type="checkbox" v-model="settings.securityAlerts" id="securityAlerts">
                      <label class="form-check-label" for="securityAlerts">
                        Security Alerts
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Security Settings -->
              <div class="tab-pane fade" id="security">
                <div class="row g-4">
                  <div class="col-md-6">
                    <h5 class="mb-3">Password Policy</h5>
                    <div class="mb-3">
                      <label class="form-label">Minimum Password Length</label>
                      <input type="number" class="form-control" v-model="settings.minPasswordLength" min="6" max="20">
                    </div>
                    <div class="form-check form-switch mb-3">
                      <input class="form-check-input" type="checkbox" v-model="settings.requireUppercase" id="requireUpper">
                      <label class="form-check-label" for="requireUpper">
                        Require Uppercase Letters
                      </label>
                    </div>
                    <div class="form-check form-switch mb-3">
                      <input class="form-check-input" type="checkbox" v-model="settings.requireNumbers" id="requireNumbers">
                      <label class="form-check-label" for="requireNumbers">
                        Require Numbers
                      </label>
                    </div>
                    <div class="form-check form-switch mb-3">
                      <input class="form-check-input" type="checkbox" v-model="settings.requireSpecialChars" id="requireSpecial">
                      <label class="form-check-label" for="requireSpecial">
                        Require Special Characters
                      </label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <h5 class="mb-3">Session Settings</h5>
                    <div class="mb-3">
                      <label class="form-label">Session Timeout (minutes)</label>
                      <input type="number" class="form-control" v-model="settings.sessionTimeout" min="5" max="480">
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Max Login Attempts</label>
                      <input type="number" class="form-control" v-model="settings.maxLoginAttempts" min="3" max="10">
                    </div>
                    <div class="form-check form-switch mb-3">
                      <input class="form-check-input" type="checkbox" v-model="settings.twoFactorAuth" id="twoFactor">
                      <label class="form-check-label" for="twoFactor">
                        Enable Two-Factor Authentication
                      </label>
                    </div>
                    <div class="form-check form-switch mb-3">
                      <input class="form-check-input" type="checkbox" v-model="settings.ipWhitelist" id="ipWhitelist">
                      <label class="form-check-label" for="ipWhitelist">
                        Enable IP Whitelist
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Backup Settings -->
              <div class="tab-pane fade" id="backup">
                <div class="row g-4">
                  <div class="col-md-6">
                    <h5 class="mb-3">Automatic Backup</h5>
                    <div class="form-check form-switch mb-3">
                      <input class="form-check-input" type="checkbox" v-model="settings.autoBackup" id="autoBackup">
                      <label class="form-check-label" for="autoBackup">
                        Enable Automatic Backup
                      </label>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Backup Frequency</label>
                      <select class="form-select" v-model="settings.backupFrequency">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Backup Time</label>
                      <input type="time" class="form-control" v-model="settings.backupTime">
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Retention Period (days)</label>
                      <input type="number" class="form-control" v-model="settings.retentionPeriod" min="7" max="365">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <h5 class="mb-3">Backup Actions</h5>
                    <div class="d-grid gap-2">
                      <button class="btn btn-primary" @click="createBackup">
                        <i class="bi bi-cloud-download me-2"></i>Create Backup Now
                      </button>
                      <button class="btn btn-outline-primary" @click="restoreBackup">
                        <i class="bi bi-cloud-upload me-2"></i>Restore from Backup
                      </button>
                      <button class="btn btn-outline-info" @click="downloadBackup">
                        <i class="bi bi-download me-2"></i>Download Latest Backup
                      </button>
                      <button class="btn btn-outline-warning" @click="scheduleBackup">
                        <i class="bi bi-calendar-check me-2"></i>Schedule Backup
                      </button>
                    </div>
                    <div class="mt-3">
                      <small class="text-muted">Last backup: {{ settings.lastBackup || 'Never' }}</small>
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
import { ref, onMounted } from 'vue'
import AppNav from '@/components/AppNav.vue'
import AppHeader from '@/components/AppHeader.vue'

export default {
  name: 'Settings',
  components: {
    AppNav,
    AppHeader
  },
  setup() {
    const isNavCollapsed = ref(false)
    
    const settings = ref({
      // General Settings
      systemName: 'MiSD Inventory Management System',
      organization: 'PNC',
      adminEmail: 'admin@pnc.edu',
      defaultTheme: 'light',
      itemsPerPage: '25',
      dateFormat: 'MM/DD/YYYY',
      
      // Notification Settings
      emailNotifications: true,
      lowStockAlerts: true,
      maintenanceAlerts: true,
      deploymentAlerts: true,
      systemAlerts: true,
      userActivity: true,
      securityAlerts: true,
      
      // Security Settings
      minPasswordLength: 8,
      requireUppercase: true,
      requireNumbers: true,
      requireSpecialChars: false,
      sessionTimeout: 60,
      maxLoginAttempts: 5,
      twoFactorAuth: false,
      ipWhitelist: false,
      
      // Backup Settings
      autoBackup: true,
      backupFrequency: 'daily',
      backupTime: '02:00',
      retentionPeriod: 30,
      lastBackup: '2024-01-15 02:00 AM'
    })

    const defaultSettings = ref({ ...settings.value })

    const toggleNav = () => {
      isNavCollapsed.value = !isNavCollapsed.value
    }

    const openProfile = () => {
      console.log('Profile clicked')
    }

    const openSettings = () => {
      console.log('Settings clicked')
    }

    const saveSettings = async () => {
      try {
        // In a real application, this would save to the backend
        localStorage.setItem('systemSettings', JSON.stringify(settings.value))
        alert('Settings saved successfully!')
      } catch (error) {
        console.error('Error saving settings:', error)
        alert('Error saving settings')
      }
    }

    const resetSettings = () => {
      if (confirm('Are you sure you want to reset all settings to default values?')) {
        settings.value = { ...defaultSettings.value }
        alert('Settings reset to default values')
      }
    }

    const createBackup = () => {
      alert('Backup creation started. This may take a few minutes...')
    }

    const restoreBackup = () => {
      alert('Restore feature would open a file selector to choose backup file')
    }

    const downloadBackup = () => {
      alert('Downloading latest backup file...')
    }

    const scheduleBackup = () => {
      alert('Backup scheduled successfully')
    }

    onMounted(() => {
      // Load settings from localStorage if available
      const savedSettings = localStorage.getItem('systemSettings')
      if (savedSettings) {
        try {
          settings.value = { ...settings.value, ...JSON.parse(savedSettings) }
        } catch (error) {
          console.error('Error loading settings:', error)
        }
      }
    })

    return {
      isNavCollapsed,
      settings,
      toggleNav,
      openProfile,
      openSettings,
      saveSettings,
      resetSettings,
      createBackup,
      restoreBackup,
      downloadBackup,
      scheduleBackup
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
