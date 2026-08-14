<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\FinancialReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(
        protected FinancialReportService $reportService
    ) {}

    public function index(Request $request)
    {
        $user = auth()->user();
        $preset = $request->query('preset', 'this_month');

        $now = Carbon::now();

        switch ($preset) {
            case 'last_month':
                $from = $now->copy()->subMonth()->startOfMonth()->format('Y-m-d');
                $to = $now->copy()->subMonth()->endOfMonth()->format('Y-m-d');
                break;
            case 'this_year':
                $from = $now->copy()->startOfYear()->format('Y-m-d');
                $to = $now->copy()->endOfYear()->format('Y-m-d');
                break;
            case 'custom':
                $from = $request->query('from', $now->copy()->startOfMonth()->format('Y-m-d'));
                $to = $request->query('to', $now->copy()->endOfMonth()->format('Y-m-d'));
                break;
            case 'this_month':
            default:
                $preset = 'this_month';
                $from = $request->query('from', $now->copy()->startOfMonth()->format('Y-m-d'));
                $to = $request->query('to', $now->copy()->endOfMonth()->format('Y-m-d'));
                break;
        }

        $report = $this->reportService->generateReport($user, $from, $to);

        return view('reports.index', compact('report', 'preset', 'from', 'to'));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $user = auth()->user();
        $from = $request->query('from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $to = $request->query('to', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $transactions = Transaction::with(['account', 'destinationAccount', 'category'])
            ->where('user_id', $user->id)
            ->whereBetween('date', [$from, $to])
            ->orderBy('date')
            ->get();

        $filename = "Laporan_Keuangan_Flowra_{$from}_sampai_{$to}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($transactions, $from, $to) {
            $handle = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['LAPORAN KEUANGAN FLOWRA']);
            fputcsv($handle, ["Periode: {$from} s/d {$to}"]);
            fputcsv($handle, []);

            fputcsv($handle, ['Tanggal', 'Jenis', 'Keterangan', 'Kategori', 'Rekening Asal', 'Rekening Tujuan', 'Nominal (Rp)', 'Catatan']);

            foreach ($transactions as $tx) {
                $typeLabel = match ($tx->type) {
                    'income' => 'Pemasukan',
                    'expense' => 'Pengeluaran',
                    'transfer' => 'Transfer',
                    default => ucfirst($tx->type),
                };

                fputcsv($handle, [
                    $tx->date ? $tx->date->format('Y-m-d') : '-',
                    $typeLabel,
                    $tx->description,
                    $tx->category?->name ?? 'Lainnya',
                    $tx->account?->name ?? '-',
                    $tx->destinationAccount?->name ?? '-',
                    $tx->amount,
                    $tx->notes ?? '',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
