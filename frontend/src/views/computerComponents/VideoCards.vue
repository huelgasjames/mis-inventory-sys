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
      
      <!-- Video Cards Content -->
      <div class="container-fluid p-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h1 class="h3 mb-0">Video Cards Management</h1>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" @click="refreshData">
              <i class="bi bi-arrow-clockwise me-2"></i>Refresh
            </button>
            <button class="btn btn-primary" @click="showCreateVideoCardModal">
              <i class="bi bi-plus-circle me-2"></i>Add Video Card
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
                    <i class="bi bi-gpu-card text-primary fs-4"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Total Video Cards</h6>
                    <h3 class="mb-0">{{ videoCards.length }}</h3>
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
                    <h3 class="mb-0">{{ availableCount }}</h3>
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
                    <h6 class="text-muted mb-1">In Use</h6>
                    <h3 class="mb-0">{{ inUseCount }}</h3>
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
                    <h3 class="mb-0">{{ defectiveCount }}</h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Video Cards Table -->
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0">Video Cards Inventory</h5>
              <div class="d-flex gap-2">
                <select class="form-select form-select-sm" v-model="statusFilter" @change="filterVideoCards">
                  <option value="">All Status</option>
                  <option value="Available">Available</option>
                  <option value="In Use">In Use</option>
                  <option value="Defective">Defective</option>
                </select>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Model</th>
                    <th>Status</th>
                    <th>Created Date</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="videoCard in filteredVideoCards" :key="videoCard.id">
                    <td>{{ videoCard.id }}</td>
                    <td>
                      <div class="d-flex align-items-center">
                        <i class="bi bi-gpu-card me-2 text-primary"></i>
                        <span class="fw-semibold">{{ videoCard.model }}</span>
                      </div>
                    </td>
                    <td>
                      <span :class="getStatusBadgeClass(videoCard.status)">
                        {{ videoCard.status }}
                      </span>
                    </td>
                    <td>{{ formatDate(videoCard.created_at) }}</td>
                    <td>
                      <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" @click="viewVideoCard(videoCard)">
                          <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-outline-warning" @click="editVideoCard(videoCard)">
                          <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-outline-danger" @click="deleteVideoCard(videoCard)">
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

  <!-- Create Video Card Modal -->
  <div class="modal fade" id="createVideoCardModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Video Card</h5>
          <button type="button" class="btn-close" @click="hideCreateVideoCardModal"></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="createVideoCard">
            <div class="mb-3">
              <label class="form-label">Model *</label>
              <input type="text" class="form-control" v-model="newVideoCard.model" placeholder="e.g., NVIDIA GTX 1650" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Status *</label>
              <select class="form-select" v-model="newVideoCard.status" required>
                <option value="">Select Status</option>
                <option value="Available">Available</option>
                <option value="In Use">In Use</option>
                <option value="Defective">Defective</option>
              </select>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="hideCreateVideoCardModal">Cancel</button>
          <button type="button" class="btn btn-primary" @click="createVideoCard">Add Video Card</button>
        </div>
      </div>
    </div>
  </div>

  <!-- View Video Card Modal -->
  <div class="modal fade" id="viewVideoCardModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Video Card Details</h5>
          <button type="button" class="btn-close" @click="hideViewVideoCardModal"></button>
        </div>
        <div class="modal-body" v-if="selectedVideoCard">
          <div class="mb-3">
            <label class="form-label">ID</label>
            <div class="form-control bg-light">{{ selectedVideoCard.id }}</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Model</label>
            <div class="form-control bg-light">{{ selectedVideoCard.model }}</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <div class="form-control bg-light">
              <span :class="getStatusBadgeClass(selectedVideoCard.status)">
                {{ selectedVideoCard.status }}
              </span>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Created Date</label>
            <div class="form-control bg-light">{{ formatDate(selectedVideoCard.created_at) }}</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="hideViewVideoCardModal">Close</button>
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
  name: 'VideoCards',
  components: {
    AppNav,
    AppHeader
  },
  setup() {
    const isNavCollapsed = ref(false)
    const videoCards = ref([])
    const statusFilter = ref('')
    const selectedVideoCard = ref(null)
    
    const newVideoCard = ref({
      model: 'NVIDIA GTX 1650',
      status: 'Available'
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
        'Available': 'badge bg-success',
        'In Use': 'badge bg-warning',
        'Defective': 'badge bg-danger'
      }
      return classes[status] || 'badge bg-secondary'
    }

    const formatDate = (dateString) => {
      if (!dateString) return '-'
      return new Date(dateString).toLocaleDateString()
    }

    const filteredVideoCards = computed(() => {
      if (!statusFilter.value) return videoCards.value
      return videoCards.value.filter(vc => vc.status === statusFilter.value)
    })

    const availableCount = computed(() => videoCards.value.filter(vc => vc.status === 'Available').length)
    const inUseCount = computed(() => videoCards.value.filter(vc => vc.status === 'In Use').length)
    const defectiveCount = computed(() => videoCards.value.filter(vc => vc.status === 'Defective').length)

    const fetchVideoCards = async () => {
      try {
        const response = await axios.get('http://localhost:8000/api/components')
        videoCards.value = response.data.data.video_cards || []
      } catch (error) {
        console.error('Error fetching video cards:', error)
        // Fallback data for demo
        videoCards.value = [
          { id: 1, model: 'NVIDIA GTX 1650', status: 'Available', created_at: '2024-01-01' },
          { id: 2, model: 'AMD Radeon RX 580', status: 'In Use', created_at: '2024-01-02' },
          { id: 3, model: 'NVIDIA RTX 3060', status: 'Defective', created_at: '2024-01-03' }
        ]
      }
    }

    const refreshData = async () => {
      await fetchVideoCards()
    }

    const showCreateVideoCardModal = () => {
      const modal = document.getElementById('createVideoCardModal')
      if (modal) {
        modal.classList.add('show')
        modal.style.display = 'block'
        document.body.classList.add('modal-open')
        
        // Create backdrop
        const backdrop = document.createElement('div')
        backdrop.className = 'modal-backdrop fade show'
        backdrop.id = 'videocard-modal-backdrop'
        document.body.appendChild(backdrop)
      }
    }

    const createVideoCard = async () => {
      try {
        const response = await axios.post('http://localhost:8000/api/components/video-cards', newVideoCard.value)
        
        if (response.data.success) {
          hideCreateVideoCardModal()
          await refreshData()
          
          // Reset form
          newVideoCard.value = {
            model: 'NVIDIA GTX 1650',
            status: 'Available'
          }
          
          alert('Video Card added successfully!')
        }
      } catch (error) {
        console.error('Error creating Video Card:', error)
        alert('Error adding Video Card: ' + (error.response?.data?.message || error.message))
      }
    }

    const hideCreateVideoCardModal = () => {
      const modal = document.getElementById('createVideoCardModal')
      const backdrop = document.getElementById('videocard-modal-backdrop')
      
      if (modal) {
        modal.classList.remove('show')
        modal.style.display = 'none'
        document.body.classList.remove('modal-open')
      }
      
      if (backdrop) {
        backdrop.remove()
      }
    }

    const viewVideoCard = (videoCard) => {
      selectedVideoCard.value = videoCard
      const modal = document.getElementById('viewVideoCardModal')
      if (modal) {
        modal.classList.add('show')
        modal.style.display = 'block'
        document.body.classList.add('modal-open')
        
        // Create backdrop
        const backdrop = document.createElement('div')
        backdrop.className = 'modal-backdrop fade show'
        backdrop.id = 'view-videocard-modal-backdrop'
        document.body.appendChild(backdrop)
      }
    }

    const hideViewVideoCardModal = () => {
      const modal = document.getElementById('viewVideoCardModal')
      const backdrop = document.getElementById('view-videocard-modal-backdrop')
      
      if (modal) {
        modal.classList.remove('show')
        modal.style.display = 'none'
        document.body.classList.remove('modal-open')
      }
      
      if (backdrop) {
        backdrop.remove()
      }
    }

    const editVideoCard = (videoCard) => {
      // For now, just show the video card details
      viewVideoCard(videoCard)
    }

    const deleteVideoCard = async (videoCard) => {
      if (!confirm('Are you sure you want to delete this video card?')) return
      
      try {
        const response = await axios.delete(`http://localhost:8000/api/components/video-cards/${videoCard.id}`)
        
        if (response.data.success) {
          await refreshData()
          alert('Video card deleted successfully!')
        }
      } catch (error) {
        console.error('Error deleting video card:', error)
        alert('Error deleting video card: ' + (error.response?.data?.message || error.message))
      }
    }

    const filterVideoCards = () => {
      // This is handled by the computed property
    }

    onMounted(() => {
      fetchVideoCards()
    })

    return {
      isNavCollapsed,
      videoCards,
      statusFilter,
      selectedVideoCard,
      newVideoCard,
      filteredVideoCards,
      availableCount,
      inUseCount,
      defectiveCount,
      toggleNav,
      openProfile,
      openSettings,
      getStatusBadgeClass,
      formatDate,
      refreshData,
      showCreateVideoCardModal,
      hideCreateVideoCardModal,
      createVideoCard,
      viewVideoCard,
      hideViewVideoCardModal,
      editVideoCard,
      deleteVideoCard,
      filterVideoCards
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
