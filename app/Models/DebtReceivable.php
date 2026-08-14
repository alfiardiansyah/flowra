<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DebtReceivable extends Model
{
    use HasFactory;

    protected $table = 'debts_receivables';

    protected $fillable = [
        'user_id',
        'type', // debt (hutang), receivable (piutang)
        'person_name',
        'amount',
        'paid_amount',
        'date',
        'due_date',
        'account_id',
        'status', // unpaid, partially_paid, paid
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'date' => 'date',
        'due_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(DebtReceivablePayment::class, 'debt_receivable_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'debt_receivable_id');
    }

    public function scopeDebt($query)
    {
        return $query->where('type', 'debt');
    }

    public function scopeReceivable($query)
    {
        return $query->where('type', 'receivable');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['unpaid', 'partially_paid']);
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, (float) $this->amount - (float) $this->paid_amount);
    }

    public function getPercentagePaidAttribute(): float
    {
        if ($this->amount <= 0) return 0;
        return round(($this->paid_amount / $this->amount) * 100, 1);
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'debt' ? 'Hutang (Saya Berhutang)' : 'Piutang (Orang Lain Berhutang)';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'paid' => 'flora-badge-success',
            'partially_paid' => 'flora-badge-info',
            'unpaid' => 'flora-badge-danger',
            default => 'flora-badge-info',
        };
    }
}
