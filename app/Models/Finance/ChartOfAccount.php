<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    protected $fillable = [
        'code',
        'name',
        'type',
        'category',
        'parent_id',
        'is_active',
        'description',
    ];

    public static function getTypes(): array
    {
        return [
            'Asset' => 'Asset',
            'Liability' => 'Liability',
            'Equity' => 'Equity',
            'Revenue' => 'Revenue',
            'Expense' => 'Expense',
        ];
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }
}
