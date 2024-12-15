<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'table_number_id',
        'shop_id',
        'customer_id',
        'payment_id',
        'checkin',
        'checkout',
        'table_charge',
        'items_charge',
        'total_time',
        'charge',
        'refund',
        'status'
    ];

    protected $casts = [
        'checkin' => 'datetime: Y-m-d H:i:s',
        'checkout' => 'datetime: Y-m-d H:i:s',
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

        static::creating(function ($order) {
            $order->invoice_number = self::generateInvoiceNumber();
        });
    }

    protected static function generateInvoiceNumber()
    {
        $number = 'INO' . Str::upper(Str::random(6));
        while (Order::where('invoice_number', $number)->exists()) {
            $number = 'INO' . Str::upper(Str::random(6));
        }
        return $number;
    }

    public function tableNumber(): BelongsTo
    {
        return $this->belongsTo(TableNumber::class, 'table_number_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'order_id', 'id');
    }
}
