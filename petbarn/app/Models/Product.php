<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'sku',
        'barcode',
        'item_name',
        'description',
        'image',
        'category',
        'subcategory',
        'measurement_unit',
        'measurement_value',
        'unit_price_usd',
        'toters_price',
        'source',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'measurement_value' => 'decimal:3',
            'unit_price_usd' => 'decimal:2',
            'toters_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function transferItems(): HasMany
    {
        return $this->hasMany(TransferItem::class);
    }
}
