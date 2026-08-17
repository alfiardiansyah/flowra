<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TransactionService
{
    /**
     * Create a new transaction and update associated account balances.
     */
    public function createTransaction(User $user, array $data): Transaction
    {
        return DB::transaction(function () use ($user, $data) {
            $data['user_id'] = $user->id;

            $transaction = Transaction::create($data);

            $this->applyBalanceAdjustment($transaction);

            return $transaction;
        });
    }

    /**
     * Update an existing transaction and adjust account balances accordingly.
     */
    public function updateTransaction(Transaction $transaction, array $data): Transaction
    {
        return DB::transaction(function () use ($transaction, $data) {
            // 1. Revert previous balance impact
            $this->revertBalanceAdjustment($transaction);

            // 2. Update transaction record
            $transaction->update($data);

            // 3. Apply new balance impact
            $this->applyBalanceAdjustment($transaction->fresh());

            return $transaction;
        });
    }

    /**
     * Delete a transaction and revert balance adjustments.
     */
    public function deleteTransaction(Transaction $transaction): bool
    {
        return DB::transaction(function () use ($transaction) {
            $this->revertBalanceAdjustment($transaction);

            return $transaction->delete();
        });
    }

    /**
     * Apply the balance change to affected accounts.
     */
    protected function applyBalanceAdjustment(Transaction $transaction): void
    {
        $amount = (float) $transaction->amount;
        $sourceAccount = Account::find($transaction->account_id);

        if (!$sourceAccount) return;

        if ($transaction->type === 'income') {
            $sourceAccount->increment('current_balance', $amount);
        } elseif ($transaction->type === 'expense') {
            $sourceAccount->decrement('current_balance', $amount);
        } elseif ($transaction->type === 'transfer') {
            $sourceAccount->decrement('current_balance', $amount);

            if ($transaction->destination_account_id) {
                $destAccount = Account::find($transaction->destination_account_id);
                if ($destAccount) {
                    $destAccount->increment('current_balance', $amount);
                }
            }
        }
    }

    /**
     * Revert the balance change from affected accounts.
     */
    protected function revertBalanceAdjustment(Transaction $transaction): void
    {
        $amount = (float) $transaction->amount;
        $sourceAccount = Account::find($transaction->account_id);

        if (!$sourceAccount) return;

        if ($transaction->type === 'income') {
            $sourceAccount->decrement('current_balance', $amount);
        } elseif ($transaction->type === 'expense') {
            $sourceAccount->increment('current_balance', $amount);
        } elseif ($transaction->type === 'transfer') {
            $sourceAccount->increment('current_balance', $amount);

            if ($transaction->destination_account_id) {
                $destAccount = Account::find($transaction->destination_account_id);
                if ($destAccount) {
                    $destAccount->decrement('current_balance', $amount);
                }
            }
        }
    }

    /**
     * Recalculate balance for a specific account based on opening balance and all transaction logs.
     */
    public function recalculateAccountBalance(Account $account): float
    {
        $opening = (float) $account->opening_balance;

        $incomes = (float) Transaction::where('account_id', $account->id)
            ->where('type', 'income')
            ->sum('amount');

        $expenses = (float) Transaction::where('account_id', $account->id)
            ->where('type', 'expense')
            ->sum('amount');

        $transfersOut = (float) Transaction::where('account_id', $account->id)
            ->where('type', 'transfer')
            ->sum('amount');

        $transfersIn = (float) Transaction::where('destination_account_id', $account->id)
            ->where('type', 'transfer')
            ->sum('amount');

        $calculated = $opening + $incomes - $expenses - $transfersOut + $transfersIn;

        $account->update(['current_balance' => $calculated]);

        return $calculated;
    }
}
