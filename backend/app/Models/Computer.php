<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Computer extends Model
{
    use HasFactory;

    protected $fillable = [
        'computer_name',
        'asset_tag',
        'pc_number',
        'serial_number',
        'department_id',
        'laboratory_id',
        'processor_id',
        'motherboard_id',
        'ram_id',
        'storage_id',
        'video_card_id',
        'dvd_rom_id',
        'psu_id',
        'status',
        'assigned_to',
        'purchase_date',
        'warranty_expiry',
        'location',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_deployed' => 'boolean',
    ];

    protected $appends = ['is_deployed', 'deployment_location'];

    /**
     * Get laboratory this computer belongs to
     */
    public function laboratory(): BelongsTo
    {
        return $this->belongsTo(Laboratory::class);
    }

    /**
     * Get the department that owns the computer
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the processor for this computer
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(Processor::class);
    }

    /**
     * Get the motherboard for this computer
     */
    public function motherboard(): BelongsTo
    {
        return $this->belongsTo(Motherboard::class);
    }

    /**
     * Get the RAM for this computer
     */
    public function ram(): BelongsTo
    {
        return $this->belongsTo(Ram::class);
    }

    /**
     * Get the storage for this computer
     */
    public function storage(): BelongsTo
    {
        return $this->belongsTo(Storage::class);
    }

    /**
     * Get the video card for this computer
     */
    public function videoCard(): BelongsTo
    {
        return $this->belongsTo(VideoCard::class);
    }

    /**
     * Get the DVD ROM for this computer
     */
    public function dvdRom(): BelongsTo
    {
        return $this->belongsTo(DvdRom::class);
    }

    /**
     * Get the PSU for this computer
     */
    public function psu(): BelongsTo
    {
        return $this->belongsTo(Psu::class);
    }

    /**
     * Get the user assigned to this computer
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get deployments for this computer
     */
    public function deployments(): HasMany
    {
        return $this->hasMany(Deployment::class);
    }

    /**
     * Get current deployment
     */
    public function currentDeployment(): BelongsTo
    {
        return $this->belongsTo(Deployment::class)->where('status', 'deployed');
    }

    /**
     * Check if computer is currently deployed
     */
    public function isDeployed(): bool
    {
        // Check if computer has an active deployment record
        $hasActiveDeployment = $this->deployments()->where('status', 'deployed')->exists();
        
        // Also check if computer is assigned to a laboratory or user
        $hasLabAssignment = $this->laboratory_id !== null;
        $hasUserAssignment = $this->assigned_to !== null;
        
        return $hasActiveDeployment || $hasLabAssignment || $hasUserAssignment;
    }

    /**
     * Get the current deployment location
     */
    public function getCurrentDeploymentLocation()
    {
        // First check if there's an active deployment record
        $activeDeployment = $this->deployments()->where('status', 'deployed')->first();
        if ($activeDeployment) {
            return [
                'type' => 'deployment',
                'location' => $activeDeployment->location,
                'department' => $activeDeployment->department,
                'laboratory' => $activeDeployment->laboratory,
                'user' => $activeDeployment->user
            ];
        }
        
        // If no active deployment, check direct assignments
        if ($this->laboratory) {
            return [
                'type' => 'laboratory',
                'location' => $this->laboratory->lab_name,
                'department' => $this->department,
                'laboratory' => $this->laboratory,
                'user' => null
            ];
        }
        
        if ($this->assignedUser) {
            return [
                'type' => 'user',
                'location' => $this->location,
                'department' => $this->department,
                'laboratory' => null,
                'user' => $this->assignedUser
            ];
        }
        
        return null;
    }

    /**
     * Get the is_deployed attribute
     */
    public function getIsDeployedAttribute(): bool
    {
        return $this->isDeployed();
    }

    /**
     * Get the deployment_location attribute
     */
    public function getDeploymentLocationAttribute()
    {
        return $this->getCurrentDeploymentLocation();
    }

    /**
     * Get working computers only
     */
    public function scopeWorking($query)
    {
        return $query->where('status', 'Working');
    }

    /**
     * Get defective computers only
     */
    public function scopeDefective($query)
    {
        return $query->where('status', 'Defective');
    }

    /**
     * Get computers for disposal only
     */
    public function scopeForDisposal($query)
    {
        return $query->where('status', 'For Disposal');
    }

    /**
     * Get computers by department
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }
}
