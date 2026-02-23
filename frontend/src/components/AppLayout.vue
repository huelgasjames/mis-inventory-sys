<script setup>
import { ref } from 'vue'

const isNavCollapsed = ref(false)

const toggleNav = () => {
  isNavCollapsed.value = !isNavCollapsed.value
}

const toggleSidebar = (collapsed) => {
  isNavCollapsed.value = collapsed
}

const openProfile = () => {
  console.log('Opening profile')
}

const openSettings = () => {
  console.log('Opening settings')
}
</script>

<template>
  <div class="dashboard-layout">
    <!-- Navigation Sidebar -->
    <AppNav :is-collapsed="isNavCollapsed" />
    
    <!-- Main Content Area -->
    <div class="main-content" :class="{ 'collapsed': isNavCollapsed }">
      <!-- Header -->
      <AppHeader 
        @menu-toggle="toggleNav"
        @sidebar-toggle="toggleSidebar"
        @profile-open="openProfile"
        @settings-open="openSettings"
      />
      
      <!-- Page Content -->
      <slot />
    </div>
  </div>
</template>

<style scoped>
.dashboard-layout {
  display: flex;
  min-height: 100vh;
}

.main-content {
  flex: 1;
  margin-left: 250px;
  transition: margin-left 0.3s ease;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.main-content.collapsed {
  margin-left: 70px;
}

@media (max-width: 768px) {
  .main-content {
    margin-left: 0;
  }
  
  .main-content.collapsed {
    margin-left: 0;
  }
}

@media (max-width: 480px) {
  .dashboard-layout {
    flex-direction: column;
  }
  
  .main-content {
    margin-left: 0;
    width: 100%;
  }
}
</style>
