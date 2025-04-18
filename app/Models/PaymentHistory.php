<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;


class PaymentHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'member_id',
        'kpay_member_id',
        'route_id',
        'payment_id',
        'screenshot',
        'phone',
        'nrc',
        'seat',
        'total',
        'note',
        'name',
        'start_time',
        'status',
        'daily_route_id'
    ];

    protected $casts = [
        'seat' => 'array',
        'created_at' => 'datetime: Y-m-d H:i:s',
        'updated_at' => 'datetime: Y-m-d H:i:s'
    ];

    protected static function boot()
    {

        parent::boot();

        static::creating(function ($model) {
            $userId = auth()->check() ? auth()->user()->id : 1;
        
            if (!$model->isDirty('created_by')) {
                $model->created_by = $userId;
            }
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = $userId;
            }
        });

        static::updating(function ($model) {
            $userId = auth()->check() ? auth()->user()->id : 1;
            
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = $userId;
            }
        });
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Routes::class, 'route_id');
    }

}