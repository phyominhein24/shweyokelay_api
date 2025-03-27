<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyRoute extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['route_id', 'driver_name', 'car_no', 'date'];

    public function route()
    {
        return $this->belongsTo(Routes::class);
    }
}
