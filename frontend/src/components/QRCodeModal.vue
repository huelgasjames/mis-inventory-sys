<template>
  <div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="qrCodeModalLabel">
            <i class="bi bi-qr-code me-2"></i>
            QR Code for {{ computer.asset_tag || computer.pc_number }}
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <div class="qr-code-container">
            <canvas ref="qrCanvas"></canvas>
          </div>
          <div class="mt-3">
            <h6 class="text-primary">{{ computer.asset_tag || computer.pc_number }}</h6>
            <p class="text-muted mb-1">{{ computer.computer_name }}</p>
            <p class="text-muted mb-1">{{ computer.department?.name || computer.department?.department_name || 'No Department' }}</p>
            <small class="text-muted">Status: {{ computer.status }}</small>
          </div>
          <div class="mt-3 d-flex gap-2 justify-content-center">
            <button type="button" class="btn btn-primary" @click="downloadQRCode">
              <i class="bi bi-download me-1"></i>Download
            </button>
            <button type="button" class="btn btn-success" @click="printQRCode">
              <i class="bi bi-printer me-1"></i>Print
            </button>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'

const props = defineProps({
  computer: {
    type: Object,
    required: true
  }
})

const qrCanvas = ref(null)
const qrCodeModal = ref(null)

const generateQRCode = () => {
  if (!qrCanvas.value || !props.computer) return

  try {
    // Create QR code data with complete computer details
    const qrData = {
      // Basic Information
      computer_name: props.computer.computer_name,
      asset_tag: props.computer.asset_tag,
      pc_number: props.computer.pc_number,
      serial_number: props.computer.serial_number || 'N/A',
      
      // Department Information
      department: props.computer.department?.name || props.computer.department?.department_name || 'No Department',
      
      // Component Details
      processor: props.computer.processor?.model || 'No Processor',
      motherboard: props.computer.motherboard?.model || 'No Motherboard',
      ram: props.computer.ram?.capacity || 'No RAM',
      storage: props.computer.storage?.capacity || 'No Storage',
      video_card: props.computer.video_card?.model || 'No Video Card',
      psu: props.computer.psu?.wattage ? `${props.computer.psu.wattage}W` : 'No PSU',
      dvd_rom: props.computer.dvd_rom?.model || 'No DVD ROM',
      
      // Status and Assignment
      status: props.computer.status,
      assigned_to: props.computer.assigned_user?.name || 'Unassigned',
      location: props.computer.location || 'Not specified',
      description: props.computer.description || 'No description available',
      
      // Metadata
      generated_at: new Date().toISOString(),
      generated_by: 'MIS Inventory System'
    }

    // Generate QR code using online API
    const qrText = JSON.stringify(qrData)
    const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(qrText)}`
    
    // Create image element and draw on canvas
    const img = new Image()
    img.crossOrigin = 'anonymous'
    img.onload = () => {
      const ctx = qrCanvas.value.getContext('2d')
      qrCanvas.value.width = 200
      qrCanvas.value.height = 200
      ctx.clearRect(0, 0, 200, 200)
      ctx.drawImage(img, 0, 0, 200, 200)
    }
    img.onerror = () => {
      // Fallback: draw a placeholder with computer info
      const ctx = qrCanvas.value.getContext('2d')
      qrCanvas.value.width = 200
      qrCanvas.value.height = 200
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
      ctx.fillText(props.computer.asset_tag || props.computer.pc_number, 100, 60)
      ctx.font = '9px Arial'
      ctx.fillText(props.computer.computer_name?.substring(0, 20) || '', 100, 80)
      ctx.fillText(props.computer.department?.name || props.computer.department?.department_name || 'No Dept', 100, 100)
      ctx.fillText(props.computer.status, 100, 120)
    }
    img.src = qrUrl
  } catch (error) {
    console.error('Error generating QR code:', error)
    // Draw fallback
    const ctx = qrCanvas.value.getContext('2d')
    qrCanvas.value.width = 200
    qrCanvas.value.height = 200
    ctx.clearRect(0, 0, 200, 200)
    ctx.fillStyle = '#f0f0f0'
    ctx.fillRect(0, 0, 200, 200)
    ctx.fillStyle = '#666'
    ctx.font = '14px Arial'
    ctx.textAlign = 'center'
    ctx.fillText('QR Code', 100, 90)
    ctx.fillText('Generation Failed', 100, 110)
    ctx.font = '10px Arial'
    ctx.fillText(props.computer.asset_tag || props.computer.pc_number, 100, 130)
  }
}

const downloadQRCode = () => {
  if (!qrCanvas.value) return

  const link = document.createElement('a')
  link.download = `QR_${props.computer.asset_tag || props.computer.pc_number}.png`
  link.href = qrCanvas.value.toDataURL()
  link.click()
}

const printQRCode = () => {
  if (!qrCanvas.value) return

  const printWindow = window.open('', '_blank')
  printWindow.document.write(`
    <html>
      <head>
        <title>QR Code - ${props.computer.asset_tag || props.computer.pc_number}</title>
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
          <img src="${qrCanvas.value.toDataURL()}" alt="QR Code" style="max-width: 150px; height: auto;" />
          <div class="qr-title">${props.computer.asset_tag || props.computer.pc_number}</div>
          
          <div class="qr-details">
            <div class="section">
              <div class="section-title">Basic Information</div>
              <div class="detail-row">
                <span class="detail-label">Computer Name:</span>
                <span class="detail-value">${props.computer.computer_name || 'N/A'}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Asset Tag:</span>
                <span class="detail-value">${props.computer.asset_tag || 'N/A'}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">PC Number:</span>
                <span class="detail-value">${props.computer.pc_number || 'N/A'}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Serial Number:</span>
                <span class="detail-value">${props.computer.serial_number || 'N/A'}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Department:</span>
                <span class="detail-value">${props.computer.department?.name || props.computer.department?.department_name || 'No Department'}</span>
              </div>
            </div>
            
            <div class="section">
              <div class="section-title">Components</div>
              <div class="detail-row">
                <span class="detail-label">Processor:</span>
                <span class="detail-value">${props.computer.processor?.model || 'No Processor'}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Motherboard:</span>
                <span class="detail-value">${props.computer.motherboard?.model || 'No Motherboard'}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">RAM:</span>
                <span class="detail-value">${props.computer.ram?.capacity || 'No RAM'}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Storage:</span>
                <span class="detail-value">${props.computer.storage?.capacity || 'No Storage'}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Video Card:</span>
                <span class="detail-value">${props.computer.video_card?.model || 'No Video Card'}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">PSU:</span>
                <span class="detail-value">${props.computer.psu?.wattage ? `${props.computer.psu.wattage}W` : 'No PSU'}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">DVD ROM:</span>
                <span class="detail-value">${props.computer.dvd_rom?.model || 'No DVD ROM'}</span>
              </div>
            </div>
            
            <div class="section">
              <div class="section-title">Status & Assignment</div>
              <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value">${props.computer.status || 'N/A'}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Assigned To:</span>
                <span class="detail-value">${props.computer.assigned_user?.name || 'Unassigned'}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Location:</span>
                <span class="detail-value">${props.computer.location || 'Not specified'}</span>
              </div>
            </div>
            
            <div class="section">
              <div class="section-title">Description</div>
              <div style="font-size: 11px; color: #666; line-height: 1.3;">
                ${props.computer.description || 'No description available'}
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

onMounted(() => {
  nextTick(() => {
    generateQRCode()
  })
})
</script>

<style scoped>
.qr-code-container {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 20px;
  background: white;
  border-radius: 8px;
  border: 1px solid #ddd;
}

.modal-content {
  border: none;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

.modal-header {
  background: linear-gradient(135deg, #0F6F43, #0d5a37);
  color: white;
  border-radius: 12px 12px 0 0;
  border: none;
}

.modal-title {
  font-weight: 600;
  font-size: 1.1rem;
}
</style>
