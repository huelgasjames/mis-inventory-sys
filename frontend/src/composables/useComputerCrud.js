import { ref, computed } from 'vue'
import computerService from '@/services/computerService'

export function useComputerCrud() {
  // State
  const computers = ref([])
  const loading = ref(false)
  const error = ref(null)
  const selectedComputer = ref(null)

  // Form state
  const computerForm = ref({
    computer_name: '',
    asset_tag: '',
    pc_number: '',
    serial_number: '',
    department_id: '',
    processor_id: '',
    motherboard_id: '',
    ram_id: '',
    storage_id: '',
    video_card_id: '',
    psu_id: '',
    status: 'Working',
    assigned_to: '',
    location: '',
    description: ''
  })

  // Computed properties
  const totalComputers = computed(() => computers.value.length)
  const workingComputers = computed(() => computers.value.filter(c => c.status === 'Working').length)
  const defectiveComputers = computed(() => computers.value.filter(c => c.status === 'Defective').length)
  const disposalComputers = computed(() => computers.value.filter(c => c.status === 'For Disposal').length)

  // CRUD Operations
  const fetchComputers = async () => {
    loading.value = true
    error.value = null
    try {
      const response = await computerService.getAllComputers()
      computers.value = response.data || []
      
      // Also fetch related data
      await Promise.all([
        fetchDepartments(),
        fetchComponents(),
        fetchUsers()
      ])
    } catch (err) {
      error.value = err.message || 'Failed to fetch computers'
      console.error('Fetch computers error:', err)
      // Use fallback data
      useFallbackData()
    } finally {
      loading.value = false
    }
  }

  const fetchDepartments = async () => {
    try {
      const response = await axios.get('http://localhost:8000/api/departments')
      departments.value = response.data.data || []
    } catch (err) {
      console.error('Error fetching departments:', err)
      departments.value = [
        { id: 1, name: 'Computer Science', category: 'Academic' },
        { id: 2, name: 'Engineering', category: 'Academic' },
        { id: 3, name: 'Business Administration', category: 'Administrative' }
      ]
    }
  }

  const fetchComponents = async () => {
    try {
      const response = await computerService.getAllComponents()
      components.value = response.data || {}
    } catch (err) {
      console.error('Error fetching components:', err)
      components.value = {
        processors: [
          { id: 1, model: 'Intel Core i5-10400', specs: '2.9GHz, 6 cores' },
          { id: 2, model: 'Intel Core i3-10100', specs: '3.2GHz, 4 cores' }
        ],
        motherboards: [
          { id: 1, model: 'ASUS Prime H410M', chipset: 'Intel H410' },
          { id: 2, model: 'Gigabyte H410M', chipset: 'Intel H410' }
        ],
        rams: [
          { id: 1, capacity: '16GB DDR4', type: 'DDR4', speed: '2666MHz' },
          { id: 2, capacity: '8GB DDR4', type: 'DDR4', speed: '2666MHz' }
        ],
        storages: [
          { id: 1, capacity: '512GB SSD', type: 'SSD', interface: 'SATA' },
          { id: 2, capacity: '256GB SSD', type: 'SSD', interface: 'SATA' }
        ],
        video_cards: [
          { id: 1, model: 'NVIDIA GTX 1650', memory: '4GB GDDR6' },
          { id: 2, model: 'Integrated', memory: 'Shared' }
        ],
        psus: [
          { id: 1, wattage: 450, model: 'Corsair CV450' },
          { id: 2, wattage: 550, model: 'Cooler Master MWE 550' },
          { id: 3, wattage: 650, model: 'EVGA 650 WQ' }
        ]
      }
    }
  }

  const fetchUsers = async () => {
    try {
      const response = await axios.get('http://localhost:8000/api/users')
      users.value = response.data.data || []
    } catch (err) {
      console.error('Error fetching users:', err)
      users.value = [
        { id: 1, name: 'John Doe', email: 'john@example.com' },
        { id: 2, name: 'Jane Smith', email: 'jane@example.com' },
        { id: 3, name: 'Mike Johnson', email: 'mike@example.com' }
      ]
    }
  }

  const useFallbackData = () => {
    // Set fallback data if API fails
    departments.value = [
      { id: 1, name: 'Computer Science', category: 'Academic' },
      { id: 2, name: 'Engineering', category: 'Academic' },
      { id: 3, name: 'Business Administration', category: 'Administrative' }
    ]
    
    components.value = {
      processors: [
        { id: 1, model: 'Intel Core i5-10400', specs: '2.9GHz, 6 cores' },
        { id: 2, model: 'Intel Core i3-10100', specs: '3.2GHz, 4 cores' }
      ],
      motherboards: [
        { id: 1, model: 'ASUS Prime H410M', chipset: 'Intel H410' },
        { id: 2, model: 'Gigabyte H410M', chipset: 'Intel H410' }
      ],
      rams: [
        { id: 1, capacity: '16GB DDR4', type: 'DDR4', speed: '2666MHz' },
        { id: 2, capacity: '8GB DDR4', type: 'DDR4', speed: '2666MHz' }
      ],
      storages: [
        { id: 1, capacity: '512GB SSD', type: 'SSD', interface: 'SATA' },
        { id: 2, capacity: '256GB SSD', type: 'SSD', interface: 'SATA' }
      ],
      video_cards: [
        { id: 1, model: 'NVIDIA GTX 1650', memory: '4GB GDDR6' },
        { id: 2, model: 'Integrated', memory: 'Shared' }
      ],
      psus: [
        { id: 1, wattage: 450, model: 'Corsair CV450' },
        { id: 2, wattage: 550, model: 'Cooler Master MWE 550' },
        { id: 3, wattage: 650, model: 'EVGA 650 WQ' }
      ]
    }
    
    users.value = [
      { id: 1, name: 'John Doe', email: 'john@example.com' },
      { id: 2, name: 'Jane Smith', email: 'jane@example.com' },
      { id: 3, name: 'Mike Johnson', email: 'mike@example.com' }
    ]
  }

  const createComputer = async (computerData) => {
    loading.value = true
    error.value = null
    try {
      const response = await computerService.createComputer(computerData)
      computers.value.push(response.data)
      resetForm()
      return response.data
    } catch (err) {
      error.value = err.message || 'Failed to create computer'
      console.error('Create computer error:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateComputer = async (id, computerData) => {
    loading.value = true
    error.value = null
    try {
      const response = await computerService.updateComputer(id, computerData)
      const index = computers.value.findIndex(c => c.id === id)
      if (index !== -1) {
        computers.value[index] = response.data
      }
      return response.data
    } catch (err) {
      error.value = err.message || 'Failed to update computer'
      console.error('Update computer error:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteComputer = async (id) => {
    loading.value = true
    error.value = null
    try {
      await computerService.deleteComputer(id)
      computers.value = computers.value.filter(c => c.id !== id)
      return true
    } catch (err) {
      error.value = err.message || 'Failed to delete computer'
      console.error('Delete computer error:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const getComputerById = async (id) => {
    loading.value = true
    error.value = null
    try {
      const response = await computerService.getComputerById(id)
      selectedComputer.value = response.data
      return response.data
    } catch (err) {
      error.value = err.message || 'Failed to fetch computer'
      console.error('Get computer error:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const searchComputers = async (query) => {
    loading.value = true
    error.value = null
    try {
      const response = await computerService.searchComputers(query)
      computers.value = response.data || []
    } catch (err) {
      error.value = err.message || 'Failed to search computers'
      console.error('Search computers error:', err)
    } finally {
      loading.value = false
    }
  }

  const exportComputers = async (format = 'csv') => {
    loading.value = true
    error.value = null
    try {
      const blob = await computerService.exportComputers(format)
      const url = window.URL.createObjectURL(blob)
      const link = document.createElement('a')
      link.href = url
      link.download = `computers.${format}`
      link.click()
      window.URL.revokeObjectURL(url)
    } catch (err) {
      error.value = err.message || 'Failed to export computers'
      console.error('Export computers error:', err)
    } finally {
      loading.value = false
    }
  }

  // Form utilities
  const resetForm = () => {
    computerForm.value = {
      computer_name: '',
      asset_tag: '',
      pc_number: '',
      serial_number: '',
      department_id: '',
      processor_id: '',
      motherboard_id: '',
      ram_id: '',
      storage_id: '',
      video_card_id: '',
      psu_id: '',
      status: 'Working',
      assigned_to: '',
      location: '',
      description: ''
    }
  }

  const setFormData = (computer) => {
    computerForm.value = { ...computer }
  }

  const generateAssetTag = () => {
    const year = new Date().getFullYear()
    const random = Math.floor(Math.random() * 10000).toString().padStart(4, '0')
    computerForm.value.asset_tag = `PNC-${year}-${random}`
  }

  // Validation
  const validateComputerForm = () => {
    const required = ['computer_name', 'asset_tag', 'pc_number', 'department_id']
    const missing = required.filter(field => !computerForm.value[field])
    
    if (missing.length > 0) {
      error.value = `Required fields missing: ${missing.join(', ')}`
      return false
    }
    
    return true
  }

  return {
    // State
    computers,
    loading,
    error,
    selectedComputer,
    computerForm,
    
    // Computed
    totalComputers,
    workingComputers,
    defectiveComputers,
    disposalComputers,
    
    // CRUD Operations
    fetchComputers,
    createComputer,
    updateComputer,
    deleteComputer,
    getComputerById,
    searchComputers,
    exportComputers,
    
    // Form utilities
    resetForm,
    setFormData,
    generateAssetTag,
    validateComputerForm
  }
}
