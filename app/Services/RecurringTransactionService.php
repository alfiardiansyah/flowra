<?php

namespace App\Services;

use App\Models\RecurringTransaction;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

class RecurringTransactionService
{
    public function __construct(
        protected TransactionService $transactionService
    ) {}

    /**
     * Get active recurring items due on or before a given date.
     */
    public function getDueRecurring(User $user, Carbon $date = null)
    {
        $date = $date ?: Carbon::today();

        return RecurringTransaction::with(['account', 'destinationAccount', 'category'])
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->where('next_run_date', '<=', $date->format('Y-m-d'))
            ->get();
    }

    /**
     * Get upcoming recurring transactions in next X days.
     */
    public function getUpcoming(User $user, int $days = 7)
    {
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays($days);

        return RecurringTransaction::with(['account', 'destinationAccount', 'category'])
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->whereBetween('next_run_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('next_run_date')
            ->get();
    }

    /**
     * Post/record a recurring transaction as an actual transaction.
     */
    public function postRecurringTransaction(RecurringTransaction $recurring, string $transactionDate = null): Transaction
    {
        $user = $recurring->user;
        $date = $transactionDate ?: $recurring->next_run_date->format('Y-m-d');

        $transaction = $this->transactionService->createTransaction($user, [
            'type' => $recurring->type,
            'account_id' => $recurring->account_id,
            'destination_account_id' => $recurring->destination_account_id,
            'category_id' => $recurring->category_id,
            'amount' => $recurring->amount,
            'date' => $date,
            'description' => $recurring->description,
            'notes' => $recurring->notes,
            'recurring_transaction_id' => $recurring->id,
        ]);

        // Advance next run date
        $currentNext = Carbon::parse($recurring->next_run_date);
        $nextDate = $recurring->calculateNextRunDate($currentNext);

        $updateData = [
            'last_run_date' => $date,
            'next_run_date' => $nextDate->format('Y-m-d'),
        ];

        // Check if end_date reached
        if ($recurring->end_date && $nextDate->greaterThan($recurring->end_date)) {
            $updateData['is_active'] = false;
        }

        $recurring->update($updateData);

        return $transaction;
    }

    /**
     * Process auto-record items for a user.
     */
    public function processAutoRecords(User $user): int
    {
        $due = RecurringTransaction::where('user_id', $user->id)
            ->where('is_active', true)
            ->where('auto_record', true)
            ->where('next_run_date', '<=', Carbon::today()->format('Y-m-d'))
            ->get();

        $processed = 0;
        foreach ($due as $item) {
            $this->postRecurringTransaction($item);
            $processed++;
        }

        return $processed;
    }
}
