<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $incomeCategories = Category::forUser($user->id)
            ->income()
            ->parentsOnly()
            ->withCount(['transactions' => function ($q) use ($user) {
                $q->where('user_id', $user->id);
            }])
            ->with(['children' => function ($q) use ($user) {
                $q->forUser($user->id)->withCount(['transactions' => function ($tq) use ($user) {
                    $tq->where('user_id', $user->id);
                }]);
            }])
            ->orderBy('name')
            ->get();

        $expenseCategories = Category::forUser($user->id)
            ->expense()
            ->parentsOnly()
            ->withCount(['transactions' => function ($q) use ($user) {
                $q->where('user_id', $user->id);
            }])
            ->with(['children' => function ($q) use ($user) {
                $q->forUser($user->id)->withCount(['transactions' => function ($tq) use ($user) {
                    $tq->where('user_id', $user->id);
                }]);
            }])
            ->orderBy('name')
            ->get();

        $allCategories = Category::forUser($user->id)->orderBy('name')->get();

        return view('categories.index', compact('incomeCategories', 'expenseCategories', 'allCategories'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:income,expense',
            'parent_id' => 'nullable|exists:categories,id',
            'icon' => 'required|string|max:50',
            'color' => 'required|string|max:20',
        ]);

        $validated['user_id'] = $user->id;
        $validated['is_default'] = false;

        $category = Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Kategori "' . $category->name . '" berhasil ditambahkan!');
    }

    public function update(Request $request, Category $category)
    {
        $this->authorizeCategory($category);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:income,expense',
            'parent_id' => 'nullable|exists:categories,id|different:id',
            'icon' => 'required|string|max:50',
            'color' => 'required|string|max:20',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Kategori "' . $category->name . '" berhasil diperbarui!');
    }

    public function destroy(Request $request, Category $category)
    {
        $this->authorizeCategory($category);

        $user = auth()->user();
        $transactionCount = Transaction::where('category_id', $category->id)->where('user_id', $user->id)->count();

        $action = $request->input('action', 'cascade');

        if ($transactionCount > 0 && $action === 'reassign') {
            $request->validate([
                'target_category_id' => 'required|exists:categories,id',
            ]);

            $targetCategoryId = $request->input('target_category_id');
            if ($targetCategoryId == $category->id) {
                return back()->with('error', 'Kategori tujuan pemindahan tidak boleh sama dengan kategori yang dihapus.');
            }

            DB::transaction(function () use ($category, $targetCategoryId, $user) {
                // Reassign transactions
                Transaction::where('category_id', $category->id)
                    ->where('user_id', $user->id)
                    ->update(['category_id' => $targetCategoryId]);

                // Reassign child categories
                Category::where('parent_id', $category->id)
                    ->update(['parent_id' => $targetCategoryId]);

                $category->delete();
            });

            return redirect()->route('categories.index')->with('success', 'Seluruh transaksi berhasil dipindahkan dan kategori telah dihapus.');
        }

        // Cascade delete / unassign
        DB::transaction(function () use ($category, $user) {
            // Unassign transactions
            Transaction::where('category_id', $category->id)
                ->where('user_id', $user->id)
                ->update(['category_id' => null]);

            // Unassign child categories
            Category::where('parent_id', $category->id)
                ->update(['parent_id' => null]);

            $category->delete();
        });

        return redirect()->route('categories.index')->with('success', 'Kategori "' . $category->name . '" berhasil dihapus.');
    }

    protected function authorizeCategory(Category $category): void
    {
        if ($category->user_id !== null && $category->user_id !== auth()->id()) {
            abort(403, 'Akses tidak diizinkan.');
        }
    }
}
