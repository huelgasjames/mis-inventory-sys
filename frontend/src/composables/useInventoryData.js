import { ref, computed } from 'vue'
import axios from 'axios'

const API_BASE_URL = 'http://localhost:8000/api'

// Global state for cached data
const globalState = ref({
  departments: [],
  categories: [],
  users: [],
  assets: [],
  computers: [],
  laboratories: [],
  components: {},
  dashboardStats: {},
  componentStats: {},
  lastFetch: null
})

// Loading state
const isLoading = ref(false)
const loadingStartTime = ref(null)

export function useInventoryData() {
  
  // Helper function to ensure minimum loading duration
  const ensureMinimumLoading = async (minDuration = 1000) => {
    if (loadingStartTime.value) {
      const elapsed = Date.now() - loadingStartTime.value
      if (elapsed < minDuration) {
        await new Promise(resolve => setTimeout(resolve, minDuration - elapsed))
      }
    }
  }

  // Generic API call function
  const apiCall = async (endpoint, options = {}) => {
    try {
      const response = await axios({
        url: `${API_BASE_URL}${endpoint}`,
        ...options
      })
      return response.data
    } catch (error) {
      console.error(`API call failed for ${endpoint}:`, error)
      throw error
    }
  }

  // Fetch all departments
  const fetchDepartments = async (forceRefresh = false) => {
    if (!forceRefresh && globalState.value.departments.length > 0) {
      return globalState.value.departments
    }

    try {
      const response = await apiCall('/departments')
      if (response.success) {
        globalState.value.departments = response.data || []
        console.log('Departments fetched:', globalState.value.departments.length)
        return globalState.value.departments
      }
    } catch (error) {
      console.error('Error fetching departments:', error)
      globalState.value.departments = []
    }
    return []
  }

  // Fetch all department categories
  const fetchCategories = async (forceRefresh = false) => {
    if (!forceRefresh && globalState.value.categories.length > 0) {
      return globalState.value.categories
    }

    try {
      const response = await apiCall('/department-categories')
      if (response.success) {
        globalState.value.categories = response.data || []
        console.log('Categories fetched:', globalState.value.categories.length)
        return globalState.value.categories
      }
    } catch (error) {
      console.error('Error fetching categories:', error)
      globalState.value.categories = []
    }
    return []
  }

  // Fetch all users
  const fetchUsers = async (forceRefresh = false) => {
    if (!forceRefresh && globalState.value.users.length > 0) {
      return globalState.value.users
    }

    try {
      const response = await apiCall('/users')
      if (response.success) {
        globalState.value.users = response.data || []
        console.log('Users fetched:', globalState.value.users.length)
        return globalState.value.users
      }
    } catch (error) {
      console.error('Error fetching users:', error)
      globalState.value.users = []
    }
    return []
  }

  // Fetch all assets
  const fetchAssets = async (forceRefresh = false) => {
    if (!forceRefresh && globalState.value.assets.length > 0) {
      return globalState.value.assets
    }

    try {
      const response = await apiCall('/assets')
      if (response.success) {
        globalState.value.assets = response.data || []
        console.log('Assets fetched:', globalState.value.assets.length)
        return globalState.value.assets
      }
    } catch (error) {
      console.error('Error fetching assets:', error)
      globalState.value.assets = []
    }
    return []
  }

  // Fetch all computers
  const fetchComputers = async (forceRefresh = false) => {
    if (!forceRefresh && globalState.value.computers.length > 0) {
      return globalState.value.computers
    }

    try {
      const response = await apiCall('/computers')
      if (response.success) {
        globalState.value.computers = response.data || []
        console.log('Computers fetched:', globalState.value.computers.length)
        return globalState.value.computers
      }
    } catch (error) {
      console.error('Error fetching computers:', error)
      globalState.value.computers = []
    }
    return []
  }

  // Fetch components
  const fetchComponents = async (forceRefresh = false) => {
    if (!forceRefresh && Object.keys(globalState.value.components).length > 0) {
      return globalState.value.components
    }

    try {
      const response = await apiCall('/components')
      if (response.success) {
        globalState.value.components = response.data || {}
        console.log('Components fetched:', globalState.value.components)
        return globalState.value.components
      }
    } catch (error) {
      console.error('Error fetching components:', error)
      globalState.value.components = {}
    }
    return {}
  }

  // Fetch dashboard statistics
  const fetchDashboardStats = async (forceRefresh = false) => {
    if (!forceRefresh && Object.keys(globalState.value.dashboardStats).length > 0) {
      return globalState.value.dashboardStats
    }

    try {
      const response = await apiCall('/reports/dashboard-stats')
      if (response.success) {
        globalState.value.dashboardStats = response.data || {}
        console.log('Dashboard stats fetched:', globalState.value.dashboardStats)
        return globalState.value.dashboardStats
      }
    } catch (error) {
      console.error('Error fetching dashboard stats:', error)
      globalState.value.dashboardStats = {
        overview: { total_assets: 0, total_computers: 0, total_departments: 0, total_laboratories: 0 },
        status_breakdown: { available: 0, deployed: 0, under_repair: 0, defective: 0 },
        recent_activities: [],
        monthly_repairs: [],
        component_health: []
      }
    }
    return globalState.value.dashboardStats
  }

  // Calculate component statistics
  const calculateComponentStats = (componentsData) => {
    if (!componentsData || Object.keys(componentsData).length === 0) {
      return {
        available_components: {
          processors: 0,
          motherboards: 0,
          rams: 0,
          storages: 0,
          video_cards: 0,
          psus: 0,
          dvd_roms: 0
        },
        total_components: {
          processors: 0,
          motherboards: 0,
          rams: 0,
          storages: 0,
          video_cards: 0,
          psus: 0,
          dvd_roms: 0
        }
      }
    }

    const stats = {
      available_components: {},
      total_components: {}
    }

    const componentTypes = ['processors', 'motherboards', 'rams', 'storages', 'video_cards', 'psus', 'dvd_roms']
    
    componentTypes.forEach(type => {
      const components = componentsData[type] || []
      stats.available_components[type] = components.filter(c => c.status === 'Available').length
      stats.total_components[type] = components.length
    })

    return stats
  }

  // Fetch component statistics
  const fetchComponentStats = async (forceRefresh = false) => {
    const components = await fetchComponents(forceRefresh)
    const stats = calculateComponentStats(components)
    globalState.value.componentStats = stats
    console.log('Component stats calculated:', stats)
    return stats
  }

  // Master refresh function - fetches all data from backend
  const refreshAllData = async (options = {}) => {
    const { showLoading = true, minLoadingDuration = 1000 } = options
    
    if (showLoading && !isLoading.value) {
      isLoading.value = true
      loadingStartTime.value = Date.now()
    }

    try {
      console.log('Starting full data refresh from backend...')
      
      // Fetch all data in parallel for better performance
      await Promise.all([
        fetchDepartments(true),
        fetchCategories(true),
        fetchUsers(true),
        fetchAssets(true),
        fetchComputers(true),
        fetchLaboratories(true),
        fetchComponents(true),
        fetchDashboardStats(true),
        fetchComponentStats(true)
      ])

      globalState.value.lastFetch = new Date().toISOString()
      console.log('All data refreshed successfully from backend')
      
    } catch (error) {
      console.error('Error during full data refresh:', error)
      throw error
    } finally {
      if (showLoading) {
        await ensureMinimumLoading(minLoadingDuration)
        isLoading.value = false
        loadingStartTime.value = null
      }
    }
  }

  // Computed properties for easy access
  const departments = computed(() => globalState.value.departments)
  const categories = computed(() => globalState.value.categories)
  const users = computed(() => globalState.value.users)
  const assets = computed(() => globalState.value.assets)
  const computers = computed(() => globalState.value.computers)
  const laboratories = computed(() => globalState.value.laboratories)
  const components = computed(() => globalState.value.components)
  const dashboardStats = computed(() => globalState.value.dashboardStats)
  const componentStats = computed(() => globalState.value.componentStats)
  const lastFetch = computed(() => globalState.value.lastFetch)

  // CRUD operations
  const createDepartment = async (departmentData) => {
    try {
      const response = await apiCall('/departments', {
        method: 'POST',
        data: departmentData
      })
      
      if (response.success) {
        // Refresh departments data after creation
        await fetchDepartments(true)
        return response
      }
      throw new Error(response.message || 'Failed to create department')
    } catch (error) {
      console.error('Error creating department:', error)
      throw error
    }
  }

  const updateDepartment = async (id, departmentData) => {
    try {
      const response = await apiCall(`/departments/${id}`, {
        method: 'PUT',
        data: departmentData
      })
      
      if (response.success) {
        // Refresh departments data after update
        await fetchDepartments(true)
        return response
      }
      throw new Error(response.message || 'Failed to update department')
    } catch (error) {
      console.error('Error updating department:', error)
      throw error
    }
  }

  const deleteDepartment = async (id) => {
    try {
      const response = await apiCall(`/departments/${id}`, {
        method: 'DELETE'
      })
      
      if (response.success) {
        // Refresh departments data after deletion
        await fetchDepartments(true)
        return response
      }
      throw new Error(response.message || 'Failed to delete department')
    } catch (error) {
      console.error('Error deleting department:', error)
      throw error
    }
  }

  const createComputer = async (computerData) => {
    try {
      const response = await apiCall('/computers/create', {
        method: 'POST',
        data: computerData
      })
      
      if (response.success) {
        // Refresh relevant data after creation
        await Promise.all([
          fetchComputers(true),
          fetchDashboardStats(true)
        ])
        return response
      }
      throw new Error(response.message || 'Failed to create computer')
    } catch (error) {
      console.error('Error creating computer:', error)
      throw error
    }
  }

  const updateComputer = async (id, computerData) => {
    try {
      const response = await apiCall(`/computers/${id}`, {
        method: 'PUT',
        data: computerData
      })
      
      if (response.success) {
        // Refresh relevant data after update
        await Promise.all([
          fetchComputers(true),
          fetchDashboardStats(true)
        ])
        return response
      }
      throw new Error(response.message || 'Failed to update computer')
    } catch (error) {
      console.error('Error updating computer:', error)
      throw error
    }
  }

  const deleteComputer = async (id) => {
    try {
      const response = await apiCall(`/computers/${id}/delete`, {
        method: 'DELETE'
      })
      
      if (response.success) {
        // Refresh relevant data after deletion
        await Promise.all([
          fetchComputers(true),
          fetchDashboardStats(true)
        ])
        return response
      }
      throw new Error(response.message || 'Failed to delete computer')
    } catch (error) {
      console.error('Error deleting computer:', error)
      throw error
    }
  }

  const fetchLaboratories = async (forceRefresh = false) => {
    if (!forceRefresh && globalState.value.laboratories && globalState.value.laboratories.length > 0) {
      return globalState.value.laboratories
    }

    try {
      const response = await apiCall('/laboratories')
      if (response.success) {
        globalState.value.laboratories = response.data || []
        console.log('Laboratories fetched:', globalState.value.laboratories.length)
        return globalState.value.laboratories
      }
    } catch (error) {
      console.error('Error fetching laboratories:', error)
      globalState.value.laboratories = []
    }
    return []
  }

  return {
    // State
    isLoading,
    lastFetch,
    
    // Data
    departments,
    categories,
    users,
    assets,
    computers,
    laboratories,
    components,
    dashboardStats,
    componentStats,
    
    // Fetch functions
    fetchDepartments,
    fetchCategories,
    fetchUsers,
    fetchAssets,
    fetchComputers,
    fetchLaboratories,
    fetchComponents,
    fetchDashboardStats,
    fetchComponentStats,
    
    // Master refresh
    refreshAllData,
    
    // CRUD operations
    createDepartment,
    updateDepartment,
    deleteDepartment,
    createComputer,
    updateComputer,
    deleteComputer
  }
}
