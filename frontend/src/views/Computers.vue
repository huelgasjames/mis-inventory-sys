<template>
  <div class="dashboard-layout">
    <!-- Loading Spinner -->
    <LoadingSpinner :is-visible="isLoading" message="Loading computers..." />

    <!-- Navigation Sidebar -->
    <AppNav :is-collapsed="isNavCollapsed" />
    
    <!-- Main Content Area -->
    <div class="main-content" :class="{ 'collapsed': isNavCollapsed }">
      <!-- Header -->
      <AppHeader 
        :is-collapsed="isNavCollapsed"
        @sidebar-toggle="(collapsed) => {
          console.log('Computers received sidebar-toggle:', collapsed)
          isNavCollapsed = collapsed
        }"
        @profile-open="openProfile"
        @settings-open="openSettings"
      />
      
      <!-- Computers Content -->
      <div class="container-fluid p-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h1 class="h3 mb-0" style="color: black;">Computer Management</h1>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" @click="refreshData">
              <i class="bi bi-arrow-clockwise me-2"></i>Refresh
            </button>
            <button class="btn btn-success" @click="showCreateModal">
              <i class="bi bi-plus-circle me-2"></i>Add Computer
            </button>
            <button class="btn btn-info" @click="exportComputers">
              <i class="bi bi-download me-2"></i>Export
            </button>
          </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
          <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                    <i class="bi bi-pc-display text-primary fs-4"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Total Computers</h6>
                    <h3 class="mb-0">{{ totalComputers }}</h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                    <i class="bi bi-check-circle text-success fs-4"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Working</h6>
                    <h3 class="mb-0">{{ workingComputers }}</h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                    <i class="bi bi-x-circle text-danger fs-4"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Defective</h6>
                    <h3 class="mb-0">{{ defectiveComputers }}</h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                    <i class="bi bi-exclamation-triangle text-warning fs-4"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">For Disposal</h6>
                    <h3 class="mb-0">{{ disposalComputers }}</h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Search and Filter -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">
            <div class="row g-3 align-items-end">
              <div class="col-md-4">
                <label class="form-label">Search Computers</label>
                <input type="text" class="form-control" placeholder="Search by name or asset tag..." v-model="searchQuery">
              </div>
              <div class="col-md-3">
                <label class="form-label">Department</label>
                <select class="form-select" v-model="departmentFilter">
                  <option value="">All Departments</option>
                  <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label">Status</label>
                <select class="form-select" v-model="statusFilter">
                  <option value="">All Status</option>
                  <option value="Working">Working</option>
                  <option value="Defective">Defective</option>
                  <option value="For Disposal">For Disposal</option>
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button class="btn btn-outline-secondary w-100" @click="clearFilters">
                  <i class="bi bi-x-circle me-2"></i>Clear
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Computer Table -->
        <div class="card border-0 shadow-sm">
          <div class="card-body p-0">
            <!-- Computer Table -->
            <div v-if="filteredComputers.length > 0">
              <div class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Asset Tag</th>
                      <th>PC Number</th>
                      <th>Computer Name</th>
                      <th>Department</th>
                      <th>Status</th>
                      <th>Deployed to</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="computer in filteredComputers" :key="computer.id">
                      <td>
                        <span class="badge bg-secondary">{{ computer.asset_tag }}</span>
                      </td>
                      <td>{{ computer.pc_number }}</td>
                      <td>
                        <strong>{{ computer.computer_name }}</strong>
                      </td>
                      <td>{{ computer.department?.name || 'N/A' }}</td>
                      <td>
                        <span :class="getStatusBadgeClass(computer.status)">
                          {{ computer.status }}
                        </span>
                        <span v-if="computer.is_deployed || computer.laboratory_id" class="badge bg-info ms-2">
                          <i class="bi bi-box-arrow-right me-1"></i>Deployed
                        </span>
                      </td>
                      <td>
                        <span v-if="computer.laboratory" class="badge bg-primary">
                          <i class="bi bi-house-door me-1"></i>
                          {{ computer.laboratory.lab_name }}
                        </span>
                        <span v-else-if="computer.assigned_user" class="badge bg-success">
                          <i class="bi bi-person me-1"></i>
                          {{ computer.assigned_user.name }}
                        </span>
                        <span v-else class="text-muted">
                          <i class="bi bi-dash-circle me-1"></i>Not Deployed
                        </span>
                      </td>
                      <td>
                        <div class="btn-group btn-group-sm">
                          <button 
                            class="btn btn-outline-primary" 
                            @click="viewComputer(computer)"
                            title="View Details"
                          >
                            <i class="bi bi-eye"></i>
                          </button>
                          <button 
                            class="btn btn-outline-warning" 
                            @click="editComputer(computer)"
                            title="Edit"
                          >
                            <i class="bi bi-pencil"></i>
                          </button>
                          <button 
                            class="btn btn-outline-success" 
                            @click="deployComputer(computer)"
                            title="Deploy"
                            :disabled="computer.status !== 'Working' || computer.is_deployed || computer.laboratory_id"
                          >
                            <i class="bi bi-box-arrow-right"></i>
                          </button>
                          <button 
                            class="btn btn-outline-danger" 
                            @click="deleteComputer(computer.id)"
                            title="Delete"
                          >
                            <i class="bi bi-trash"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-5">
              <div class="mb-4">
                <i class="bi bi-pc-display text-muted" style="font-size: 4rem;"></i>
              </div>
              <h5 class="text-muted">No computers found</h5>
              <p class="text-muted">
                {{ searchQuery || statusFilter || departmentFilter 
                  ? 'Try adjusting your search or filters' 
                  : 'Get started by adding your first computer' }}
              </p>
              <button 
                v-if="!searchQuery && !statusFilter && !departmentFilter" 
                class="btn btn-primary"
                @click="showCreateModal"
              >
                <i class="bi bi-plus-circle me-2"></i>Add Computer
              </button>
            </div>
          </div>
        </div>

        <!-- Department Overview -->
        <div class="row g-4 mt-4">
          <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-white border-bottom">
                <h5 class="card-title mb-0">Department Overview</h5>
              </div>
              <div class="card-body">
                <div class="row g-3">
                  <div v-for="dept in departmentStats" :key="dept.id" class="col-md-6">
                    <div class="card border h-100">
                      <div class="card-body text-center">
                        <h6 class="card-title">{{ dept.name }}</h6>
                        <div class="mb-2">
                          <span v-if="dept.category" class="badge" :style="{ backgroundColor: dept.category.color + '20', color: dept.category.color }">
                            {{ dept.category.name }}
                          </span>
                        </div>
                        <div class="row text-center">
                          <div class="col-6">
                            <h4 class="text-primary mb-0">{{ dept.total_computers }}</h4>
                            <small class="text-muted">Total</small>
                          </div>
                          <div class="col-6">
                            <h4 class="text-success mb-0">{{ dept.working }}</h4>
                            <small class="text-muted">Working</small>
                          </div>
                        </div>
                        <div class="row text-center">
                          <div class="col-6">
                            <h4 class="text-danger mb-0">{{ dept.defective }}</h4>
                            <small class="text-muted">Defective</small>
                          </div>
                          <div class="col-6">
                            <h4 class="text-warning mb-0">{{ dept.disposal }}</h4>
                            <small class="text-muted">Disposal</small>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Component Overview -->
          <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-white border-bottom">
                <h5 class="card-title mb-0">Component Distribution</h5>
              </div>
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-md-6">
                    <div class="card border h-100">
                      <div class="card-body text-center">
                        <h6 class="card-title">Processors</h6>
                        <div class="mb-2">
                          <i class="bi bi-cpu text-primary fs-3"></i>
                        </div>
                        <h4 class="text-primary mb-0">{{ componentStats.processors }}</h4>
                        <small class="text-muted">Total</small>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="card border h-100">
                      <div class="card-body text-center">
                        <h6 class="card-title">RAM</h6>
                        <div class="mb-2">
                          <i class="bi bi-memory text-info fs-3"></i>
                        </div>
                        <h4 class="text-info mb-0">{{ componentStats.ram }}</h4>
                        <small class="text-muted">Total</small>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="card border h-100">
                      <div class="card-body text-center">
                        <h6 class="card-title">Storage</h6>
                        <div class="mb-2">
                          <i class="bi bi-hdd text-success fs-3"></i>
                        </div>
                        <h4 class="text-success mb-0">{{ componentStats.storage }}</h4>
                        <small class="text-muted">Total</small>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="card border h-100">
                      <div class="card-body text-center">
                        <h6 class="card-title">Video Cards</h6>
                        <div class="mb-2">
                          <i class="bi bi-gpu text-warning fs-3"></i>
                        </div>
                        <h4 class="text-warning mb-0">{{ componentStats.video_cards }}</h4>
                        <small class="text-muted">Total</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Create Computer Modal -->
  <div class="modal fade" id="createComputerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Computer</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="createComputer">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Computer Name *</label>
                <input type="text" class="form-control" v-model="newComputer.computer_name" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Asset Tag *</label>
                <div class="input-group">
                  <input type="text" class="form-control" v-model="newComputer.asset_tag" required readonly>
                  <button type="button" class="btn btn-outline-secondary" @click="generateAssetTag">
                    <i class="bi bi-arrow-clockwise"></i> Generate
                  </button>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label">PC Number *</label>
                <input type="text" class="form-control" v-model="newComputer.pc_number" placeholder="e.g., PC-001" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Serial Number</label>
                <input type="text" class="form-control" v-model="newComputer.serial_number">
              </div>
              <div class="col-md-6">
                <label class="form-label">Department *</label>
                <select class="form-select" v-model="newComputer.department_id" required>
                  <option value="">Select Department</option>
                  <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Processor</label>
                <select class="form-select" v-model="newComputer.processor_id">
                  <option value="">Select Processor</option>
                  <option v-for="cpu in components.processors" :key="cpu.id" :value="cpu.id">{{ cpu.model }}</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Motherboard</label>
                <select class="form-select" v-model="newComputer.motherboard_id">
                  <option value="">Select Motherboard</option>
                  <option v-for="mb in components.motherboards" :key="mb.id" :value="mb.id">{{ mb.model }}</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">RAM</label>
                <select class="form-select" v-model="newComputer.ram_id">
                  <option value="">Select RAM</option>
                  <option v-for="ram in components.rams" :key="ram.id" :value="ram.id">{{ ram.capacity }}</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Storage</label>
                <select class="form-select" v-model="newComputer.storage_id">
                  <option value="">Select Storage</option>
                  <option v-for="storage in components.storages" :key="storage.id" :value="storage.id">{{ storage.capacity }}</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">GPU</label>
                <select class="form-select" v-model="newComputer.video_card_id">
                  <option value="">Select GPU</option>
                  <option v-for="gpu in components.video_cards" :key="gpu.id" :value="gpu.id">{{ gpu.model }}</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">PSU</label>
                <select class="form-select" v-model="newComputer.psu_id">
                  <option value="">Select PSU</option>
                  <option v-for="psu in components.psus" :key="psu.id" :value="psu.id">{{ psu.wattage }}W</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">DVD ROM</label>
                <select class="form-select" v-model="newComputer.dvd_rom_id">
                  <option value="">Select DVD ROM</option>
                  <option v-for="dvd in components.dvd_roms" :key="dvd.id" :value="dvd.id">{{ dvd.model }}</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Status *</label>
                <select class="form-select" v-model="newComputer.status" required>
                  <option value="Working">Working</option>
                  <option value="Defective">Defective</option>
                  <option value="For Disposal">For Disposal</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Assigned To</label>
                <select class="form-select" v-model="newComputer.assigned_to">
                  <option value="">Unassigned</option>
                  <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                </select>
              </div>
              <div class="col-md-12">
                <label class="form-label">Location</label>
                <input type="text" class="form-control" v-model="newComputer.location" placeholder="Room/Office">
              </div>
              <div class="col-md-12">
                <label class="form-label">Description</label>
                <textarea class="form-control" v-model="newComputer.description" rows="3"></textarea>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" @click="createComputer">Add Computer</button>
        </div>
      </div>
    </div>
  </div>

  <!-- View Computer Modal -->
  <div class="modal fade" id="viewComputerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Computer Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" v-if="selectedComputer">
          <div class="row g-3">
            <!-- Left Column - Computer Details -->
            <div class="col-md-8">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Computer Name</label>
                  <div class="form-control bg-light">{{ selectedComputer.computer_name }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Asset Tag</label>
                  <div class="form-control bg-light">{{ selectedComputer.asset_tag }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Product Number</label>
                  <div class="form-control bg-light">{{ selectedComputer.serial_number || 'N/A' }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Department</label>
                  <div class="form-control bg-light">
                    <span v-if="selectedComputer.department" class="badge bg-light text-dark">
                      {{ selectedComputer.department.name }}
                    </span>
                    <span v-else class="text-muted">No Department</span>
                  </div>
                </div>
                
                <div class="col-md-6">
                  <label class="form-label">Processor</label>
                  <div class="form-control bg-light">
                    <span v-if="selectedComputer.processor" class="badge bg-info">
                      {{ selectedComputer.processor.model }}
                    </span>
                    <span v-else class="text-muted">No Processor</span>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Motherboard</label>
                  <div class="form-control bg-light">
                    <span v-if="selectedComputer.motherboard" class="badge bg-info">
                      {{ selectedComputer.motherboard.model }}
                    </span>
                    <span v-else class="text-muted">No Motherboard</span>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">RAM</label>
                  <div class="form-control bg-light">
                    <span v-if="selectedComputer.ram" class="badge bg-info">
                      {{ selectedComputer.ram.capacity }}
                    </span>
                    <span v-else class="text-muted">No RAM</span>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Storage</label>
                  <div class="form-control bg-light">
                    <span v-if="selectedComputer.storage" class="badge bg-info">
                      {{ selectedComputer.storage.capacity }}
                    </span>
                    <span v-else class="text-muted">No Storage</span>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Video Card</label>
                  <div class="form-control bg-light">
                    <span v-if="selectedComputer.video_card" class="badge bg-info">
                      {{ selectedComputer.video_card.model }}
                    </span>
                    <span v-else class="text-muted">No Video Card</span>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">PSU</label>
                  <div class="form-control bg-light">
                    <span v-if="selectedComputer.psu" class="badge bg-info">
                      {{ selectedComputer.psu.wattage }}W
                    </span>
                    <span v-else class="text-muted">No PSU</span>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">DVD ROM</label>
                  <div class="form-control bg-light">
                    <span v-if="selectedComputer.dvd_rom" class="badge bg-info">
                      {{ selectedComputer.dvd_rom.model }}
                    </span>
                    <span v-else class="text-muted">No DVD ROM</span>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Status</label>
                  <div class="form-control bg-light">
                    <span :class="getStatusBadgeClass(selectedComputer.status)">
                      {{ selectedComputer.status }}
                    </span>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Assigned To</label>
                  <div class="form-control bg-light">
                    <span v-if="selectedComputer.assigned_user" class="badge bg-success">
                      {{ selectedComputer.assigned_user.name }}
                    </span>
                    <span v-else class="text-muted">Unassigned</span>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Location</label>
                  <div class="form-control bg-light">{{ selectedComputer.location || 'Not specified' }}</div>
                </div>
                <div class="col-12">
                  <label class="form-label">Description</label>
                  <div class="form-control bg-light" style="min-height: 80px;">
                    {{ selectedComputer.description || 'No description available' }}
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Right Column - QR Code -->
            <div class="col-md-4">
              <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                  <h6 class="mb-0">
                    <i class="bi bi-qr-code me-2"></i>QR Code
                  </h6>
                </div>
                <div class="card-body text-center">
                  <div class="qr-code-container mb-3">
                    <canvas ref="qrCanvas" width="200" height="200"></canvas>
                  </div>
                  <div class="computer-info-qr">
                    <h6 class="text-primary mb-2">{{ selectedComputer.asset_tag || selectedComputer.pc_number }}</h6>
                    <p class="text-muted mb-1 small">{{ selectedComputer.computer_name }}</p>
                    <p class="text-muted mb-1 small">{{ selectedComputer.department?.name || 'No Department' }}</p>
                    <span :class="getStatusBadgeClass(selectedComputer.status)" class="small">
                      {{ selectedComputer.status }}
                    </span>
                  </div>
                  <div class="mt-3 d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-sm btn-primary" @click="downloadQRCode">
                      <i class="bi bi-download me-1"></i>Download
                    </button>
                    <button type="button" class="btn btn-sm btn-success" @click="printQRCode">
                      <i class="bi bi-printer me-1"></i>Print
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Computer Modal -->
  <div class="modal fade" id="editComputerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Computer</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="updateComputer">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Computer Name *</label>
                <input type="text" class="form-control" v-model="editingComputer.computer_name" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Asset Tag *</label>
                <input type="text" class="form-control" v-model="editingComputer.asset_tag" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">PC Number *</label>
                <input type="text" class="form-control" v-model="editingComputer.pc_number" placeholder="e.g., PC-001" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Serial Number</label>
                <input type="text" class="form-control" v-model="editingComputer.serial_number">
              </div>
              <div class="col-md-6">
                <label class="form-label">Department *</label>
                <select class="form-select" v-model="editingComputer.department_id" required>
                  <option value="">Select Department</option>
                  <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Processor</label>
                <select class="form-select" v-model="editingComputer.processor_id">
                  <option value="">Select Processor</option>
                  <option v-for="cpu in components.processors" :key="cpu.id" :value="cpu.id">{{ cpu.model }}</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Motherboard</label>
                <select class="form-select" v-model="editingComputer.motherboard_id">
                  <option value="">Select Motherboard</option>
                  <option v-for="mb in components.motherboards" :key="mb.id" :value="mb.id">{{ mb.model }}</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">RAM</label>
                <select class="form-select" v-model="editingComputer.ram_id">
                  <option value="">Select RAM</option>
                  <option v-for="ram in components.rams" :key="ram.id" :value="ram.id">{{ ram.capacity }}</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Storage</label>
                <select class="form-select" v-model="editingComputer.storage_id">
                  <option value="">Select Storage</option>
                  <option v-for="storage in components.storages" :key="storage.id" :value="storage.id">{{ storage.capacity }}</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Video Card</label>
                <select class="form-select" v-model="editingComputer.video_card_id">
                  <option value="">Select Video Card</option>
                  <option v-for="gpu in components.video_cards" :key="gpu.id" :value="gpu.id">{{ gpu.model }}</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">PSU</label>
                <select class="form-select" v-model="editingComputer.psu_id">
                  <option value="">Select PSU</option>
                  <option v-for="psu in components.psus" :key="psu.id" :value="psu.id">{{ psu.wattage }}W</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">DVD ROM</label>
                <select class="form-select" v-model="editingComputer.dvd_rom_id">
                  <option value="">Select DVD ROM</option>
                  <option v-for="dvd in components.dvd_roms" :key="dvd.id" :value="dvd.id">{{ dvd.model }}</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Status *</label>
                <select class="form-select" v-model="editingComputer.status" required>
                  <option value="Working">Working</option>
                  <option value="Defective">Defective</option>
                  <option value="For Disposal">For Disposal</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Assigned To</label>
                <select class="form-select" v-model="editingComputer.assigned_to">
                  <option value="">Unassigned</option>
                  <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                </select>
              </div>
              <div class="col-md-12">
                <label class="form-label">Location</label>
                <input type="text" class="form-control" v-model="editingComputer.location" placeholder="Room/Office">
              </div>
              <div class="col-md-12">
                <label class="form-label">Description</label>
                <textarea class="form-control" v-model="editingComputer.description" rows="3"></textarea>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" @click="updateComputer">Update Computer</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Deployment Modal -->
  <div class="modal fade" id="deploymentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Deploy Computer to Laboratory</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Deploy this computer to a specific laboratory room. Only working computers can be deployed.
          </div>
          
          <div class="row g-3 mb-4">
            <div class="col-md-12">
              <label class="form-label">Selected Computer</label>
              <div class="card bg-light">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-4">
                      <strong>Computer Name:</strong> {{ deployingComputer?.computer_name || 'N/A' }}
                    </div>
                    <div class="col-md-4">
                      <strong>Asset Tag:</strong> {{ deployingComputer?.asset_tag || 'N/A' }}
                    </div>
                    <div class="col-md-4">
                      <strong>Status:</strong> 
                      <span :class="getStatusBadgeClass(deployingComputer?.status)">
                        {{ deployingComputer?.status || 'N/A' }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <form @submit.prevent="confirmDeployment">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Department *</label>
                <select class="form-select" v-model="deployment.department_id" @change="onDepartmentChange" required>
                  <option value="">Select Department</option>
                  <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Laboratory</label>
                <select class="form-select" v-model="deployment.laboratory_id">
                  <option value="">Select Laboratory (Optional)</option>
                  <option v-for="lab in filteredLaboratories" :key="lab.id" :value="lab.id">
                    {{ lab.lab_name }} ({{ lab.current_computer_count || 0 }}/{{ lab.capacity }})
                  </option>
                </select>
                <small class="text-muted" v-if="deployment.laboratory_id && selectedLabCapacity">
                  Available: {{ selectedLabCapacity.available }} slots
                </small>
              </div>
              <div class="col-md-6">
                <label class="form-label">PC Number Position *</label>
                <input type="text" class="form-control" v-model="deployment.pc_position" 
                       placeholder="e.g., PC-01, PC-15" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">User</label>
                <select class="form-select" v-model="deployment.user_id">
                  <option value="">Select User (Optional)</option>
                  <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                </select>
              </div>
              <div class="col-md-12">
                <label class="form-label">Location</label>
                <input type="text" class="form-control" v-model="deployment.location" placeholder="Room/Office">
              </div>
              <div class="col-md-12">
                <label class="form-label">Deployment Notes</label>
                <textarea class="form-control" v-model="deployment.notes" rows="3" 
                          placeholder="Any special notes about this deployment..."></textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label">Deployed By</label>
                <input type="text" class="form-control" :value="currentUser?.name || 'Current User'" readonly>
              </div>
              <div class="col-md-6">
                <label class="form-label">Deployment Date</label>
                <input type="date" class="form-control" v-model="deployment.deployment_date" required>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-warning" @click="confirmDeployment">
            <i class="bi bi-box-arrow-right me-2"></i>Deploy Computer
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- QR Code Modal -->
  <QRCodeModal 
    v-if="selectedComputerForQR" 
    :computer="selectedComputerForQR" 
    @close="selectedComputerForQR = null"
  />
</template>

<script setup>

import AppHeader from '@/components/AppHeader.vue'
import AppNav from '@/components/AppNav.vue'
import LoadingSpinner from '@/components/LoadingSpinner.vue'
import QRCodeModal from '@/components/QRCodeModal.vue'
import { ref, computed, onMounted, watch } from 'vue'
import axios from 'axios'
import { Modal } from 'bootstrap'
import { useDarkMode } from '@/composables/useDarkMode.js'

const { initDarkMode } = useDarkMode()

const isNavCollapsed = ref(false)
const isLoading = ref(false)
const loadingStartTime = ref(null)

// Helper function to ensure minimum loading duration
const ensureMinimumLoading = async (minDuration = 5000) => {
  if (loadingStartTime.value) {
    const elapsed = Date.now() - loadingStartTime.value
    if (elapsed < minDuration) {
      await new Promise(resolve => setTimeout(resolve, minDuration - elapsed))
    }
  }
}
// Watch for changes in navigation state
watch(isNavCollapsed, (newValue, oldValue) => {
  console.log('Computers isNavCollapsed changed from', oldValue, 'to', newValue)
})
const computers = ref([])
const departments = ref([])
const components = ref({})
const users = ref([])

const searchQuery = ref('')
const statusFilter = ref('')
const departmentFilter = ref('')

const selectedComputer = ref(null)
const editingComputer = ref({})
const deployingComputer = ref(null)
const selectedComputerForQR = ref(null)
const laboratories = ref([])
const currentUser = ref({ name: 'Current User' })

const deployment = ref({
  department_id: '',
  laboratory_id: '',
  user_id: '',
  pc_position: '',
  location: '',
  notes: '',
  deployment_date: new Date().toISOString().split('T')[0]
})

const newComputer = ref({
  computer_name: '',
  asset_tag: '',
  pc_number: '',
  serial_number: '',
  department_id: '',
  processor_id: '',
  motherboard_id: '',
  ram_id: '',
  storage_id: '',
  video_card_id: '',
  psu_id: '',
  dvd_rom_id: '',
  status: 'Working',
  assigned_to: '',
  location: '',
  description: ''
})

// Statistics computed properties
const totalComputers = computed(() => filteredComputers.value.length)
const workingComputers = computed(() => filteredComputers.value.filter(c => c.status === 'Working').length)
const defectiveComputers = computed(() => filteredComputers.value.filter(c => c.status === 'Defective').length)
const disposalComputers = computed(() => filteredComputers.value.filter(c => c.status === 'For Disposal').length)

// Department statistics
const departmentStats = computed(() => {
  const stats = {}
  departments.value.forEach(dept => {
    stats[dept.id] = {
      name: dept.name,
      category: dept.category,
      total_computers: 0,
      working: 0,
      defective: 0,
      disposal: 0
    }
  })
  
  filteredComputers.value.forEach(computer => {
    if (computer.department_id) {
      if (!stats[computer.department_id]) {
        stats[computer.department_id] = {
          name: computer.department?.name || 'Unknown',
          category: computer.department?.category,
          total_computers: 0,
          working: 0,
          defective: 0,
          disposal: 0
        }
      }
      
      stats[computer.department_id].total_computers++
      if (computer.status === 'Working') stats[computer.department_id].working++
      if (computer.status === 'Defective') stats[computer.department_id].defective++
      if (computer.status === 'For Disposal') stats[computer.department_id].disposal++
    }
  })
  
  return Object.values(stats)
})

// Component statistics
const componentStats = computed(() => {
  return {
    processors: computers.value.filter(c => c.processor_id).length,
    ram: computers.value.filter(c => c.ram_id).length,
    storage: computers.value.filter(c => c.storage_id).length,
    video_cards: computers.value.filter(c => c.video_card_id).length
  }
})

// Laboratory filtering and capacity
const filteredLaboratories = computed(() => {
  if (!deployment.value.department_id) {
    return laboratories.value.filter(lab => lab.is_active)
  }
  return laboratories.value.filter(lab => 
    lab.is_active && lab.department_id === deployment.value.department_id
  )
})

const selectedLabCapacity = computed(() => {
  if (!deployment.value.laboratory_id) return null
  const lab = laboratories.value.find(l => l.id === deployment.value.laboratory_id)
  if (!lab) return null
  return {
    total: lab.capacity,
    current: lab.current_computer_count || 0,
    available: lab.capacity - (lab.current_computer_count || 0)
  }
})

const filteredComputers = computed(() => {
  let filtered = computers.value

  if (searchQuery.value) {
    filtered = filtered.filter(comp => 
      comp.computer_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      comp.asset_tag.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  }

  if (statusFilter.value) {
    filtered = filtered.filter(comp => comp.status === statusFilter.value)
  }

  if (departmentFilter.value) {
    filtered = filtered.filter(comp => comp.department_id == departmentFilter.value)
  }

  return filtered
})

const getStatusBadgeClass = (status) => {
  switch (status) {
    case 'Working':
      return 'badge bg-success'
    case 'Defective':
      return 'badge bg-danger'
    case 'For Disposal':
      return 'badge bg-warning text-dark'
    default:
      return 'badge bg-secondary'
  }
}

const fetchComputers = async () => {
  if (!isLoading.value) {
    isLoading.value = true
    loadingStartTime.value = Date.now()
  }
  try {
    console.log('Fetching computers from API...')
    const response = await axios.get('http://localhost:8000/api/computers?include=laboratory,department,assigned_user')
    console.log('Computers API Response:', response.data)
    computers.value = response.data.data || []
    console.log('Computers loaded:', computers.value.length)
  } catch (error) {
    console.error('Error fetching computers:', error)
    console.log('Error details:', error.response?.data || error.message)
    // Fallback data for demo
    computers.value = [
      {
        id: 1,
        computer_name: 'CS Lab Computer 1',
        asset_tag: 'PC001',
        pc_number: 'PC-001',
        department_id: 1,
        department: { id: 1, name: 'Computer Science', category: { id: 1, name: 'Academic', color: '#0F6F43' } },
        status: 'Working',
        location: 'Lab Room 101',
        laboratory_id: 1,
        laboratory: { id: 1, lab_name: 'Computer Laboratory 1', room_number: 'Room 101' },
        is_deployed: true,
        deployment_status: 'deployed',
        assigned_to: null,
        assigned_user: null,
        description: 'Desktop computer for Computer Science Lab',
        processor: { id: 1, model: 'Intel Core i5-10400' },
        motherboard: { id: 1, model: 'ASUS Prime H410M' },
        ram: { id: 1, capacity: '16GB DDR4' },
        storage: { id: 1, capacity: '512GB SSD' },
        video_card: { id: 1, model: 'NVIDIA GTX 1650' }
      },
      {
        id: 2,
        computer_name: 'Admin Office PC',
        asset_tag: 'PC002',
        pc_number: 'PC-002',
        department_id: 2,
        department: { id: 2, name: 'Administration', category: { id: 2, name: 'Administrative', color: '#004D7A' } },
        status: 'Working',
        location: 'Admin Office',
        laboratory_id: null,
        laboratory: null,
        is_deployed: false,
        deployment_status: 'available',
        assigned_to: 1,
        assigned_user: { id: 1, name: 'Admin User' },
        description: 'Desktop computer for Administration',
        processor: { id: 2, model: 'Intel Core i3-10100' },
        motherboard: { id: 2, model: 'Gigabyte H410M' },
        ram: { id: 2, capacity: '8GB DDR4' },
        storage: { id: 2, capacity: '256GB SSD' },
        video_card: { id: 2, model: 'Integrated' }
      },
      {
        id: 3,
        computer_name: 'Library PC 1',
        asset_tag: 'PC003',
        pc_number: 'PC-003',
        department_id: 3,
        department: { id: 3, name: 'Library', category: { id: 1, name: 'Academic', color: '#0F6F43' } },
        status: 'Defective',
        location: 'Library Room 201',
        laboratory_id: null,
        laboratory: null,
        is_deployed: false,
        deployment_status: 'available',
        assigned_to: null,
        assigned_user: null,
        description: 'Desktop computer for Library',
        processor: { id: 3, model: 'Intel Core i3-9100' },
        motherboard: { id: 3, model: 'MSI H410M' },
        ram: { id: 3, capacity: '8GB DDR4' },
        storage: { id: 3, capacity: '1TB HDD' },
        video_card: { id: 3, model: 'Integrated' }
      },
      {
        id: 4,
        computer_name: 'CS Lab Computer 2',
        asset_tag: 'PC004',
        pc_number: 'PC-004',
        department_id: 1,
        department: { id: 1, name: 'Computer Science', category: { id: 1, name: 'Academic', color: '#0F6F43' } },
        status: 'Working',
        location: 'Lab Room 102',
        laboratory_id: 2,
        laboratory: { id: 2, lab_name: 'Computer Laboratory 2', room_number: 'Room 102' },
        is_deployed: true,
        deployment_status: 'deployed',
        assigned_to: null,
        assigned_user: null,
        description: 'Desktop computer for Computer Science Lab',
        processor: { id: 4, model: 'Intel Core i5-11400' },
        motherboard: { id: 4, model: 'ASUS Prime H510M' },
        ram: { id: 4, capacity: '16GB DDR4' },
        storage: { id: 4, capacity: '1TB SSD' },
        video_card: { id: 4, model: 'NVIDIA GTX 1660' }
      }
    ]
  } finally {
    await ensureMinimumLoading()
    isLoading.value = false
    loadingStartTime.value = null
  }
}

const fetchDepartments = async () => {
  if (!isLoading.value) {
    isLoading.value = true
    loadingStartTime.value = Date.now()
  }
  try {
    const response = await axios.get('http://localhost:8000/api/departments')
    departments.value = response.data.data || []
  } catch (error) {
    console.error('Error fetching departments:', error)
    // Fallback data
    departments.value = [
      { id: 1, name: 'Computer Science', category_id: 1, category: { id: 1, name: 'Academic', color: '#0F6F43' } },
      { id: 2, name: 'Administration', category_id: 2, category: { id: 2, name: 'Administrative', color: '#004D7A' } },
      { id: 3, name: 'Library', category_id: 1, category: { id: 1, name: 'Academic', color: '#0F6F43' } }
    ]
  } finally {
    await ensureMinimumLoading()
    isLoading.value = false
    loadingStartTime.value = null
  }
}

const fetchComponents = async () => {
  if (!isLoading.value) {
    isLoading.value = true
    loadingStartTime.value = Date.now()
  }
  try {
    const response = await axios.get('http://localhost:8000/api/components')
    components.value = response.data.data || {}
  } catch (error) {
    console.error('Error fetching components:', error)
    // Fallback data
    components.value = {
      processors: [
        { id: 1, model: 'Intel Core i5-10400' },
        { id: 2, model: 'Intel Core i3-10100' }
      ],
      motherboards: [
        { id: 1, model: 'ASUS Prime H410M' },
        { id: 2, model: 'Gigabyte H410M' }
      ],
      rams: [
        { id: 1, capacity: '16GB DDR4' },
        { id: 2, capacity: '8GB DDR4' }
      ],
      storages: [
        { id: 1, capacity: '512GB SSD' },
        { id: 2, capacity: '256GB SSD' }
      ],
      video_cards: [
        { id: 1, model: 'NVIDIA GTX 1650' },
        { id: 2, model: 'Integrated' }
      ],
      psus: [
        { id: 1, wattage: 450, model: 'Corsair CV450' },
        { id: 2, wattage: 550, model: 'Cooler Master MWE 550' },
        { id: 3, wattage: 650, model: 'EVGA 650 WQ' }
      ],
      dvd_roms: [
        { id: 1, model: 'DVD-RW Samsung' },
        { id: 2, model: 'DVD-RW LG' },
        { id: 3, model: 'No DVD ROM' }
      ]
    }
  } finally {
    await ensureMinimumLoading()
    isLoading.value = false
    loadingStartTime.value = null
  }
}

const fetchUsers = async () => {
  if (!isLoading.value) {
    isLoading.value = true
    loadingStartTime.value = Date.now()
  }
  try {
    const response = await axios.get('http://localhost:8000/api/users')
    users.value = response.data.data || []
  } catch (error) {
    console.error('Error fetching users:', error)
    // Fallback data
    users.value = [
      { id: 1, name: 'John Doe', email: 'john@example.com' },
      { id: 2, name: 'Jane Smith', email: 'jane@example.com' },
      { id: 3, name: 'Bob Wilson', email: 'bob@example.com' }
    ]
  } finally {
    await ensureMinimumLoading()
    isLoading.value = false
    loadingStartTime.value = null
  }
}

const createComputer = async () => {
  isLoading.value = true
  try {
    const response = await axios.post('http://localhost:8000/api/computers/create', newComputer.value)
    
    if (response.data.success) {
      Modal.getInstance(document.getElementById('createComputerModal')).hide()
      await refreshData()
      
      // Reset form
      newComputer.value = {
        computer_name: '',
        asset_tag: '',
        pc_number: '',
        serial_number: '',
        department_id: '',
        processor_id: '',
        motherboard_id: '',
        ram_id: '',
        storage_id: '',
        video_card_id: '',
        psu_id: '',
        dvd_rom_id: '',
        status: 'Working',
        assigned_to: '',
        location: '',
        description: ''
      }
      
      alert('Computer created successfully!')
    }
  } catch (error) {
    console.error('Error creating computer:', error)
    alert('Error creating computer: ' + (error.response?.data?.message || error.message))
  } finally {
    isLoading.value = false
  }
}

const viewComputer = (computer) => {
  selectedComputer.value = computer
  const modal = new Modal(document.getElementById('viewComputerModal'))
  modal.show()
  
  // Generate QR code after modal is shown
  setTimeout(() => {
    generateQRCode()
  }, 500)
}

const editComputer = (computer) => {
  editingComputer.value = { ...computer }
  const modal = new Modal(document.getElementById('editComputerModal'))
  modal.show()
}

const updateComputer = async () => {
  isLoading.value = true
  try {
    const response = await axios.put(`http://localhost:8000/api/computers/${editingComputer.value.id}`, editingComputer.value)
    
    if (response.data.success) {
      Modal.getInstance(document.getElementById('editComputerModal')).hide()
      await refreshData()
      alert('Computer updated successfully!')
    }
  } catch (error) {
    console.error('Error updating computer:', error)
    alert('Error updating computer: ' + (error.response?.data?.message || error.message))
  } finally {
    isLoading.value = false
  }
}

const deleteComputer = async (id) => {
  if (!confirm('Are you sure you want to delete this computer?')) return
  
  isLoading.value = true
  try {
    const response = await axios.delete(`http://localhost:8000/api/computers/${id}/delete`)
    
    if (response.data.success) {
      await refreshData()
      alert('Computer deleted successfully!')
    }
  } catch (error) {
    console.error('Error deleting computer:', error)
    alert('Error deleting computer: ' + (error.response?.data?.message || error.message))
  } finally {
    isLoading.value = false
  }
}

const generateQRCode = () => {
  const qrCanvas = document.querySelector('#viewComputerModal canvas')
  if (!qrCanvas || !selectedComputer.value) return

  try {
    // Create QR code data with complete computer details
    const qrData = {
      // Basic Information
      computer_name: selectedComputer.value.computer_name,
      asset_tag: selectedComputer.value.asset_tag,
      pc_number: selectedComputer.value.pc_number,
      serial_number: selectedComputer.value.serial_number || 'N/A',
      
      // Department Information
      department: selectedComputer.value.department?.name || selectedComputer.value.department?.department_name || 'No Department',
      
      // Component Details
      processor: selectedComputer.value.processor?.model || 'No Processor',
      motherboard: selectedComputer.value.motherboard?.model || 'No Motherboard',
      ram: selectedComputer.value.ram?.capacity || 'No RAM',
      storage: selectedComputer.value.storage?.capacity || 'No Storage',
      video_card: selectedComputer.value.video_card?.model || 'No Video Card',
      psu: selectedComputer.value.psu?.wattage ? `${selectedComputer.value.psu.wattage}W` : 'No PSU',
      dvd_rom: selectedComputer.value.dvd_rom?.model || 'No DVD ROM',
      
      // Status and Assignment
      status: selectedComputer.value.status,
      assigned_to: selectedComputer.value.assigned_user?.name || 'Unassigned',
      location: selectedComputer.value.location || 'Not specified',
      description: selectedComputer.value.description || 'No description available',
      
      // Metadata
      generated_at: new Date().toISOString(),
      generated_by: 'MIS Inventory System',
      
      // Direct PDF Download URL - This will directly download PDF when QR is scanned
      pdf_download_url: `${import.meta.env.VITE_API_URL || 'https://your-backend-domain.com'}/api/computers/${selectedComputer.value.id}/pdf/download`
    }

    // Generate QR code using online API
    const qrText = JSON.stringify(qrData)
    const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(qrText)}`
    
    // Create image element and draw on canvas
    const img = new Image()
    img.crossOrigin = 'anonymous'
    img.onload = () => {
      const ctx = qrCanvas.getContext('2d')
      qrCanvas.width = 200
      qrCanvas.height = 200
      ctx.clearRect(0, 0, 200, 200)
      ctx.drawImage(img, 0, 0, 200, 200)
    }
    img.onerror = () => {
      // Fallback: draw a placeholder with computer info
      const ctx = qrCanvas.getContext('2d')
      qrCanvas.width = 200
      qrCanvas.height = 200
      ctx.clearRect(0, 0, 200, 200)
      
      // Background
      ctx.fillStyle = '#ffffff'
      ctx.fillRect(0, 0, 200, 200)
      
      // Border
      ctx.strokeStyle = '#000000'
      ctx.lineWidth = 2
      ctx.strokeRect(10, 10, 180, 180)
      
      // Text placeholder
      ctx.fillStyle = '#000000'
      ctx.font = 'bold 12px Arial'
      ctx.textAlign = 'center'
      ctx.fillText('QR CODE', 100, 40)
      ctx.font = '10px Arial'
      ctx.fillText(selectedComputer.value.asset_tag || selectedComputer.value.pc_number, 100, 60)
      ctx.font = '9px Arial'
      ctx.fillText(selectedComputer.value.computer_name?.substring(0, 20) || '', 100, 80)
      ctx.fillText(selectedComputer.value.department?.name || selectedComputer.value.department?.department_name || 'No Dept', 100, 100)
      ctx.fillText(selectedComputer.value.status, 100, 120)
    }
    img.src = qrUrl
  } catch (error) {
    console.error('Error generating QR code:', error)
    // Draw fallback
    const ctx = qrCanvas.getContext('2d')
    qrCanvas.width = 200
    qrCanvas.height = 200
    ctx.clearRect(0, 0, 200, 200)
    ctx.fillStyle = '#f0f0f0'
    ctx.fillRect(0, 0, 200, 200)
    ctx.fillStyle = '#666'
    ctx.font = '14px Arial'
    ctx.textAlign = 'center'
    ctx.fillText('QR Code', 100, 90)
    ctx.fillText('Generation Failed', 100, 110)
    ctx.font = '10px Arial'
    ctx.fillText(selectedComputer.value.asset_tag || selectedComputer.value.pc_number, 100, 130)
  }
}

const downloadQRCode = () => {
  const qrCanvas = document.querySelector('#viewComputerModal canvas')
  if (!qrCanvas) return

  const link = document.createElement('a')
  link.download = `QR_${selectedComputer.value.asset_tag || selectedComputer.value.pc_number}.png`
  link.href = qrCanvas.toDataURL()
  link.click()
}

const printQRCode = () => {
  const qrCanvas = document.querySelector('#viewComputerModal canvas')
  if (!qrCanvas) return

  const printWindow = window.open('', '_blank')
  printWindow.document.write(`
    <html>
      <head>
        <title>QR Code - ${selectedComputer.value.asset_tag || selectedComputer.value.pc_number}</title>
        <style>
          body { 
            display: flex; 
            justify-content: center; 
            align-items: flex-start; 
            min-height: 100vh; 
            margin: 20px 0; 
            font-family: Arial, sans-serif;
          }
          .qr-container { 
            text-align: center; 
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            max-width: 400px;
          }
          .qr-title { 
            font-size: 18px; 
            font-weight: bold; 
            margin-bottom: 15px; 
            color: #0F6F43;
          }
          .qr-details { 
            margin-top: 15px; 
            font-size: 12px;
            text-align: left;
          }
          .qr-details .section {
            margin-bottom: 15px;
          }
          .qr-details .section-title {
            font-weight: bold;
            color: #0F6F43;
            margin-bottom: 5px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
          }
          .qr-details .detail-row {
            margin: 3px 0;
            display: flex;
            justify-content: space-between;
          }
          .qr-details .detail-label {
            font-weight: 600;
            color: #333;
            flex: 1;
          }
          .qr-details .detail-value {
            color: #666;
            flex: 2;
            text-align: right;
          }
          @media print {
            body { margin: 0; }
            .no-print { display: none; }
            .qr-container {
              border: none;
              box-shadow: none;
            }
          }
        </style>
      </head>
      <body>
        <div class="qr-container">
          <img src="${qrCanvas.toDataURL()}" alt="QR Code" style="max-width: 150px; height: auto;" />
          <div class="qr-title">${selectedComputer.value.asset_tag || selectedComputer.value.pc_number}</div>
          
          <div class="qr-details">
            <div class="section">
              <div class="section-title">Basic Information</div>
              <div class="detail-row">
                <span class="detail-label">Computer Name:</span>
                <span class="detail-value">${selectedComputer.value.computer_name || 'N/A'}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Asset Tag:</span>
                <span class="detail-value">${selectedComputer.value.asset_tag || 'N/A'}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">PC Number:</span>
                <span class="detail-value">${selectedComputer.value.pc_number || 'N/A'}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Serial Number:</span>
                <span class="detail-value">${selectedComputer.value.serial_number || 'N/A'}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Department:</span>
                <span class="detail-value">${selectedComputer.value.department?.name || selectedComputer.value.department?.department_name || 'No Department'}</span>
              </div>
            </div>
            
            <div class="section">
              <div class="section-title">Components</div>
              <div class="detail-row">
                <span class="detail-label">Processor:</span>
                <span class="detail-value">${selectedComputer.value.processor?.model || 'No Processor'}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Motherboard:</span>
                <span class="detail-value">${selectedComputer.value.motherboard?.model || 'No Motherboard'}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">RAM:</span>
                <span class="detail-value">${selectedComputer.value.ram?.capacity || 'No RAM'}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Storage:</span>
                <span class="detail-value">${selectedComputer.value.storage?.capacity || 'No Storage'}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Video Card:</span>
                <span class="detail-value">${selectedComputer.value.video_card?.model || 'No Video Card'}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">PSU:</span>
                <span class="detail-value">${selectedComputer.value.psu?.wattage ? `${selectedComputer.value.psu.wattage}W` : 'No PSU'}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">DVD ROM:</span>
                <span class="detail-value">${selectedComputer.value.dvd_rom?.model || 'No DVD ROM'}</span>
              </div>
            </div>
            
            <div class="section">
              <div class="section-title">Status & Assignment</div>
              <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value">${selectedComputer.value.status || 'N/A'}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Assigned To:</span>
                <span class="detail-value">${selectedComputer.value.assigned_user?.name || 'Unassigned'}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Location:</span>
                <span class="detail-value">${selectedComputer.value.location || 'Not specified'}</span>
              </div>
            </div>
            
            <div class="section">
              <div class="section-title">Description</div>
              <div style="font-size: 11px; color: #666; line-height: 1.3;">
                ${selectedComputer.value.description || 'No description available'}
              </div>
            </div>
            
            <div style="margin-top: 15px; font-size: 10px; color: #999;">
              Generated: ${new Date().toLocaleString()} | MIS Inventory System
            </div>
          </div>
        </div>
      </body>
    </html>
  `)
  printWindow.document.close()
  printWindow.print()
}

const clearFilters = () => {
  searchQuery.value = ''
  statusFilter.value = ''
  departmentFilter.value = ''
}

const showCreateModal = () => {
  generateAssetTag()
  const modal = new Modal(document.getElementById('createComputerModal'))
  modal.show()
}

const refreshData = async () => {
  if (!isLoading.value) {
    isLoading.value = true
    loadingStartTime.value = Date.now()
  }
  
  await Promise.all([fetchComputers(), fetchDepartments(), fetchComponents(), fetchUsers(), fetchLaboratories()])
  
  await ensureMinimumLoading()
  isLoading.value = false
  loadingStartTime.value = null
}

const exportComputers = () => {
  alert('Export feature would generate CSV/PDF report of current computers')
}

const toggleNav = () => {
  isNavCollapsed.value = !isNavCollapsed.value
}

const openProfile = () => {
  console.log('Opening profile')
}

const openSettings = () => {
  console.log('Opening settings')
}

// Deployment Management Functions
const fetchLaboratories = async () => {
  isLoading.value = true
  try {
    const response = await axios.get('http://localhost:8000/api/laboratories')
    laboratories.value = response.data.data || []
    console.log('Laboratories loaded:', laboratories.value.length)
  } catch (error) {
    console.error('Error fetching laboratories:', error)
    // Fallback data with proper structure
    laboratories.value = [
      { id: 1, lab_name: 'Computer Laboratory 1', room_number: 'Room 101', capacity: 30, department_id: 1, is_active: true, current_computer_count: 0 },
      { id: 2, lab_name: 'Computer Laboratory 2', room_number: 'Room 102', capacity: 25, department_id: 1, is_active: true, current_computer_count: 0 },
      { id: 3, lab_name: 'Computer Laboratory 3', room_number: 'Room 201', capacity: 30, department_id: 2, is_active: true, current_computer_count: 0 },
      { id: 4, lab_name: 'Computer Laboratory 4', room_number: 'Room 202', capacity: 20, department_id: 2, is_active: true, current_computer_count: 0 }
    ]
  } finally {
    isLoading.value = false
  }
}

const showDeploymentModal = () => {
  if (!deployingComputer.value) {
    alert('Please select a computer to deploy first')
    return
  }
  const modal = new Modal(document.getElementById('deploymentModal'))
  modal.show()
}

const onDepartmentChange = () => {
  // Reset laboratory when department changes
  deployment.value.laboratory_id = ''
}

const deployComputer = (computer) => {
  if (computer.status !== 'Working') {
    alert('Only working computers can be deployed!')
    return
  }
  
  if (computer.is_deployed || computer.laboratory_id) {
    alert('This computer is already deployed! Please return it before redeploying.')
    return
  }
  
  deployingComputer.value = computer
  const modal = new Modal(document.getElementById('deploymentModal'))
  modal.show()
}

const confirmDeployment = async () => {
  if (!deployment.value.department_id || !deployment.value.pc_position) {
    alert('Please fill in all required fields')
    return
  }

  isLoading.value = true
  try {
    // Validate laboratory capacity if selected
    if (deployment.value.laboratory_id && selectedLabCapacity.value) {
      if (selectedLabCapacity.value.available <= 0) {
        alert('Selected laboratory is at full capacity. Please choose a different laboratory or leave laboratory field empty.')
        return
      }
    }

    const deploymentData = {
      computer_id: deployingComputer.value.id,
      department_id: deployment.value.department_id,
      laboratory_id: deployment.value.laboratory_id || null,
      user_id: deployment.value.user_id || null,
      location: deployment.value.location || '',
      pc_position: deployment.value.pc_position,
      notes: deployment.value.notes,
      deployment_date: deployment.value.deployment_date,
      deployed_by: currentUser.value.name,
      status: 'deployed'
    }

    const response = await axios.post('http://localhost:8000/api/deployments', deploymentData)
    
    if (response.data.success) {
      alert('Computer deployed successfully!')
      Modal.getInstance(document.getElementById('deploymentModal')).hide()
      
      // Reset deployment form
      deployment.value = {
        department_id: '',
        laboratory_id: '',
        user_id: '',
        pc_position: '',
        location: '',
        notes: '',
        deployment_date: new Date().toISOString().split('T')[0]
      }
      deployingComputer.value = null
      
      // Update computer status to show it's deployed
      await refreshData()
    }
  } catch (error) {
    console.error('Error deploying computer:', error)
    alert('Error deploying computer: ' + (error.response?.data?.message || error.message))
  } finally {
    isLoading.value = false
  }
}

onMounted(async () => {
  initDarkMode()
  
  if (!isLoading.value) {
    isLoading.value = true
    loadingStartTime.value = Date.now()
  }
  
  await refreshData()
  
  await ensureMinimumLoading()
  isLoading.value = false
  loadingStartTime.value = null
})
</script>

<style scoped>
.dashboard-layout {
  display: flex;
  min-height: 100vh;
  background-color: #f8f9fa;
}

.main-content {
  flex: 1;
  margin-left: 250px;
  transition: margin-left 0.3s ease;
  display: flex;
  flex-direction: column;
}

.main-content.collapsed {
  margin-left: 60px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .main-content {
    margin-left: 60px;
  }
  
  .main-content.collapsed {
    margin-left: 60px;
  }
}

.progress {
  background-color: #e9ecef;
  border-radius: 0.25rem;
}

.progress-bar {
  background-color: #198754;
  border-radius: 0.25rem;
}

/* Dark mode styles */
:global(.dark-mode) .dashboard-layout {
  background-color: #121212;
}

:global(.dark-mode) .main-content {
  background-color: #121212;
}

:global(.dark-mode) .card {
  background-color: #1e1e1e;
  border-color: #333;
}

:global(.dark-mode) .card-header {
  background-color: #2d2d2d;
  border-color: #333;
  color: #fff;
}

:global(.dark-mode) .card-body {
  background-color: #1e1e1e;
  color: #fff;
}

:global(.dark-mode) .h1,
:global(.dark-mode) .h2,
:global(.dark-mode) .h3,
:global(.dark-mode) .h4,
:global(.dark-mode) .h5,
:global(.dark-mode) .h6 {
  color: #fff !important;
}

:global(.dark-mode) .text-muted {
  color: #b3b3b3 !important;
}

:global(.dark-mode) .btn-outline-primary {
  border-color: #0F6F43;
  color: #0F6F43;
}

:global(.dark-mode) .btn-outline-primary:hover {
  background-color: #0F6F43;
  border-color: #0F6F43;
  color: #fff;
}

:global(.dark-mode) .btn-success {
  background-color: #198754;
  border-color: #198754;
}

:global(.dark-mode) .btn-success:hover {
  background-color: #157347;
  border-color: #157347;
}

:global(.dark-mode) .btn-info {
  background-color: #0c8599;
  border-color: #0c8599;
}

:global(.dark-mode) .btn-info:hover {
  background-color: #0a6b7c;
  border-color: #0a6b7c;
}

:global(.dark-mode) .table {
  color: #fff;
}

:global(.dark-mode) .table thead th {
  background-color: #2d2d2d;
  border-color: #333;
  color: #fff;
}

:global(.dark-mode) .table tbody td {
  background-color: #1e1e1e;
  border-color: #333;
  color: #fff;
}

:global(.dark-mode) .table tbody tr:hover td {
  background-color: #2d2d2d;
}

:global(.dark-mode) .form-control {
  background-color: #2d2d2d;
  border-color: #444;
  color: #fff;
}

:global(.dark-mode) .form-control:focus {
  background-color: #2d2d2d;
  border-color: #0F6F43;
  color: #fff;
  box-shadow: 0 0 0 0.25rem rgba(15, 111, 67, 0.25);
}

:global(.dark-mode) .form-select {
  background-color: #2d2d2d;
  border-color: #444;
  color: #fff;
}

:global(.dark-mode) .form-select:focus {
  background-color: #2d2d2d;
  border-color: #0F6F43;
  color: #fff;
  box-shadow: 0 0 0 0.25rem rgba(15, 111, 67, 0.25);
}

:global(.dark-mode) .modal-content {
  background-color: #1e1e1e;
  color: #fff;
}

:global(.dark-mode) .modal-header {
  background-color: #2d2d2d;
  border-color: #333;
}

:global(.dark-mode) .modal-body {
  background-color: #1e1e1e;
}

:global(.dark-mode) .modal-footer {
  background-color: #2d2d2d;
  border-color: #333;
}

:global(.dark-mode) .badge {
  background-color: #0F6F43;
}

:global(.dark-mode) .progress {
  background-color: #2d2d2d;
}

:global(.dark-mode) .dropdown-menu {
  background-color: #1e1e1e;
  border-color: #333;
}

:global(.dark-mode) .dropdown-item {
  color: #fff;
}

:global(.dark-mode) .dropdown-item:hover {
  background-color: #2d2d2d;
  color: #fff;
}

:global(.dark-mode) .pagination .page-link {
  background-color: #1e1e1e;
  border-color: #333;
  color: #fff;
}

:global(.dark-mode) .pagination .page-link:hover {
  background-color: #2d2d2d;
  border-color: #444;
  color: #fff;
}

:global(.dark-mode) .pagination .page-item.active .page-link {
  background-color: #0F6F43;
  border-color: #0F6F43;
}

/* QR Code Styles */
.qr-code-container {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 15px;
  background: white;
  border-radius: 8px;
  border: 1px solid #ddd;
  margin-bottom: 15px;
}

.computer-info-qr {
  text-align: center;
}

.computer-info-qr h6 {
  margin-bottom: 8px;
  font-weight: 600;
}

.computer-info-qr p {
  margin-bottom: 4px;
  font-size: 0.875rem;
}

/* Dark mode QR code styles */
:global(.dark-mode) .qr-code-container {
  background: #2d2d2d;
  border-color: #444;
}

:global(.dark-mode) .computer-info-qr h6 {
  color: #fff;
}

:global(.dark-mode) .computer-info-qr p {
  color: #b3b3b3;
}
</style>
