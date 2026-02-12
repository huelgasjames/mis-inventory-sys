<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deployment extends Model
{
    use HasFactory;

    protected $table = 'deployment_management';

    protected $fillable = [
        'computer_id',
        'department_id',
        'laboratory_id',
        'user_id',
        'location',
        'status',
        'deployment_date',
        'return_date',
        'notes',
        'deployed_by',
    ];

    protected $casts = [
        'deployment_date' => 'date',
        'return_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function computer(): BelongsTo
    {
        return $this->belongsTo(Computer::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function laboratory(): BelongsTo
    {
        return $this->belongsTo(Laboratory::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeDeployed($query)
    {
        return $query->where('status', 'deployed');
    }

    public function scopeReturned($query)
    {
        return $query->where('status', 'returned');
    }

    public function scopeMaintenance($query)
    {
        return $query->where('status', 'maintenance');
    }

    public function scopeRetired($query)
    {
        return $query->where('status', 'retired');
    }

    public function getDurationAttribute(): ?int
    {
        if ($this->return_date && $this->deployment_date) {
            return $this->deployment_date->diffInDays($this->return_date);
        }
        return null;
    }

    public function isActive(): bool
    {
        return $this->status === 'deployed' && (!$this->return_date || $this->return_date->isFuture());
    }
}
