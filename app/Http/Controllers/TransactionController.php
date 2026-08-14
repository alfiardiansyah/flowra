<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(
        protected TransactionService $transactionService
    ) {}

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Transaction::with(['account', 'destinationAccount', 'category'])
            ->where('user_id', $user->id);

        // Filter: Type
        if ($request->filled('type') && in_array($request->type, ['income', 'expense', 'transfer'])) {
            $query->where('type', $request->type);
        }

        // Filter: Account
        if ($request->filled('account_id')) {
            $accId = $request->account_id;
            $query->where(function ($q) use ($accId) {
                $q->where('account_id', $accId)
                  ->orWhere('destination_account_id', $accId);
            });
        }

        // Filter: Category
        if ($request->filled('category_id')) {
            $catId = $request->category_id;
            // Also include subcategories if selected parent category
            $subCategoryIds = Category::where('parent_id', $catId)->pluck('id')->toArray();
            $allCategoryIds = array_merge([$catId], $subCategoryIds);
            $query->whereIn('category_id', $allCategoryIds);
        }

        // Filter: Date Range
        if ($request->filled('from')) {
            $query->where('date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->where('date', '<=', $request->to);
        }

        // Filter: Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Summary for current filter query
        $summaryQuery = clone $query;
        $totalIncomes = (float) (clone $summaryQuery)->where('type', 'income')->sum('amount');
        $totalExpenses = (float) (clone $summaryQuery)->where('type', 'expense')->sum('amount');
        $netAmount = $totalIncomes - $totalExpenses;

        $transactions = $query->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $accounts = Account::where('user_id', $user->id)->where('is_active', true)->orderBy('name')->get();
        $categories = Category::forUser($user->id)->orderBy('name')->get();

        return view('transactions.index', compact(
            'transactions',
            'accounts',
            'categories',
            'totalIncomes',
            'totalExpenses',
            'netAmount'
        ));
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        $accounts = Account::where('user_id', $user->id)->where('is_active', true)->orderBy('name')->get();
        $categories = Category::forUser($user->id)->orderBy('name')->get();

        $defaultType = $request->query('type', 'expense');
        $defaultAccountId = $request->query('account_id');
        $defaultCategoryId = $request->query('category_id');

        return view('transactions.create', compact(
            'accounts',
            'categories',
            'defaultType',
            'defaultAccountId',
            'defaultCategoryId'
        ));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $isTransfer = $request->input('type') === 'transfer';

        // When not transferring, nullify destination_account_id so different:account_id rule is not triggered
        if (!$isTransfer) {
            $request->merge(['destination_account_id' => null]);
        }

        $validated = $request->validate([
            'type' => 'required|in:income,expense,transfer',
            'amount' => 'required|numeric|min:0.01',
            'account_id' => 'required|exists:accounts,id',
            'destination_account_id' => $isTransfer 
                ? 'required|exists:accounts,id|different:account_id' 
                : 'nullable',
            'category_id' => 'nullable|exists:categories,id',
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|image|max:3072',
        ], [
            'destination_account_id.different' => 'Rekening tujuan transfer tidak boleh sama dengan rekening asal.',
            'destination_account_id.required' => 'Rekening tujuan wajib dipilih untuk transaksi transfer.',
        ]);

        // Authorization check on source account
        $sourceAccount = Account::where('id', $validated['account_id'])->where('user_id', $user->id)->firstOrFail();
        if ($isTransfer && !empty($validated['destination_account_id'])) {
            Account::where('id', $validated['destination_account_id'])->where('user_id', $user->id)->firstOrFail();
        }

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')->store('proofs', 'public');
        }

        try {
            $transaction = $this->transactionService->createTransaction($user, $validated);

            $typeLabel = match ($transaction->type) {
                'income' => 'Pemasukan berhasil dicatat!',
                'expense' => 'Pengeluaran berhasil dicatat!',
                'transfer' => 'Transfer antar rekening berhasil dicatat!',
            };

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $typeLabel,
                    'transaction' => $transaction,
                ]);
            }

            return redirect()->route('transactions.index')->with('success', $typeLabel);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage());
        }
    }

    public function edit(Transaction $transaction)
    {
        $this->authorizeTransaction($transaction);

        $user = auth()->user();
        $accounts = Account::where('user_id', $user->id)->where('is_active', true)->orderBy('name')->get();
        $categories = Category::forUser($user->id)->orderBy('name')->get();

        return view('transactions.edit', compact('transaction', 'accounts', 'categories'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $this->authorizeTransaction($transaction);
        $user = auth()->user();
        $isTransfer = $request->input('type') === 'transfer';

        if (!$isTransfer) {
            $request->merge(['destination_account_id' => null]);
        }

        $validated = $request->validate([
            'type' => 'required|in:income,expense,transfer',
            'amount' => 'required|numeric|min:0.01',
            'account_id' => 'required|exists:accounts,id',
            'destination_account_id' => $isTransfer 
                ? 'required|exists:accounts,id|different:account_id' 
                : 'nullable',
            'category_id' => 'nullable|exists:categories,id',
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|image|max:3072',
        ], [
            'destination_account_id.different' => 'Rekening tujuan transfer tidak boleh sama dengan rekening asal.',
        ]);

        // Authorize account ownership
        Account::where('id', $validated['account_id'])->where('user_id', $user->id)->firstOrFail();
        if ($isTransfer && !empty($validated['destination_account_id'])) {
            Account::where('id', $validated['destination_account_id'])->where('user_id', $user->id)->firstOrFail();
        }

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')->store('proofs', 'public');
        }

        try {
            $this->transactionService->updateTransaction($transaction, $validated);

            return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil diperbarui!');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui transaksi: ' . $e->getMessage());
        }
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorizeTransaction($transaction);

        $this->transactionService->deleteTransaction($transaction);

        return back()->with('success', 'Transaksi berhasil dihapus.');
    }

    protected function authorizeTransaction(Transaction $transaction): void
    {
        if ($transaction->user_id !== auth()->id()) {
            abort(403, 'Akses tidak diizinkan.');
        }
    }
}
