<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ExpenseController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $query = auth()->user()->expenses()->latest();
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('keterangan', 'like', '%' . $request->search . '%')
                  ->orWhere('kategori', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        
        $expenses = $query->paginate(12);
        return view('expenses.index', compact('expenses'));
    }

    public function create()
    {
        return view('expenses.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nominal' => 'required|numeric',
            'keterangan' => 'nullable|string',
            'kategori' => 'nullable|string',
            'tanggal' => 'nullable|date',
            'metode_pembayaran' => 'nullable|string',
            'bukti_pembayaran' => 'nullable|file|image|max:2048',
        ]);
        if($request->hasFile('bukti_pembayaran')){
            $data['bukti_pembayaran'] = $request->file('bukti_pembayaran')->store('proofs','public');
        }
        $data['user_id'] = auth()->id();
        Expense::create($data);
        return redirect()->route('expenses.index')->with('success','Expense recorded in your garden');
    }

    public function edit(Expense $expense)
    {
        $this->authorize('view', $expense);
        return view('expenses.edit', compact('expense'));
    }

    public function update(Request $request, Expense $expense)
    {
        $this->authorize('update', $expense);
        $data = $request->validate([
            'nominal' => 'required|numeric',
            'keterangan' => 'nullable|string',
            'kategori' => 'nullable|string',
            'tanggal' => 'nullable|date',
            'metode_pembayaran' => 'nullable|string',
            'bukti_pembayaran' => 'nullable|file|image|max:2048',
        ]);
        if($request->hasFile('bukti_pembayaran')){
            $data['bukti_pembayaran'] = $request->file('bukti_pembayaran')->store('proofs','public');
        }
        $expense->update($data);
        return redirect()->route('expenses.index')->with('success','Expense updated');
    }

    public function destroy(Expense $expense)
    {
        $this->authorize('delete', $expense);
        $expense->delete();
        return back()->with('success','Expense removed');
    }
}
