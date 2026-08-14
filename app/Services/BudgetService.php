<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

class BudgetService
{
    /**
     * Get monthly budget summaries with category spending and health indicators.
     */
    public function getMonthlyBudgets(User $user, string $month = null): array
    {
        $month = $month ?: Carbon::now()->format('Y-m');

        $budgets = Budget::with('category')
            ->where('user_id', $user->id)
            ->where('month', $month)
            ->get();

        if ($budgets->isEmpty()) {
            return [
                'month' => $month,
                'items' => collect(),
                'total_budget' => 0,
                'total_spent' => 0,
                'total_remaining' => 0,
                'total_percentage' => 0,
                'is_over_budget' => false,
            ];
        }

        // Fetch subcategories mapping for all budget categories in 1 query
        $categoryIds = $budgets->pluck('category_id')->unique()->filter()->values()->toArray();
        $subCategoriesGrouped = Category::whereIn('parent_id', $categoryIds)
            ->get()
            ->groupBy('parent_id');

        // Fetch spent amounts for all relevant category IDs in 1 aggregate query
        $allTargetCategoryIds = collect($categoryIds);
        foreach ($categoryIds as $catId) {
            if (isset($subCategoriesGrouped[$catId])) {
                $allTargetCategoryIds = $allTargetCategoryIds->merge($subCategoriesGrouped[$catId]->pluck('id'));
            }
        }

        $spentGrouped = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereIn('category_id', $allTargetCategoryIds->unique()->values()->toArray())
            ->where('date', 'like', $month . '%')
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        $totalBudget = 0;
        $totalSpent = 0;

        $items = $budgets->map(function (Budget $budget) use ($subCategoriesGrouped, $spentGrouped, &$totalBudget, &$totalSpent) {
            $catId = $budget->category_id;
            $catIds = [$catId];
            if (isset($subCategoriesGrouped[$catId])) {
                $catIds = array_merge($catIds, $subCategoriesGrouped[$catId]->pluck('id')->toArray());
            }

            $spent = 0.0;
            foreach ($catIds as $id) {
                $spent += (float) ($spentGrouped[$id] ?? 0);
            }

            $budget->setSpentAmount($spent);

            $remaining = $budget->remaining_amount;
            $percentage = $budget->percentage;
            $isOver = $budget->is_over_budget;

            $totalBudget += (float) $budget->amount;
            $totalSpent += $spent;

            return [
                'budget' => $budget,
                'category' => $budget->category,
                'amount' => (float) $budget->amount,
                'spent' => $spent,
                'remaining' => $remaining,
                'percentage' => $percentage,
                'is_over' => $isOver,
                'status_color' => $budget->status_color,
            ];
        });

        $totalRemaining = max(0, $totalBudget - $totalSpent);
        $totalPercentage = $totalBudget > 0 ? round(($totalSpent / $totalBudget) * 100, 1) : 0;

        return [
            'month' => $month,
            'items' => $items,
            'total_budget' => $totalBudget,
            'total_spent' => $totalSpent,
            'total_remaining' => $totalRemaining,
            'total_percentage' => $totalPercentage,
            'is_over_budget' => $totalSpent > $totalBudget,
        ];
    }

    /**
     * Copy previous month's budgets into the target month.
     */
    public function copyPreviousMonthBudgets(User $user, string $targetMonth): int
    {
        $prevMonth = Carbon::parse($targetMonth . '-01')->subMonth()->format('Y-m');

        $prevBudgets = Budget::where('user_id', $user->id)
            ->where('month', $prevMonth)
            ->get();

        $copiedCount = 0;
        foreach ($prevBudgets as $prev) {
            $exists = Budget::where('user_id', $user->id)
                ->where('category_id', $prev->category_id)
                ->where('month', $targetMonth)
                ->exists();

            if (!$exists) {
                Budget::create([
                    'user_id' => $user->id,
                    'category_id' => $prev->category_id,
                    'amount' => $prev->amount,
                    'month' => $targetMonth,
                    'notes' => $prev->notes,
                ]);
                $copiedCount++;
            }
        }

        return $copiedCount;
    }
}
