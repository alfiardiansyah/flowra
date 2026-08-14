<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-heading text-3xl text-sage-600 flex items-center gap-3">
                    <x-icon name="cherry-blossom" class="w-8 h-8 text-sage-400" />
                    Laporan Keuangan & Analisis
                </h2>
                <p class="mt-1 text-earth-600 text-sm">Analisis mendalam arus kas, komposisi pengeluaran, dan pertumbuhan kekayaan</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('reports.export-csv', ['from' => $from, 'to' => $to]) }}" class="btn-flora-secondary text-xs flex items-center gap-1.5" title="Download spreadsheet CSV">
                    <x-icon name="download" class="w-4 h-4" />
                    <span>Export CSV / Excel</span>
                </a>
                <button onclick="window.print()" class="btn-flora-secondary text-xs flex items-center gap-1.5" title="Cetak laporan">
                    <x-icon name="printer" class="w-4 h-4" />
                    <span>Cetak Laporan</span>
                </button>
            </div>
        </div>
    </x-slot>

    <!-- Period Filter Presets & Date Range -->
    <x-card class="mb-8">
        <form method="GET" action="{{ route('reports.index') }}" class="space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('reports.index', ['preset' => 'this_month']) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-semibold transition-all {{ $preset === 'this_month' ? 'bg-sage-600 text-white shadow-sm' : 'bg-sage-50 text-earth-700 hover:bg-sage-100' }}">
                        Bulan Ini
                    </a>
                    <a href="{{ route('reports.index', ['preset' => 'last_month']) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-semibold transition-all {{ $preset === 'last_month' ? 'bg-sage-600 text-white shadow-sm' : 'bg-sage-50 text-earth-700 hover:bg-sage-100' }}">
                        Bulan Lalu
                    </a>
                    <a href="{{ route('reports.index', ['preset' => 'this_year']) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-semibold transition-all {{ $preset === 'this_year' ? 'bg-sage-600 text-white shadow-sm' : 'bg-sage-50 text-earth-700 hover:bg-sage-100' }}">
                        Tahun Ini
                    </a>
                </div>

                <!-- Custom Range Inputs -->
                <div class="flex flex-wrap items-center gap-3">
                    <input type="hidden" name="preset" value="custom">
                    <div class="flex items-center gap-2">
                        <label class="text-xs text-earth-600 font-medium">Dari:</label>
                        <input type="date" name="from" value="{{ $from }}" class="flora-input text-xs py-1.5 px-3">
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-xs text-earth-600 font-medium">Sampai:</label>
                        <input type="date" name="to" value="{{ $to }}" class="flora-input text-xs py-1.5 px-3">
                    </div>
                    <button type="submit" class="btn-flora-primary text-xs py-2 px-4 flex items-center gap-1.5">
                        <x-icon name="flower" class="w-3.5 h-3.5 text-white" />
                        <span>Terapkan</span>
                    </button>
                </div>
            </div>
        </form>
    </x-card>

    <!-- Cash Flow Summary Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        <!-- Saldo Awal -->
        <x-card class="p-4 border-l-4 border-l-sage-400">
            <div class="text-xs text-earth-500 font-medium">Saldo Awal Periode</div>
            <div class="text-xl font-bold text-sage-700 mt-1">
                Rp {{ number_format($report['opening_balance'], 0, ',', '.') }}
            </div>
            <div class="text-[10px] text-earth-400 mt-0.5">Posisi sebelum {{ \Carbon\Carbon::parse($from)->format('d M Y') }}</div>
        </x-card>

        <!-- Total Pemasukan -->
        <x-card class="p-4 border-l-4 border-l-leaf-400">
            <div class="text-xs text-earth-500 font-medium">Total Pemasukan</div>
            <div class="text-xl font-bold text-leaf-600 mt-1">
                Rp {{ number_format($report['total_income'], 0, ',', '.') }}
            </div>
            <div class="text-[10px] text-earth-400 mt-0.5">Selama periode terpilih</div>
        </x-card>

        <!-- Total Pengeluaran -->
        <x-card class="p-4 border-l-4 border-l-coral-400">
            <div class="text-xs text-earth-500 font-medium">Total Pengeluaran</div>
            <div class="text-xl font-bold text-coral-600 mt-1">
                Rp {{ number_format($report['total_expense'], 0, ',', '.') }}
            </div>
            <div class="text-[10px] text-earth-400 mt-0.5">Selama periode terpilih</div>
        </x-card>

        <!-- Arus Kas Bersih -->
        <x-card class="p-4 border-l-4 border-l-sky-400">
            <div class="text-xs text-earth-500 font-medium">Arus Kas Bersih</div>
            <div class="text-xl font-bold {{ $report['net_cash_flow'] >= 0 ? 'text-leaf-600' : 'text-coral-600' }} mt-1">
                {{ $report['net_cash_flow'] >= 0 ? '+' : '' }} Rp {{ number_format($report['net_cash_flow'], 0, ',', '.') }}
            </div>
            <div class="text-[10px] text-earth-400 mt-0.5">Rasio Simpan: {{ $report['savings_rate'] }}%</div>
        </x-card>

        <!-- Saldo Akhir -->
        <x-card class="p-4 border-l-4 border-l-earth-500">
            <div class="text-xs text-earth-500 font-medium">Saldo Akhir Periode</div>
            <div class="text-xl font-bold text-earth-800 mt-1">
                Rp {{ number_format($report['closing_balance'], 0, ',', '.') }}
            </div>
            <div class="text-[10px] text-earth-400 mt-0.5">Saldo Awal + Arus Kas</div>
        </x-card>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Monthly / Daily Trend Line Chart -->
        <x-card variant="chart">
            <h3 class="font-heading text-lg text-sage-700 mb-4 flex items-center gap-2">
                <x-icon name="flower-bloom" class="w-5 h-5 text-sage-500" />
                Tren Arus Kas (Pemasukan vs Pengeluaran)
            </h3>
            <div style="position: relative; height: 280px;">
                <canvas id="reportTrendChart"></canvas>
            </div>
        </x-card>

        <!-- Expense Category Breakdown Doughnut -->
        <x-card variant="chart">
            <h3 class="font-heading text-lg text-sage-700 mb-4 flex items-center gap-2">
                <x-icon name="bouquet" class="w-5 h-5 text-coral-500" />
                Komposisi Pengeluaran per Kategori
            </h3>
            <div style="position: relative; height: 280px;">
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
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Ranked Expenses by Category -->
        <x-card>
            <h3 class="font-heading text-lg text-sage-700 mb-4 pb-2 border-b border-sage-100 flex items-center gap-2">
                <x-icon name="falling-leaves" class="w-5 h-5 text-coral-500" />
                Rincian Pengeluaran per Kategori
            </h3>

            @if(count($report['expenses_by_category']) > 0)
                <div class="space-y-4">
                    @foreach($report['expenses_by_category'] as $item)
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1 font-medium">
                                <div class="flex items-center gap-2">
                                    <x-icon :name="$item['icon']" class="w-4 h-4" />
                                    <span class="font-semibold text-earth-800">{{ $item['name'] }}</span>
                                    <span class="text-[10px] text-earth-500">({{ $item['count'] }} transaksi)</span>
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
        <x-card>
            <h3 class="font-heading text-lg text-sage-700 mb-4 pb-2 border-b border-sage-100 flex items-center gap-2">
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
