<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Budget;
use App\Models\Category;
use App\Models\DebtReceivable;
use App\Models\RecurringTransaction;
use App\Models\Transaction;
use App\Services\BudgetService;
use App\Services\RecurringTransactionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(
        protected BudgetService $budgetService,
        protected RecurringTransactionService $recurringService
    ) {}

    public function index()
    {
        $user = auth()->user();
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth()->format('Y-m-d');
        $endOfMonth = $now->copy()->endOfMonth()->format('Y-m-d');
        $currentMonthKey = $now->format('Y-m');

        // 1. Total Net Worth: Accounts Balance + Outstanding Receivables (Assets) - Outstanding Debts (Liabilities)
        $accounts = Account::where('user_id', $user->id)
            ->where('is_active', true)
            ->orderByDesc('current_balance')
            ->get();
        $totalAccountBalance = (float) $accounts->sum('current_balance');

        $debtsReceivablesTotals = DebtReceivable::where('user_id', $user->id)
            ->whereIn('status', ['unpaid', 'partially_paid'])
            ->selectRaw('type, SUM(amount - paid_amount) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $totalReceivable = (float) ($debtsReceivablesTotals['receivable'] ?? 0);
        $totalDebt = (float) ($debtsReceivablesTotals['debt'] ?? 0);

        $totalNetWorth = $totalAccountBalance + $totalReceivable - $totalDebt;

        // 2. This Month Financial Totals (Excluding Transfers)
        $thisMonthIncome = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $thisMonthExpense = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $netCashFlow = $thisMonthIncome - $thisMonthExpense;

        // Compare with last month
        $lastMonthStart = $now->copy()->subMonth()->startOfMonth()->format('Y-m-d');
        $lastMonthEnd = $now->copy()->subMonth()->endOfMonth()->format('Y-m-d');
        $lastMonthExpense = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('date', [$lastMonthStart, $lastMonthEnd])
            ->sum('amount');
        $expenseDiffPercent = $lastMonthExpense > 0 
            ? round((($thisMonthExpense - $lastMonthExpense) / $lastMonthExpense) * 100, 1) 
            : 0;

        // 3. Last 7 Days chart data (Combined income & expense in single query)
        $startDate = $now->copy()->subDays(6)->startOfDay();
        $endDate = $now->copy()->endOfDay();

        $last7DaysData = Transaction::where('user_id', $user->id)
            ->whereIn('type', ['income', 'expense'])
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->select(
                DB::raw('DATE(date) as date_val'),
                'type',
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('date_val', 'type')
            ->get()
            ->groupBy('type');

        $incomesGrouped = $last7DaysData->get('income', collect())->pluck('total', 'date_val');
        $expensesGrouped = $last7DaysData->get('expense', collect())->pluck('total', 'date_val');

        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateKey = $date->format('Y-m-d');
            $last7Days->push([
                'date' => $dateKey,
                'label' => $date->format('d M'),
                'income' => (float) ($incomesGrouped[$dateKey] ?? 0),
                'expense' => (float) ($expensesGrouped[$dateKey] ?? 0),
            ]);
        }

        // 4. Expenses by Category for Chart (This Month)
        $expenseByCategory = Transaction::where('transactions.user_id', $user->id)
            ->where('transactions.type', 'expense')
            ->whereBetween('transactions.date', [$startOfMonth, $endOfMonth])
            ->leftJoin('categories', 'transactions.category_id', '=', 'categories.id')
            ->select(
                DB::raw("COALESCE(categories.name, 'Lainnya') as kategori"),
                DB::raw('SUM(transactions.amount) as total'),
                DB::raw("COALESCE(categories.color, '#FF6B6B') as color")
            )
            ->groupBy('categories.name', 'categories.color')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) {
                return [
                    'kategori' => $item->kategori,
                    'total' => (float) $item->total,
                    'color' => $item->color,
                ];
            });

        // 5. Recent Transactions (last 6 items with full relations)
        $recent = Transaction::with(['account', 'destinationAccount', 'category'])
            ->where('user_id', $user->id)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        // 6. Budget Highlights (This Month)
        $budgetSummary = $this->budgetService->getMonthlyBudgets($user, $currentMonthKey);

        // 7. Upcoming Recurring Bills / Incomes (due within 14 days)
        $upcomingRecurring = $this->recurringService->getUpcoming($user, 14);

        // 8. Pending Debts & Receivables
        $pendingDebts = DebtReceivable::where('user_id', $user->id)
            ->pending()
            ->orderBy('due_date')
            ->limit(4)
            ->get();

        // Categories & Accounts for quick modal
        $allCategories = Category::forUser($user->id)->orderBy('name')->get();
        $allAccounts = $accounts;

        return view('dashboard.index', compact(
            'totalNetWorth',
            'totalAccountBalance',
            'totalReceivable',
            'totalDebt',
            'thisMonthIncome',
            'thisMonthExpense',
            'netCashFlow',
            'expenseDiffPercent',
            'accounts',
            'last7Days',
            'expenseByCategory',
            'recent',
            'budgetSummary',
            'upcomingRecurring',
            'pendingDebts',
            'allCategories',
            'allAccounts'
        ));
    }
}
