import { createRouter, createWebHistory } from 'vue-router'
import Login from '@/views/Login.vue'
import Dashboard from '@/views/Dashboard.vue'
import Components from '@/views/Components.vue'
import Computers from '@/views/Computers.vue'
import Deployment from '@/views/Deployment.vue'
import Processors from '@/views/Processors.vue'
import Motherboards from '@/views/computerComponents/Motherboards.vue'
import RAM from '@/views/computerComponents/RAM.vue'
import Storage from '@/views/computerComponents/Storage.vue'
import VideoCards from '@/views/computerComponents/VideoCards.vue'
import PSUs from '@/views/computerComponents/PSUs.vue'
import DVDROMs from '@/views/computerComponents/DVDROMs.vue'
import Users from '@/views/Users.vue'
import Departments from '@/views/Departments.vue'
import LaboratoryManagement from '@/views/LaboratoryManagement.vue'
import Reports from '@/views/Reports.vue'
import Settings from '@/views/Settings.vue'


const routes = [
  {
    path: '/',
    redirect: '/login'
  },
  {
    path: '/login',
    name: 'Login',
    component: Login
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/components',
    name: 'Components',
    component: Components,
    meta: { requiresAuth: true }
  },
  {
    path: '/computers',
    name: 'Computers',
    component: Computers,
    meta: { requiresAuth: true }
  },
  {
    path: '/deployment',
    name: 'Deployment',
    component: Deployment,
    meta: { requiresAuth: true }
  },
  {
    path: '/processors',
    name: 'Processors',
    component: Processors,
    meta: { requiresAuth: true }
  },
  {
    path: '/motherboards',
    name: 'Motherboards',
    component: Motherboards,
    meta: { requiresAuth: true }
  },
  {
    path: '/rams',
    name: 'RAM',
    component: RAM,
    meta: { requiresAuth: true }
  },
  {
    path: '/storage',
    name: 'Storage',
    component: Storage,
    meta: { requiresAuth: true }
  },
  {
    path: '/video-cards',
    name: 'VideoCards',
    component: VideoCards,
    meta: { requiresAuth: true }
  },
  {
    path: '/psus',
    name: 'PSUs',
    component: PSUs,
    meta: { requiresAuth: true }
  },
  {
    path: '/dvd-roms',
    name: 'DVDROMs',
    component: DVDROMs,
    meta: { requiresAuth: true }
  },
  {
    path: '/departments',
    name: 'Departments',
    component: Departments,
    meta: { requiresAuth: true }
  },
  {
    path: '/laboratory-management',
    name: 'LaboratoryManagement',
    component: LaboratoryManagement,
    meta: { requiresAuth: true }
  },
  {
    path: '/department-inventory',
    name: 'DepartmentInventory',
    component: Departments,
    meta: { requiresAuth: true }
  },
  {
    path: '/users',
    name: 'Users',
    component: Users,
    meta: { requiresAuth: true }
  },
  {
    path: '/reports',
    name: 'Reports',
    component: Reports,
    meta: { requiresAuth: true }
  },
  {
    path: '/settings',
    name: 'Settings',
    component: Settings,
    meta: { requiresAuth: true }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation guard for protected routes
router.beforeEach((to, from, next) => {
  const isAuthenticated = localStorage.getItem('isAuthenticated')
  const token = localStorage.getItem('token')
  const user = localStorage.getItem('user')
  
  // Check if user is actually authenticated (has token and user data)
  const isFullyAuthenticated = isAuthenticated === 'true' && token && user
  
  // If trying to access protected route without authentication
  if (to.meta.requiresAuth && !isFullyAuthenticated) {
    // Clear any invalid authentication data
    localStorage.removeItem('isAuthenticated')
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    sessionStorage.removeItem('isAuthenticated')
    sessionStorage.removeItem('token')
    sessionStorage.removeItem('user')
    next('/')
  } 
  // If already authenticated and trying to access login page
  else if (to.path === '/' && isFullyAuthenticated) {
    next('/dashboard')
  }
  // Allow access to login page or other routes
  else {
    next()
  }
})

export default router
