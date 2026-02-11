<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Psu extends Model
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
     * Get computers using this PSU
     */
    public function computers(): HasMany
    {
        return $this->hasMany(Computer::class);
    }

    /**
     * Get PSUs by brand
     */
    public function scopeByBrand($query, $brand)
    {
        return $query->where('brand', $brand);
    }

    /**
     * Get PSUs by minimum wattage
     */
    public function scopeMinWattage($query, $minWattage)
    {
        return $query->where('wattage', '>=', $minWattage);
    }

    /**
     * Get PSUs by efficiency rating
     */
    public function scopeByEfficiency($query, $rating)
    {
        return $query->where('efficiency_rating', $rating);
    }

    /**
     * Get modular PSUs only
     */
    public function scopeModular($query)
    {
        return $query->where('modular', true);
    }

    /**
     * Get non-modular PSUs only
     */
    public function scopeNonModular($query)
    {
        return $query->where('modular', false);
    }

    /**
     * Get PSUs by certification
     */
    public function scopeByCertification($query, $certification)
    {
        return $query->where('certification', $certification);
    }

    /**
     * Get 80+ Bronze PSUs
     */
    public function scopeBronze($query)
    {
        return $query->where('certification', '80+ Bronze');
    }

    /**
     * Get 80+ Gold PSUs
     */
    public function scopeGold($query)
    {
        return $query->where('certification', '80+ Gold');
    }

    /**
     * Get 80+ Platinum PSUs
     */
    public function scopePlatinum($query)
    {
        return $query->where('certification', '80+ Platinum');
    }
}
