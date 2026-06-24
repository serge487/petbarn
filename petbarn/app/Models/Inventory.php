<?php

namespace App\Models;

use App\Observers\InventoryObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(InventoryObserver::class)]
class Inventory extends Model
{
    public $timestamps = false;

    protected $table = 'inventory';

    protected $fillable = [
        'branch_id',
        'product_id',
        'quantity',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'updated_at' => 'datetime',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
