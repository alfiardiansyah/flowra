<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialReportService
{
    /**
     * Generate complete financial report for given date range and optional filters.
     */
    public function generateReport(
        User $user, 
        string $from, 
        string $to, 
        ?int $accountId = null, 
        ?int $categoryId = null, 
        ?string $search = null
    ): array {
        $userId = $user->id;

        // Base transaction query for the period with user isolation
        $baseQuery = function () use ($userId, $from, $to, $accountId, $categoryId, $search) {
            $query = Transaction::where('transactions.user_id', $userId)
                ->whereBetween('transactions.date', [$from, $to]);

            if ($accountId) {
                $query->where(function ($q) use ($accountId) {
                    $q->where('transactions.account_id', $accountId)
                      ->orWhere('transactions.destination_account_id', $accountId);
                });
            }

            if ($categoryId) {
                $query->where('transactions.category_id', $categoryId);
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('transactions.description', 'LIKE', "%{$search}%")
                      ->orWhere('transactions.notes', 'LIKE', "%{$search}%");
                });
            }

            return $query;
        };

        // 1. Incomes & Expenses (Transfers strictly excluded!)
        $totalIncome = (float) $baseQuery()
            ->where('type', 'income')
            ->sum('amount');

        $totalExpense = (float) $baseQuery()
            ->where('type', 'expense')
            ->sum('amount');

        $netCashFlow = $totalIncome - $totalExpense;

        // 2. Opening Balance calculation
        $accountsOpening = (float) Account::where('user_id', $userId)->where('is_active', true)->sum('opening_balance');
        $priorIncomes = (float) Transaction::where('user_id', $userId)->where('type', 'income')->where('date', '<', $from)->sum('amount');
        $priorExpenses = (float) Transaction::where('user_id', $userId)->where('type', 'expense')->where('date', '<', $from)->sum('amount');
        $openingBalance = $accountsOpening + $priorIncomes - $priorExpenses;
        $closingBalance = $openingBalance + $netCashFlow;

        // 3. Category Breakdown (Expenses)
        $expensesByCategory = $baseQuery()
            ->where('transactions.type', 'expense')
            ->leftJoin('categories', 'transactions.category_id', '=', 'categories.id')
            ->select(
                DB::raw("COALESCE(categories.name, 'Lainnya') as name"),
                DB::raw("COALESCE(categories.icon, 'mixed-leaves') as icon"),
                DB::raw("COALESCE(categories.color, '#FF6B6B') as color"),
                DB::raw('SUM(transactions.amount) as total'),
                DB::raw('COUNT(transactions.id) as count')
            )
            ->groupBy('categories.name', 'categories.icon', 'categories.color')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) use ($totalExpense) {
                $total = (float) $item->total;
                $pct = $totalExpense > 0 ? round(($total / $totalExpense) * 100, 1) : 0;
                return [
                    'name' => $item->name,
                    'icon' => $item->icon,
                    'color' => $item->color,
                    'total' => $total,
                    'count' => (int) $item->count,
                    'percentage' => $pct,
                ];
            });

        // 4. Category Breakdown (Incomes)
        $incomesByCategory = $baseQuery()
            ->where('transactions.type', 'income')
            ->leftJoin('categories', 'transactions.category_id', '=', 'categories.id')
            ->select(
                DB::raw("COALESCE(categories.name, 'Lainnya') as name"),
                DB::raw("COALESCE(categories.icon, 'bouquet') as icon"),
                DB::raw("COALESCE(categories.color, '#87A96B') as color"),
                DB::raw('SUM(transactions.amount) as total'),
                DB::raw('COUNT(transactions.id) as count')
            )
            ->groupBy('categories.name', 'categories.icon', 'categories.color')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) use ($totalIncome) {
                $total = (float) $item->total;
                $pct = $totalIncome > 0 ? round(($total / $totalIncome) * 100, 1) : 0;
                return [
                    'name' => $item->name,
                    'icon' => $item->icon,
                    'color' => $item->color,
                    'total' => $total,
                    'count' => (int) $item->count,
                    'percentage' => $pct,
                ];
            });

        // 5. Account Dynamics in Period
        $accounts = Account::where('user_id', $userId)->where('is_active', true)->get()->map(function (Account $acc) use ($from, $to) {
            $in = (float) Transaction::where('account_id', $acc->id)->where('type', 'income')->whereBetween('date', [$from, $to])->sum('amount');
            $out = (float) Transaction::where('account_id', $acc->id)->where('type', 'expense')->whereBetween('date', [$from, $to])->sum('amount');
            $transfersIn = (float) Transaction::where('destination_account_id', $acc->id)->where('type', 'transfer')->whereBetween('date', [$from, $to])->sum('amount');
            $transfersOut = (float) Transaction::where('account_id', $acc->id)->where('type', 'transfer')->whereBetween('date', [$from, $to])->sum('amount');

            return [
                'account' => $acc,
                'name' => $acc->name,
                'type' => $acc->type_name,
                'icon' => $acc->icon,
                'color' => $acc->color,
                'income' => $in,
                'expense' => $out,
                'transfers_in' => $transfersIn,
                'transfers_out' => $transfersOut,
                'current_balance' => (float) $acc->current_balance,
            ];
        });

        // 6. Detailed Transactions Ledger for Period
        $transactions = $baseQuery()
            ->with(['account', 'destinationAccount', 'category'])
            ->orderBy('date')
            ->orderBy('id')
            ->get();

        // 7. Monthly / Daily Trend
        $fromDate = Carbon::parse($from);
        $toDate = Carbon::parse($to);
        $diffMonths = $fromDate->diffInMonths($toDate);

        $trendData = collect();
        if ($diffMonths <= 1) {
            $cursor = $fromDate->copy();
            while ($cursor->lte($toDate)) {
                $dateKey = $cursor->format('Y-m-d');
                $inc = (float) Transaction::where('user_id', $userId)->where('type', 'income')->where('date', $dateKey)->sum('amount');
                $exp = (float) Transaction::where('user_id', $userId)->where('type', 'expense')->where('date', $dateKey)->sum('amount');

                $trendData->push([
                    'label' => $cursor->format('d M'),
                    'date' => $dateKey,
                    'income' => $inc,
                    'expense' => $exp,
                    'net' => $inc - $exp,
                ]);
                $cursor->addDay();
            }
        } else {
            $cursor = $fromDate->copy()->startOfMonth();
            $endMonth = $toDate->copy()->endOfMonth();
            while ($cursor->lte($endMonth)) {
                $ym = $cursor->format('Y-m');
                $inc = (float) Transaction::where('user_id', $userId)->where('type', 'income')->where('date', 'like', $ym . '%')->sum('amount');
                $exp = (float) Transaction::where('user_id', $userId)->where('type', 'expense')->where('date', 'like', $ym . '%')->sum('amount');

                $trendData->push([
                    'label' => $cursor->format('M Y'),
                    'month' => $ym,
                    'income' => $inc,
                    'expense' => $exp,
                    'net' => $inc - $exp,
                ]);
                $cursor->addMonth();
            }
        }

        return [
            'from' => $from,
            'to' => $to,
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'net_cash_flow' => $netCashFlow,
            'opening_balance' => $openingBalance,
            'closing_balance' => $closingBalance,
            'expenses_by_category' => $expensesByCategory,
            'incomes_by_category' => $incomesByCategory,
            'accounts' => $accounts,
            'transactions' => $transactions,
            'trend_data' => $trendData,
            'savings_rate' => $totalIncome > 0 ? round((max(0, $netCashFlow) / $totalIncome) * 100, 1) : 0,
        ];
    }
}
