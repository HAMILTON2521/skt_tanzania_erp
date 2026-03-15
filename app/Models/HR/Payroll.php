<?php

namespace App\Models\HR;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'payroll_period',
        'payment_date',
        'basic_salary',
        'allowances',
        'overtime',
        'bonus',
        'gross_pay',
        'paye',
        'nssf',
        'wcf',
        'other_deductions',
        'total_deductions',
        'net_pay',
        'status',
        'processed_by',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'processed_at' => 'datetime',
            'basic_salary' => 'decimal:2',
            'allowances' => 'decimal:2',
            'overtime' => 'decimal:2',
            'bonus' => 'decimal:2',
            'gross_pay' => 'decimal:2',
            'paye' => 'decimal:2',
            'nssf' => 'decimal:2',
            'wcf' => 'decimal:2',
            'other_deductions' => 'decimal:2',
            'total_deductions' => 'decimal:2',
            'net_pay' => 'decimal:2',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
