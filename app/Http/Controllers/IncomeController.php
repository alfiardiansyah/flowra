<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Income;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class IncomeController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $query = auth()->user()->incomes()->latest();
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('keterangan', 'like', '%' . $request->search . '%')
                  ->orWhere('kategori', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        
        $incomes = $query->paginate(12);
        return view('incomes.index', compact('incomes'));
    }

    public function create()
    {
        return view('incomes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nominal' => 'required|numeric',
            'keterangan' => 'nullable|string',
            'kategori' => 'nullable|string',
            'tanggal' => 'nullable|date',
            'nama_bank' => 'nullable|string',
            'bukti_transfer' => 'nullable|file|image|max:2048',
        ]);
        if($request->hasFile('bukti_transfer')){
            $data['bukti_transfer'] = $request->file('bukti_transfer')->store('proofs','public');
        }
        $data['user_id'] = auth()->id();
        Income::create($data);
        return redirect()->route('incomes.index')->with('success','Your income has bloomed!');
    }

    public function edit(Income $income)
    {
        $this->authorize('view', $income);
        return view('incomes.edit', compact('income'));
    }

    public function update(Request $request, Income $income)
    {
        $this->authorize('update', $income);
        $data = $request->validate([
            'nominal' => 'required|numeric',
            'keterangan' => 'nullable|string',
            'kategori' => 'nullable|string',
            'tanggal' => 'nullable|date',
            'nama_bank' => 'nullable|string',
            'bukti_transfer' => 'nullable|file|image|max:2048',
        ]);
        if($request->hasFile('bukti_transfer')){
            $data['bukti_transfer'] = $request->file('bukti_transfer')->store('proofs','public');
        }
        $income->update($data);
        return redirect()->route('incomes.index')->with('success','Income updated successfully');
    }

    public function destroy(Income $income)
    {
        $this->authorize('delete', $income);
        $income->delete();
        return back()->with('success','Income removed');
    }
}
