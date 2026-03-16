<template>
  <header class="navbar navbar-expand bg-success navbar-dark py-3 px-4 w-100">
    <div class="container-fluid">
      <!-- Left side: Logo and Title -->
      <div class="d-flex align-items-center flex-shrink-0">
        <div class="text-white text-center">
          <div class="fw-bold fs-4">MiSINV</div>
          <div class="small d-none d-sm-block">MISD INVENTORY</div>
          <div class="small d-block d-sm-none">INVENTORY</div>
        </div>
      </div>

      <!-- Right side: Admin and Icons -->
      <div class="d-flex align-items-center gap-3 flex-shrink-0">
        <span class="text-white me-3 d-none d-md-flex">Admin</span>
        <span class="text-white me-2 d-flex d-md-none">A</span>
        
        <!-- Hamburger Menu -->
        <i class="bi bi-list text-white fs-5 cursor-pointer" @click="toggleSidebar"></i>
        
        <!-- Settings Icon -->
        <i class="bi bi-gear-fill text-white fs-5 cursor-pointer" @click="openSettings"></i>
        
        <!-- User Profile Icon with Dropdown -->
        <div class="dropdown position-relative">
          <i class="bi bi-person-circle text-white fs-5 cursor-pointer" @click="toggleProfileDropdown"></i>
          
          <!-- Dropdown Menu -->
          <div v-if="showProfileDropdown" class="dropdown-menu show position-absolute end-0" :style="dropdownStyles">
            <div class="dropdown-header text-muted small px-3 py-2">
              <i class="bi bi-person me-2"></i>
              <span v-if="user">{{ user.name || 'Admin User' }}</span>
              <span v-else>Admin User</span>
            </div>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="#" @click="logout">
              <i class="bi bi-box-arrow-right me-2"></i>Logout
            </a>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'

