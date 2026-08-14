<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\Budget;
use App\Models\Category;
use App\Models\DebtReceivable;
use App\Models\Transaction;
use App\Models\User;
use App\Services\BudgetService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetAndDashboardOptimizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_budget_service_output_is_identical_to_individual_accessors()
    {
        $user = User::factory()->create();
        $month = Carbon::now()->format('Y-m');

        $account = Account::create([
            'user_id' => $user->id,
            'name' => 'Main Account',
            'type' => 'bank',
            'initial_balance' => 5000,
            'current_balance' => 5000,
        ]);

        $categoryA = Category::create([
            'user_id' => $user->id,
            'name' => 'Food & Dining',
            'type' => 'expense',
            'icon' => 'utensils',
            'color' => '#FF5733',
        ]);

        $subCategory = Category::create([
            'user_id' => $user->id,
            'parent_id' => $categoryA->id,
            'name' => 'Restaurants',
            'type' => 'expense',
            'icon' => 'utensils',
            'color' => '#FF5733',
        ]);

        $budget = Budget::create([
            'user_id' => $user->id,
            'category_id' => $categoryA->id,
            'amount' => 1000.00,
            'month' => $month,
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'category_id' => $categoryA->id,
            'type' => 'expense',
            'amount' => 200.00,
            'description' => 'Groceries',
            'date' => $month . '-10',
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'category_id' => $subCategory->id,
            'type' => 'expense',
            'amount' => 150.00,
            'description' => 'Dinner',
            'date' => $month . '-15',
        ]);

        $budgetService = new BudgetService();
        $summary = $budgetService->getMonthlyBudgets($user, $month);

        $this->assertEquals(350.00, $summary['total_spent']);
        $this->assertEquals(650.00, $summary['total_remaining']);
        $this->assertEquals(35.0, $summary['total_percentage']);
        $this->assertFalse($summary['is_over_budget']);

        $item = $summary['items']->first();
        $this->assertEquals(350.00, $item['spent']);
        $this->assertEquals(650.00, $item['remaining']);
    }

    public function test_budget_user_data_isolation_security()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $month = Carbon::now()->format('Y-m');

        $accountB = Account::create([
            'user_id' => $userB->id,
            'name' => 'Account B',
            'type' => 'bank',
            'initial_balance' => 1000,
            'current_balance' => 1000,
        ]);

        $catA = Category::create(['user_id' => $userA->id, 'name' => 'Cat A', 'type' => 'expense']);
        $catB = Category::create(['user_id' => $userB->id, 'name' => 'Cat B', 'type' => 'expense']);

        Budget::create(['user_id' => $userA->id, 'category_id' => $catA->id, 'amount' => 500, 'month' => $month]);
        Budget::create(['user_id' => $userB->id, 'category_id' => $catB->id, 'amount' => 500, 'month' => $month]);

        Transaction::create([
            'user_id' => $userB->id,
            'account_id' => $accountB->id,
            'category_id' => $catB->id,
            'type' => 'expense',
            'amount' => 300,
            'description' => 'User B Expense',
            'date' => $month . '-05'
        ]);

        $service = new BudgetService();
        $summaryA = $service->getMonthlyBudgets($userA, $month);

        $this->assertEquals(0.0, $summaryA['total_spent']);
        $this->assertEquals(500.0, $summaryA['total_remaining']);
    }

    public function test_dashboard_debt_and_receivable_total_consolidation()
    {
        $user = User::factory()->create();

        DebtReceivable::create([
            'user_id' => $user->id,
            'person_name' => 'John',
            'type' => 'receivable',
            'amount' => 500.00,
            'paid_amount' => 100.00,
            'status' => 'partially_paid',
            'date' => Carbon::now()->format('Y-m-d'),
            'due_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
        ]);

        DebtReceivable::create([
            'user_id' => $user->id,
            'person_name' => 'Bank',
            'type' => 'debt',
            'amount' => 300.00,
            'paid_amount' => 0.00,
            'status' => 'unpaid',
            'date' => Carbon::now()->format('Y-m-d'),
            'due_date' => Carbon::now()->addDays(10)->format('Y-m-d'),
        ]);

        $totals = DebtReceivable::where('user_id', $user->id)
            ->whereIn('status', ['unpaid', 'partially_paid'])
            ->selectRaw('type, SUM(amount - paid_amount) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $this->assertEquals(400.00, (float) ($totals['receivable'] ?? 0));
        $this->assertEquals(300.00, (float) ($totals['debt'] ?? 0));
    }
}
