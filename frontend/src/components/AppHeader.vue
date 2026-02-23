<template>
  <header class="navbar navbar-expand bg-success navbar-dark py-2 px-3">
    <div class="container-fluid">
      <!-- Left side: User Info -->
      <div class="d-flex align-items-center">
        <div class="text-white">
          <span class="fw-bold">MiSD Inventory</span>
         
          
        </div>
      </div>

      <!-- Right side: Icons and Toggle Switch -->
      <div class="d-flex align-items-center gap-3">
        <!-- Dark Mode Toggle -->
        <div class="form-check form-switch me-2">
          <input 
            class="form-check-input" 
            type="checkbox" 
            id="darkModeToggle" 
            @click="handleDarkModeToggle"
            :checked="isDarkMode"
          >
          <label class="form-check-label text-white" for="darkModeToggle" title="Toggle Dark Mode">
            <i :class="isDarkMode ? 'bi bi-moon-fill' : 'bi bi-sun-fill'"></i>
          </label>
        </div>

        <!-- On/Off Toggle Switch for Sidebar -->
        <div class="form-check form-switch me-2">
          <input 
            class="form-check-input" 
            type="checkbox" 
            id="sidebarToggle" 
            @click="toggleSidebar"
            :checked="!props.isCollapsed"
          >
          <label class="form-check-label text-white" for="sidebarToggle"></label>
        </div>

        <i class="bi bi-bell-fill text-white fs-5 cursor-pointer" @click="openNotifications"></i>
        <i class="bi bi-grid-3x3-gap-fill text-white fs-5 cursor-pointer" @click="openGrid"></i>
        <img 
          src="/pnc-logo.png" 
          alt="Profile" 
          class="rounded-circle" 
          style="width: 30px; height: 30px; object-fit: cover; border: 1px solid white;"
          @click="openProfile"
        >
      </div>
    </div>
  </header>
</template>

<script setup>
import { defineEmits, ref, onMounted, onUnmounted } from 'vue'
import { useDarkMode } from '@/composables/useDarkMode.js'

const props = defineProps({
  isCollapsed: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['menu-toggle', 'settings-open', 'profile-open', 'sidebar-toggle'])
const { isDarkMode, toggleDarkMode, initDarkMode } = useDarkMode()

const toggleSidebar = (event) => {
  event.preventDefault()
  const newValue = !props.isCollapsed
  console.log('Toggle clicked, current state:', props.isCollapsed, 'new state:', newValue)
  emit('sidebar-toggle', newValue)
}

const handleDarkModeToggle = () => {
  console.log('Dark mode toggle clicked')
  toggleDarkMode()
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

onMounted(() => {
  initDarkMode()
})
</script>

<style scoped>
.navbar {
  background: #0F6F43;
  color: white;
  padding: 1rem 2rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  position: sticky;
  top: 0;
  z-index: 1000;
  height: auto;
  min-height: 60px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.cursor-pointer {
  cursor: pointer;
  transition: opacity 0.2s;
}

.cursor-pointer:hover {
  opacity: 0.8;
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

/* Dark mode styles for the navbar */
:global(.dark-mode) .navbar {
  background: #1a1a1a !important;
  color: white !important;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
  border-bottom: 1px solid #333;
}

/* Dark mode toggle switch styles */
:global(.dark-mode) .form-check-input:checked {
  background-color: #ffc107;
  border-color: #ffc107;
}

:global(.dark-mode) .form-check-input {
  background-color: #495057;
  border-color: #6c757d;
}

/* Light mode styles */
:global(.light-mode) .navbar {
  background: #0F6F43;
  color: white;
}
</style>
