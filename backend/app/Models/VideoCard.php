<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VideoCard extends Model
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
     * Get computers using this video card
     */
    public function computers(): HasMany
    {
        return $this->hasMany(Computer::class);
    }

    /**
     * Get video cards by brand
     */
    public function scopeByBrand($query, $brand)
    {
        return $query->where('brand', $brand);
    }

    /**
     * Get video cards by chipset
     */
    public function scopeByChipset($query, $chipset)
    {
        return $query->where('chipset', $chipset);
    }

    /**
     * Get video cards by minimum memory
     */
    public function scopeMinMemory($query, $minMemory)
    {
        return $query->where('memory_gb', '>=', $minMemory);
    }

    /**
     * Get NVIDIA cards only
     */
    public function scopeNvidia($query)
    {
        return $query->where('brand', 'NVIDIA');
    }

    /**
     * Get AMD cards only
     */
    public function scopeAmd($query)
    {
        return $query->where('brand', 'AMD');
    }

    /**
     * Get Intel cards only
     */
    public function scopeIntel($query)
    {
        return $query->where('brand', 'Intel');
    }
}
