<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  isCollapsed: {
    type: Boolean,
    default: false
  }
})

// Accordion state
const componentsOpen = ref(false)
const managementOpen = ref(false)
const otherOpen = ref(false)
const isMobile = ref(false)
const isMobileMenuOpen = ref(false)

// Check if mobile
const checkMobile = () => {
  isMobile.value = window.innerWidth < 768
  // Auto-hide sidebar on mobile
  if (isMobile.value) {
    isMobileMenuOpen.value = false
  }
}

// Handle window resize
const handleResize = () => {
  checkMobile()
}

// Toggle mobile menu
const toggleMobileMenu = () => {
  if (!isMobile.value) return
  isMobileMenuOpen.value = !isMobileMenuOpen.value
}

// Close mobile menu
const closeMobileMenu = () => {
  if (isMobile.value) {
    isMobileMenuOpen.value = false
  }
}

// Handle escape key
const handleEscape = (event) => {
  if (event.key === 'Escape' && isMobileMenuOpen.value) {
    closeMobileMenu()
  }
}

onMounted(() => {
  checkMobile()
  window.addEventListener('resize', handleResize)
  window.addEventListener('keydown', handleEscape)
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
  window.removeEventListener('keydown', handleEscape)
})

function toggleAccordion(section) {
  // Only prevent accordion if sidebar is collapsed in desktop view
  if (props.isCollapsed && !isMobile.value) return
  
  switch(section) {
    case 'components':
      componentsOpen.value = !componentsOpen.value
      break
    case 'management':
      managementOpen.value = !managementOpen.value
      break
    case 'other':
      otherOpen.value = !otherOpen.value
      break
  }
}

// Computed width and shadow functions
const getSidebarWidth = () => {
  if (isMobile.value) {
    return isMobileMenuOpen.value ? '280px' : '0px'
  }
  return props.isCollapsed ? '70px' : '250px'
}

const getSidebarShadow = () => {
  if (isMobile.value && isMobileMenuOpen.value) {
    return '2px 0 15px rgba(0, 0, 0, 0.3)'
  }
  return '2px 0 5px rgba(0,0,0,0.1)'
}
</script>

