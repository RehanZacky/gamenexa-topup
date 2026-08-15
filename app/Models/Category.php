<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'publisher',
        'image',
        'banner',
        'instruction',
        'has_zone_id',
        'zone_id_label',
        'user_id_label',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'has_zone_id' => 'boolean',
        ];
    }

    /**
     * Relasi ke Products
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class)->orderBy('sort_order', 'asc');
    }

    /**
     * Scope kategori aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
