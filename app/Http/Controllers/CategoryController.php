<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $incomeCategories = Category::forUser($user->id)
            ->income()
            ->parentsOnly()
            ->with(['children' => function ($q) use ($user) {
                $q->forUser($user->id);
            }])
            ->get();

        $expenseCategories = Category::forUser($user->id)
            ->expense()
            ->parentsOnly()
            ->with(['children' => function ($q) use ($user) {
                $q->forUser($user->id);
            }])
            ->get();

        return view('categories.index', compact('incomeCategories', 'expenseCategories'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        $type = $request->query('type', 'expense');
        $parentId = $request->query('parent_id');

        $parentCategories = Category::forUser($user->id)
            ->where('type', $type)
            ->parentsOnly()
            ->get();

        return view('categories.create', compact('type', 'parentId', 'parentCategories'));
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

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Kategori baru berhasil ditambahkan!');
    }

    public function edit(Category $category)
    {
        $this->authorizeCategory($category);
        $user = auth()->user();

        $parentCategories = Category::forUser($user->id)
            ->where('type', $category->type)
            ->where('id', '!=', $category->id)
            ->parentsOnly()
            ->get();

        return view('categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, Category $category)
    {
        $this->authorizeCategory($category);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'parent_id' => 'nullable|exists:categories,id|different:id',
            'icon' => 'required|string|max:50',
            'color' => 'required|string|max:20',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroy(Category $category)
    {
        $this->authorizeCategory($category);

        $hasTransactions = Transaction::where('category_id', $category->id)->exists();

        if ($hasTransactions) {
            return back()->with('error', 'Kategori ini tidak dapat dihapus karena sudah memiliki catatan transaksi terkait.');
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }

    protected function authorizeCategory(Category $category): void
    {
        if ($category->user_id !== null && $category->user_id !== auth()->id()) {
            abort(403, 'Akses tidak diizinkan.');
        }
    }
}
