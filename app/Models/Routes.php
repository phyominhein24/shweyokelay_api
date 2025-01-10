<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Routes extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'vehicles_type_id',
        'starting_point',
        'ending_point',
        'distance',
        'duration',
        'is_ac',
        'day_off',
        'start_date',
        'price',
        'departure',
        'arrivals',
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

    public function vehicles_type(): BelongsTo
    {
        return $this->belongsTo(VehiclesType::class, 'vehicles_type_id');
    }

    public function counter(): BelongsTo
    {
        return $this->belongsTo(Counter::class, 'starting_point', 'ending_point');
    }

}