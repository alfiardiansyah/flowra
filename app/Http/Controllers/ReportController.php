<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
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
        $accountId = $request->query('account_id') ? (int) $request->query('account_id') : null;
        $categoryId = $request->query('category_id') ? (int) $request->query('category_id') : null;
        $search = $request->query('search') ? trim($request->query('search')) : null;

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

        $report = $this->reportService->generateReport($user, $from, $to, $accountId, $categoryId, $search);

        $accounts = Account::where('user_id', $user->id)->where('is_active', true)->orderBy('name')->get();
        $categories = Category::forUser($user->id)->orderBy('name')->get();

        return view('reports.index', compact('report', 'preset', 'from', 'to', 'accounts', 'categories', 'accountId', 'categoryId', 'search'));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $user = auth()->user();
        $from = $request->query('from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $to = $request->query('to', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $accountId = $request->query('account_id') ? (int) $request->query('account_id') : null;
        $categoryId = $request->query('category_id') ? (int) $request->query('category_id') : null;
        $search = $request->query('search') ? trim($request->query('search')) : null;

        $report = $this->reportService->generateReport($user, $from, $to, $accountId, $categoryId, $search);

        $filename = "Laporan_Keuangan_Flowra_{$from}_sampai_{$to}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($report, $user, $from, $to) {
            $handle = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header Banner Block
            fputcsv($handle, ['FLOWRA - FINANCIAL MANAGEMENT SYSTEM']);
            fputcsv($handle, ['LAPORAN KEUANGAN & STATEMENT ARUS KAS']);
            fputcsv($handle, ['Pemilik Laporan:', $user->name . " ({$user->email})"]);
            fputcsv($handle, ['Periode Laporan:', "{$from} s/d {$to}"]);
            fputcsv($handle, ['Waktu Ekspor:', now()->locale('id')->isoFormat('D MMMM Y, HH:mm') . ' WIB']);
            fputcsv($handle, []);

            // I. RINGKASAN EKSEKUTIF ARUS KAS
            fputcsv($handle, ['I. RINGKASAN EKSEKUTIF ARUS KAS']);
            fputcsv($handle, ['Deskripsi Indikator', 'Nominal (Rp)']);
            fputcsv($handle, ['1. Saldo Awal Periode', number_format($report['opening_balance'], 2, ',', '.')]);
            fputcsv($handle, ['2. Total Pemasukan (+)', number_format($report['total_income'], 2, ',', '.')]);
            fputcsv($handle, ['3. Total Pengeluaran (-)', number_format($report['total_expense'], 2, ',', '.')]);
            fputcsv($handle, ['4. Arus Kas Bersih / Surplus (Defisit)', number_format($report['net_cash_flow'], 2, ',', '.')]);
            fputcsv($handle, ['5. Saldo Akhir Periode', number_format($report['closing_balance'], 2, ',', '.')]);
            fputcsv($handle, ['* Rasio Tabungan Periode Ini', $report['savings_rate'] . '%']);
            fputcsv($handle, []);

            // II. RINCIAN AKTIVITAS REKENING & DOMPET
            fputcsv($handle, ['II. RINCIAN AKTIVITAS REKENING & DOMPET']);
            fputcsv($handle, ['Nama Rekening', 'Tipe', 'Uang Masuk (Rp)', 'Uang Keluar (Rp)', 'Saldo Terkini (Rp)']);
            foreach ($report['accounts'] as $acc) {
                fputcsv($handle, [
                    $acc['name'],
                    $acc['type'],
                    number_format($acc['income'] + $acc['transfers_in'], 2, ',', '.'),
                    number_format($acc['expense'] + $acc['transfers_out'], 2, ',', '.'),
                    number_format($acc['current_balance'], 2, ',', '.'),
                ]);
            }
            fputcsv($handle, []);

            // III. KOMPOSISI PENGELUARAN PER KATEGORI
            fputcsv($handle, ['III. KOMPOSISI PENGELUARAN PER KATEGORI']);
            fputcsv($handle, ['Kategori Pengeluaran', 'Jumlah Transaksi', 'Total Nominal (Rp)', 'Persentase (%)']);
            if (count($report['expenses_by_category']) > 0) {
                foreach ($report['expenses_by_category'] as $cat) {
                    fputcsv($handle, [
                        $cat['name'],
                        $cat['count'] . ' transaksi',
                        number_format($cat['total'], 2, ',', '.'),
                        $cat['percentage'] . '%',
                    ]);
                }
            } else {
                fputcsv($handle, ['Tidak ada data pengeluaran pada periode ini.']);
            }
            fputcsv($handle, []);

            // IV. JURNAL MUTASI TRANSAKSI LENGKAP
            fputcsv($handle, ['IV. JURNAL MUTASI TRANSAKSI LENGKAP (' . count($report['transactions']) . ' Transaksi)']);
            fputcsv($handle, ['No', 'Tanggal', 'Jenis', 'Keterangan', 'Kategori', 'Rekening Asal', 'Rekening Tujuan', 'Nominal (Rp)', 'Catatan']);
            if (count($report['transactions']) > 0) {
                foreach ($report['transactions'] as $i => $tx) {
                    $typeLabel = match ($tx->type) {
                        'income' => 'Pemasukan',
                        'expense' => 'Pengeluaran',
                        'transfer' => 'Transfer',
                        default => ucfirst($tx->type),
                    };

                    fputcsv($handle, [
                        $i + 1,
                        $tx->date ? $tx->date->format('Y-m-d') : '-',
                        $typeLabel,
                        $tx->description,
                        $tx->category?->name ?? 'Lainnya',
                        $tx->account?->name ?? '-',
                        $tx->destinationAccount?->name ?? '-',
                        number_format($tx->amount, 2, ',', '.'),
                        $tx->notes ?? '',
                    ]);
                }
            } else {
                fputcsv($handle, ['Belum ada mutasi transaksi yang tercatat pada periode ini.']);
            }
            fputcsv($handle, []);

            // V. PENGESAHAN & PENUTUP
            fputcsv($handle, ['V. PENGESAHAN & AUDIT']);
            fputcsv($handle, ['Dibuat Oleh:', $user->name . " ({$user->email})"]);
            fputcsv($handle, ['Catatan:', 'Laporan ini di-generate secara otomatis oleh Sistem Manajemen Keuangan Flowra.']);

            fclose($handle);
        }, 200, $headers);
    }
}
