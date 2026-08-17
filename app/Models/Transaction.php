<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type', // income, expense, transfer
        'account_id',
        'destination_account_id',
        'category_id',
        'amount',
        'date',
        'description',
        'notes',
        'recurring_transaction_id',
        'debt_receivable_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
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

    public function recurringTransaction(): BelongsTo
    {
        return $this->belongsTo(RecurringTransaction::class);
    }

    public function debtReceivable(): BelongsTo
    {
        return $this->belongsTo(DebtReceivable::class);
    }

    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopeTransfer($query)
    {
        return $query->where('type', 'transfer');
    }

    public function scopeForPeriod($query, $from, $to)
    {
        return $query->whereBetween('date', [$from, $to]);
    }

    public function getFormattedAmountAttribute(): string
    {
        $prefix = match ($this->type) {
            'income' => '+ Rp ',
            'expense' => '- Rp ',
            'transfer' => '⇄ Rp ',
        };
        return $prefix . number_format($this->amount, 0, ',', '.');
    }

    public function getTypeBadgeClassAttribute(): string
    {
        return match ($this->type) {
            'income' => 'flora-badge-success',
            'expense' => 'flora-badge-danger',
            'transfer' => 'flora-badge-info',
        };
    }
}
