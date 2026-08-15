<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DebtReceivableController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecurringTransactionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

// Global Route Parameter Constraints (Prevents PostgreSQL 22P02 invalid integer syntax)
Route::pattern('account', '[0-9]+');
Route::pattern('transaction', '[0-9]+');
Route::pattern('debt', '[0-9]+');
Route::pattern('payment', '[0-9]+');
Route::pattern('budget', '[0-9]+');
Route::pattern('recurring', '[0-9]+');
Route::pattern('category', '[0-9]+');
Route::pattern('income', '[0-9]+');
Route::pattern('expense', '[0-9]+');

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Unified Transactions Hub
    Route::resource('transactions', TransactionController::class);

    // Accounts / Wallets
    Route::post('accounts/{account}/recalculate', [AccountController::class, 'recalculate'])->name('accounts.recalculate');
    Route::resource('accounts', AccountController::class);

    // Budgets
    Route::post('budgets/copy-previous', [BudgetController::class, 'copyPrevious'])->name('budgets.copy-previous');
    Route::resource('budgets', BudgetController::class);

    // Recurring Transactions
    Route::post('recurring/{recurring}/post-now', [RecurringTransactionController::class, 'postNow'])->name('recurring.post-now');
    Route::patch('recurring/{recurring}/toggle', [RecurringTransactionController::class, 'toggleStatus'])->name('recurring.toggle');
    Route::resource('recurring', RecurringTransactionController::class);

    // Debts & Receivables (Hutang & Piutang)
    Route::post('debts/{debt}/payment', [DebtReceivableController::class, 'recordPayment'])->name('debts.payment');
    Route::delete('debts/payments/{payment}', [DebtReceivableController::class, 'deletePayment'])->name('debts.payments.destroy');
    Route::resource('debts', DebtReceivableController::class);

    // Categories
    Route::resource('categories', CategoryController::class);

    // Reports & Analytics
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-csv', [ReportController::class, 'exportCsv'])->name('reports.export-csv');

    // Backward Compatible Income & Expense Routes
    Route::resource('incomes', IncomeController::class);
    Route::resource('expenses', ExpenseController::class);

    // Profile & Settings Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/reset-financial-data', [ProfileController::class, 'resetFinancialData'])->name('profile.reset-financial-data');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
