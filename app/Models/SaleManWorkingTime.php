<?php

namespace App\Models;

use App\Traits\BasicAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleManWorkingTime extends Model
{
    use BasicAudit, HasFactory, SoftDeletes;

    protected $fillable = [
        'cashier_id',
        'table_id',
        'total_time'
    ];
}
