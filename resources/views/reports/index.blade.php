<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 no-print">
            <div>
                <h2 class="font-heading text-2xl sm:text-3xl text-sage-600 flex items-center gap-2.5">
                    <x-icon name="cherry-blossom" class="w-7 h-7 sm:w-8 sm:h-8 text-sage-400" />
                    Laporan Keuangan & Analisis
                </h2>
                <p class="mt-0.5 text-earth-600 text-xs sm:text-sm">Analisis mendalam arus kas, rincian pengeluaran, dan jurnal transaksi</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('reports.export-csv', array_merge(request()->all(), ['from' => $from, 'to' => $to])) }}" class="btn-flora-secondary text-xs py-2 px-3 flex items-center gap-1.5" title="Download spreadsheet CSV">
                    <x-icon name="download" class="w-3.5 h-3.5" />
                    <span class="hidden sm:inline">Export CSV / Excel</span>
                    <span class="sm:hidden">CSV</span>
                </a>
                <button onclick="window.print()" class="btn-flora-primary text-xs py-2 px-3 flex items-center gap-1.5 shadow-sm" title="Cetak laporan">
                    <x-icon name="printer" class="w-3.5 h-3.5 text-white" />
                    <span>Cetak Laporan</span>
                </button>
            </div>
        </div>
    </x-slot>

    <!-- LIVE FILTER & PERIOD SEARCH BAR (SCREEN ONLY) -->
    <x-card class="mb-5 sm:mb-6 p-4 sm:p-5 no-print" x-data="{
        submitTimeout: null,
        triggerLiveFilter() {
            clearTimeout(this.submitTimeout);
            this.submitTimeout = setTimeout(() => {
                $refs.reportFilterForm.submit();
            }, 400);
        }
    }">
        <form method="GET" action="{{ route('reports.index') }}" x-ref="reportFilterForm" class="space-y-4">
            <input type="hidden" name="preset" value="custom">

            <!-- Preset Tabs (Horizontal Scroll on Mobile) -->
            <div class="flex items-center justify-between pb-2 border-b border-sage-100 gap-2">
                <div class="flex items-center gap-1.5 overflow-x-auto -mx-1 px-1 custom-scrollbar scrollbar-none whitespace-nowrap">
                    <a href="{{ route('reports.index', array_merge(request()->except('preset', 'from', 'to', 'page'), ['preset' => 'this_month'])) }}" 
                       class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all duration-200 flex-shrink-0 {{ $preset === 'this_month' ? 'bg-sage-600 text-white shadow-sm' : 'bg-sage-50 text-earth-700 hover:bg-sage-100' }}">
                        Bulan Ini
                    </a>
                    <a href="{{ route('reports.index', array_merge(request()->except('preset', 'from', 'to', 'page'), ['preset' => 'last_month'])) }}" 
                       class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all duration-200 flex-shrink-0 {{ $preset === 'last_month' ? 'bg-sage-600 text-white shadow-sm' : 'bg-sage-50 text-earth-700 hover:bg-sage-100' }}">
                        Bulan Lalu
                    </a>
                    <a href="{{ route('reports.index', array_merge(request()->except('preset', 'from', 'to', 'page'), ['preset' => 'this_year'])) }}" 
                       class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all duration-200 flex-shrink-0 {{ $preset === 'this_year' ? 'bg-sage-600 text-white shadow-sm' : 'bg-sage-50 text-earth-700 hover:bg-sage-100' }}">
                        Tahun Ini
                    </a>
                </div>

                @if(request()->hasAny(['search', 'account_id', 'category_id', 'from', 'to']) || $preset !== 'this_month')
                    <a href="{{ route('reports.index') }}" class="btn-flora-secondary text-xs py-1.5 px-3 flex-shrink-0 flex items-center gap-1" title="Reset Filter">
                        <x-icon name="delete-wilt" class="w-3.5 h-3.5 text-coral-500" />
                        <span>Reset Filter</span>
                    </a>
                @endif
            </div>

            <!-- Live Filter Inputs Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                <div>
                    <label class="block text-[11px] sm:text-xs font-medium text-earth-700 mb-1">Dari Tanggal</label>
                    <input type="date" name="from" value="{{ $from }}" @change="$refs.reportFilterForm.submit()" class="flora-input text-xs py-2">
                </div>

                <div>
                    <label class="block text-[11px] sm:text-xs font-medium text-earth-700 mb-1">Sampai Tanggal</label>
                    <input type="date" name="to" value="{{ $to }}" @change="$refs.reportFilterForm.submit()" class="flora-input text-xs py-2">
                </div>

                <div>
                    <label class="block text-[11px] sm:text-xs font-medium text-earth-700 mb-1">Rekening</label>
                    <select name="account_id" @change="$refs.reportFilterForm.submit()" class="flora-input text-xs py-2">
                        <option value="">Semua Rekening</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ (string)$accountId === (string)$acc->id ? 'selected' : '' }}>
                                {{ $acc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] sm:text-xs font-medium text-earth-700 mb-1">Kategori</label>
                    <select name="category_id" @change="$refs.reportFilterForm.submit()" class="flora-input text-xs py-2">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (string)$categoryId === (string)$cat->id ? 'selected' : '' }}>
                                {{ $cat->name }} ({{ $cat->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] sm:text-xs font-medium text-earth-700 mb-1">Cari Kata Kunci</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ $search }}" 
                               @input="triggerLiveFilter()" 
                               x-init="if ($el.value) { $el.focus(); $el.setSelectionRange($el.value.length, $el.value.length); }"
                               placeholder="Cari transaksi..." 
                               class="flora-input text-xs py-2 pr-8">
                        <x-icon name="search" class="w-3.5 h-3.5 text-sage-400 absolute right-2.5 top-2.5 pointer-events-none" />
                    </div>
                </div>
            </div>
        </form>
    </x-card>

    <!-- ON-SCREEN REPORT VIEW (SCREEN ONLY) -->
    <div class="no-print space-y-6">
        <!-- Cash Flow Summary Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
            <!-- Saldo Awal -->
            <x-card class="p-3.5 sm:p-4 border-l-4 border-l-sage-400">
                <div class="text-[11px] text-earth-500 font-medium">Saldo Awal Periode</div>
                <div class="text-lg sm:text-xl font-bold text-sage-700 mt-0.5">
                    Rp {{ number_format($report['opening_balance'], 0, ',', '.') }}
                </div>
                <div class="text-[10px] text-earth-400 mt-0.5">Sebelum {{ \Carbon\Carbon::parse($from)->format('d M Y') }}</div>
            </x-card>

            <!-- Total Pemasukan -->
            <x-card class="p-3.5 sm:p-4 border-l-4 border-l-leaf-400">
                <div class="text-[11px] text-earth-500 font-medium">Total Pemasukan</div>
                <div class="text-lg sm:text-xl font-bold text-leaf-600 mt-0.5">
                    Rp {{ number_format($report['total_income'], 0, ',', '.') }}
                </div>
                <div class="text-[10px] text-earth-400 mt-0.5">Periode terpilih</div>
            </x-card>

            <!-- Total Pengeluaran -->
            <x-card class="p-3.5 sm:p-4 border-l-4 border-l-coral-400">
                <div class="text-[11px] text-earth-500 font-medium">Total Pengeluaran</div>
                <div class="text-lg sm:text-xl font-bold text-coral-600 mt-0.5">
                    Rp {{ number_format($report['total_expense'], 0, ',', '.') }}
                </div>
                <div class="text-[10px] text-earth-400 mt-0.5">Periode terpilih</div>
            </x-card>

            <!-- Arus Kas Bersih -->
            <x-card class="p-3.5 sm:p-4 border-l-4 border-l-sky-400">
                <div class="text-[11px] text-earth-500 font-medium">Arus Kas Bersih</div>
                <div class="text-lg sm:text-xl font-bold {{ $report['net_cash_flow'] >= 0 ? 'text-leaf-600' : 'text-coral-600' }} mt-0.5">
                    {{ $report['net_cash_flow'] >= 0 ? '+' : '' }} Rp {{ number_format($report['net_cash_flow'], 0, ',', '.') }}
                </div>
                <div class="text-[10px] text-earth-400 mt-0.5">Rasio Simpan: {{ $report['savings_rate'] }}%</div>
            </x-card>

            <!-- Saldo Akhir -->
            <x-card class="p-3.5 sm:p-4 border-l-4 border-l-earth-500">
                <div class="text-[11px] text-earth-500 font-medium">Saldo Akhir Periode</div>
                <div class="text-lg sm:text-xl font-bold text-earth-800 mt-0.5">
                    Rp {{ number_format($report['closing_balance'], 0, ',', '.') }}
                </div>
                <div class="text-[10px] text-earth-400 mt-0.5">Saldo Awal + Arus Kas</div>
            </x-card>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Monthly / Daily Trend Line Chart -->
            <x-card variant="chart" class="p-4 sm:p-5">
                <h3 class="font-heading text-base sm:text-lg text-sage-700 mb-3 flex items-center gap-2">
                    <x-icon name="flower-bloom" class="w-5 h-5 text-sage-500" />
                    Tren Arus Kas (Pemasukan vs Pengeluaran)
                </h3>
                <div style="position: relative; height: 260px;">
                    <canvas id="reportTrendChart"></canvas>
                </div>
            </x-card>

            <!-- Expense Category Breakdown Doughnut -->
            <x-card variant="chart" class="p-4 sm:p-5">
                <h3 class="font-heading text-base sm:text-lg text-sage-700 mb-3 flex items-center gap-2">
                    <x-icon name="bouquet" class="w-5 h-5 text-coral-500" />
                    Komposisi Pengeluaran per Kategori
                </h3>
                <div style="position: relative; height: 260px;">
                    @if(count($report['expenses_by_category']) > 0)
                        <canvas id="reportExpenseChart"></canvas>
                    @else
                        <div class="h-full flex items-center justify-center text-center p-4">
                            <p class="text-xs text-earth-500">Tidak ada data pengeluaran pada periode ini</p>
                        </div>
                    @endif
                </div>
            </x-card>
        </div>

        <!-- Detailed Breakdown Tables Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Ranked Expenses by Category -->
            <x-card class="p-4 sm:p-5">
                <h3 class="font-heading text-base sm:text-lg text-sage-700 mb-3 pb-2 border-b border-sage-100 flex items-center gap-2">
                    <x-icon name="falling-leaves" class="w-5 h-5 text-coral-500" />
                    Rincian Pengeluaran per Kategori
                </h3>

                @if(count($report['expenses_by_category']) > 0)
                    <div class="space-y-3.5">
                        @foreach($report['expenses_by_category'] as $item)
                            <div>
                                <div class="flex items-center justify-between text-xs mb-1 font-medium">
                                    <div class="flex items-center gap-2">
                                        <x-icon :name="$item['icon']" class="w-4 h-4" />
                                        <span class="font-semibold text-earth-800">{{ $item['name'] }}</span>
                                        <span class="text-[10px] text-earth-500">({{ $item['count'] }} tx)</span>
                                    </div>
                                    <div>
                                        <span class="font-bold text-earth-800">Rp {{ number_format($item['total'], 0, ',', '.') }}</span>
                                        <span class="text-xs text-coral-600 font-semibold ml-1">({{ $item['percentage'] }}%)</span>
                                    </div>
                                </div>
                                <div class="w-full bg-sage-100 rounded-full h-2 overflow-hidden">
                                    <div class="h-2 rounded-full" style="width: {{ $item['percentage'] }}%; background-color: {{ $item['color'] }};"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-earth-500 py-6 text-center">Belum ada transaksi pengeluaran pada periode ini.</p>
                @endif
            </x-card>

            <!-- Account Dynamics in Period -->
            <x-card class="p-4 sm:p-5">
                <h3 class="font-heading text-base sm:text-lg text-sage-700 mb-3 pb-2 border-b border-sage-100 flex items-center gap-2">
                    <x-icon name="cash-leaf" class="w-5 h-5 text-sage-500" />
                    Aktivitas per Rekening / Dompet
                </h3>

                <div class="overflow-x-auto">
                    <table class="flora-table text-xs">
                        <thead>
                            <tr>
                                <th>Rekening</th>
                                <th>Uang Masuk</th>
                                <th>Uang Keluar</th>
                                <th class="text-right">Saldo Terkini</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($report['accounts'] as $accData)
                                <tr>
                                    <td class="font-semibold text-earth-800 flex items-center gap-1.5">
                                        <x-icon :name="$accData['icon']" class="w-4 h-4" />
                                        <span>{{ $accData['name'] }}</span>
                                    </td>
                                    <td class="text-leaf-600 font-medium">
                                        +Rp {{ number_format($accData['income'] + $accData['transfers_in'], 0, ',', '.') }}
                                    </td>
                                    <td class="text-coral-600 font-medium">
                                        -Rp {{ number_format($accData['expense'] + $accData['transfers_out'], 0, ',', '.') }}
                                    </td>
                                    <td class="text-right font-bold text-sage-700">
                                        Rp {{ number_format($accData['current_balance'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- STUNNING EXECUTIVE PRINT REPORT TEMPLATE (HIDDEN ON SCREEN, VISIBLE ON PRINT) -->
    <!-- ========================================================================= -->
    <div class="hidden print:block print-document bg-white text-slate-900 font-sans leading-snug text-[10px] p-0 m-0 w-full">
        <!-- Official Header Block -->
        <div class="border-b-2 border-slate-800 pb-3 mb-4 flex items-start justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-emerald-700 text-white flex items-center justify-center font-bold text-lg shadow-sm">
                    🌿
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight leading-none uppercase">FLOWRA</h1>
                    <div class="text-[10px] text-slate-500 font-medium mt-0.5 uppercase tracking-wider">Sistem Manajemen Keuangan Pribadi</div>
                </div>
            </div>

            <div class="text-right">
                <div class="text-base font-bold text-slate-900 tracking-tight">LAPORAN KEUANGAN</div>
                <div class="text-[11px] font-semibold text-emerald-800 mt-0.5">PERIODE: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</div>
            </div>
        </div>

        <!-- Metadata Information Strip -->
        <div class="grid grid-cols-3 gap-3 p-2.5 bg-slate-100 rounded-lg border border-slate-200 mb-4 text-[10px]">
            <div>
                <span class="text-slate-500 block uppercase text-[8.5px] font-bold">Pemilik Laporan</span>
                <span class="font-bold text-slate-900">{{ Auth::user()->name }}</span>
            </div>
            <div>
                <span class="text-slate-500 block uppercase text-[8.5px] font-bold">Email Pengguna</span>
                <span class="font-semibold text-slate-800">{{ Auth::user()->email }}</span>
            </div>
            <div class="text-right">
                <span class="text-slate-500 block uppercase text-[8.5px] font-bold">Waktu Cetak</span>
                <span class="font-semibold text-slate-800">{{ now()->locale('id')->isoFormat('D MMMM Y, HH:mm') }} WIB</span>
            </div>
        </div>

        <!-- I. RINGKASAN EKSEKUTIF ARUS KAS -->
        <div class="mb-5">
            <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-800 border-b border-slate-300 pb-1 mb-2">
                I. RINGKASAN EKSEKUTIF ARUS KAS
            </h2>

            <table class="w-full border-collapse text-[10px] mb-1.5 border border-slate-200" style="table-layout: fixed; width: 100%;">
                <thead>
                    <tr class="bg-slate-800 text-white font-bold text-[10px]">
                        <th class="p-1.5 text-left w-[65%]">Deskripsi Indikator</th>
                        <th class="p-1.5 text-right w-[35%]">Nominal (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <tr>
                        <td class="p-1.5 font-medium">1. Saldo Awal Periode (Sebelum {{ \Carbon\Carbon::parse($from)->format('d M Y') }})</td>
                        <td class="p-1.5 text-right font-bold text-slate-800">Rp {{ number_format($report['opening_balance'], 0, ',', '.') }}</td>
                    </tr>
                    <tr class="bg-emerald-50/50">
                        <td class="p-1.5 font-medium text-emerald-900">2. Total Pemasukan (+)</td>
                        <td class="p-1.5 text-right font-bold text-emerald-700">+ Rp {{ number_format($report['total_income'], 0, ',', '.') }}</td>
                    </tr>
                    <tr class="bg-rose-50/50">
                        <td class="p-1.5 font-medium text-rose-900">3. Total Pengeluaran (-)</td>
                        <td class="p-1.5 text-right font-bold text-rose-700">- Rp {{ number_format($report['total_expense'], 0, ',', '.') }}</td>
                    </tr>
                    <tr class="bg-slate-100 font-bold border-t-2 border-slate-400">
                        <td class="p-1.5">4. Arus Kas Bersih / Surplus (Defisit)</td>
                        <td class="p-1.5 text-right {{ $report['net_cash_flow'] >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                            {{ $report['net_cash_flow'] >= 0 ? '+' : '' }} Rp {{ number_format($report['net_cash_flow'], 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr class="bg-slate-800 text-white font-bold">
                        <td class="p-2">5. SALDO AKHIR PERIODE (Posisi per {{ \Carbon\Carbon::parse($to)->format('d M Y') }})</td>
                        <td class="p-2 text-right text-xs">Rp {{ number_format($report['closing_balance'], 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
            <div class="text-[9px] text-slate-500 italic mt-0.5">* Rasio Tabungan Periode Ini: <b class="text-slate-800">{{ $report['savings_rate'] }}%</b> dari total pemasukan.</div>
        </div>

        <!-- II. RINCIAN AKTIVITAS PER REKENING -->
        <div class="mb-5 page-break-inside-avoid">
            <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-800 border-b border-slate-300 pb-1 mb-2">
                II. RINCIAN AKTIVITAS REKENING & DOMPET
            </h2>

            <table class="w-full border-collapse text-[9.5px] border border-slate-200" style="table-layout: fixed; width: 100%;">
                <thead>
                    <tr class="bg-slate-100 border-b border-slate-300 text-slate-700 font-bold text-[9px] uppercase">
                        <th class="p-1.5 text-left w-[28%]">Nama Rekening</th>
                        <th class="p-1.5 text-left w-[18%]">Tipe</th>
                        <th class="p-1.5 text-right w-[18%]">Uang Masuk</th>
                        <th class="p-1.5 text-right w-[18%]">Uang Keluar</th>
                        <th class="p-1.5 text-right w-[18%]">Saldo Terkini</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($report['accounts'] as $accData)
                        <tr>
                            <td class="p-1.5 font-bold text-slate-900 truncate">{{ $accData['name'] }}</td>
                            <td class="p-1.5 text-slate-600 text-[9px] truncate">{{ $accData['type'] }}</td>
                            <td class="p-1.5 text-right text-emerald-700 font-medium">+Rp {{ number_format($accData['income'] + $accData['transfers_in'], 0, ',', '.') }}</td>
                            <td class="p-1.5 text-right text-rose-700 font-medium">-Rp {{ number_format($accData['expense'] + $accData['transfers_out'], 0, ',', '.') }}</td>
                            <td class="p-1.5 text-right font-bold text-slate-900">Rp {{ number_format($accData['current_balance'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- III. RINCIAN PENGELUARAN PER KATEGORI -->
        <div class="mb-5 page-break-inside-avoid">
            <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-800 border-b border-slate-300 pb-1 mb-2">
                III. KOMPOSISI PENGELUARAN PER KATEGORI
            </h2>

            @if(count($report['expenses_by_category']) > 0)
                <table class="w-full border-collapse text-[9.5px] border border-slate-200" style="table-layout: fixed; width: 100%;">
                    <thead>
                        <tr class="bg-slate-100 border-b border-slate-300 text-slate-700 font-bold text-[9px] uppercase">
                            <th class="p-1.5 text-left w-[40%]">Kategori Pengeluaran</th>
                            <th class="p-1.5 text-center w-[20%]">Jumlah Transaksi</th>
                            <th class="p-1.5 text-right w-[25%]">Total Nominal (Rp)</th>
                            <th class="p-1.5 text-right w-[15%]">Persentase (%)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($report['expenses_by_category'] as $item)
                            <tr>
                                <td class="p-1.5 font-semibold text-slate-900 truncate">{{ $item['name'] }}</td>
                                <td class="p-1.5 text-center text-slate-600">{{ $item['count'] }} tx</td>
                                <td class="p-1.5 text-right font-bold text-slate-800">Rp {{ number_format($item['total'], 0, ',', '.') }}</td>
                                <td class="p-1.5 text-right font-semibold text-rose-600">{{ $item['percentage'] }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-[9.5px] text-slate-500 italic py-1.5">Tidak ada transaksi pengeluaran pada periode ini.</p>
            @endif
        </div>

        <!-- IV. JURNAL MUTASI TRANSAKSI LENGKAP -->
        <div class="mb-5 page-break-inside-avoid">
            <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-800 border-b border-slate-300 pb-1 mb-2">
                IV. JURNAL MUTASI TRANSAKSI PERIODE INI ({{ count($report['transactions']) }} Transaksi)
            </h2>

            @if(count($report['transactions']) > 0)
                <table class="w-full border-collapse text-[9px] border border-slate-200" style="table-layout: fixed; width: 100%;">
                    <thead>
                        <tr class="bg-slate-800 text-white font-bold uppercase text-[8.5px]">
                            <th class="p-1 text-left w-[13%]">Tanggal</th>
                            <th class="p-1 text-left w-[13%]">Jenis</th>
                            <th class="p-1 text-left w-[30%]">Keterangan</th>
                            <th class="p-1 text-left w-[16%]">Kategori</th>
                            <th class="p-1 text-left w-[14%]">Rekening</th>
                            <th class="p-1 text-right w-[14%]">Nominal (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($report['transactions'] as $tx)
                            <tr>
                                <td class="p-1 text-slate-600 truncate">{{ $tx->date ? $tx->date->format('d/m/Y') : '-' }}</td>
                                <td class="p-1 uppercase font-bold text-[8.5px] {{ $tx->type === 'income' ? 'text-emerald-700' : ($tx->type === 'expense' ? 'text-rose-700' : 'text-sky-700') }} truncate">
                                    {{ $tx->type === 'income' ? 'Pemasukan' : ($tx->type === 'expense' ? 'Pengeluaran' : 'Transfer') }}
                                </td>
                                <td class="p-1 font-medium text-slate-900 truncate">{{ $tx->description }}</td>
                                <td class="p-1 text-slate-600 truncate">{{ $tx->category->name ?? '-' }}</td>
                                <td class="p-1 text-slate-600 truncate">
                                    @if($tx->type === 'transfer')
                                        {{ $tx->account->name ?? '-' }} → {{ $tx->destinationAccount->name ?? '-' }}
                                    @else
                                        {{ $tx->account->name ?? '-' }}
                                    @endif
                                </td>
                                <td class="p-1 text-right font-bold {{ $tx->type === 'income' ? 'text-emerald-700' : ($tx->type === 'expense' ? 'text-rose-700' : 'text-sky-700') }}">
                                    {{ $tx->type === 'income' ? '+' : ($tx->type === 'expense' ? '-' : '') }} {{ number_format($tx->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-[9.5px] text-slate-500 italic py-1.5">Belum ada mutasi transaksi yang tercatat pada periode ini.</p>
            @endif
        </div>

        <!-- Official Endorsement & Signature Block -->
        <div class="mt-6 pt-3 border-t border-slate-300 grid grid-cols-2 gap-6 text-[10px] page-break-inside-avoid">
            <div>
                <span class="text-slate-500 block mb-0.5">Catatan Auditor / Pemilik:</span>
                <p class="text-slate-600 italic text-[9px] leading-relaxed">
                    Laporan ini dihasilkan secara otomatis oleh sistem manajemen keuangan Flowra. Semua saldo dan kalkulasi telah diverifikasi sesuai jurnal transaksi terdaftar.
                </p>
            </div>

            <div class="text-center">
                <span class="text-slate-500 block mb-10">Dibuat Oleh Pemilik Akun,</span>
                <span class="font-bold text-slate-900 underline block">{{ Auth::user()->name }}</span>
                <span class="text-[9px] text-slate-500">Tanggal: {{ now()->locale('id')->isoFormat('D MMMM Y') }}</span>
            </div>
        </div>
    </div>

    <!-- PRINT STYLING CSS SPECIFIC FOR PRINTING -->
    <style type="text/css">
        @media print {
            @page {
                size: A4 portrait;
                margin: 8mm 8mm 8mm 8mm;
            }
            html, body, div, main, .print-document {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: visible !important;
                box-sizing: border-box !important;
            }
            body {
                background: #ffffff !important;
                color: #0f172a !important;
                font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .flora-sidebar, 
            nav, 
            header, 
            .no-print,
            .bottom-nav,
            aside {
                display: none !important;
                visibility: hidden !important;
            }
            .lg\:ml-64 {
                margin-left: 0 !important;
            }
            main {
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
            .print-document {
                display: block !important;
                font-size: 9.5px !important;
                line-height: 1.3 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
            .print-document table {
                width: 100% !important;
                max-width: 100% !important;
                table-layout: fixed !important;
                border-collapse: collapse !important;
            }
            .print-document td, .print-document th {
                word-wrap: break-word !important;
                overflow-wrap: break-word !important;
            }
            .page-break-inside-avoid {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }
    </style>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Trend Line Chart
            const trendCtx = document.getElementById('reportTrendChart');
            if (trendCtx) {
                const trendData = @json($report['trend_data'] ?? []);
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: trendData.map(d => d.label || ''),
                        datasets: [
                            {
                                label: 'Pemasukan',
                                data: trendData.map(d => parseFloat(d.income) || 0),
                                borderColor: '#6B8E23',
                                backgroundColor: 'rgba(107, 142, 35, 0.15)',
                                tension: 0.35,
                                fill: true
                            },
                            {
                                label: 'Pengeluaran',
                                data: trendData.map(d => parseFloat(d.expense) || 0),
                                borderColor: '#FF6B6B',
                                backgroundColor: 'rgba(255, 107, 107, 0.15)',
                                tension: 0.35,
                                fill: true
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'top' },
                            tooltip: {
                                callbacks: {
                                    label: function(c) {
                                        return c.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(c.parsed.y);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: v => 'Rp ' + new Intl.NumberFormat('id-ID').format(v)
                                }
                            }
                        }
                    }
                });
            }

            // Expense Doughnut Chart
            const expCtx = document.getElementById('reportExpenseChart');
            if (expCtx) {
                const expData = @json($report['expenses_by_category'] ?? []);
                if (expData && expData.length > 0) {
                    new Chart(expCtx, {
                        type: 'doughnut',
                        data: {
                            labels: expData.map(d => d.name),
                            datasets: [{
                                data: expData.map(d => parseFloat(d.total) || 0),
                                backgroundColor: expData.map(d => d.color || '#FF6B6B')
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } },
                                tooltip: {
                                    callbacks: {
                                        label: function(c) {
                                            const total = c.dataset.data.reduce((a, b) => a + b, 0);
                                            const val = c.parsed || 0;
                                            const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                            return c.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(val) + ' (' + pct + '%)';
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
