<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Reset all financial data for the authenticated user while keeping the account intact.
     */
    public function resetFinancialData(Request $request): RedirectResponse
    {
        $request->validateWithBag('financialReset', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($user) {
                // 1. Clean up receipt/proof attachments from storage
                $attachments = \App\Models\Transaction::where('user_id', $user->id)
                    ->whereNotNull('attachment')
                    ->pluck('attachment');

                foreach ($attachments as $file) {
                    if ($file && \Illuminate\Support\Facades\Storage::disk('public')->exists($file)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($file);
                    }
                }

                // 2. Delete debt receivable payments
                \App\Models\DebtReceivablePayment::whereHas('debtReceivable', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->delete();

                // 3. Delete all unified transactions
                \App\Models\Transaction::where('user_id', $user->id)->delete();

                // 4. Delete all debts & receivables
                \App\Models\DebtReceivable::where('user_id', $user->id)->delete();

                // 5. Delete all recurring transactions
                \App\Models\RecurringTransaction::where('user_id', $user->id)->delete();

                // 6. Delete all budgets
                \App\Models\Budget::where('user_id', $user->id)->delete();

                // 7. Delete legacy incomes & expenses
                \App\Models\Income::where('user_id', $user->id)->delete();
                \App\Models\Expense::where('user_id', $user->id)->delete();

                // 8. Delete user financial accounts
                \App\Models\Account::where('user_id', $user->id)->delete();

                // 9. Delete custom user-created categories (keeping global/default categories intact)
                \App\Models\Category::where('user_id', $user->id)
                    ->where('is_default', false)
                    ->delete();
            });

            // Invalidate user cache if any
            \Illuminate\Support\Facades\Cache::forget("user_{$user->id}_dashboard");
            \Illuminate\Support\Facades\Cache::forget("user_{$user->id}_net_worth");

            return Redirect::route('dashboard')->with('success', 'Semua data keuangan berhasil dihapus. Flowra Anda sekarang siap dimulai dari awal!');
        } catch (\Throwable $e) {
            return Redirect::route('profile.edit')->with('error', 'Gagal mereset data keuangan: ' . $e->getMessage());
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
