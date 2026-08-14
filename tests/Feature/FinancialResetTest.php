<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Budget;
use App\Models\Category;
use App\Models\DebtReceivable;
use App\Models\DebtReceivablePayment;
use App\Models\RecurringTransaction;
use App\Models\Transaction;
use App\Models\User;
use App\Services\DebtReceivableService;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialResetTest extends TestCase
{
    use RefreshDatabase;

    protected User $userA;
    protected User $userB;
    protected Account $accountA;
    protected Account $accountB;
    protected Category $customCategoryA;
    protected Category $customCategoryB;
    protected Category $defaultCategory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userA = User::factory()->create([
            'email' => 'usera@flowra.test',
            'password' => bcrypt('password123'),
        ]);

        $this->userB = User::factory()->create([
            'email' => 'userb@flowra.test',
            'password' => bcrypt('password123'),
        ]);

        // Default global category (shared)
        $this->defaultCategory = Category::create([
            'user_id' => null,
            'name' => 'Kategori Global',
            'type' => 'expense',
            'is_default' => true,
        ]);

        // Custom categories
        $this->customCategoryA = Category::create([
            'user_id' => $this->userA->id,
            'name' => 'Kategori Khusus A',
            'type' => 'expense',
            'is_default' => false,
        ]);

        $this->customCategoryB = Category::create([
            'user_id' => $this->userB->id,
            'name' => 'Kategori Khusus B',
            'type' => 'expense',
            'is_default' => false,
        ]);

        // Accounts
        $this->accountA = Account::create([
            'user_id' => $this->userA->id,
            'name' => 'BCA User A',
            'type' => 'bank',
            'opening_balance' => 1000000,
            'current_balance' => 1000000,
        ]);

        $this->accountB = Account::create([
            'user_id' => $this->userB->id,
            'name' => 'BCA User B',
            'type' => 'bank',
            'opening_balance' => 2000000,
            'current_balance' => 2000000,
        ]);

        // Transactions
        Transaction::create([
            'user_id' => $this->userA->id,
            'type' => 'expense',
            'account_id' => $this->accountA->id,
            'category_id' => $this->customCategoryA->id,
            'amount' => 50000,
            'date' => now()->format('Y-m-d'),
            'description' => 'Makan Siang A',
        ]);

        Transaction::create([
            'user_id' => $this->userB->id,
            'type' => 'expense',
            'account_id' => $this->accountB->id,
            'category_id' => $this->customCategoryB->id,
            'amount' => 75000,
            'date' => now()->format('Y-m-d'),
            'description' => 'Makan Siang B',
        ]);

        // Debts
        $debtService = app(DebtReceivableService::class);
        $debtA = $debtService->createDebtReceivable($this->userA, [
            'type' => 'receivable',
            'person_name' => 'Teman A',
            'amount' => 100000,
            'date' => now()->format('Y-m-d'),
            'account_id' => $this->accountA->id,
        ]);

        $debtB = $debtService->createDebtReceivable($this->userB, [
            'type' => 'receivable',
            'person_name' => 'Teman B',
            'amount' => 200000,
            'date' => now()->format('Y-m-d'),
            'account_id' => $this->accountB->id,
        ]);

        // Budgets
        Budget::create([
            'user_id' => $this->userA->id,
            'category_id' => $this->customCategoryA->id,
            'amount' => 500000,
            'month' => now()->format('Y-m'),
        ]);

        Budget::create([
            'user_id' => $this->userB->id,
            'category_id' => $this->customCategoryB->id,
            'amount' => 800000,
            'month' => now()->format('Y-m'),
        ]);

        // Recurring
        RecurringTransaction::create([
            'user_id' => $this->userA->id,
            'type' => 'expense',
            'account_id' => $this->accountA->id,
            'amount' => 150000,
            'description' => 'Langganan Netflix A',
            'frequency' => 'monthly',
            'start_date' => now()->format('Y-m-d'),
            'next_run_date' => now()->format('Y-m-d'),
        ]);

        RecurringTransaction::create([
            'user_id' => $this->userB->id,
            'type' => 'expense',
            'account_id' => $this->accountB->id,
            'amount' => 200000,
            'description' => 'Langganan Spotify B',
            'frequency' => 'monthly',
            'start_date' => now()->format('Y-m-d'),
            'next_run_date' => now()->format('Y-m-d'),
        ]);
    }

    public function test_unauthenticated_user_cannot_reset_data(): void
    {
        $response = $this->post(route('profile.reset-financial-data'), [
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_reset_fails_with_invalid_password(): void
    {
        $this->actingAs($this->userA);

        $response = $this->post(route('profile.reset-financial-data'), [
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrorsIn('financialReset', 'password');

        // Verify data was NOT deleted
        $this->assertDatabaseHas('accounts', ['id' => $this->accountA->id]);
        $this->assertEquals(2, Transaction::where('user_id', $this->userA->id)->count());
        $this->assertEquals(4, Transaction::count());
    }

    public function test_user_can_reset_all_financial_data_with_valid_password(): void
    {
        $this->actingAs($this->userA);

        $response = $this->post(route('profile.reset-financial-data'), [
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        // 1. User A's account record MUST STILL EXIST
        $this->assertDatabaseHas('users', [
            'id' => $this->userA->id,
            'email' => 'usera@flowra.test',
        ]);

        // 2. User A's financial data MUST BE EMPTY
        $this->assertEquals(0, Account::where('user_id', $this->userA->id)->count());
        $this->assertEquals(0, Transaction::where('user_id', $this->userA->id)->count());
        $this->assertEquals(0, DebtReceivable::where('user_id', $this->userA->id)->count());
        $this->assertEquals(0, Budget::where('user_id', $this->userA->id)->count());
        $this->assertEquals(0, RecurringTransaction::where('user_id', $this->userA->id)->count());
        $this->assertEquals(0, Category::where('user_id', $this->userA->id)->count());

        // 3. Global default categories MUST STILL EXIST
        $this->assertDatabaseHas('categories', [
            'id' => $this->defaultCategory->id,
            'is_default' => true,
        ]);

        // 4. User B's financial data MUST REMAIN COMPLETELY INTACT (Strict user scoping)
        $this->assertEquals(1, Account::where('user_id', $this->userB->id)->count());
        $this->assertDatabaseHas('accounts', ['id' => $this->accountB->id]);
        $this->assertDatabaseHas('categories', ['id' => $this->customCategoryB->id]);
        $this->assertEquals(2, Transaction::where('user_id', $this->userB->id)->count()); // 1 expense + 1 debt disbursement
        $this->assertEquals(1, DebtReceivable::where('user_id', $this->userB->id)->count());
        $this->assertEquals(1, Budget::where('user_id', $this->userB->id)->count());
        $this->assertEquals(1, RecurringTransaction::where('user_id', $this->userB->id)->count());
    }

    public function test_dashboard_displays_empty_state_after_reset(): void
    {
        $this->actingAs($this->userA);

        // Perform reset
        $this->post(route('profile.reset-financial-data'), [
            'password' => 'password123',
        ]);

        // Visit Dashboard
        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Total Kekayaan Bersih');
        $response->assertSee('Rp 0');
        $response->assertSee('Taman Keuangan Masih Kosong');
    }
}
