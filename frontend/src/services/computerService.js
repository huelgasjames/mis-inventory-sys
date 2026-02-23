import axios from 'axios'

const API_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api'

class ComputerService {
  // Get all computers
  async getAllComputers() {
    try {
      const response = await axios.get(`${API_URL}/computers`)
      return response.data
    } catch (error) {
      console.error('Error fetching computers:', error)
      throw error
    }
  }

  // Get computer by ID
  async getComputerById(id) {
    try {
      const response = await axios.get(`${API_URL}/computers/${id}`)
      return response.data
    } catch (error) {
      console.error('Error fetching computer:', error)
      throw error
    }
  }

  // Create new computer
  async createComputer(computerData) {
    try {
      const response = await axios.post(`${API_URL}/computers`, computerData)
      return response.data
    } catch (error) {
      console.error('Error creating computer:', error)
      throw error
    }
  }

  // Update computer
  async updateComputer(id, computerData) {
    try {
      const response = await axios.put(`${API_URL}/computers/${id}`, computerData)
      return response.data
    } catch (error) {
      console.error('Error updating computer:', error)
      throw error
    }
  }

  // Delete computer
  async deleteComputer(id) {
    try {
      const response = await axios.delete(`${API_URL}/computers/${id}`)
      return response.data
    } catch (error) {
      console.error('Error deleting computer:', error)
      throw error
    }
  }

  // Get computer statistics
  async getComputerStats() {
    try {
      const response = await axios.get(`${API_URL}/computers/stats`)
      return response.data
    } catch (error) {
      console.error('Error fetching computer stats:', error)
      throw error
    }
  }

  // Search computers
  async searchComputers(query) {
    try {
      const response = await axios.get(`${API_URL}/computers/search?q=${query}`)
      return response.data
    } catch (error) {
      console.error('Error searching computers:', error)
      throw error
    }
  }

  // Export computers
  async exportComputers(format = 'csv') {
    try {
      const response = await axios.get(`${API_URL}/computers/export?format=${format}`, {
        responseType: 'blob'
      })
      return response.data
    } catch (error) {
      console.error('Error exporting computers:', error)
      throw error
    }
  }
}

export default new ComputerService()
