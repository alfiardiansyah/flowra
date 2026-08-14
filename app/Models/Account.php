<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'account_number',
        'opening_balance',
        'current_balance',
        'currency',
        'color',
        'icon',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'account_id');
    }

    public function incomingTransfers(): HasMany
    {
        return $this->hasMany(Transaction::class, 'destination_account_id');
    }

    public function recurringTransactions(): HasMany
    {
        return $this->hasMany(RecurringTransaction::class, 'account_id');
    }

    public function debtsReceivables(): HasMany
    {
        return $this->hasMany(DebtReceivable::class, 'account_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getFormattedBalanceAttribute(): string
    {
        return 'Rp ' . number_format($this->current_balance, 0, ',', '.');
    }

    public function getTypeNameAttribute(): string
    {
        return match ($this->type) {
            'cash' => 'Tunai / Cash',
            'bank' => 'Rekening Bank',
            'ewallet' => 'Dompet Digital / E-Wallet',
            'savings' => 'Tabungan',
            'credit_card' => 'Kartu Kredit',
            'investment' => 'Investasi',
            default => 'Lainnya',
        };
    }
}
