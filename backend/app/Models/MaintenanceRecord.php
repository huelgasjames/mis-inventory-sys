<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'computer_id',
        'maintenance_type',
        'description',
        'cost',
        'performed_by',
        'created_by',
        'maintenance_date',
        'next_maintenance_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the asset that was maintained
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Get the computer that was maintained
     */
    public function computer(): BelongsTo
    {
        return $this->belongsTo(Computer::class);
    }

    /**
     * Get the user who performed the maintenance
     */
    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    /**
     * Get the user who created the record
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get completed maintenance records
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Get pending maintenance records
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Get maintenance records by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('maintenance_type', $type);
    }

    /**
     * Get maintenance records due soon
     */
    public function scopeDueSoon($query, $days = 7)
    {
        return $query->where('next_maintenance_date', '<=', now()->addDays($days))
                    ->where('next_maintenance_date', '>', now());
    }
}