<template>
  <!-- Mobile Menu Overlay -->
  <div 
    v-if="isMobile && isMobileMenuOpen" 
    class="mobile-overlay"
    @click="closeMobileMenu"
  ></div>
  
  <!-- Mobile Menu Toggle Button -->
  <button 
    v-if="isMobile"
    class="mobile-menu-toggle"
    @click="toggleMobileMenu"
    :class="{ 'active': isMobileMenuOpen }"
  >
    <span></span>
    <span></span>
    <span></span>
  </button>
  
  <aside :class="[
    'd-flex', 
    'flex-column', 
    'flex-shrink-0', 
    'p-3', 
    'bg-white', 
    'border-end', 
    { 
      'collapsed': props.isCollapsed,
      'mobile-hidden': isMobile && !isMobileMenuOpen,
      'mobile-open': isMobile && isMobileMenuOpen
    }
  ]" :style="`width: ${getSidebarWidth()}; height: 100vh; position: fixed; top: 0; left: 0; z-index: 1000; transition: all 0.3s ease; overflow-y: auto; overflow-x: hidden; box-shadow: ${getSidebarShadow()};`">
    <!-- University Logo and Name -->
    <div class="mb-4 text-center">
      <router-link to="/dashboard" class="d-block text-decoration-none">
        <div v-if="!props.isCollapsed" class="text-center">
          <img 
            src="/pnc-logo.png" 
            alt="University Logo" 
            class="img-fluid mb-2" 
            style="width: 50px; height: auto; object-fit: contain;"
          >
          <div class="fw-bold text-dark">University of Cabuyao</div>
        </div>
        <div v-if="props.isCollapsed" class="d-flex align-items-center justify-content-center">
          <img 
            src="/pnc-logo.png" 
            alt="University Logo" 
            class="img-fluid" 
            style="width: 40px; height: auto; object-fit: contain;"
          >
        </div>
      </router-link>
    </div>

    <!-- Navigation Menu -->
    <nav class="d-flex flex-column gap-1">
      <router-link to="/dashboard" class="nav-link text-decoration-none d-flex align-items-center gap-3 px-3 py-2 rounded text-success fw-bold" active-class="active" title="Dashboard">
        <i class="bi bi-house-door-fill"></i>
        <span :class="{ 'd-none': props.isCollapsed }">Dashboard</span>
      </router-link>
      
      <router-link to="/computers" class="nav-link text-decoration-none d-flex align-items-center gap-3 px-3 py-2 rounded text-success" active-class="active" title="Computers">
        <i class="bi bi-pc-display"></i>
        <span :class="{ 'd-none': props.isCollapsed }">Computers</span>
      </router-link>

      
      <!-- Components Accordion -->
      <div class="mt-3">
        <div 
          class="text-muted small px-3 mb-2 d-flex align-items-center justify-content-between cursor-pointer user-select-none" 
          :class="{ 'd-none': props.isCollapsed }"
          @click="toggleAccordion('components')"
        >
          <span class="bi bi-pc" > Computer Components</span>
          <i :class="['bi', componentsOpen ? 'bi-chevron-down' : 'bi-chevron-right']"></i>
        </div>
        <div :class="{ 'collapse': !componentsOpen && !props.isCollapsed && !(isMobile && isMobileMenuOpen), 'show': componentsOpen || props.isCollapsed || (isMobile && isMobileMenuOpen) }">
          <router-link to="/processors" class="nav-link text-decoration-none d-flex align-items-center gap-3 px-3 py-2 rounded text-dark hover-bg-light" active-class="active" title="Processors">
            <i class="bi bi-cpu"></i>
            <span :class="{ 'd-none': props.isCollapsed }">Processors</span>
          </router-link>
          <router-link to="/motherboards" class="nav-link text-decoration-none d-flex align-items-center gap-3 px-3 py-2 rounded text-dark hover-bg-light" active-class="active" title="Motherboards">
            <i class="bi bi-motherboard"></i>
            <span :class="{ 'd-none': props.isCollapsed }">Motherboards</span>
          </router-link>
          <router-link to="/video-cards" class="nav-link text-decoration-none d-flex align-items-center gap-3 px-3 py-2 rounded text-dark hover-bg-light" active-class="active" title="Video Cards">
            <i class="bi bi-gpu-card"></i>
            <span :class="{ 'd-none': props.isCollapsed }">Video Cards</span>
          </router-link>
          <router-link to="/dvd-roms" class="nav-link text-decoration-none d-flex align-items-center gap-3 px-3 py-2 rounded text-dark hover-bg-light" active-class="active" title="DVD ROMs">
            <i class="bi bi-disc"></i>
            <span :class="{ 'd-none': props.isCollapsed }">DVD ROMs</span>
          </router-link>
          <router-link to="/psus" class="nav-link text-decoration-none d-flex align-items-center gap-3 px-3 py-2 rounded text-dark hover-bg-light" active-class="active" title="PSUs">
            <i class="bi bi-lightning-charge"></i>
            <span :class="{ 'd-none': props.isCollapsed }">PSUs</span>
          </router-link>
          <router-link to="/rams" class="nav-link text-decoration-none d-flex align-items-center gap-3 px-3 py-2 rounded text-dark hover-bg-light" active-class="active" title="RAM">
            <i class="bi bi-memory"></i>
            <span :class="{ 'd-none': props.isCollapsed }">RAM</span>
          </router-link>
          <router-link to="/storage" class="nav-link text-decoration-none d-flex align-items-center gap-3 px-3 py-2 rounded text-dark hover-bg-light" active-class="active" title="Storage">
            <i class="bi bi-hdd"></i>
            <span :class="{ 'd-none': props.isCollapsed }">Storage</span>
          </router-link>
         
        </div>
      </div>
      
      <!-- Management Accordion -->
      <div class="mt-3">
        <div 
          class="text-muted small px-3 mb-2 d-flex align-items-center justify-content-between cursor-pointer user-select-none" 
          :class="{ 'd-none': props.isCollapsed }"
          @click="toggleAccordion('management')"
        >
          <span class="bi bi-kanban-fill"> Management</span>
          <i :class="['bi', managementOpen ? 'bi-chevron-down' : 'bi-chevron-right']"></i>
        </div>
        <div :class="{ 'collapse': !managementOpen && !props.isCollapsed, 'show': managementOpen || props.isCollapsed }">
          <ul class="nav nav-pills flex-column" :class="{ 'ms-3': !props.isCollapsed, 'ms-0': props.isCollapsed }">
            <li class="nav-item">
              <router-link to="/deployment" class="nav-link text-decoration-none d-flex align-items-center gap-3 px-3 py-2 rounded text-dark hover-bg-light" active-class="active" title="Deploy Assets">
                <i class="bi bi-box-arrow-right"></i>
                <span :class="{ 'd-none': props.isCollapsed }">Deploy Assets</span>
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/departments" class="nav-link text-decoration-none d-flex align-items-center gap-3 px-3 py-2 rounded text-dark hover-bg-light" active-class="active" title="Departments">
                <i class="bi bi-building"></i>
                <span :class="{ 'd-none': props.isCollapsed }">Departments</span>
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/laboratory-management" class="nav-link text-decoration-none d-flex align-items-center gap-3 px-3 py-2 rounded text-dark hover-bg-light" active-class="active" title="Laboratory Management">
                <i class="bi bi-building"></i>
                <span :class="{ 'd-none': props.isCollapsed }">Laboratory Management</span>
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/users" class="nav-link text-decoration-none d-flex align-items-center gap-3 px-3 py-2 rounded text-dark hover-bg-light" active-class="active" title="Users">
                <i class="bi bi-people"></i>
                <span :class="{ 'd-none': props.isCollapsed }">Users</span>
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/component-manager" class="nav-link text-decoration-none d-flex align-items-center gap-3 px-3 py-2 rounded text-dark hover-bg-light" active-class="active" title="Component Manager">
                <i class="bi bi-pc-display-horizontal"></i>
                <span :class="{ 'd-none': props.isCollapsed }">Component Manager</span>
              </router-link>
            </li>
          </ul>
        </div>
      </div>
      
      <!-- Other Accordion -->
      <div class="mt-3">
        <div 
          class="text-muted small px-3 mb-2 d-flex align-items-center justify-content-between cursor-pointer user-select-none" 
          :class="{ 'd-none': props.isCollapsed }"
          @click="toggleAccordion('other')"
        >
          <span class="bi bi-sliders2"> Other</span>
          <i :class="['bi', otherOpen ? 'bi-chevron-down' : 'bi-chevron-right']"></i>
        </div>
        <div :class="{ 'collapse': !otherOpen && !props.isCollapsed, 'show': otherOpen || props.isCollapsed }">
          <router-link to="/reports" class="nav-link text-decoration-none d-flex align-items-center gap-3 px-3 py-2 rounded text-dark hover-bg-light" active-class="active">
            <i class="bi bi-file-earmark-bar-graph-fill"></i>
            <span :class="{ 'd-none': props.isCollapsed }">Reports</span>
          </router-link>
          <router-link to="/settings" class="nav-link text-decoration-none d-flex align-items-center gap-3 px-3 py-2 rounded text-dark hover-bg-light" active-class="active">
            <i class="bi bi-gear-fill"></i>
            <span :class="{ 'd-none': props.isCollapsed }">Settings</span>
          </router-link>
        </div>
      </div>
    </nav>
  </aside>
