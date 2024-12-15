<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;
use App\Traits\BasicAudit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends SpatieRole
{
    use BasicAudit, HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'description', 'guard_name', 'permissions'
    ];

    protected $casts = [
        'created_at' => 'datetime: Y-m-d H:i:s',
        'updated_at' => 'datetime: Y-m-d H:i:s'
    ];

    protected $hidden = [
        'guard_name',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {                       
            if (!$model->isDirty('created_by')) {
                $model->created_by = auth()->user()->id;
            }
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = auth()->user()->id;
            }            
        });

        static::updating(function ($model) {            
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = auth()->user()->id;
            }            
        });
    }
    

}
