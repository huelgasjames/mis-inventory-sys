import { createRouter, createWebHistory } from 'vue-router'
import Login from '@/views/Login.vue'
import Dashboard from '@/views/Dashboard.vue'
import Components from '@/views/Components.vue'
import Computers from '@/views/Computers.vue'
import Deployment from '@/views/Deployment.vue'
import Processors from '@/views/Processors.vue'
import ComponentManager from '@/views/ComponentManager.vue'
import Users from '@/views/Users.vue'

const routes = [
  {
    path: '/',
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
    component: ComponentManager,
    meta: { requiresAuth: true }
  },
  {
    path: '/rams',
    name: 'RAM',
    component: ComponentManager,
    meta: { requiresAuth: true }
  },
  {
    path: '/storage',
    name: 'Storage',
    component: ComponentManager,
    meta: { requiresAuth: true }
  },
  {
    path: '/video-cards',
    name: 'VideoCards',
    component: ComponentManager,
    meta: { requiresAuth: true }
  },
  {
    path: '/psus',
    name: 'PSUs',
    component: ComponentManager,
    meta: { requiresAuth: true }
  },
  {
    path: '/dvd-roms',
    name: 'DVDROMs',
    component: ComponentManager,
    meta: { requiresAuth: true }
  },
  {
    path: '/departments',
    name: 'Departments',
    component: ComponentManager,
    meta: { requiresAuth: true }
  },
  {
    path: '/department-inventory',
    name: 'DepartmentInventory',
    component: ComponentManager,
    meta: { requiresAuth: true }
  },
  {
    path: '/laboratory-management',
    name: 'LaboratoryManagement',
    component: ComponentManager,
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
    component: ComponentManager,
    meta: { requiresAuth: true }
  },
  {
    path: '/settings',
    name: 'Settings',
    component: ComponentManager,
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
  
  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/')
  } else if (to.path === '/' && isAuthenticated) {
    next('/dashboard')
  } else {
    next()
  }
})

export default router
