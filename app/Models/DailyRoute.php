<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyRoute extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'route_id',
        'driver_name',
        'car_no',
        'status',
        'start_date'
    ];

    protected $casts = [
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
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = auth()->user()->id;
            }
        });
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Routes::class, 'route_id');
    }

    public function paymentHistories()
    {
        return $this->hasMany(PaymentHistory::class);
    }
}
