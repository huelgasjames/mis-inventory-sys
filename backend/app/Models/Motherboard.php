<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Motherboard extends Model
{
    use HasFactory;

    protected $fillable = [
        'model',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get computers using this motherboard
     */
    public function computers(): HasMany
    {
        return $this->hasMany(Computer::class);
    }

    /**
     * Get motherboards by brand
     */
    public function scopeByBrand($query, $brand)
    {
        return $query->where('brand', $brand);
    }

    /**
     * Get motherboards by socket type
     */
    public function scopeBySocket($query, $socketType)
    {
        return $query->where('socket_type', $socketType);
    }

    /**
     * Get motherboards by form factor
     */
    public function scopeByFormFactor($query, $formFactor)
    {
        return $query->where('form_factor', $formFactor);
    }

    /**
     * Get motherboards by chipset
     */
    public function scopeByChipset($query, $chipset)
    {
        return $query->where('chipset', $chipset);
    }
}
