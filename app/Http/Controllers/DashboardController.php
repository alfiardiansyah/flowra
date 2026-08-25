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

    public function index(Request $request)
    {
        $user = auth()->user();
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth()->format('Y-m-d');
        $endOfMonth = $now->copy()->endOfMonth()->format('Y-m-d');
        $currentMonthKey = $now->format('Y-m');

        // Accounts list owned by user
        $accounts = Account::where('user_id', $user->id)
            ->where('is_active', true)
            ->orderByDesc('current_balance')
            ->get();

        // Optional Account Filter Validation (Tenant Isolation Security Check)
        $selectedAccountId = null;
        $selectedAccount = null;
        if ($request->filled('account_id')) {
            $candidateId = (int) $request->query('account_id');
            $selectedAccount = $accounts->firstWhere('id', $candidateId);

            // Security: If account_id is invalid or belongs to another user, abort 403
            if (!$selectedAccount) {
                // Verify if candidateId exists at all for another user
                $otherUserAcc = Account::where('id', $candidateId)->exists();
                if ($otherUserAcc) {
                    abort(403, 'Akses akun tidak diizinkan.');
                }
            } else {
                $selectedAccountId = $selectedAccount->id;
            }
        }

        // 1. Total Net Worth & Account Balances
        if ($selectedAccountId) {
            $totalAccountBalance = (float) $selectedAccount->current_balance;
        } else {
            $totalAccountBalance = (float) $accounts->sum('current_balance');
        }

        // Debts / Receivables Filtered Query
        $debtsQuery = DebtReceivable::where('user_id', $user->id)
            ->whereIn('status', ['unpaid', 'partially_paid']);

        if ($selectedAccountId) {
            $debtsQuery->where('account_id', $selectedAccountId);
        }

        $debtsReceivablesTotals = $debtsQuery
            ->selectRaw('type, SUM(amount - paid_amount) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $totalReceivable = (float) ($debtsReceivablesTotals['receivable'] ?? 0);
        $totalDebt = (float) ($debtsReceivablesTotals['debt'] ?? 0);

        $totalNetWorth = $totalAccountBalance + $totalReceivable - $totalDebt;

        // 2. This Month Financial Totals (Excluding Transfers unless filtered)
        $incomeQuery = Transaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->whereBetween('date', [$startOfMonth, $endOfMonth]);

        $expenseQuery = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('date', [$startOfMonth, $endOfMonth]);

        if ($selectedAccountId) {
            $incomeQuery->where('account_id', $selectedAccountId);
            $expenseQuery->where('account_id', $selectedAccountId);
        }

        $thisMonthIncome = (float) $incomeQuery->sum('amount');
        $thisMonthExpense = (float) $expenseQuery->sum('amount');

        $netCashFlow = $thisMonthIncome - $thisMonthExpense;

        // Compare with last month
        $lastMonthStart = $now->copy()->subMonth()->startOfMonth()->format('Y-m-d');
        $lastMonthEnd = $now->copy()->subMonth()->endOfMonth()->format('Y-m-d');

        $lastMonthExpenseQuery = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('date', [$lastMonthStart, $lastMonthEnd]);

        if ($selectedAccountId) {
            $lastMonthExpenseQuery->where('account_id', $selectedAccountId);
        }

        $lastMonthExpense = (float) $lastMonthExpenseQuery->sum('amount');

        $expenseDiffPercent = $lastMonthExpense > 0 
            ? round((($thisMonthExpense - $lastMonthExpense) / $lastMonthExpense) * 100, 1) 
            : 0;

        // 3. Last 7 Days chart data (Combined income & expense in single query)
        $startDate = $now->copy()->subDays(6)->startOfDay();
        $endDate = $now->copy()->endOfDay();

        $trendQuery = Transaction::where('user_id', $user->id)
            ->whereIn('type', ['income', 'expense'])
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);

        if ($selectedAccountId) {
            $trendQuery->where('account_id', $selectedAccountId);
        }

        $last7DaysData = $trendQuery
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
        $categoryChartQuery = Transaction::where('transactions.user_id', $user->id)
            ->where('transactions.type', 'expense')
            ->whereBetween('transactions.date', [$startOfMonth, $endOfMonth]);

        if ($selectedAccountId) {
            $categoryChartQuery->where('transactions.account_id', $selectedAccountId);
        }

        $expenseByCategory = $categoryChartQuery
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
        $recentQuery = Transaction::with(['account', 'destinationAccount', 'category'])
            ->where('user_id', $user->id);

        if ($selectedAccountId) {
            $recentQuery->where(function ($q) use ($selectedAccountId) {
                $q->where('account_id', $selectedAccountId)
                  ->orWhere('destination_account_id', $selectedAccountId);
            });
        }

        $recent = $recentQuery
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        // 6. Budget Highlights (This Month - Category based)
        $budgetSummary = $this->budgetService->getMonthlyBudgets($user, $currentMonthKey);

        // 7. Upcoming Recurring Bills / Incomes (due within 14 days)
        $upcomingRecurring = $this->recurringService->getUpcoming($user, 14);
        if ($selectedAccountId) {
            $upcomingRecurring = $upcomingRecurring->filter(function ($rec) use ($selectedAccountId) {
                return $rec->account_id == $selectedAccountId || $rec->destination_account_id == $selectedAccountId;
            });
        }

        // 8. Pending Debts & Receivables
        $pendingDebtsQuery = DebtReceivable::where('user_id', $user->id)
            ->pending();

        if ($selectedAccountId) {
            $pendingDebtsQuery->where('account_id', $selectedAccountId);
        }

        $pendingDebts = $pendingDebtsQuery
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
            'selectedAccountId',
            'selectedAccount',
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
