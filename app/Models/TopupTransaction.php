<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TopupTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_item_id',
        'provider',
        'ref_id',
        'provider_product_code',
        'customer_number',
        'price',
        'status',
        'response_code',
        'message',
        'serial_number',
        'submitted_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'submitted_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke OrderItem
     */
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    /**
     * Relasi ke Callbacks
     */
    public function callbacks(): HasMany
    {
        return $this->hasMany(TopupCallback::class);
    }
}
