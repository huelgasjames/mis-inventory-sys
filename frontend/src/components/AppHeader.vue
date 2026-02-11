<template>
  <header class="navbar navbar-expand bg-success navbar-dark">
    <div class="container-fluid">
      <div class="navbar-brand d-flex align-items-center">
        <img src="/pnc-logo.png" alt="MiSD Logo" class="img-fluid me-2 rounded-circle" style="height: 30px; border: 1px solid white;">
        <span class="fw-bold">MiSD Inventory Management</span>
      </div>
      
      <div class="d-flex align-items-center gap-3">
        <div class="text-white d-flex align-items-center">
          <span class="badge bg-white text-success me-2">Admin</span>
          <small class="text-white-50">System Administrator</small>
        </div>
        
        <div class="dropdown">
          <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="bi bi-gear"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="#" @click="setTheme('light')">
              <i class="bi bi-sun me-2"></i>Light
            </a></li>
            <li><a class="dropdown-item" href="#" @click="setTheme('dark')">
              <i class="bi bi-moon me-2"></i>Dark
            </a></li>
            <li><a class="dropdown-item" href="#" @click="setTheme('auto')">
              <i class="bi bi-circle-half me-2"></i>Auto
            </a></li>
          </ul>
        </div>
        
        <button class="btn btn-outline-light btn-sm" @click="toggleMenu">
          <i class="bi bi-list"></i>
        </button>
        <button class="btn btn-outline-light btn-sm" @click="openProfile">
          <i class="bi bi-person"></i>
        </button>
      </div>
    </div>
  </header>
</template>

<script setup>
import { defineEmits, ref, onMounted, onUnmounted } from 'vue'

const emit = defineEmits(['menu-toggle', 'settings-open', 'profile-open'])

const currentTheme = ref('light')

const toggleMenu = () => {
  emit('menu-toggle')
}

const openSettings = () => {
  emit('settings-open')
}

const openProfile = () => {
  emit('profile-open')
}

const setTheme = (theme) => {
  currentTheme.value = theme
  localStorage.setItem('theme', theme)
  applyTheme(theme)
}

const applyTheme = (theme) => {
  const html = document.documentElement
  const body = document.body
  
  if (theme === 'auto') {
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
    html.classList.toggle('dark-mode', prefersDark)
    html.classList.toggle('light-mode', !prefersDark)
    body.classList.toggle('dark-mode', prefersDark)
    body.classList.toggle('light-mode', !prefersDark)
  } else {
    const isDark = theme === 'dark'
    html.classList.toggle('dark-mode', isDark)
    html.classList.toggle('light-mode', !isDark)
    body.classList.toggle('dark-mode', isDark)
    body.classList.toggle('light-mode', !isDark)
  }
}

const handleSystemThemeChange = (e) => {
  if (currentTheme.value === 'auto') {
    applyTheme('auto')
  }
}

onMounted(() => {
  // Load saved theme or default to light
  const savedTheme = localStorage.getItem('theme') || 'light'
  currentTheme.value = savedTheme
  applyTheme(savedTheme)
  
  // Add system theme change listener
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', handleSystemThemeChange)
})

onUnmounted(() => {
  window.matchMedia('(prefers-color-scheme: dark)').removeEventListener('change', handleSystemThemeChange)
})
</script>

<style scoped>
.navbar {
  background: #0F6F43;
  color: white;
  padding: 1rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  position: sticky;
  top: 0;
  z-index: 1000;
}

.navbar-brand {
  display: flex;
  align-items: center;
}

.navbar-brand img {
  height: 30px;
  margin-right: 0.5rem;
  border-radius: 50%;
  border: 1px solid white;
}

.navbar-brand span {
  font-weight: bold;
}

.dropdown-menu {
  margin-top: 0.5rem;
  border: none;
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
  border-radius: 0.375rem;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  cursor: pointer;
  transition: all 0.2s ease;
  color: #2d3748;
  font-size: 0.9rem;
}

.dropdown-item:hover {
  background-color: #f8f9fa;
  color: #0F6F43;
}

.dropdown-item i {
  font-size: 1rem;
  width: 16px;
  text-align: center;
}

.btn-outline-light {
  border-color: rgba(255, 255, 255, 0.5);
  color: white;
}

.btn-outline-light:hover {
  background-color: rgba(255, 255, 255, 0.1);
  border-color: white;
  color: white;
}

/* Dark mode styles for the dropdown */
:global(.dark-mode) .dropdown-menu {
  background: #2d3748;
  border-color: #4a5568;
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.3);
}

:global(.dark-mode) .dropdown-item {
  color: #e2e8f0;
}

:global(.dark-mode) .dropdown-item:hover {
  background-color: #4a5568;
  color: #e2e8f0;
}

/* Dark mode styles for the navbar */
:global(.dark-mode) .navbar {
  background: #0F6F43 !important;
  color: white !important;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

/* Light mode styles */
:global(.light-mode) .navbar {
  background: #0F6F43;
  color: white;
}
</style>