</template>

<style scoped>
/* Mobile overlay */
.mobile-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  z-index: 999;
  backdrop-filter: blur(2px);
}

/* Mobile menu toggle button */
.mobile-menu-toggle {
  position: fixed;
  top: 1rem;
  left: 1rem;
  z-index: 1001;
  background: #0F6F43;
  border: none;
  border-radius: 8px;
  width: 44px;
  height: 44px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  gap: 4px;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(15, 111, 67, 0.3);
}

.mobile-menu-toggle:hover {
  background: #0a5234;
  transform: scale(1.05);
}

.mobile-menu-toggle span {
  width: 20px;
  height: 2px;
  background: white;
  border-radius: 2px;
  transition: all 0.3s ease;
}

.mobile-menu-toggle.active span:nth-child(1) {
  transform: rotate(45deg) translate(5px, 5px);
}

.mobile-menu-toggle.active span:nth-child(2) {
  opacity: 0;
}

.mobile-menu-toggle.active span:nth-child(3) {
  transform: rotate(-45deg) translate(7px, -6px);
}

/* Mobile sidebar states */
.mobile-hidden {
  transform: translateX(-100%) !important;
  width: 0px !important;
}

.mobile-open {
  transform: translateX(0) !important;
  box-shadow: 2px 0 15px rgba(0, 0, 0, 0.3) !important;
}
aside {
  width: 250px;
  overflow-y: auto;
  overflow-x: hidden;
}

aside::-webkit-scrollbar {
  width: 6px;
}

aside::-webkit-scrollbar-track {
  background: #f1f1f1;
}

aside::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

aside::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

aside.collapsed {
  width: 60px;
}

.nav-link.router-link-active,
.nav-link.active {
  background-color: #0F6F43 !important;
  color: white !important;
  font-weight: 600;
}

.nav-link {
  color: #000000 !important;
  transition: all 0.2s ease;
  border-radius: 0.375rem;
}

.nav-link:hover {
  background-color: #f8f9fa;
  color: #0F6F43;
  transform: translateX(4px);
}

aside.collapsed .nav-link:hover {
  transform: translateX(0);
}

/* Accordion styles */
.collapse {
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.3s ease;
}

.show {
  max-height: 500px;
  transition: max-height 0.3s ease;
}

