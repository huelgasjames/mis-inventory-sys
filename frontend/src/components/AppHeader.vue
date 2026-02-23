<template>
  <header class="navbar navbar-expand bg-success navbar-dark py-3 px-4 w-100">
    <div class="container-fluid">
      <!-- Left side: Logo and Title -->
      <div class="d-flex align-items-center">
        <div class="text-white text-center">
          <div class="fw-bold fs-4">MiSINV</div>
          <div class="small">MISD INVENTORY</div>
        </div>
      </div>

      <!-- Right side: Admin and Icons -->
      <div class="d-flex align-items-center gap-3">
        <span class="text-white me-3">Admin</span>
        
        <!-- Hamburger Menu -->
        <i class="bi bi-list text-white fs-5 cursor-pointer" @click="toggleSidebar"></i>
        
        <!-- Settings Icon -->
        <i class="bi bi-gear-fill text-white fs-5 cursor-pointer" @click="openSettings"></i>
        
        <!-- User Profile Icon -->
        <i class="bi bi-person-circle text-white fs-5 cursor-pointer" @click="openProfile"></i>
      </div>
    </div>
  </header>
</template>

<script setup>
import { defineEmits, ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  isCollapsed: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['menu-toggle', 'settings-open', 'profile-open', 'sidebar-toggle'])

const toggleSidebar = (event) => {
  event.preventDefault()
  const newValue = !props.isCollapsed
  console.log('Toggle clicked, current state:', props.isCollapsed, 'new state:', newValue)
  emit('sidebar-toggle', newValue)
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
  // Clear authentication data
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  sessionStorage.removeItem('token')
  sessionStorage.removeItem('user')
  
  // Show confirmation
  if (confirm('Are you sure you want to logout?')) {
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
  transition: opacity 0.2s;
}

.cursor-pointer:hover {
  opacity: 0.8;
}

/* Responsive header */
@media (max-width: 768px) {
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
    font-size: 1.25rem;
  }
  
  .small {
    font-size: 0.7rem;
  }
  
  .fs-5 {
    font-size: 1rem;
  }
  
  .me-3 {
    margin-right: 0.5rem !important;
  }
  
  .gap-3 {
    gap: 0.75rem !important;
  }
}

@media (max-width: 576px) {
  .navbar {
    padding: 0.5rem 0.75rem;
    min-height: 55px;
  }
  
  .fs-4 {
    font-size: 1.1rem;
  }
  
  .small {
    font-size: 0.65rem;
  }
  
  .fs-5 {
    font-size: 0.9rem;
  }
  
  .me-3 {
    margin-right: 0.25rem !important;
  }
  
  .gap-3 {
    gap: 0.5rem !important;
  }
  
  span.text-white {
    font-size: 0.8rem;
  }
}

@media (max-width: 480px) {
  .navbar {
    padding: 0.4rem 0.5rem;
    min-height: 50px;
  }
  
  .fs-4 {
    font-size: 1rem;
  }
  
  .small {
    font-size: 0.6rem;
  }
  
  .fs-5 {
    font-size: 0.85rem;
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
</style>
