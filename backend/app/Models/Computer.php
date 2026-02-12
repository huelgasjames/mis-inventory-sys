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
    ];

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
        return $this->deployments()->where('status', 'deployed')->exists();
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
