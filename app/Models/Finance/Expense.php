<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'expense_number',
        'expense_date',
        'category',
        'vendor_name',
        'amount',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'expense_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }
}
