<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    public function __construct(
        protected TransactionService $transactionService
    ) {}

    public function index()
    {
        $user = auth()->user();
        $accounts = Account::where('user_id', $user->id)
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        $totalNetWorth = (float) $accounts->where('is_active', true)->sum('current_balance');
        $activeCount = $accounts->where('is_active', true)->count();

        return view('accounts.index', compact('accounts', 'totalNetWorth', 'activeCount'));
    }

    public function create()
    {
        return view('accounts.create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:cash,bank,ewallet,savings,credit_card,investment,other',
            'opening_balance' => 'required|numeric|min:0',
            'account_number' => 'nullable|string|max:50',
            'color' => 'required|string|max:20',
            'icon' => 'required|string|max:50',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['user_id'] = $user->id;
        $validated['current_balance'] = $validated['opening_balance'];
        $validated['currency'] = 'IDR';
        $validated['is_active'] = true;

        Account::create($validated);

        return redirect()->route('accounts.index')->with('success', 'Rekening baru berhasil ditambahkan!');
    }

    public function show(Account $account)
    {
        $this->authorizeAccount($account);
        $user = auth()->user();

        $transactions = Transaction::with(['category', 'destinationAccount', 'account'])
            ->where(function ($q) use ($account) {
                $q->where('account_id', $account->id)
                  ->orWhere('destination_account_id', $account->id);
            })
            ->where('user_id', $user->id)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(15);

        // Account statistics
        $totalIn = (float) Transaction::where('account_id', $account->id)->where('type', 'income')->sum('amount');
        $totalOut = (float) Transaction::where('account_id', $account->id)->where('type', 'expense')->sum('amount');
        $transfersIn = (float) Transaction::where('destination_account_id', $account->id)->where('type', 'transfer')->sum('amount');
        $transfersOut = (float) Transaction::where('account_id', $account->id)->where('type', 'transfer')->sum('amount');

        $categories = Category::forUser($user->id)->get();
        $otherAccounts = Account::where('user_id', $user->id)->where('id', '!=', $account->id)->where('is_active', true)->get();

        return view('accounts.show', compact(
            'account',
            'transactions',
            'totalIn',
            'totalOut',
            'transfersIn',
            'transfersOut',
            'categories',
            'otherAccounts'
        ));
    }

    public function edit(Account $account)
    {
        $this->authorizeAccount($account);
        $user = auth()->user();

        $transactionCount = Transaction::where('account_id', $account->id)
            ->orWhere('destination_account_id', $account->id)
            ->count();

        $otherAccounts = Account::where('user_id', $user->id)
            ->where('id', '!=', $account->id)
            ->where('is_active', true)
            ->get();

        return view('accounts.edit', compact('account', 'transactionCount', 'otherAccounts'));
    }

    public function update(Request $request, Account $account)
    {
        $this->authorizeAccount($account);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:cash,bank,ewallet,savings,credit_card,investment,other',
            'opening_balance' => 'required|numeric',
            'account_number' => 'nullable|string|max:50',
            'color' => 'required|string|max:20',
            'icon' => 'required|string|max:50',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $account->update($validated);

        // Recalculate balance with new opening balance
        $this->transactionService->recalculateAccountBalance($account);

        return redirect()->route('accounts.index')->with('success', 'Rekening berhasil diperbarui!');
    }

    public function destroy(Request $request, Account $account)
    {
        $this->authorizeAccount($account);

        $hasTransactions = Transaction::where('account_id', $account->id)
            ->orWhere('destination_account_id', $account->id)
            ->exists();

        if (!$hasTransactions) {
            $account->delete();
            return redirect()->route('accounts.index')->with('success', 'Rekening berhasil dihapus secara permanen.');
        }

        $action = $request->input('action', 'deactivate');

        if ($action === 'reassign') {
            $request->validate([
                'target_account_id' => 'required|exists:accounts,id',
            ]);

            $targetAccountId = $request->input('target_account_id');
            if ($targetAccountId == $account->id) {
                return back()->with('error', 'Akun pemindahan tidak boleh sama dengan akun yang dihapus.');
            }

            $targetAccount = Account::where('user_id', auth()->id())->where('id', $targetAccountId)->firstOrFail();

            DB::transaction(function () use ($account, $targetAccount) {
                // Reassign main transactions
                Transaction::where('account_id', $account->id)->update(['account_id' => $targetAccount->id]);
                Transaction::where('destination_account_id', $account->id)->update(['destination_account_id' => $targetAccount->id]);

                // Reassign recurring transactions & debts
                \App\Models\RecurringTransaction::where('account_id', $account->id)->update(['account_id' => $targetAccount->id]);
                \App\Models\RecurringTransaction::where('destination_account_id', $account->id)->update(['destination_account_id' => $targetAccount->id]);
                \App\Models\DebtReceivable::where('account_id', $account->id)->update(['account_id' => $targetAccount->id]);
                \App\Models\DebtReceivablePayment::where('account_id', $account->id)->update(['account_id' => $targetAccount->id]);

                $account->delete();

                // Recalculate balance for target account
                $this->transactionService->recalculateAccountBalance($targetAccount);
            });

            return redirect()->route('accounts.index')->with('success', 'Seluruh transaksi berhasil dipindahkan ke ' . $targetAccount->name . ' dan rekening lama telah dihapus.');
        }

        if ($action === 'cascade') {
            DB::transaction(function () use ($account) {
                // Collect affected transfer partner accounts
                $sourcePartnerIds = Transaction::where('type', 'transfer')
                    ->where('destination_account_id', $account->id)
                    ->pluck('account_id');

                $destPartnerIds = Transaction::where('type', 'transfer')
                    ->where('account_id', $account->id)
                    ->whereNotNull('destination_account_id')
                    ->pluck('destination_account_id');

                $affectedAccountIds = $sourcePartnerIds->merge($destPartnerIds)
                    ->filter(fn($id) => $id != $account->id)
                    ->unique();

                // Delete transactions
                Transaction::where('account_id', $account->id)
                    ->orWhere('destination_account_id', $account->id)
                    ->delete();

                // Delete recurring & debt records linked to this account
                \App\Models\RecurringTransaction::where('account_id', $account->id)
                    ->orWhere('destination_account_id', $account->id)
                    ->delete();
                \App\Models\DebtReceivablePayment::where('account_id', $account->id)->delete();

                $account->delete();

                // Recalculate affected partner accounts
                foreach ($affectedAccountIds as $accId) {
                    $partnerAcc = Account::find($accId);
                    if ($partnerAcc) {
                        $this->transactionService->recalculateAccountBalance($partnerAcc);
                    }
                }
            });

            return redirect()->route('accounts.index')->with('success', 'Rekening dan seluruh riwayat transaksinya berhasil dihapus.');
        }

        // Default action: deactivate (soft delete / archive)
        $account->update(['is_active' => false]);
        return redirect()->route('accounts.index')->with('success', 'Rekening berhasil dinonaktifkan.');
    }

    public function recalculate(Account $account)
    {
        $this->authorizeAccount($account);
        $this->transactionService->recalculateAccountBalance($account);

        return back()->with('success', 'Saldo rekening berhasil dihitung ulang dan disinkronkan!');
    }

    protected function authorizeAccount(Account $account): void
    {
        if ($account->user_id !== auth()->id()) {
            abort(403, 'Akses tidak diizinkan.');
        }
    }
}