const props = defineProps({
  isCollapsed: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['menu-toggle', 'settings-open', 'profile-open', 'sidebar-toggle'])

// Reactive variables
const showProfileDropdown = ref(false)
const user = ref(null)
const isMobile = ref(false)

// Check if mobile
const checkMobile = () => {
  isMobile.value = window.innerWidth < 768
}

// Calculate dropdown position based on screen size
const dropdownStyles = computed(() => {
  if (isMobile.value) {
    return {
      right: '0',
      left: 'auto',
      top: '100%',
      marginTop: '0.5rem',
      minWidth: '180px',
      maxWidth: '90vw'
    }
  }
  return {
    right: '0',
    left: 'auto',
    top: '100%',
    marginTop: '0.5rem'
  }
})

// Get user data from localStorage
onMounted(() => {
  checkMobile()
  
  try {
    const userData = localStorage.getItem('user')
    if (userData) {
      user.value = JSON.parse(userData)
    }
  } catch (error) {
    console.error('Error parsing user data:', error)
  }
  
  // Close dropdown when clicking outside
  document.addEventListener('click', handleClickOutside)
  // Handle window resize
  window.addEventListener('resize', checkMobile)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  window.removeEventListener('resize', checkMobile)
})

const handleClickOutside = (event) => {
  const dropdown = event.target.closest('.dropdown')
  if (!dropdown) {
    showProfileDropdown.value = false
  }
}

const toggleProfileDropdown = (event) => {
  event.stopPropagation()
  showProfileDropdown.value = !showProfileDropdown.value
}

const toggleSidebar = (event) => {
  event.preventDefault()
  const newValue = !props.isCollapsed
  console.log('Toggle clicked, current state:', props.isCollapsed, 'new state:', newValue)
  emit('sidebar-toggle', newValue)
}


const openSettings = () => {
  emit('settings-open')
}

const openNotifications = () => {
  console.log('Notifications clicked')
}

const openGrid = () => {
  console.log('Grid clicked')
}

const openProfile = () => {
  emit('profile-open')
}

const logout = () => {
  showProfileDropdown.value = false
  
  // Show confirmation
  if (confirm('Are you sure you want to logout?')) {
    // Clear authentication data
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    localStorage.removeItem('isAuthenticated')
    sessionStorage.removeItem('token')
    sessionStorage.removeItem('user')
    
    // Redirect to login page
    window.location.href = '/login'
  }
}

</script>

<style scoped>
.navbar {
  background: #0F6F43;
  color: white;
  padding: 1rem 1.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  position: sticky;
  top: 0;
  z-index: 1000;
  height: auto;
  min-height: 70px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
}

.cursor-pointer {
  cursor: pointer;
  transition: opacity 0.2s, transform 0.2s;
  -webkit-tap-highlight-color: transparent;
}

.cursor-pointer:hover {
  opacity: 0.8;
}

.cursor-pointer:active {
  transform: scale(0.95);
}

/* Responsive header */
@media (max-width: 1200px) {
  .navbar {
    padding: 0.875rem 1.25rem;
    min-height: 65px;
  }
  
  .fs-4 {
    font-size: 1.125rem;
  }
  
  .small {
    font-size: 0.8rem;
  }
}

@media (max-width: 992px) {
  .navbar {
    padding: 0.75rem 1rem;
    min-height: 60px;
  }
  
  .container-fluid {
    padding: 0;
  }
  
  .d-flex.align-items-center {
    gap: 0.5rem;
  }
  
  .fs-4 {
    font-size: 1rem;
  }
  
  .small {
    font-size: 0.75rem;
  }
  
  .fs-5 {
    font-size: 0.95rem;
  }
  
  .me-3 {
    margin-right: 0.5rem !important;
  }
  
  .gap-3 {
    gap: 0.75rem !important;
  }
}

@media (max-width: 768px) {
  .navbar {
    padding: 0.5rem 0.75rem;
    min-height: 55px;
  }
  
  .container-fluid {
    padding: 0;
  }
  
  .d-flex.align-items-center {
    gap: 0.25rem;
    flex-wrap: wrap;
  }
  
  .fs-4 {
    font-size: 0.9rem;
  }
  
  .small {
    font-size: 0.7rem;
  }
  
  .fs-5 {
    font-size: 0.85rem;
  }
  
  .me-3 {
    margin-right: 0.25rem !important;
  }
  
  .gap-3 {
    gap: 0.5rem !important;
  }
  
  span.text-white {
    font-size: 0.75rem;
  }
}

@media (max-width: 576px) {
  .navbar {
    padding: 0.4rem 0.5rem;
    min-height: 50px;
  }
  
  .fs-4 {
    font-size: 0.85rem;
  }
  
  .small {
    font-size: 0.65rem;
  }
  
  .fs-5 {
    font-size: 0.8rem;
  }
  
  .me-3 {
    margin-right: 0.125rem !important;
  }
  
  .gap-3 {
    gap: 0.25rem !important;
  }
  
  span.text-white {
    font-size: 0.7rem;
  }
}

@media (max-width: 480px) {
  .navbar {
    padding: 0.3rem 0.4rem;
    min-height: 45px;
  }
  
  .fs-4 {
    font-size: 0.8rem;
  }
  
  .small {
    font-size: 0.6rem;
  }
  
  .fs-5 {
    font-size: 0.75rem;
  }
  
  .me-3 {
    margin-right: 0.125rem !important;
  }
  
  .gap-3 {
    gap: 0.25rem !important;
  }
  
  span.text-white {
    display: none;
  }
  
  /* Touch-friendly tap targets */
  .cursor-pointer {
    padding: 0.25rem;
    margin: -0.25rem;
  }
}

/* Custom toggle switch styles */
.form-check-input:checked {
  background-color: #28a745;
  border-color: #28a745;
}

.form-check-input {
  width: 3em;
  height: 1.5em;
  cursor: pointer;
}

.form-check-input:focus {
  border-color: #28a745;
  outline: 0;
  box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
}

/* Light mode styles */
:global(.light-mode) .navbar {
  background: #0F6F43;
  color: white;
}

/* Dropdown Menu Responsive Styles */
.dropdown-menu {
  background: white;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
  min-width: 200px;
  z-index: 9999;
  animation: fadeInDown 0.2s ease-out;
}

@media (max-width: 768px) {
  .dropdown-menu {
    min-width: 180px;
    max-width: 90vw;
    right: 0 !important;
    left: auto !important;
  }
}

@media (max-width: 480px) {
  .dropdown-menu {
    min-width: 160px;
    max-width: 85vw;
    font-size: 0.875rem;
  }
  
  .dropdown-item {
    padding: 0.4rem 0.8rem;
  }
  
  .dropdown-header {
    padding: 0.4rem 0.8rem;
    font-size: 0.8rem;
  }
}

.dropdown-header {
  background: #f8f9fa;
  border-bottom: 1px solid #e9ecef;
  font-weight: 600;
  color: #495057;
}

.dropdown-item {
  color: #495057;
  padding: 0.5rem 1rem;
  transition: all 0.2s ease;
  border: none;
  background: transparent;
}

.dropdown-item:hover {
  background: #0F6F43;
  color: white;
  text-decoration: none;
}

.dropdown-item i {
  width: 16px;
  text-align: center;
}

.dropdown-divider {
  margin: 0;
  border-top: 1px solid #e9ecef;
}

@keyframes fadeInDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
