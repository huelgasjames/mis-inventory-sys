<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Storage extends Model
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
     * Get computers using this storage
     */
    public function computers(): HasMany
    {
        return $this->hasMany(Computer::class);
    }

    /**
     * Get storage by brand
     */
    public function scopeByBrand($query, $brand)
    {
        return $query->where('brand', $brand);
    }

    /**
     * Get storage by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get storage by interface
     */
    public function scopeByInterface($query, $interface)
    {
        return $query->where('interface', $interface);
    }

    /**
     * Get SSD storage only
     */
    public function scopeSsd($query)
    {
        return $query->where('type', 'SSD');
    }

    /**
     * Get HDD storage only
     */
    public function scopeHdd($query)
    {
        return $query->where('type', 'HDD');
    }

    /**
     * Get NVMe storage only
     */
    public function scopeNvme($query)
    {
        return $query->where('nvme', true);
    }

    /**
     * Get storage by minimum capacity
     */
    public function scopeMinCapacity($query, $minCapacity)
    {
        return $query->where('capacity_gb', '>=', $minCapacity);
    }
}
