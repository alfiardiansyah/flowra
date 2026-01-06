<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Income;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Date range
        $from = $request->query('from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $to = $request->query('to', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        // Totals
        $totalIncome = $user->incomes()
            ->whereBetween('tanggal', [$from, $to])
            ->sum('nominal');
        $totalExpense = $user->expenses()
            ->whereBetween('tanggal', [$from, $to])
            ->sum('nominal');
        $balance = $totalIncome - $totalExpense;
        
        // Income by category
        $incomeByCategory = $user->incomes()
            ->select('kategori', DB::raw('sum(nominal) as total'))
            ->whereBetween('tanggal', [$from, $to])
            ->groupBy('kategori')
            ->get();
        
        // Expense by category
        $expenseByCategory = $user->expenses()
            ->select('kategori', DB::raw('sum(nominal) as total'))
            ->whereBetween('tanggal', [$from, $to])
            ->groupBy('kategori')
            ->get();
        
        // Monthly trend (last 6 months)
        $monthlyData = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthIncome = $user->incomes()
                ->whereMonth('tanggal', $date->month)
                ->whereYear('tanggal', $date->year)
                ->sum('nominal');
            $monthExpense = $user->expenses()
                ->whereMonth('tanggal', $date->month)
                ->whereYear('tanggal', $date->year)
                ->sum('nominal');
            
            $monthlyData->push([
                'month' => $date->format('M Y'),
                'income' => $monthIncome,
                'expense' => $monthExpense,
            ]);
        }
        
        return view('reports.index', compact(
            'totalIncome',
            'totalExpense',
            'balance',
            'incomeByCategory',
            'expenseByCategory',
            'monthlyData',
            'from',
            'to'
        ));
    }
}
