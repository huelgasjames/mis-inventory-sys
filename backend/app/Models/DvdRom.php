<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DvdRom extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_field',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get computers using this DVD ROM
     */
    public function computers(): HasMany
    {
        return $this->hasMany(Computer::class);
    }

    /**
     * Get DVD ROMs by brand
     */
    public function scopeByBrand($query, $brand)
    {
        return $query->where('brand', $brand);
    }

    /**
     * Get DVD ROMs by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get DVD-ROM drives only
     */
    public function scopeDvdRom($query)
    {
        return $query->where('type', 'DVD-ROM');
    }

    /**
     * Get DVD-RW drives only
     */
    public function scopeDvdRw($query)
    {
        return $query->where('type', 'DVD-RW');
    }

    /**
     * Get Blu-ray drives only
     */
    public function scopeBluRay($query)
    {
        return $query->where('type', 'Blu-ray');
    }

    /**
     * Get by interface type
     */
    public function scopeByInterface($query, $interface)
    {
        return $query->where('interface', $interface);
    }
}
