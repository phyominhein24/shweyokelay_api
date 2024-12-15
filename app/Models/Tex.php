<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tex extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'amount', 'is_percentage'
    ];

    protected $casts = [
        'is_percentage' => 'boolean'
    ];
}
