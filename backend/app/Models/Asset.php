<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_tag',
        'serial_number',
        'asset_type',
        'brand',
        'model',
        'department_id',
        'status',
        'purchase_date',
        'warranty_expiry',
        'location',
        'assigned_to',
        'notes',
        'specifications',
        'description',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
        'specifications' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the department that owns the asset
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the user assigned to this asset
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get maintenance records for this asset
     */
    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class);
    }

    /**
     * Get all activities for this asset (polymorphic)
     */
    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    /**
     * Get working assets only
     */
    public function scopeWorking($query)
    {
        return $query->where('status', 'Working');
    }

    /**
     * Get defective assets only
     */
    public function scopeDefective($query)
    {
        return $query->where('status', 'Defective');
    }

    /**
     * Get assets for disposal only
     */
    public function scopeForDisposal($query)
    {
        return $query->where('status', 'For Disposal');
    }

    /**
     * Get assets by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('asset_type', $type);
    }

    /**
     * Get assets by department
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Get assets expiring soon
     */
    public function scopeWarrantyExpiringSoon($query, $days = 30)
    {
        return $query->where('warranty_expiry', '<=', now()->addDays($days))
                    ->where('warranty_expiry', '>', now());
    }
}
