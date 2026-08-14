<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Budget;
use App\Models\Category;
use App\Models\DebtReceivable;
use App\Models\RecurringTransaction;
use App\Models\Transaction;
use App\Models\User;
use App\Services\BudgetService;
use App\Services\DebtReceivableService;
use App\Services\FinancialReportService;
use App\Services\RecurringTransactionService;
use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Account $bcaAccount;
    protected Account $cashAccount;
    protected Category $salaryCategory;
    protected Category $foodCategory;
    protected TransactionService $transactionService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->bcaAccount = Account::create([
            'user_id' => $this->user->id,
            'name' => 'BCA',
            'type' => 'bank',
            'opening_balance' => 1000000,
            'current_balance' => 1000000,
            'currency' => 'IDR',
            'is_active' => true,
        ]);

        $this->cashAccount = Account::create([
            'user_id' => $this->user->id,
            'name' => 'Tunai',
            'type' => 'cash',
            'opening_balance' => 200000,
            'current_balance' => 200000,
            'currency' => 'IDR',
            'is_active' => true,
        ]);

        $this->salaryCategory = Category::create([
            'user_id' => $this->user->id,
            'name' => 'Gaji',
            'type' => 'income',
            'icon' => 'sunflower',
            'color' => '#87A96B',
        ]);

        $this->foodCategory = Category::create([
            'user_id' => $this->user->id,
            'name' => 'Makanan',
            'type' => 'expense',
            'icon' => 'apple',
            'color' => '#FF6B6B',
        ]);

        $this->transactionService = app(TransactionService::class);
    }

    public function test_user_can_create_income_transaction_and_account_balance_increases(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('transactions.store'), [
            'type' => 'income',
            'amount' => 500000,
            'account_id' => $this->bcaAccount->id,
            'category_id' => $this->salaryCategory->id,
            'date' => now()->format('Y-m-d'),
            'description' => 'Gaji Bulanan',
        ]);

        $response->assertRedirect(route('transactions.index'));
        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user->id,
            'type' => 'income',
            'amount' => 500000,
            'description' => 'Gaji Bulanan',
        ]);

        $this->bcaAccount->refresh();
        $this->assertEquals(1500000, $this->bcaAccount->current_balance);
    }

    public function test_user_can_create_expense_transaction_and_account_balance_decreases(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('transactions.store'), [
            'type' => 'expense',
            'amount' => 150000,
            'account_id' => $this->cashAccount->id,
            'category_id' => $this->foodCategory->id,
            'date' => now()->format('Y-m-d'),
            'description' => 'Makan Siang Restoran',
        ]);

        $response->assertRedirect(route('transactions.index'));
        $this->cashAccount->refresh();
        $this->assertEquals(50000, $this->cashAccount->current_balance);
    }

    public function test_transfer_between_accounts_adjusts_balances_without_creating_income_or_expense(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('transactions.store'), [
            'type' => 'transfer',
            'amount' => 300000,
            'account_id' => $this->bcaAccount->id,
            'destination_account_id' => $this->cashAccount->id,
            'date' => now()->format('Y-m-d'),
            'description' => 'Tarik Tunai dari BCA',
        ]);

        $response->assertRedirect(route('transactions.index'));

        $this->bcaAccount->refresh();
        $this->cashAccount->refresh();

        $this->assertEquals(700000, $this->bcaAccount->current_balance);
        $this->assertEquals(500000, $this->cashAccount->current_balance);

        // Report check: transfers must not count as income or expense
        $reportService = app(FinancialReportService::class);
        $today = now()->format('Y-m-d');
        $report = $reportService->generateReport($this->user, $today, $today);

        $this->assertEquals(0, $report['total_income']);
        $this->assertEquals(0, $report['total_expense']);
        $this->assertEquals(0, $report['net_cash_flow']);
    }

    public function test_budget_calculation_and_over_budget_detection(): void
    {
        $this->actingAs($this->user);
        $currentMonth = now()->format('Y-m');

        $budget = Budget::create([
            'user_id' => $this->user->id,
            'category_id' => $this->foodCategory->id,
            'amount' => 400000,
            'month' => $currentMonth,
        ]);

        // Add expense within budget
        $this->transactionService->createTransaction($this->user, [
            'type' => 'expense',
            'amount' => 300000,
            'account_id' => $this->cashAccount->id,
            'category_id' => $this->foodCategory->id,
            'date' => now()->format('Y-m-d'),
            'description' => 'Belanja makanan',
        ]);

        $budgetService = app(BudgetService::class);
        $summary = $budgetService->getMonthlyBudgets($this->user, $currentMonth);

        $this->assertEquals(400000, $summary['total_budget']);
        $this->assertEquals(300000, $summary['total_spent']);
        $this->assertEquals(100000, $summary['total_remaining']);
        $this->assertEquals(75.0, $summary['total_percentage']);
        $this->assertFalse($summary['is_over_budget']);

        // Add expense that exceeds budget
        $this->transactionService->createTransaction($this->user, [
            'type' => 'expense',
            'amount' => 150000,
            'account_id' => $this->cashAccount->id,
            'category_id' => $this->foodCategory->id,
            'date' => now()->format('Y-m-d'),
            'description' => 'Makan malam',
        ]);

        $summaryAfter = $budgetService->getMonthlyBudgets($this->user, $currentMonth);
        $this->assertEquals(450000, $summaryAfter['total_spent']);
        $this->assertTrue($summaryAfter['is_over_budget']);
    }

    public function test_recurring_transaction_post_advances_next_run_date(): void
    {
        $this->actingAs($this->user);
        $startDate = Carbon::parse('2026-08-01');

        $recurring = RecurringTransaction::create([
            'user_id' => $this->user->id,
            'type' => 'expense',
            'account_id' => $this->bcaAccount->id,
            'category_id' => $this->foodCategory->id,
            'amount' => 100000,
            'description' => 'Langganan Internet',
            'frequency' => 'monthly',
            'start_date' => $startDate,
            'next_run_date' => $startDate,
            'is_active' => true,
        ]);

        $recurringService = app(RecurringTransactionService::class);
        $tx = $recurringService->postRecurringTransaction($recurring);

        $this->assertDatabaseHas('transactions', [
            'recurring_transaction_id' => $recurring->id,
            'amount' => 100000,
        ]);

        $recurring->refresh();
        $this->assertEquals('2026-09-01', $recurring->next_run_date->format('Y-m-d'));
        $this->assertEquals('2026-08-01', $recurring->last_run_date->format('Y-m-d'));
    }

    public function test_debt_and_receivable_payment_tracking(): void
    {
        $this->actingAs($this->user);

        $debtService = app(DebtReceivableService::class);

        // 1. User lends money to Andi (Receivable Rp 500.000 from BCA Account having Rp 1.000.000)
        $debt = $debtService->createDebtReceivable($this->user, [
            'type' => 'receivable',
            'person_name' => 'Andi',
            'amount' => 500000,
            'date' => now()->format('Y-m-d'),
            'account_id' => $this->bcaAccount->id,
            'notes' => 'Pinjaman awal',
        ]);

        $this->bcaAccount->refresh();
        $this->assertEquals(500000, $this->bcaAccount->current_balance, 'Saldo BCA harus berkurang dari 1.000.000 menjadi 500.000');
        $this->assertEquals(500000, $debt->remaining_amount);
        $this->assertEquals('unpaid', $debt->status);

        // 2. Andi makes partial payment of Rp 200.000 into BCA
        $payment1 = $debtService->recordPayment($debt, [
            'amount' => 200000,
            'account_id' => $this->bcaAccount->id,
            'date' => now()->format('Y-m-d'),
            'notes' => 'Cicilan 1',
        ]);

        $this->bcaAccount->refresh();
        $debt->refresh();
        $this->assertEquals(700000, $this->bcaAccount->current_balance, 'Saldo BCA harus bertambah 200.000 menjadi 700.000');
        $this->assertEquals(200000, $debt->paid_amount);
        $this->assertEquals(300000, $debt->remaining_amount);
        $this->assertEquals('partially_paid', $debt->status);

        // 3. Andi pays the remaining Rp 300.000
        $payment2 = $debtService->recordPayment($debt, [
            'amount' => 300000,
            'account_id' => $this->bcaAccount->id,
            'date' => now()->format('Y-m-d'),
            'notes' => 'Pelunasan',
        ]);

        $this->bcaAccount->refresh();
        $debt->refresh();
        $this->assertEquals(1000000, $this->bcaAccount->current_balance, 'Saldo BCA harus kembali penuh ke 1.000.000');
        $this->assertEquals(500000, $debt->paid_amount);
        $this->assertEquals(0, $debt->remaining_amount);
        $this->assertEquals('paid', $debt->status);
    }

    public function test_user_data_isolation_security(): void
    {
        $userB = User::factory()->create();
        $accountB = Account::create([
            'user_id' => $userB->id,
            'name' => 'Account User B',
            'type' => 'bank',
            'opening_balance' => 5000000,
            'current_balance' => 5000000,
        ]);

        // Attempting to view or edit User B's account while authenticated as User A
        $this->actingAs($this->user);

        $responseShow = $this->get(route('accounts.show', $accountB));
        $responseShow->assertStatus(403);

        $responseEdit = $this->get(route('accounts.edit', $accountB));
        $responseEdit->assertStatus(403);
    }
}
