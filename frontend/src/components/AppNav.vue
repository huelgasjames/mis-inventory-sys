<script setup>
import { ref } from 'vue'

const props = defineProps({
  isCollapsed: {
    type: Boolean,
    default: false
  }
})

// Accordion state
const componentsOpen = ref(true)
const managementOpen = ref(true)
const otherOpen = ref(false)

function toggleAccordion(section) {
  if (props.isCollapsed) return
  
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
</script>

<template>
  <aside :class="['d-flex', 'flex-column', 'flex-shrink-0', 'p-3', 'bg-white', 'border-end', { 'collapsed': isCollapsed }]" style="width: 250px; height: 100vh; position: fixed; top: 0; left: 0; z-index: 1000; transition: width 0.3s ease; overflow-y: auto; overflow-x: hidden; box-shadow: 2px 0 5px rgba(0,0,0,0.1);">
    <div class="mb-4 text-center">
      <router-link to="/dashboard" class="d-block text-decoration-none">
        <img 
          v-if="isCollapsed" 
          src="/pnc-logo.png" 
          alt="MiSD Logo" 
          class="img-fluid rounded-circle" 
          style="width: 40px; height: auto; object-fit: contain;"
        >
        <div v-else class="d-flex align-items-center justify-content-center">
          <img 
            src="/pnc-logo.png" 
            alt="MiSD Inventory Logo" 
            class="img-fluid me-2 rounded-circle" 
            style="width: 40px; height: auto; object-fit: contain;"
          >
          <span class="fs-5 fw-bold" style="color: #0F6F43;">MiSD Inventory</span>
        </div>
      </router-link>
    </div>
    <nav class="d-flex flex-column gap-1">
      <router-link to="/dashboard" class="nav-link text-decoration-none d-flex align-items-center gap-3 px-3 py-2 rounded text-dark hover-bg-light" active-class="active">
        <i class="bi bi-house-door-fill"></i>
        <span :class="{ 'd-none': isCollapsed }">Dashboard</span>
      </router-link>

      
      <!-- Components Accordion -->
      <div class="mt-3">
        <div 
          class="text-muted small px-3 mb-2 d-flex align-items-center justify-content-between cursor-pointer user-select-none" 
          :class="{ 'd-none': isCollapsed }"
          @click="toggleAccordion('components')"
        >
          <span>Components</span>
          <i :class="['bi', componentsOpen ? 'bi-chevron-down' : 'bi-chevron-right']"></i>
        </div>
        <div :class="{ 'collapse': !componentsOpen, 'show': componentsOpen }">
          <router-link to="/processors" class="nav-link text-decoration-none d-flex align-items-center gap-3 px-3 py-2 rounded text-dark hover-bg-light" active-class="active">
            <i class="bi bi-cpu"></i>
            <span :class="{ 'd-none': isCollapsed }">Processors</span>
          </router-link>
          <router-link to="/motherboards" class="nav-link text-decoration-none d-flex align-items-center gap-3 px-3 py-2 rounded text-dark hover-bg-light" active-class="active">
            <i class="bi bi-motherboard"></i>
            <span :class="{ 'd-none': isCollapsed }">Motherboards</span>
          </router-link>
          <router-link to="/video-cards" class="nav-link text-decoration-none d-flex align-items-center gap-3 px-3 py-2 rounded text-dark hover-bg-light" active-class="active">
            <i class="bi bi-gpu-card"></i>
            <span :class="{ 'd-none': isCollapsed }">Video Cards</span>
          </router-link>
          <router-link to="/dvd-roms" class="nav-link text-decoration-none d-flex align-items-center gap-3 px-3 py-2 rounded text-dark hover-bg-light" active-class="active">
            <i class="bi bi-disc"></i>
            <span :class="{ 'd-none': isCollapsed }">DVD ROMs</span>
          </router-link>
          <router-link to="/psus" class="nav-link text-decoration-none d-flex align-items-center gap-3 px-3 py-2 rounded text-dark hover-bg-light" active-class="active">
            <i class="bi bi-lightning-charge"></i>
            <span :class="{ 'd-none': isCollapsed }">PSUs</span>
          </router-link>
          <router-link to="/rams" class="nav-link text-decoration-none d-flex align-items-center gap-3 px-3 py-2 rounded text-dark hover-bg-light" active-class="active">
            <i class="bi bi-memory"></i>
            <span :class="{ 'd-none': isCollapsed }">RAM</span>
          </router-link>
          <router-link to="/storage" class="nav-link text-decoration-none d-flex align-items-center gap-3 px-3 py-2 rounded text-dark hover-bg-light" active-class="active">
            <i class="bi bi-hdd"></i>
            <span :class="{ 'd-none': isCollapsed }">Storage</span>
          </router-link>
        </div>
      </div>
      
      <!-- Management Accordion -->
      <div class="mt-3">
        <div 
          class="text-muted small px-3 mb-2 d-flex align-items-center justify-content-between cursor-pointer user-select-none" 
          :class="{ 'd-none': isCollapsed }"
          @click="toggleAccordion('management')"
        >
          <span>Management</span>
          <i :class="['bi', managementOpen ? 'bi-chevron-down' : 'bi-chevron-right']"></i>
        </div>
        <div :class="{ 'collapse': !managementOpen, 'show': managementOpen }">
          <ul class="nav nav-pills flex-column ms-3" :class="{ 'd-none': !managementOpen && !isCollapsed }">
            <li class="nav-item">
              <router-link to="/deployment" class="nav-link text-decoration-none d-flex align-items-center gap-3 px-3 py-2 rounded text-dark hover-bg-light" active-class="active">
                <i class="bi bi-box-arrow-right"></i>
                <span :class="{ 'd-none': isCollapsed }">Deploy Assets</span>
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/departments" class="nav-link text-decoration-none d-flex align-items-center gap-3 px-3 py-2 rounded text-dark hover-bg-light" active-class="active">
                <i class="bi bi-building"></i>
                <span :class="{ 'd-none': isCollapsed }">Departments</span>
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/department-inventory" class="nav-link text-decoration-none text-dark hover-bg-light" active-class="active">
                <i class="bi bi-pc-display-horizontal me-2"></i>Department Inventory
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/laboratory-management" class="nav-link text-decoration-none text-dark hover-bg-light" active-class="active">
                <i class="bi bi-building me-2"></i>Laboratory Management
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/computers" class="nav-link text-decoration-none text-dark hover-bg-light" active-class="active">
                <i class="bi bi-pc-display-horizontal me-2"></i>Computers
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/users" class="nav-link text-decoration-none text-dark hover-bg-light" active-class="active">
                <i class="bi bi-people me-2"></i>Users
              </router-link>
            </li>
          </ul>
        </div>
      </div>
      
      <!-- Other Accordion -->
      <div class="mt-3">
        <div 
          class="text-muted small px-3 mb-2 d-flex align-items-center justify-content-between cursor-pointer user-select-none" 
          :class="{ 'd-none': isCollapsed }"
          @click="toggleAccordion('other')"
        >
          <span>Other</span>
          <i :class="['bi', otherOpen ? 'bi-chevron-down' : 'bi-chevron-right']"></i>
        </div>
        <div :class="{ 'collapse': !otherOpen, 'show': otherOpen }">
          <router-link to="/reports" class="nav-link text-decoration-none d-flex align-items-center gap-3 px-3 py-2 rounded text-dark hover-bg-light" active-class="active">
            <i class="bi bi-file-earmark-bar-graph-fill"></i>
            <span :class="{ 'd-none': isCollapsed }">Reports</span>
          </router-link>
          <router-link to="/settings" class="nav-link text-decoration-none d-flex align-items-center gap-3 px-3 py-2 rounded text-dark hover-bg-light" active-class="active">
            <i class="bi bi-gear-fill"></i>
            <span :class="{ 'd-none': isCollapsed }">Settings</span>
          </router-link>
        </div>
      </div>
    </nav>
  </aside>
</template>

<style scoped>
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
  width: 70px;
}

.nav-link.router-link-active,
.nav-link.active {
  background-color: #0F6F43 !important;
  color: white !important;
  font-weight: 600;
}

.nav-link {
  color: #333;
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

/* Logo styling */
.img-fluid.rounded-circle {
  border-radius: 50%;
  border: 2px solid #0F6F43;
}

/* Text selection for accordion headers */
.text-muted:hover {
  color: #0F6F43 !important;
}

/* Ensure proper scrolling on mobile */
@media (max-width: 768px) {
  aside {
    width: 70px;
  }
  
  aside.collapsed {
    width: 70px;
  }
}
</style>
