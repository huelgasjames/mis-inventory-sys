<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the category this department belongs to
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(DepartmentCategory::class);
    }

    /**
     * Get laboratories in this department
     */
    public function laboratories(): HasMany
    {
        return $this->hasMany(Laboratory::class);
    }

    /**
     * Get computers in this department
     */
    public function computers(): HasMany
    {
        return $this->hasMany(Computer::class);
    }

    /**
     * Get the assets in this department
     */
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    /**
     * Get the users in this department
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get total assets count
     */
    public function getTotalAssetsAttribute(): int
    {
        return $this->assets()->count();
    }

    /**
     * Get total computers count
     */
    public function getTotalComputersAttribute(): int
    {
        return $this->computers()->count();
    }

    /**
     * Get total laboratories count
     */
    public function getTotalLaboratoriesAttribute(): int
    {
        return $this->laboratories()->count();
    }
}
