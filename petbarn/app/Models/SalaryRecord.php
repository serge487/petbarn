<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryRecord extends Model
{
    protected $fillable = [
        'employee_id',
        'recorded_by',
        'month',
        'year',
        'amount_usd',
        'bonus_usd',
        'deduction_usd',
        'net_usd',
        'payment_status',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'integer',
            'year' => 'integer',
            'amount_usd' => 'decimal:2',
            'bonus_usd' => 'decimal:2',
            'deduction_usd' => 'decimal:2',
            'net_usd' => 'decimal:2',
            'paid_at' => 'date',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
