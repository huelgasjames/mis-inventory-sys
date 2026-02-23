<script setup>
import { ref } from 'vue'

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
  <aside :class="['d-flex', 'flex-column', 'flex-shrink-0', 'p-3', 'bg-white', 'border-end', { 'collapsed': props.isCollapsed }]" :style="`width: ${props.isCollapsed ? '70px' : '250px'}; height: 100vh; position: fixed; top: 0; left: 0; z-index: 1000; transition: width 0.3s ease; overflow-y: auto; overflow-x: hidden; box-shadow: 2px 0 5px rgba(0,0,0,0.1);`">
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
        <div :class="{ 'collapse': !componentsOpen && !props.isCollapsed, 'show': componentsOpen || props.isCollapsed }">
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
@media (max-width: 768px) {
  aside {
    width: 70px !important;
  }
  
  aside:not(.collapsed) {
    width: 250px !important;
  }
}

@media (max-width: 480px) {
  aside {
    width: 60px !important;
    position: fixed;
    z-index: 1000;
  }
  
  aside:not(.collapsed) {
    width: 280px !important;
  }
  
  .nav-link {
    padding: 0.75rem !important;
  }
  
  .nav-link i {
    font-size: 1.1rem !important;
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
