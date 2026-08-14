<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\RecurringTransaction;
use App\Services\RecurringTransactionService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RecurringTransactionController extends Controller
{
    public function __construct(
        protected RecurringTransactionService $recurringService
    ) {}

    public function index()
    {
        $user = auth()->user();
        $recurringTransactions = RecurringTransaction::with(['account', 'destinationAccount', 'category'])
            ->where('user_id', $user->id)
            ->orderBy('next_run_date')
            ->get();

        $activeCount = $recurringTransactions->where('is_active', true)->count();
        $totalMonthlyObligation = $recurringTransactions->where('is_active', true)->where('type', 'expense')->sum(function ($item) {
            return match ($item->frequency) {
                'daily' => $item->amount * 30,
                'weekly' => $item->amount * 4.33,
                'monthly' => (float) $item->amount,
                'yearly' => $item->amount / 12,
                default => (float) $item->amount,
            };
        });

        return view('recurring.index', compact('recurringTransactions', 'activeCount', 'totalMonthlyObligation'));
    }

    public function create()
    {
        $user = auth()->user();
        $accounts = Account::where('user_id', $user->id)->where('is_active', true)->orderBy('name')->get();
        $categories = Category::forUser($user->id)->orderBy('name')->get();

        return view('recurring.create', compact('accounts', 'categories'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'type' => 'required|in:income,expense,transfer',
            'account_id' => 'required|exists:accounts,id',
            'destination_account_id' => 'nullable|required_if:type,transfer|exists:accounts,id|different:account_id',
            'category_id' => 'nullable|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'frequency' => 'required|in:daily,weekly,monthly,yearly',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'auto_record' => 'boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['user_id'] = $user->id;
        $validated['next_run_date'] = $validated['start_date'];
        $validated['is_active'] = true;
        $validated['auto_record'] = $request->has('auto_record');

        // Authorize account
        Account::where('id', $validated['account_id'])->where('user_id', $user->id)->firstOrFail();

        RecurringTransaction::create($validated);

        return redirect()->route('recurring.index')->with('success', 'Transaksi rutin berhasil didaftarkan!');
    }

    public function edit(RecurringTransaction $recurring)
    {
        $this->authorizeRecurring($recurring);
        $user = auth()->user();
        $accounts = Account::where('user_id', $user->id)->where('is_active', true)->orderBy('name')->get();
        $categories = Category::forUser($user->id)->orderBy('name')->get();

        return view('recurring.edit', compact('recurring', 'accounts', 'categories'));
    }

    public function update(Request $request, RecurringTransaction $recurring)
    {
        $this->authorizeRecurring($recurring);
        $user = auth()->user();

        $validated = $request->validate([
            'type' => 'required|in:income,expense,transfer',
            'account_id' => 'required|exists:accounts,id',
            'destination_account_id' => 'nullable|required_if:type,transfer|exists:accounts,id|different:account_id',
            'category_id' => 'nullable|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'frequency' => 'required|in:daily,weekly,monthly,yearly',
            'next_run_date' => 'required|date',
            'end_date' => 'nullable|date',
            'auto_record' => 'boolean',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['auto_record'] = $request->has('auto_record');
        $validated['is_active'] = $request->has('is_active');

        // Authorize account
        Account::where('id', $validated['account_id'])->where('user_id', $user->id)->firstOrFail();

        $recurring->update($validated);

        return redirect()->route('recurring.index')->with('success', 'Jadwal transaksi rutin berhasil diperbarui!');
    }

    public function postNow(RecurringTransaction $recurring)
    {
        $this->authorizeRecurring($recurring);

        $transaction = $this->recurringService->postRecurringTransaction($recurring);

        return back()->with('success', "Transaksi '{$recurring->description}' sebesar Rp " . number_format($transaction->amount, 0, ',', '.') . " berhasil dicatat!");
    }

    public function toggleStatus(RecurringTransaction $recurring)
    {
        $this->authorizeRecurring($recurring);

        $recurring->update(['is_active' => !$recurring->is_active]);

        $statusText = $recurring->is_active ? 'diaktifkan kembali' : 'dijeda';
        return back()->with('success', "Transaksi rutin berhasil {$statusText}.");
    }

    public function destroy(RecurringTransaction $recurring)
    {
        $this->authorizeRecurring($recurring);

        $recurring->delete();

        return redirect()->route('recurring.index')->with('success', 'Transaksi rutin berhasil dihapus.');
    }

    protected function authorizeRecurring(RecurringTransaction $recurring): void
    {
        if ($recurring->user_id !== auth()->id()) {
            abort(403, 'Akses tidak diizinkan.');
        }
    }
}
