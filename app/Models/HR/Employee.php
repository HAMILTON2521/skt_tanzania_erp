<?php

namespace App\Models\HR;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'employees';

    protected $fillable = [
        'employee_code',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'date_of_birth',
        'hire_date',
        'department_id',
        'position',
        'salary',
        'bank_name',
        'bank_account',
        'tin_number',
        'nssf_number',
        'wcf_number',
        'emergency_contact',
        'emergency_phone',
        'status',
        'photo',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'hire_date' => 'date',
            'salary' => 'decimal:2',
        ];
    }

    public const STATUS_ACTIVE = 'active';
    public const STATUS_ON_LEAVE = 'on_leave';
    public const STATUS_TERMINATED = 'terminated';
    public const STATUS_SUSPENDED = 'suspended';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_ON_LEAVE => 'On Leave',
            self::STATUS_TERMINATED => 'Terminated',
            self::STATUS_SUSPENDED => 'Suspended',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function calculatePAYE(float $grossPay): float
    {
        if ($grossPay <= 270000) {
            return 0.0;
        }

        if ($grossPay <= 520000) {
            return ($grossPay - 270000) * 0.09;
        }

        if ($grossPay <= 760000) {
            return 22500 + (($grossPay - 520000) * 0.2);
        }

        if ($grossPay <= 1000000) {
            return 70500 + (($grossPay - 760000) * 0.25);
        }

        return 130500 + (($grossPay - 1000000) * 0.3);
    }

    public function calculateNSSF(float $grossPay): float
    {
        return min($grossPay * 0.1, 200000);
    }

    public function calculateWCF(float $grossPay): float
    {
        return $grossPay * 0.01;
    }
}
