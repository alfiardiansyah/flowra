<?php

namespace App\Services;

use App\Models\DebtReceivable;
use App\Models\DebtReceivablePayment;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DebtReceivableService
{
    public function __construct(
        protected TransactionService $transactionService
    ) {}

    /**
     * Create a new Debt or Receivable, and record initial cash flow transaction if an account is linked.
     */
    public function createDebtReceivable(User $user, array $data): DebtReceivable
    {
        return DB::transaction(function () use ($user, $data) {
            $data['user_id'] = $user->id;
            $data['paid_amount'] = 0;
            $data['status'] = 'unpaid';
            $data['date'] = $data['date'] ?? now()->format('Y-m-d');

            $debtReceivable = DebtReceivable::create($data);

            // If account_id is specified, record the initial cash flow transaction
            if (!empty($data['account_id'])) {
                $amount = (float) $data['amount'];
                $accountId = $data['account_id'];
                $date = $data['date'] ?? now()->format('Y-m-d');
                $notes = $data['notes'] ?? null;

                // When user lends money (receivable), cash goes OUT (expense)
                // When user borrows money (debt), cash comes IN (income)
                $type = $debtReceivable->type === 'receivable' ? 'expense' : 'income';
                $description = $debtReceivable->type === 'receivable'
                    ? 'Pemberian Pinjaman (Piutang) ke ' . $debtReceivable->person_name
                    : 'Penerimaan Pinjaman (Hutang) dari ' . $debtReceivable->person_name;

                $this->transactionService->createTransaction($user, [
                    'type' => $type,
                    'account_id' => $accountId,
                    'amount' => $amount,
                    'date' => $date,
                    'description' => $description,
                    'notes' => $notes,
                    'debt_receivable_id' => $debtReceivable->id,
                ]);
            }

            return $debtReceivable;
        });
    }

    /**
     * Update an existing Debt or Receivable, adjusting the initial transaction if present.
     */
    public function updateDebtReceivable(DebtReceivable $debtReceivable, array $data): DebtReceivable
    {
        return DB::transaction(function () use ($debtReceivable, $data) {
            $oldAccountId = $debtReceivable->account_id;
            $debtReceivable->update($data);

            // Recalculate status
            $paid = (float) $debtReceivable->paid_amount;
            $total = (float) $debtReceivable->amount;
            $status = $paid >= $total ? 'paid' : ($paid > 0 ? 'partially_paid' : 'unpaid');
            $debtReceivable->update(['status' => $status]);

            // Find the initial disbursement transaction (transaction not tied to a DebtReceivablePayment)
            $paymentTxIds = $debtReceivable->payments()->pluck('transaction_id')->filter()->toArray();
            $initialTx = $debtReceivable->transactions()
                ->whereNotIn('id', $paymentTxIds)
                ->first();

            if ($initialTx) {
                $type = $debtReceivable->type === 'receivable' ? 'expense' : 'income';
                $description = $debtReceivable->type === 'receivable'
                    ? 'Pemberian Pinjaman (Piutang) ke ' . $debtReceivable->person_name
                    : 'Penerimaan Pinjaman (Hutang) dari ' . $debtReceivable->person_name;

                $updateData = [
                    'amount' => $debtReceivable->amount,
                    'date' => $debtReceivable->date,
                    'description' => $description,
                    'notes' => $debtReceivable->notes,
                ];

                if (!empty($debtReceivable->account_id)) {
                    $updateData['account_id'] = $debtReceivable->account_id;
                }

                $this->transactionService->updateTransaction($initialTx, $updateData);
            } elseif (!empty($debtReceivable->account_id) && !$oldAccountId) {
                // If account_id was newly added
                $type = $debtReceivable->type === 'receivable' ? 'expense' : 'income';
                $description = $debtReceivable->type === 'receivable'
                    ? 'Pemberian Pinjaman (Piutang) ke ' . $debtReceivable->person_name
                    : 'Penerimaan Pinjaman (Hutang) dari ' . $debtReceivable->person_name;

                $this->transactionService->createTransaction($debtReceivable->user, [
                    'type' => $type,
                    'account_id' => $debtReceivable->account_id,
                    'amount' => $debtReceivable->amount,
                    'date' => $debtReceivable->date,
                    'description' => $description,
                    'notes' => $debtReceivable->notes,
                    'debt_receivable_id' => $debtReceivable->id,
                ]);
            }

            return $debtReceivable;
        });
    }

    /**
     * Record a payment/settlement towards a debt or receivable.
     */
    public function recordPayment(DebtReceivable $debtReceivable, array $data): DebtReceivablePayment
    {
        return DB::transaction(function () use ($debtReceivable, $data) {
            $user = $debtReceivable->user;
            $amount = (float) $data['amount'];
            $accountId = $data['account_id'];
            $date = $data['date'] ?? now()->format('Y-m-d');
            $notes = $data['notes'] ?? null;

            // 1. Create linked transaction:
            // When borrower pays back a receivable -> cash IN (income)
            // When user pays back a debt -> cash OUT (expense)
            $transaction = null;
            if ($accountId) {
                $type = $debtReceivable->type === 'debt' ? 'expense' : 'income';
                $description = $debtReceivable->type === 'debt'
                    ? 'Pembayaran Hutang ke ' . $debtReceivable->person_name
                    : 'Penerimaan Pembayaran Piutang dari ' . $debtReceivable->person_name;

                $transaction = $this->transactionService->createTransaction($user, [
                    'type' => $type,
                    'account_id' => $accountId,
                    'amount' => $amount,
                    'date' => $date,
                    'description' => $description,
                    'notes' => $notes,
                    'debt_receivable_id' => $debtReceivable->id,
                ]);
            }

            // 2. Create DebtReceivablePayment record
            $payment = DebtReceivablePayment::create([
                'debt_receivable_id' => $debtReceivable->id,
                'transaction_id' => $transaction?->id,
                'account_id' => $accountId,
                'amount' => $amount,
                'date' => $date,
                'notes' => $notes,
            ]);

            // 3. Update DebtReceivable paid_amount and status
            $newPaid = (float) $debtReceivable->paid_amount + $amount;
            $newStatus = $newPaid >= (float) $debtReceivable->amount ? 'paid' : ($newPaid > 0 ? 'partially_paid' : 'unpaid');

            $debtReceivable->update([
                'paid_amount' => $newPaid,
                'status' => $newStatus,
            ]);

            return $payment;
        });
    }

    /**
     * Delete a payment and revert amounts and transaction.
     */
    public function deletePayment(DebtReceivablePayment $payment): bool
    {
        return DB::transaction(function () use ($payment) {
            $debtReceivable = $payment->debtReceivable;

            if ($payment->transaction) {
                $this->transactionService->deleteTransaction($payment->transaction);
            }

            $revertedPaid = max(0, (float) $debtReceivable->paid_amount - (float) $payment->amount);
            $newStatus = $revertedPaid >= (float) $debtReceivable->amount ? 'paid' : ($revertedPaid > 0 ? 'partially_paid' : 'unpaid');

            $debtReceivable->update([
                'paid_amount' => $revertedPaid,
                'status' => $newStatus,
            ]);

            return $payment->delete();
        });
    }

    /**
     * Delete a debt or receivable, reverting all linked transactions and payments.
     */
    public function deleteDebtReceivable(DebtReceivable $debtReceivable): bool
    {
        return DB::transaction(function () use ($debtReceivable) {
            // Delete all linked transactions (initial disbursement and payments) to revert account balances
            foreach ($debtReceivable->transactions as $tx) {
                $this->transactionService->deleteTransaction($tx);
            }

            // Delete payment records
            $debtReceivable->payments()->delete();

            return $debtReceivable->delete();
        });
    }
}
