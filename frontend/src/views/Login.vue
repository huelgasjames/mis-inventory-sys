<template>
  <div class="login-container">
    <!-- Full screen university branding -->
    <div class="login-full">
      <img 
        src="/pnc-bg.jpg" 
        alt="University Building" 
        class="login-background"
      > 
    </div>

    <!-- Login Form Overlay -->
    <div class="login-form-overlay">
      <div class="login-form-container">
        <!-- Student Login Header -->
        <div class="text-center mb-4">
          <button class="student-login-header-btn" disabled>
            ADMIN LOGIN
          </button>
        </div>
        
        <!-- University Logo and Title -->
        <div class="text-center mb-4">
          <img src="/pnc-logo.png" alt="University of Cabuyao" class="university-logo-small">
          <h1 class="university-title-small">University of Cabuyao</h1>
          <p class="system-title-small">MiSD Inventory Management System</p>
        </div>
        
        <!-- Login Form -->
        <form @submit.prevent="handleLogin">
          <!-- Student ID Field -->
          <div class="form-group">
            <div class="input-group input-group-lg">
              <span class="input-group-text">
                <i class="bi bi-person"></i>
              </span>
              <input 
                type="text" 
                class="form-control" 
                placeholder="Username / Email"
                v-model="username"
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
            class="login-btn student-login-btn"
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
import '@/assets/login.css'

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
    
    // Call backend API
    const response = await fetch('http://localhost:8000/api/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        username: username.value,
        password: password.value
      })
    })
    
    const data = await response.json()
    
    if (data.success) {
      // Store authentication state
      localStorage.setItem('isAuthenticated', 'true')
      localStorage.setItem('user', JSON.stringify(data.data.user))
      localStorage.setItem('token', data.data.token)
      
      // Redirect to dashboard
      router.push('/dashboard')
    } else {
      errorMessage.value = data.message || 'Login failed. Please try again.'
    }
  } catch (error) {
    console.error('Login error:', error)
    errorMessage.value = 'Network error. Please check your connection and try again.'
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
.login-container {
  display: flex;
  min-height: 100vh;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.login-left {
  flex: 1;
  position: relative;
  overflow: hidden;
}

.login-background {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.login-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(15, 111, 67, 0.9) 0%, rgba(15, 111, 67, 0.7) 100%);
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  padding: 2rem;
  color: white;
}

.university-logo {
  width: 120px;
  height: 120px;
  margin-bottom: 1rem;
  border-radius: 50%;
  border: 3px solid rgba(255, 255, 255, 0.3);
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.university-title {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

.system-title {
  font-size: 1.5rem;
  font-weight: 300;
  opacity: 0.9;
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
}

.login-right {
  flex: 1;
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 2rem;
}

.login-form-container {
  background: white;
  border-radius: 20px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
  padding: 3rem;
  width: 100%;
  max-width: 450px;
  border: 1px solid rgba(0, 0, 0, 0.1);
}

.student-login-header-btn {
  background: linear-gradient(135deg, #0F6F43 0%, #0a5234 100%);
  color: white;
  border: none;
  padding: 0.75rem 2rem;
  border-radius: 50px;
  font-weight: 600;
  font-size: 0.9rem;
  letter-spacing: 1px;
  box-shadow: 0 4px 15px rgba(15, 111, 67, 0.3);
  margin-bottom: 2rem;
}

.university-logo-small {
  width: 80px;
  height: 80px;
  margin-bottom: 1rem;
  border-radius: 50%;
  border: 2px solid #0F6F43;
  box-shadow: 0 4px 15px rgba(15, 111, 67, 0.2);
}

.university-title-small {
  font-size: 1.5rem;
  font-weight: 700;
  color: #0F6F43;
  margin-bottom: 0.5rem;
}

.system-title-small {
  font-size: 1rem;
  color: #6c757d;
  margin-bottom: 0;
}

.form-group {
  margin-bottom: 1.5rem;
}

.input-group-lg {
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

.input-group-lg:focus-within {
  box-shadow: 0 4px 20px rgba(15, 111, 67, 0.2);
  border-color: #0F6F43;
}

.input-group-text {
  background: linear-gradient(135deg, #0F6F43 0%, #0a5234 100%);
  color: white;
  border: none;
  padding: 0 1rem;
  font-size: 1.2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 60px;
}

.form-control {
  border: none;
  padding: 1rem;
  font-size: 1rem;
  background: #f8f9fa;
  transition: all 0.3s ease;
}

.form-control:focus {
  background: white;
  box-shadow: none;
  outline: none;
}

.form-control::placeholder {
  color: #6c757d;
  font-style: italic;
}

.forgot-password {
  color: #0F6F43;
  text-decoration: none;
  font-size: 0.9rem;
  font-weight: 500;
  transition: all 0.3s ease;
}

.forgot-password:hover {
  color: #0a5234;
  text-decoration: underline;
}

.login-btn {
  width: 100%;
  padding: 1rem;
  font-size: 1.1rem;
  font-weight: 600;
  border-radius: 12px;
  border: none;
  background: linear-gradient(135deg, #0F6F43 0%, #0a5234 100%);
  color: white;
  letter-spacing: 1px;
  box-shadow: 0 4px 20px rgba(15, 111, 67, 0.3);
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.login-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 25px rgba(15, 111, 67, 0.4);
  background: linear-gradient(135deg, #0a5234 0%, #0F6F43 100%);
}

.login-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

.spinner-border-sm {
  width: 1rem;
  height: 1rem;
  border-width: 2px;
}

.alert-danger {
  background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
  border: 1px solid #f5c6cb;
  color: #721c24;
  border-radius: 12px;
  padding: 1rem;
  margin-top: 1rem;
  box-shadow: 0 2px 10px rgba(220, 53, 69, 0.1);
}

/* Responsive Design */
@media (max-width: 768px) {
  .login-container {
    flex-direction: column;
  }
  
  .login-left {
    min-height: 40vh;
  }
  
  .login-right {
    padding: 1rem;
  }
  
  .login-form-container {
    padding: 2rem;
    margin: 1rem;
  }
  
  .university-title {
    font-size: 2rem;
  }
  
  .system-title {
    font-size: 1.2rem;
  }
  
  .university-title-small {
    font-size: 1.3rem;
  }
}

@media (max-width: 480px) {
  .login-form-container {
    padding: 1.5rem;
  }
  
  .university-logo-small {
    width: 60px;
    height: 60px;
  }
  
  .university-title-small {
    font-size: 1.1rem;
  }
  
  .system-title-small {
    font-size: 0.9rem;
  }
}

/* Animation for form inputs */
.form-group {
  animation: slideInUp 0.6s ease-out;
}

@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.form-group:nth-child(1) { animation-delay: 0.1s; }
.form-group:nth-child(2) { animation-delay: 0.2s; }
.form-group:nth-child(3) { animation-delay: 0.3s; }
.form-group:nth-child(4) { animation-delay: 0.4s; }
</style>
