<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DepartmentCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'color',
    ];

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }
}
