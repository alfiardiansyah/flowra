<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'amount',
        'month',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    protected ?float $spentAmountCache = null;

    public function setSpentAmount(float $amount): self
    {
        $this->spentAmountCache = $amount;
        return $this;
    }

    public function getSpentAmountAttribute(): float
    {
        if ($this->spentAmountCache !== null) {
            return $this->spentAmountCache;
        }

        $userId = $this->user_id;
        $categoryId = $this->category_id;
        $month = $this->month; // 'YYYY-MM'

        // Check category and its subcategories if any
        $categoryIds = [$categoryId];
        $subCategoryIds = Category::where('parent_id', $categoryId)->pluck('id')->toArray();
        $allCategoryIds = array_merge($categoryIds, $subCategoryIds);

        return $this->spentAmountCache = (float) Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereIn('category_id', $allCategoryIds)
            ->where('date', 'like', $month . '%')
            ->sum('amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, (float) $this->amount - $this->spent_amount);
    }

    public function getPercentageAttribute(): float
    {
        if ($this->amount <= 0) return 0;
        return round(($this->spent_amount / $this->amount) * 100, 1);
    }

    public function getIsOverBudgetAttribute(): bool
    {
        return $this->spent_amount > $this->amount;
    }

    public function getStatusColorAttribute(): string
    {
        $pct = $this->percentage;
        if ($pct >= 100) return '#FF6B6B'; // Coral red (danger)
        if ($pct >= 80) return '#FFD700';  // Golden yellow (warning)
        return '#87A96B';                  // Sage green (good)
    }
}