.cursor-pointer {
  cursor: pointer;
}

.user-select-none {
  user-select: none;
}

/* Management nav links styling */
.nav-pills .nav-link {
  border-radius: 0.375rem;
  margin-bottom: 0.25rem;
  color: #000000 !important;
}

.nav-pills .nav-link.router-link-active,
.nav-pills .nav-link.active {
  background-color: #0F6F43 !important;
  color: white !important;
}

.nav-pills .nav-link:hover {
  background-color: #e9ecef;
  color: #0F6F43;
}

/* Collapsed state adjustments */
aside.collapsed {
  padding: 0.5rem !important;
}

aside.collapsed .nav-pills {
  margin-left: 0 !important;
}

aside.collapsed .nav-pills .nav-link {
  padding-left: 0.75rem !important;
  padding-right: 0.75rem !important;
  justify-content: center !important;
}

aside.collapsed .nav-link span {
  display: none !important;
}

aside.collapsed .text-muted {
  display: none !important;
}

aside.collapsed .nav-link {
  justify-content: center !important;
  min-height: 50px;
}

/* Logo styling */
.img-fluid.rounded-circle {
  border-radius: 50%;
  border: 2px solid #0F6F43;
}

/* Text selection for accordion headers */
.text-muted:hover {
  color: #0F6F43 !important;
}

/* Responsive adjustments */
@media (max-width: 1200px) {
  aside {
    width: 220px !important;
  }
  
  aside:not(.collapsed) {
    width: 220px !important;
  }
}

@media (max-width: 992px) {
  aside {
    width: 70px !important;
  }
  
  aside:not(.collapsed) {
    width: 220px !important;
  }
  
  .nav-link {
    padding: 0.75rem 1rem;
    font-size: 0.9rem;
  }
  
  .nav-link i {
    font-size: 1rem;
  }
}

@media (max-width: 768px) {
  aside {
    width: 0px !important;
    transform: translateX(-100%);
    transition: transform 0.3s ease, width 0.3s ease;
  }
  
  aside:not(.collapsed) {
    width: 280px !important;
    transform: translateX(0);
    box-shadow: 2px 0 10px rgba(0,0,0,0.2);
  }
  
  .nav-link {
    padding: 1rem;
    font-size: 0.9rem;
    min-height: 55px;
  }
  
  .nav-link i {
    font-size: 1.1rem;
    min-width: 24px;
  }
  
  .nav-link span {
    font-size: 0.9rem;
  }
  
  .text-muted.small {
    font-size: 0.8rem;
  }
  
  .img-fluid {
    width: 45px;
  }
  
  .fw-bold {
    font-size: 0.9rem;
  }
  
  /* Mobile overlay background */
  aside::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: -1;
    display: none;
  }
  
  aside:not(.collapsed)::before {
    display: block;
  }
}

@media (max-width: 576px) {
  aside {
    width: 0px !important;
  }
  
  aside:not(.collapsed) {
    width: 260px !important;
  }
  
  .nav-link {
    padding: 0.8rem;
    font-size: 0.85rem;
    min-height: 50px;
  }
  
  .nav-link i {
    font-size: 1rem;
    min-width: 20px;
  }
  
  .nav-link span {
    font-size: 0.85rem;
  }
  
  .text-muted.small {
    font-size: 0.75rem;
  }
  
  .img-fluid {
    width: 40px;
  }
  
  .fw-bold {
    font-size: 0.85rem;
  }
}

@media (max-width: 480px) {
  aside {
    width: 0px !important;
  }
  
  aside:not(.collapsed) {
    width: 240px !important;
  }
  
  .nav-link {
    padding: 0.7rem;
    font-size: 0.8rem;
    min-height: 48px;
  }
  
  .nav-link i {
    font-size: 0.95rem;
    min-width: 18px;
  }
  
  .nav-link span {
    font-size: 0.8rem;
  }
  
  .text-muted.small {
    font-size: 0.7rem;
  }
  
  .img-fluid {
    width: 35px;
  }
  
  .fw-bold {
    font-size: 0.8rem;
  }
  
  /* Touch-friendly tap targets */
  .nav-link {
    min-height: 48px;
    min-width: 48px;
  }
}

/* Tooltip for collapsed state */
.nav-link[title]:hover::after {
  content: attr(title);
  position: absolute;
  left: 100%;
  top: 50%;
  transform: translateY(-50%);
  background: #333;
  color: white;
  padding: 0.25rem 0.5rem;
  border-radius: 0.25rem;
  font-size: 0.875rem;
  white-space: nowrap;
  z-index: 1001;
  margin-left: 0.5rem;
}
</style>
