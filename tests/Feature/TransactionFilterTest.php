<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionFilterTest extends TestCase
{
    use RefreshDatabase;

    protected User $userA;
    protected User $userB;
    protected Account $accountBca;
    protected Account $accountMandiri;
    protected Account $accountUserB;
    protected Category $categoryFood;
    protected Category $categorySalary;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userA = User::factory()->create();
        $this->userB = User::factory()->create();

        $this->accountBca = Account::create([
            'user_id' => $this->userA->id,
            'name' => 'BCA',
            'type' => 'bank',
            'current_balance' => 5000000,
        ]);

        $this->accountMandiri = Account::create([
            'user_id' => $this->userA->id,
            'name' => 'Mandiri',
            'type' => 'bank',
            'current_balance' => 3000000,
        ]);

        $this->accountUserB = Account::create([
            'user_id' => $this->userB->id,
            'name' => 'BCA User B',
            'type' => 'bank',
            'current_balance' => 10000000,
        ]);

        $this->categoryFood = Category::create([
            'user_id' => $this->userA->id,
            'name' => 'Makanan',
            'type' => 'expense',
        ]);

        $this->categorySalary = Category::create([
            'user_id' => $this->userA->id,
            'name' => 'Gaji',
            'type' => 'income',
        ]);
    }

    public function test_user_can_view_all_transactions_by_default(): void
    {
        $tx1 = Transaction::create([
            'user_id' => $this->userA->id,
            'account_id' => $this->accountBca->id,
            'category_id' => $this->categoryFood->id,
            'type' => 'expense',
            'amount' => 50000,
            'description' => 'Makan Siang BCA',
            'date' => now()->format('Y-m-d'),
        ]);

        $tx2 = Transaction::create([
            'user_id' => $this->userA->id,
            'account_id' => $this->accountMandiri->id,
            'category_id' => $this->categorySalary->id,
            'type' => 'income',
            'amount' => 5000000,
            'description' => 'Gaji Mandiri',
            'date' => now()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($this->userA)->get(route('transactions.index'));

        $response->assertOk();
        $response->assertSee('Makan Siang BCA');
        $response->assertSee('Gaji Mandiri');
    }

    public function test_user_can_filter_transactions_by_account(): void
    {
        Transaction::create([
            'user_id' => $this->userA->id,
            'account_id' => $this->accountBca->id,
            'category_id' => $this->categoryFood->id,
            'type' => 'expense',
            'amount' => 50000,
            'description' => 'Makan Siang BCA',
            'date' => now()->format('Y-m-d'),
        ]);

        Transaction::create([
            'user_id' => $this->userA->id,
            'account_id' => $this->accountMandiri->id,
            'category_id' => $this->categorySalary->id,
            'type' => 'income',
            'amount' => 5000000,
            'description' => 'Gaji Mandiri',
            'date' => now()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($this->userA)->get(route('transactions.index', [
            'account_id' => $this->accountBca->id,
        ]));

        $response->assertOk();
        $response->assertSee('Makan Siang BCA');
        $response->assertDontSee('Gaji Mandiri');
    }

    public function test_user_can_combine_account_and_type_filters(): void
    {
        Transaction::create([
            'user_id' => $this->userA->id,
            'account_id' => $this->accountBca->id,
            'category_id' => $this->categoryFood->id,
            'type' => 'expense',
            'amount' => 50000,
            'description' => 'Pengeluaran BCA',
            'date' => now()->format('Y-m-d'),
        ]);

        Transaction::create([
            'user_id' => $this->userA->id,
            'account_id' => $this->accountBca->id,
            'category_id' => $this->categorySalary->id,
            'type' => 'income',
            'amount' => 1000000,
            'description' => 'Pemasukan BCA',
            'date' => now()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($this->userA)->get(route('transactions.index', [
            'account_id' => $this->accountBca->id,
            'type' => 'expense',
        ]));

        $response->assertOk();
        $response->assertSee('Pengeluaran BCA');
        $response->assertDontSee('Pemasukan BCA');
    }

    public function test_user_can_combine_account_and_category_filters(): void
    {
        Transaction::create([
            'user_id' => $this->userA->id,
            'account_id' => $this->accountBca->id,
            'category_id' => $this->categoryFood->id,
            'type' => 'expense',
            'amount' => 50000,
            'description' => 'Makanan BCA',
            'date' => now()->format('Y-m-d'),
        ]);

        Transaction::create([
            'user_id' => $this->userA->id,
            'account_id' => $this->accountBca->id,
            'category_id' => $this->categorySalary->id,
            'type' => 'income',
            'amount' => 1000000,
            'description' => 'Gaji BCA',
            'date' => now()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($this->userA)->get(route('transactions.index', [
            'account_id' => $this->accountBca->id,
            'category_id' => $this->categoryFood->id,
        ]));

        $response->assertOk();
        $response->assertSee('Makanan BCA');
        $response->assertDontSee('Gaji BCA');
    }

    public function test_account_filter_persists_across_pagination(): void
    {
        for ($i = 1; $i <= 20; $i++) {
            Transaction::create([
                'user_id' => $this->userA->id,
                'account_id' => $this->accountBca->id,
                'type' => 'expense',
                'amount' => 10000 * $i,
                'description' => "Item BCA $i",
                'date' => now()->format('Y-m-d'),
            ]);
        }

        $responsePage2 = $this->actingAs($this->userA)->get(route('transactions.index', [
            'account_id' => $this->accountBca->id,
            'page' => 2,
        ]));

        $responsePage2->assertOk();
        $responsePage2->assertSee('account_id=' . $this->accountBca->id);
    }

    public function test_reset_account_filter_shows_all_transactions(): void
    {
        Transaction::create([
            'user_id' => $this->userA->id,
            'account_id' => $this->accountBca->id,
            'type' => 'expense',
            'amount' => 50000,
            'description' => 'Tx BCA',
            'date' => now()->format('Y-m-d'),
        ]);

        Transaction::create([
            'user_id' => $this->userA->id,
            'account_id' => $this->accountMandiri->id,
            'type' => 'income',
            'amount' => 500000,
            'description' => 'Tx Mandiri',
            'date' => now()->format('Y-m-d'),
        ]);

        $responseReset = $this->actingAs($this->userA)->get(route('transactions.index'));

        $responseReset->assertOk();
        $responseReset->assertSee('Tx BCA');
        $responseReset->assertSee('Tx Mandiri');
    }

    public function test_account_owned_by_user_b_cannot_be_accessed_by_user_a(): void
    {
        Transaction::create([
            'user_id' => $this->userB->id,
            'account_id' => $this->accountUserB->id,
            'type' => 'expense',
            'amount' => 999000,
            'description' => 'Rahasia User B',
            'date' => now()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($this->userA)->get(route('transactions.index', [
            'account_id' => $this->accountUserB->id,
        ]));

        $response->assertStatus(403);
    }

    public function test_account_with_no_transactions_returns_empty_state(): void
    {
        $emptyAccount = Account::create([
            'user_id' => $this->userA->id,
            'name' => 'Rekening Kosong',
            'type' => 'bank',
            'current_balance' => 0,
        ]);

        $response = $this->actingAs($this->userA)->get(route('transactions.index', [
            'account_id' => $emptyAccount->id,
        ]));

        $response->assertOk();
        $response->assertSee('Belum Ada Transaksi pada Rekening Kosong');
    }
}
