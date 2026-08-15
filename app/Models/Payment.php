<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'provider',
        'transaction_id',
        'payment_type',
        'payment_method_code',
        'gross_amount',
        'transaction_status',
        'fraud_status',
        'snap_token',
        'checkout_url',
        'qr_string',
        'va_number',
        'bank_name',
        'paid_at',
        'expired_at',
    ];

    protected function casts(): array
    {
        return [
            'gross_amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'expired_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke Order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relasi ke PaymentLogs
     */
    public function logs(): HasMany
    {
        return $this->hasMany(PaymentLog::class);
    }
}
