<template>
  <div class="min-vh-100 bg-light">
    <!-- Header -->
    <header class="bg-white shadow-sm border-bottom">
      <div class="container-fluid">
        <div class="row align-items-center py-3">
          <div class="col">
            <h1 class="h3 mb-0 fw-bold text-dark">Inventory Dashboard</h1>
          </div>
          <div class="col-auto">
            <div class="d-flex align-items-center gap-3">
              <button class="btn btn-outline-secondary btn-sm position-relative">
                <i class="bi bi-bell"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                  3
                </span>
              </button>
              <div class="dropdown">
                <button class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center" 
                        style="width: 40px; height: 40px;" 
                        data-bs-toggle="dropdown">
                  <span class="fw-bold">A</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li><a class="dropdown-item" href="#">Profile</a></li>
                  <li><a class="dropdown-item" href="#">Settings</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="#" @click="logout">Logout</a></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="container-fluid py-4">
      <!-- Stats Grid -->
      <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
          <div class="card shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <h6 class="card-subtitle mb-2 text-muted">Total Products</h6>
                  <h3 class="card-title fw-bold">{{ stats.totalProducts.toLocaleString() }}</h3>
                  <small class="text-success">
                    <i class="bi bi-arrow-up"></i> +12% from last month
                  </small>
                </div>
                <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                  <i class="bi bi-box text-primary fs-4"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <div class="card shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <h6 class="card-subtitle mb-2 text-muted">Low Stock Items</h6>
                  <h3 class="card-title fw-bold text-danger">{{ stats.lowStock }}</h3>
                  <small class="text-danger">
                    <i class="bi bi-exclamation-triangle"></i> Needs attention
                  </small>
                </div>
                <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                  <i class="bi bi-exclamation-triangle text-danger fs-4"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <div class="card shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <h6 class="card-subtitle mb-2 text-muted">Total Value</h6>
                  <h3 class="card-title fw-bold">${{ stats.totalValue.toLocaleString() }}</h3>
                  <small class="text-success">
                    <i class="bi bi-arrow-up"></i> +8% from last month
                  </small>
                </div>
                <div class="bg-success bg-opacity-10 rounded-circle p-3">
                  <i class="bi bi-currency-dollar text-success fs-4"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <div class="card shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <h6 class="card-subtitle mb-2 text-muted">Orders Today</h6>
                  <h3 class="card-title fw-bold">{{ stats.ordersToday }}</h3>
                  <small class="text-primary">
                    <i class="bi bi-arrow-up"></i> +23% from yesterday
                  </small>
                </div>
                <div class="bg-info bg-opacity-10 rounded-circle p-3">
                  <i class="bi bi-bag text-info fs-4"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Activity and Quick Actions -->
      <div class="row g-4">
        <!-- Recent Activity -->
        <div class="col-lg-8">
          <div class="card shadow-sm">
            <div class="card-header bg-white border-bottom">
              <h5 class="card-title mb-0">Recent Activity</h5>
            </div>
            <div class="card-body">
              <div class="list-group list-group-flush">
                <div v-for="activity in recentActivity" :key="activity.id" 
                     class="list-group-item d-flex justify-content-between align-items-center px-0">
                  <div class="d-flex align-items-center">
                    <div :class="`rounded-circle p-2 me-3 ${activity.color}`">
                      <i :class="`${activity.iconColor} ${activity.iconClass}`"></i>
                    </div>
                    <div>
                      <h6 class="mb-1 fw-semibold">{{ activity.title }}</h6>
                      <small class="text-muted">{{ activity.description }}</small>
                    </div>
                  </div>
                  <small class="text-muted">{{ activity.time }}</small>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-4">
          <div class="card shadow-sm">
            <div class="card-header bg-white border-bottom">
              <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
              <div class="d-grid gap-2">
                <button class="btn btn-primary">
                  <i class="bi bi-plus-circle me-2"></i>Add New Product
                </button>
                <button class="btn btn-outline-primary">
                  <i class="bi bi-file-earmark-text me-2"></i>Generate Report
                </button>
                <button class="btn btn-outline-primary">
                  <i class="bi bi-upload me-2"></i>Import Products
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const stats = ref({
  totalProducts: 1248,
  lowStock: 23,
  totalValue: 456789,
  ordersToday: 47
})

const recentActivity = ref([
  {
    id: 1,
    title: 'New product added',
    description: 'Laptop Model X-2000 added to inventory',
    time: '2 hours ago',
    color: 'bg-primary bg-opacity-10',
    iconColor: 'text-primary',
    iconClass: 'bi bi-plus-circle'
  },
  {
    id: 2,
    title: 'Low stock alert',
    description: 'Wireless Mouse running low (5 units left)',
    time: '4 hours ago',
    color: 'bg-danger bg-opacity-10',
    iconColor: 'text-danger',
    iconClass: 'bi bi-exclamation-triangle'
  },
  {
    id: 3,
    title: 'Order completed',
    description: 'Order #12345 has been processed',
    time: '6 hours ago',
    color: 'bg-success bg-opacity-10',
    iconColor: 'text-success',
    iconClass: 'bi bi-check-circle'
  },
  {
    id: 4,
    title: 'Inventory updated',
    description: 'Batch update for 50 products completed',
    time: '1 day ago',
    color: 'bg-info bg-opacity-10',
    iconColor: 'text-info',
    iconClass: 'bi bi-arrow-clockwise'
  }
])

const logout = () => {
  localStorage.removeItem('isAuthenticated')
  localStorage.removeItem('user')
  router.push('/')
}
</script>
