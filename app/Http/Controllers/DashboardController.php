<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Income;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Calculate totals
        $totalIncome = $user->incomes()->sum('nominal');
        $totalExpense = $user->expenses()->sum('nominal');
        $totalSaldo = $totalIncome - $totalExpense;
        
        // This month's totals
        $thisMonthIncome = $user->incomes()
            ->whereMonth('tanggal', Carbon::now()->month)
            ->whereYear('tanggal', Carbon::now()->year)
            ->sum('nominal');
            
        $thisMonthExpense = $user->expenses()
            ->whereMonth('tanggal', Carbon::now()->month)
            ->whereYear('tanggal', Carbon::now()->year)
            ->sum('nominal');
        
        // Last 7 days transactions for chart
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dayIncome = (float) $user->incomes()
                ->whereDate('tanggal', $date->format('Y-m-d'))
                ->sum('nominal');
            $dayExpense = (float) $user->expenses()
                ->whereDate('tanggal', $date->format('Y-m-d'))
                ->sum('nominal');
            
            $last7Days->push([
                'date' => $date->format('Y-m-d'),
                'label' => $date->format('d M'),
                'income' => (float) $dayIncome,
                'expense' => (float) $dayExpense,
            ]);
        }
        
        // Recent transactions (last 5)
        $recentIncomes = $user->incomes()->latest('tanggal')->limit(3)->get();
        $recentExpenses = $user->expenses()->latest('tanggal')->limit(3)->get();
        $recent = $recentIncomes->concat($recentExpenses)
            ->sortByDesc('tanggal')
            ->take(5);
        
        // Category summaries
        $incomeByCategory = $user->incomes()
            ->select('kategori', DB::raw('sum(nominal) as total'))
            ->groupBy('kategori')
            ->get()
            ->map(function ($item) {
                return [
                    'kategori' => $item->kategori ?? 'Lainnya',
                    'total' => (float) $item->total
                ];
            });
            
        $expenseByCategory = $user->expenses()
            ->select('kategori', DB::raw('sum(nominal) as total'))
            ->groupBy('kategori')
            ->get()
            ->map(function ($item) {
                return [
                    'kategori' => $item->kategori ?? 'Lainnya',
                    'total' => (float) $item->total
                ];
            });
        
        return view('dashboard.index', compact(
            'totalSaldo',
            'totalIncome',
            'totalExpense',
            'thisMonthIncome',
            'thisMonthExpense',
            'last7Days',
            'recent',
            'incomeByCategory',
            'expenseByCategory'
        ));
    }
}
