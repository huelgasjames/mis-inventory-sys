<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ram extends Model
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
     * Get computers using this RAM
     */
    public function computers(): HasMany
    {
        return $this->hasMany(Computer::class);
    }

    /**
     * Get RAM by brand
     */
    public function scopeByBrand($query, $brand)
    {
        return $query->where('brand', $brand);
    }

    /**
     * Get RAM by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get RAM by minimum capacity
     */
    public function scopeMinCapacity($query, $minCapacity)
    {
        return $query->where('capacity_gb', '>=', $minCapacity);
    }

    /**
     * Get RAM by minimum speed
     */
    public function scopeMinSpeed($query, $minSpeed)
    {
        return $query->where('speed_mhz', '>=', $minSpeed);
    }

    /**
     * Get ECC RAM only
     */
    public function scopeEcc($query)
    {
        return $query->where('ecc', true);
    }

    /**
     * Get non-ECC RAM only
     */
    public function scopeNonEcc($query)
    {
        return $query->where('ecc', false);
    }
}
