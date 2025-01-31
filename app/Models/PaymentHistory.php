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
        'route_id',
        'phone',
        'nrc',
        'seat',
        'total',
        'note',
        'start_time',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime: Y-m-d H:i:s',
        'updated_at' => 'datetime: Y-m-d H:i:s'
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

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Routes::class, 'route_id');
    }

}