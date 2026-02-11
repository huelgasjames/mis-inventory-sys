<template>
  <div class="login-container">
    <!-- Left side with university branding -->
    <div class="login-left">
      <img 
        src="/pnc-bg-1.jpg" 
        alt="University Building" 
        class="login-background"
      >
      <div class="login-overlay">
        <img src="/pnc-logo.png" alt="University Seal" class="university-logo">
        <h1 class="university-title">University of Cabuyao</h1>
        <h2 class="system-title">MiSD Inventory System</h2>
      </div>
    </div>

    <!-- Right side with login form -->
    <div class="login-right">
      <div class="login-form-container">
        <!-- Login Form -->
        <form @submit.prevent="handleLogin">
          <!-- Username Field -->
          <div class="form-group">
            <div class="input-group input-group-lg">
              <span class="input-group-text">
                <i class="bi bi-person"></i>
              </span>
              <input 
                type="text" 
                class="form-control" 
                placeholder="Username"
                v-model="username"
                value="2001746"
                required
              >
            </div>
          </div>

          <!-- Password Field -->
          <div class="form-group">
            <div class="input-group input-group-lg">
              <span class="input-group-text">
                <i class="bi bi-lock"></i>
              </span>
              <input 
                :type="showPassword ? 'text' : 'password'" 
                class="form-control" 
                placeholder="Password"
                v-model="password"
                required
              >
              <button 
                type="button" 
                class="input-group-text"
                @click="showPassword = !showPassword"
              >
                <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
              </button>
            </div>
          </div>

          <!-- Forgot Password Link -->
          <div class="text-end mb-4">
            <a href="#" class="forgot-password">
              Forgot Password ?
            </a>
          </div>

          <!-- Login Button -->
          <button 
            type="submit" 
            class="login-btn"
            :disabled="isLoading"
          >
            <span v-if="isLoading">
              <span class="spinner-border spinner-border-sm me-2"></span>
              Logging in...
            </span>
            <span v-else>
              LOGIN
            </span>
          </button>
        </form>

        <!-- Error Message -->
        <div v-if="errorMessage" class="alert alert-danger">
          <i class="bi bi-exclamation-triangle me-2"></i>
          {{ errorMessage }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const username = ref('')
const password = ref('')
const showPassword = ref(false)
const isLoading = ref(false)
const errorMessage = ref('')

const handleLogin = async () => {
  try {
    isLoading.value = true
    errorMessage.value = ''
    
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1500))
    
    // Simple validation (in real app, this would be an API call)
    if (username.value && password.value) {
      // Store authentication state
      localStorage.setItem('isAuthenticated', 'true')
      localStorage.setItem('user', JSON.stringify({
        username: username.value,
        role: 'student'
      }))
      
      // Redirect to dashboard
      router.push('/dashboard')
    } else {
      errorMessage.value = 'Please enter both username and password'
    }
  } catch (error) {
    errorMessage.value = 'Login failed. Please try again.'
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
/* Login page uses custom CSS from main.css */
</style>
