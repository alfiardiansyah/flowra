<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected TransactionService $transactionService
    ) {}

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Transaction::with(['account', 'category'])
            ->where('user_id', $user->id)
            ->where('type', 'expense')
            ->orderByDesc('date')
            ->orderByDesc('id');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $catName = $request->kategori;
            $query->whereHas('category', function ($q) use ($catName) {
                $q->where('name', $catName);
            });
        }

        $expenses = $query->paginate(12)->withQueryString();
        $categories = Category::forUser($user->id)->expense()->get();

        return view('expenses.index', compact('expenses', 'categories'));
    }

    public function create()
    {
        $user = auth()->user();
        $accounts = Account::where('user_id', $user->id)->where('is_active', true)->get();
        $categories = Category::forUser($user->id)->expense()->get();

        return view('expenses.create', compact('accounts', 'categories'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'nominal' => 'required|numeric|min:0.01',
            'keterangan' => 'nullable|string|max:255',
            'kategori' => 'nullable|string|max:100',
            'tanggal' => 'nullable|date',
            'metode_pembayaran' => 'nullable|string|max:100',
            'bukti_pembayaran' => 'nullable|file|image|max:3072',
            'account_id' => 'nullable|exists:accounts,id',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $attachment = null;
        if ($request->hasFile('bukti_pembayaran')) {
            $attachment = $request->file('bukti_pembayaran')->store('proofs', 'public');
        }

        // Determine account
        $accountId = $data['account_id'] ?? null;
        if (!$accountId) {
            $bankName = $data['metode_pembayaran'] ?? 'Tunai';
            $acc = Account::where('user_id', $user->id)
                ->where(function ($q) use ($bankName) {
                    $q->where('name', 'like', "%{$bankName}%")
                      ->orWhere('type', strtolower($bankName));
                })->first();

            if (!$acc) {
                $acc = Account::where('user_id', $user->id)->first() ?? Account::create([
                    'user_id' => $user->id,
                    'name' => 'Dompet Tunai',
                    'type' => 'cash',
                    'opening_balance' => 0,
                    'current_balance' => 0,
                ]);
            }
            $accountId = $acc->id;
        }

        // Determine category
        $categoryId = $data['category_id'] ?? null;
        if (!$categoryId && !empty($data['kategori'])) {
            $cat = Category::forUser($user->id)->expense()->where('name', $data['kategori'])->first();
            if (!$cat) {
                $cat = Category::forUser($user->id)->where('name', 'like', "%{$data['kategori']}%")->first();
            }
            $categoryId = $cat?->id;
        }

        try {
            $this->transactionService->createTransaction($user, [
                'type' => 'expense',
                'account_id' => $accountId,
                'category_id' => $categoryId,
                'amount' => $data['nominal'],
                'date' => $data['tanggal'] ?? now()->format('Y-m-d'),
                'description' => $data['keterangan'] ?: ($data['kategori'] ?? 'Pengeluaran'),
                'attachment' => $attachment,
            ]);

            return redirect()->route('expenses.index')->with('success', 'Pengeluaran berhasil dicatat di kebun keuangan Anda!');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Gagal mencatat pengeluaran: ' . $e->getMessage());
        }
    }

    public function edit(Transaction $expense)
    {
        $this->authorizeTransaction($expense);
        $user = auth()->user();
        $accounts = Account::where('user_id', $user->id)->where('is_active', true)->get();
        $categories = Category::forUser($user->id)->expense()->get();

        return view('expenses.edit', compact('expense', 'accounts', 'categories'));
    }

    public function update(Request $request, Transaction $expense)
    {
        $this->authorizeTransaction($expense);
        $user = auth()->user();

        $data = $request->validate([
            'nominal' => 'required|numeric|min:0.01',
            'keterangan' => 'nullable|string|max:255',
            'kategori' => 'nullable|string|max:100',
            'tanggal' => 'nullable|date',
            'metode_pembayaran' => 'nullable|string|max:100',
            'bukti_pembayaran' => 'nullable|file|image|max:3072',
            'account_id' => 'nullable|exists:accounts,id',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $updateData = [
            'amount' => $data['nominal'],
            'date' => $data['tanggal'] ?? $expense->date,
            'description' => $data['keterangan'] ?: ($data['kategori'] ?? $expense->description),
        ];

        if (!empty($data['account_id'])) {
            $updateData['account_id'] = $data['account_id'];
        }
        if (!empty($data['category_id'])) {
            $updateData['category_id'] = $data['category_id'];
        } elseif (!empty($data['kategori'])) {
            $cat = Category::forUser($user->id)->expense()->where('name', $data['kategori'])->first();
            if ($cat) $updateData['category_id'] = $cat->id;
        }

        if ($request->hasFile('bukti_pembayaran')) {
            $updateData['attachment'] = $request->file('bukti_pembayaran')->store('proofs', 'public');
        }

        $this->transactionService->updateTransaction($expense, $updateData);

        return redirect()->route('expenses.index')->with('success', 'Pengeluaran berhasil diperbarui!');
    }

    public function destroy(Transaction $expense)
    {
        $this->authorizeTransaction($expense);

        $this->transactionService->deleteTransaction($expense);

        return back()->with('success', 'Pengeluaran berhasil dihapus.');
    }

    protected function authorizeTransaction(Transaction $transaction): void
    {
        if ($transaction->user_id !== auth()->id()) {
            abort(403, 'Akses tidak diizinkan.');
        }
    }
}
