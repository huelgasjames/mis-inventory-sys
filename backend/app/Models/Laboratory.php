<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Laboratory extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'lab_code',
        'lab_name',
        'location',
        'building',
        'floor',
        'capacity',
        'head_of_lab',
        'is_active',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user in charge of this laboratory
     */
    public function headOfLab(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_of_lab');
    }

    /**
     * Get the department this laboratory belongs to
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get computers for this laboratory
     */
    public function computers(): HasMany
    {
        return $this->hasMany(Computer::class);
    }

    /**
     * Get current computer count
     */
    public function getCurrentComputerCountAttribute(): int
    {
        return $this->computers()->count();
    }

    /**
     * Get available capacity
     */
    public function getAvailableCapacityAttribute(): int
    {
        return $this->capacity - $this->getCurrentComputerCountAttribute();
    }

    /**
     * Get active laboratories only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get inactive laboratories only
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
}
