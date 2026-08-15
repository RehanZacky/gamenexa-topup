<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TopupCallback extends Model
{
    use HasFactory;

    protected $fillable = [
        'topup_transaction_id',
        'event',
        'payload',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'processed_at' => 'datetime',
        ];
    }

    public function topupTransaction(): BelongsTo
    {
        return $this->belongsTo(TopupTransaction::class);
    }
}
