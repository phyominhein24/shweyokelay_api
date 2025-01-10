<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;


class Member extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'is_agent',
        'commission',
        'status',
    ];

    protected $hidden = [
        'password'
    ];

    protected $casts = [
        'is_agent' => 'boolean',
        'created_at' => 'datetime: Y-m-d H:i:s',
        'updated_at' => 'datetime: Y-m-d H:i:s'
    ];
    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Check if an authenticated user exists
            $user = auth()->user();
            
            if (!$model->isDirty('created_by')) {
                // Set 'created_by' to null if it's the same user or no user is authenticated
                $model->created_by = $user ? ($model->id === $user->id ? null : $user->id) : null;
            }

            if (!$model->isDirty('updated_by')) {
                // Set 'updated_by' if an authenticated user exists
                $model->updated_by = $user ? $user->id : null;
            }
        });

        static::updating(function ($model) {
            // Check if an authenticated user exists
            $user = auth()->user();

            if (!$model->isDirty('updated_by')) {
                // Set 'updated_by' if an authenticated user exists
                $model->updated_by = $user ? $user->id : null;
            }
        });
    }


    public function getJWTIdentifier()
    {
        return $this->getKey();  // Typically returns the primary key of the user model
    }

    /**
     * Return an array of custom claims to add to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];  // You can add custom claims if necessary
    }

}