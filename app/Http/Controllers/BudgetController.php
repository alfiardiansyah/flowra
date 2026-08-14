<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Services\BudgetService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function __construct(
        protected BudgetService $budgetService
    ) {}

    public function index(Request $request)
    {
        $user = auth()->user();
        $month = $request->query('month', Carbon::now()->format('Y-m'));

        $budgetData = $this->budgetService->getMonthlyBudgets($user, $month);
        $categories = Category::forUser($user->id)->expense()->parentsOnly()->get();

        // Check if previous month has budgets to copy
        $prevMonth = Carbon::parse($month . '-01')->subMonth()->format('Y-m');
        $hasPrevBudgets = Budget::where('user_id', $user->id)->where('month', $prevMonth)->exists();

        return view('budgets.index', compact('budgetData', 'month', 'categories', 'hasPrevBudgets', 'prevMonth'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        $month = $request->query('month', Carbon::now()->format('Y-m'));
        $categories = Category::forUser($user->id)->expense()->orderBy('name')->get();

        // Filter out categories that already have a budget in this month
        $existingCatIds = Budget::where('user_id', $user->id)->where('month', $month)->pluck('category_id')->toArray();
        $availableCategories = $categories->whereNotIn('id', $existingCatIds);

        return view('budgets.create', compact('availableCategories', 'month'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:1',
            'month' => 'required|date_format:Y-m',
            'notes' => 'nullable|string|max:500',
        ]);

        $exists = Budget::where('user_id', $user->id)
            ->where('category_id', $validated['category_id'])
            ->where('month', $validated['month'])
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors(['category_id' => 'Anggaran untuk kategori ini pada bulan yang dipilih sudah dibuat.']);
        }

        $validated['user_id'] = $user->id;
        Budget::create($validated);

        return redirect()->route('budgets.index', ['month' => $validated['month']])
            ->with('success', 'Anggaran kategori berhasil dibuat!');
    }

    public function edit(Budget $budget)
    {
        $this->authorizeBudget($budget);
        $user = auth()->user();
        $categories = Category::forUser($user->id)->expense()->orderBy('name')->get();

        return view('budgets.edit', compact('budget', 'categories'));
    }

    public function update(Request $request, Budget $budget)
    {
        $this->authorizeBudget($budget);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        $budget->update($validated);

        return redirect()->route('budgets.index', ['month' => $budget->month])
            ->with('success', 'Anggaran berhasil diperbarui!');
    }

    public function destroy(Budget $budget)
    {
        $this->authorizeBudget($budget);
        $month = $budget->month;
        $budget->delete();

        return redirect()->route('budgets.index', ['month' => $month])
            ->with('success', 'Anggaran berhasil dihapus.');
    }

    public function copyPrevious(Request $request)
    {
        $user = auth()->user();
        $targetMonth = $request->validate(['month' => 'required|date_format:Y-m'])['month'];

        $copied = $this->budgetService->copyPreviousMonthBudgets($user, $targetMonth);

        if ($copied > 0) {
            return back()->with('success', "Berhasil menyalin {$copied} anggaran dari bulan sebelumnya!");
        }

        return back()->with('info', 'Tidak ada anggaran baru yang disalin.');
    }

    protected function authorizeBudget(Budget $budget): void
    {
        if ($budget->user_id !== auth()->id()) {
            abort(403, 'Akses tidak diizinkan.');
        }
    }
}
