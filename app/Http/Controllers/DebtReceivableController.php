<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\DebtReceivable;
use App\Models\DebtReceivablePayment;
use App\Services\DebtReceivableService;
use Illuminate\Http\Request;

class DebtReceivableController extends Controller
{
    public function __construct(
        protected DebtReceivableService $debtService
    ) {}

    public function index(Request $request)
    {
        $user = auth()->user();
        $type = $request->query('type', 'all'); // 'all', 'debt', 'receivable'

        $query = DebtReceivable::with(['account', 'payments'])
            ->where('user_id', $user->id);

        if ($type !== 'all' && in_array($type, ['debt', 'receivable'])) {
            $query->where('type', $type);
        }

        $items = $query->orderByRaw("CASE WHEN status = 'unpaid' THEN 1 WHEN status = 'partially_paid' THEN 2 ELSE 3 END")
            ->orderBy('due_date')
            ->get();

        $totalDebt = (float) DebtReceivable::where('user_id', $user->id)->debt()->whereIn('status', ['unpaid', 'partially_paid'])->sum('amount') 
                   - (float) DebtReceivable::where('user_id', $user->id)->debt()->whereIn('status', ['unpaid', 'partially_paid'])->sum('paid_amount');

        $totalReceivable = (float) DebtReceivable::where('user_id', $user->id)->receivable()->whereIn('status', ['unpaid', 'partially_paid'])->sum('amount')
                         - (float) DebtReceivable::where('user_id', $user->id)->receivable()->whereIn('status', ['unpaid', 'partially_paid'])->sum('paid_amount');

        $accounts = Account::where('user_id', $user->id)->where('is_active', true)->orderBy('name')->get();

        return view('debts.index', compact('items', 'type', 'totalDebt', 'totalReceivable', 'accounts'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        $type = $request->query('type', 'debt');
        $accounts = Account::where('user_id', $user->id)->where('is_active', true)->orderBy('name')->get();

        return view('debts.create', compact('type', 'accounts'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'type' => 'required|in:debt,receivable',
            'person_name' => 'required|string|max:150',
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:date',
            'account_id' => 'nullable|exists:accounts,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $this->debtService->createDebtReceivable($user, $validated);

        $typeLabel = $validated['type'] === 'debt' ? 'Catatan hutang' : 'Catatan piutang';
        return redirect()->route('debts.index', ['type' => $validated['type']])
            ->with('success', "{$typeLabel} berhasil ditambahkan dan saldo rekening telah disesuaikan!");
    }

    public function show(DebtReceivable $debt)
    {
        $this->authorizeDebt($debt);
        $user = auth()->user();
        $debt->load(['account', 'payments.account']);
        $accounts = Account::where('user_id', $user->id)->where('is_active', true)->orderBy('name')->get();

        return view('debts.show', compact('debt', 'accounts'));
    }

    public function edit(DebtReceivable $debt)
    {
        $this->authorizeDebt($debt);
        $user = auth()->user();
        $accounts = Account::where('user_id', $user->id)->where('is_active', true)->orderBy('name')->get();

        return view('debts.edit', compact('debt', 'accounts'));
    }

    public function update(Request $request, DebtReceivable $debt)
    {
        $this->authorizeDebt($debt);

        $validated = $request->validate([
            'person_name' => 'required|string|max:150',
            'amount' => 'required|numeric|min:' . $debt->paid_amount,
            'date' => 'required|date',
            'due_date' => 'nullable|date',
            'account_id' => 'nullable|exists:accounts,id',
            'notes' => 'nullable|string|max:500',
        ], [
            'amount.min' => 'Jumlah tidak boleh lebih kecil dari nominal yang sudah dibayarkan (Rp ' . number_format($debt->paid_amount, 0, ',', '.') . ').',
        ]);

        $this->debtService->updateDebtReceivable($debt, $validated);

        return redirect()->route('debts.index', ['type' => $debt->type])
            ->with('success', 'Catatan berhasil diperbarui dan transaksi terkait telah disesuaikan!');
    }

    public function recordPayment(Request $request, DebtReceivable $debt)
    {
        $this->authorizeDebt($debt);

        $remaining = $debt->remaining_amount;

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $remaining,
            'account_id' => 'required|exists:accounts,id',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ], [
            'amount.max' => 'Nominal pembayaran tidak boleh melebihi sisa kewajiban (Rp ' . number_format($remaining, 0, ',', '.') . ').',
        ]);

        $this->debtService->recordPayment($debt, $validated);

        return back()->with('success', 'Pembayaran berhasil dicatat dan saldo rekening telah disesuaikan!');
    }

    public function deletePayment(DebtReceivablePayment $payment)
    {
        if ($payment->debtReceivable->user_id !== auth()->id()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $this->debtService->deletePayment($payment);

        return back()->with('success', 'Riwayat pembayaran berhasil dibatalkan dan saldo dikembalikan.');
    }

    public function destroy(DebtReceivable $debt)
    {
        $this->authorizeDebt($debt);

        $type = $debt->type;
        $this->debtService->deleteDebtReceivable($debt);

        return redirect()->route('debts.index', ['type' => $type])
            ->with('success', 'Catatan berhasil dihapus dan saldo rekening telah dikembalikan.');
    }

    protected function authorizeDebt(DebtReceivable $debt): void
    {
        if ($debt->user_id !== auth()->id()) {
            abort(403, 'Akses tidak diizinkan.');
        }
    }
}
