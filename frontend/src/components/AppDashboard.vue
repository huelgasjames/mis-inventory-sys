<template>
  <div class="container-fluid p-4">
    <!-- Stats Cards -->
    <section class="mb-4">
      <div class="row g-3">
        <div class="col-lg-3 col-md-6">
          <div class="card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
              <div class="flex-shrink-0">
                <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                  <i class="bi bi-pc-display text-primary fs-4"></i>
                </div>
              </div>
              <div class="flex-grow-1">
                <h3 class="card-title h4 mb-1 fw-bold">{{ totalAssets }}</h3>
                <p class="card-text text-muted mb-0">Total Assets</p>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
          <div class="card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
              <div class="flex-shrink-0">
                <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                  <i class="bi bi-building text-danger fs-4"></i>
                </div>
              </div>
              <div class="flex-grow-1">
                <h3 class="card-title h4 mb-1 fw-bold">{{ totalDepartments }}</h3>
                <p class="card-text text-muted mb-0">Departments</p>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
          <div class="card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
              <div class="flex-shrink-0">
                <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                  <i class="bi bi-people text-info fs-4"></i>
                </div>
              </div>
              <div class="flex-grow-1">
                <h3 class="card-title h4 mb-1 fw-bold">{{ totalUsers }}</h3>
                <p class="card-text text-muted mb-0">Users</p>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
          <div class="card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
              <div class="flex-shrink-0">
                <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                  <i class="bi bi-check-circle text-success fs-4"></i>
                </div>
              </div>
              <div class="flex-grow-1">
                <h3 class="card-title h4 mb-1 fw-bold">{{ workingAssets }}</h3>
                <p class="card-text text-muted mb-0">Working</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Main Content Grid -->
    <section class="mb-4">
      <div class="row g-4">
        <!-- Recent Assets -->
        <div class="col-lg-8">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0 fw-semibold">Recent Assets</h5>
              <router-link to="/assets" class="btn btn-sm btn-outline-primary">View All</router-link>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Asset Tag</th>
                      <th>Name</th>
                      <th>Status</th>
                      <th>Department</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="asset in recentAssets" :key="asset.id">
                      <td>{{ asset.asset_tag }}</td>
                      <td>{{ asset.computer_name }}</td>
                      <td>
                        <span :class="getStatusBadgeClass(asset.status)" class="badge">
                          {{ asset.status }}
                        </span>
                      </td>
                      <td>{{ asset.department?.name || '-' }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-4">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
              <h5 class="card-title mb-0 fw-semibold">Quick Actions</h5>
            </div>
            <div class="card-body">
              <div class="d-grid gap-2">
                <router-link to="/assets" class="btn btn-outline-primary d-flex align-items-center justify-content-start">
                  <i class="bi bi-pc-display me-2"></i>
                  <span>Manage Assets</span>
                </router-link>
                <router-link to="/departments" class="btn btn-outline-primary d-flex align-items-center justify-content-start">
                  <i class="bi bi-building me-2"></i>
                  <span>Manage Departments</span>
                </router-link>
                <router-link to="/users" class="btn btn-outline-primary d-flex align-items-center justify-content-start">
                  <i class="bi bi-people me-2"></i>
                  <span>Manage Users</span>
                </router-link>
                <router-link to="/computers" class="btn btn-outline-primary d-flex align-items-center justify-content-start">
                  <i class="bi bi-laptop me-2"></i>
                  <span>Manage Computers</span>
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  assets: {
    type: Array,
    default: () => []
  },
  departments: {
    type: Array,
    default: () => []
  },
  users: {
    type: Array,
    default: () => []
  }
})

const totalAssets = computed(() => props.assets.length)
const totalDepartments = computed(() => props.departments.length)
const totalUsers = computed(() => props.users.length)
const workingAssets = computed(() => props.assets.filter(a => a.status === 'Working').length)
const recentAssets = computed(() => props.assets.slice(0, 5))

function getStatusBadgeClass(status) {
  switch (status) {
    case 'Working':
      return 'bg-success text-white'
    case 'Defective':
      return 'bg-danger text-white'
    case 'For Disposal':
      return 'bg-warning text-dark'
    default:
      return 'bg-secondary text-white'
  }
}
</script>
