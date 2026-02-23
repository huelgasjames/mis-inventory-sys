<template>
  <div class="loading-overlay" v-if="isVisible">
    <div class="loading-content">
      <div class="logo-container">
        <img 
          src="/pnc-logo.png" 
          alt="Loading..." 
          class="loading-logo"
        />
      </div>
      <p class="loading-text">{{ message }}</p>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue'

const props = defineProps({
  isVisible: {
    type: Boolean,
    default: false
  },
  message: {
    type: String,
    default: 'Loading data...'
  }
})
</script>

<style scoped>
.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(255, 255, 255, 0.95);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
  backdrop-filter: blur(2px);
}

.loading-content {
  text-align: center;
  animation: fadeIn 0.3s ease-in;
}

.logo-container {
  position: relative;
  display: inline-block;
  margin-bottom: 1rem;
}

.loading-logo {
  width: 80px;
  height: 80px;
  object-fit: contain;
  animation: pulse 2s infinite ease-in-out;
}

.loading-text {
  margin: 0;
  font-size: 1.1rem;
  color: #0F6F43;
  font-weight: 500;
  animation: fadeIn 0.5s ease-in 0.2s both;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
    transform: scale(1);
  }
  50% {
    opacity: 0.8;
    transform: scale(1.05);
  }
}

/* Dark mode support */
:global(.dark-mode) .loading-overlay {
  background-color: rgba(0, 0, 0, 0.95);
}

:global(.dark-mode) .loading-text {
  color: #28a745;
}

:global(.dark-mode) .loading-logo {
  filter: brightness(1.1) contrast(1.1);
}

/* Enhanced dark mode animation */
:global(.dark-mode) .loading-content {
  animation: fadeInDark 0.3s ease-in;
}

@keyframes fadeInDark {
  from {
    opacity: 0;
    transform: translateY(20px);
    filter: brightness(0.8);
  }
  to {
    opacity: 1;
    transform: translateY(0);
    filter: brightness(1);
  }
}
</style>
