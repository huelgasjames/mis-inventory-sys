<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Processor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand',
        'model',
        'quantity',
        'unit',
        'asset_tag',
        'status',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get computers using this processor
     */
    public function computers(): HasMany
    {
        return $this->hasMany(Computer::class);
    }

    /**
     * Get processors by brand
     */
    public function scopeByBrand($query, $brand)
    {
        return $query->where('brand', $brand);
    }

    /**
     * Get available processors only
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Get deployed processors only
     */
    public function scopeDeployed($query)
    {
        return $query->where('status', 'deployed');
    }

    /**
     * Get processors by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
