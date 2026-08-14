<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecurringTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type', // income, expense, transfer
        'account_id',
        'destination_account_id',
        'category_id',
        'amount',
        'description',
        'frequency', // daily, weekly, monthly, yearly
        'start_date',
        'end_date',
        'next_run_date',
        'last_run_date',
        'is_active',
        'auto_record',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'next_run_date' => 'date',
        'last_run_date' => 'date',
        'is_active' => 'boolean',
        'auto_record' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function destinationAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'destination_account_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDue($query, $date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();
        return $query->where('is_active', true)
            ->where('next_run_date', '<=', $date->format('Y-m-d'));
    }

    public function getFrequencyLabelAttribute(): string
    {
        return match ($this->frequency) {
            'daily' => 'Harian (Daily)',
            'weekly' => 'Mingguan (Weekly)',
            'monthly' => 'Bulanan (Monthly)',
            'yearly' => 'Tahunan (Yearly)',
            default => ucfirst($this->frequency),
        };
    }

    public function calculateNextRunDate(Carbon $currentDate): Carbon
    {
        return match ($this->frequency) {
            'daily' => $currentDate->copy()->addDay(),
            'weekly' => $currentDate->copy()->addWeek(),
            'monthly' => $currentDate->copy()->addMonth(),
            'yearly' => $currentDate->copy()->addYear(),
            default => $currentDate->copy()->addMonth(),
        };
    }
}
