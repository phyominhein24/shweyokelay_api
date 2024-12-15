<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use HasFactory;

    protected $fillable = [
        'name', 'description',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'guard_name',
    ];
}
