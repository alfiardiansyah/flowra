<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-heading text-3xl text-sage-600 flex items-center gap-3">
                    <x-icon name="flower-bloom" class="w-8 h-8 text-sage-400" />
                    Your Financial Garden Report
                </h2>
                <p class="mt-1 text-earth-600">Analyze your financial growth</p>
            </div>
        </div>
    </x-slot>

    <!-- Date Range Filter -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="min-w-[150px]">
                <label class="block text-sm font-medium text-earth-700 mb-2">Dari Tanggal</label>
                <input type="date" name="from" value="{{ $from }}" class="flora-input">
            </div>
            <div class="min-w-[150px]">
                <label class="block text-sm font-medium text-earth-700 mb-2">Sampai Tanggal</label>
                <input type="date" name="to" value="{{ $to }}" class="flora-input">
            </div>
            <button type="submit" class="btn-flora-primary">
                <x-icon name="flower" class="w-4 h-4" />
                Filter
            </button>
            <a href="{{ route('reports.index') }}" class="btn-flora-secondary">
                Reset
            </a>
        </form>
    </x-card>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <x-card variant="summary" class="relative">
            <div class="absolute top-0 right-0 w-20 h-20 opacity-10">
                <x-icon name="sprout" class="w-full h-full text-leaf-400" />
            </div>
            <div class="relative z-10">
                <h3 class="font-heading text-base text-leaf-700 mb-2">Total Pemasukan</h3>
                <div class="text-3xl font-bold text-leaf-600 mb-1">
                    Rp {{ number_format($totalIncome ?? 0, 0, ',', '.') }}
                </div>
                <p class="text-xs text-earth-600">Periode terpilih</p>
            </div>
        </x-card>

        <x-card variant="summary" class="relative">
            <div class="absolute top-0 right-0 w-20 h-20 opacity-10">
                <x-icon name="falling-leaves" class="w-full h-full text-coral-400" />
            </div>
            <div class="relative z-10">
                <h3 class="font-heading text-base text-coral-700 mb-2">Total Pengeluaran</h3>
                <div class="text-3xl font-bold text-coral-600 mb-1">
                    Rp {{ number_format($totalExpense ?? 0, 0, ',', '.') }}
                </div>
                <p class="text-xs text-earth-600">Periode terpilih</p>
            </div>
        </x-card>

        <x-card variant="summary" class="relative">
            <div class="absolute top-0 right-0 w-20 h-20 opacity-10">
                <x-icon name="tree" class="w-full h-full text-sage-400" />
            </div>
            <div class="relative z-10">
                <h3 class="font-heading text-base text-sage-700 mb-2">Saldo</h3>
                <div class="text-3xl font-bold {{ ($balance ?? 0) >= 0 ? 'text-leaf-600' : 'text-coral-600' }} mb-1">
                    Rp {{ number_format($balance ?? 0, 0, ',', '.') }}
                </div>
                <p class="text-xs text-earth-600">Balance</p>
            </div>
        </x-card>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Monthly Trend -->
        <x-card variant="chart">
            <h3 class="font-heading text-xl text-sage-700 mb-6 flex items-center gap-2">
                <x-icon name="flower-bloom" class="w-6 h-6 text-sage-400" />
                Trend 6 Bulan Terakhir
            </h3>
            <canvas id="monthlyTrendChart" class="max-h-80"></canvas>
        </x-card>

        <!-- Income by Category -->
        <x-card variant="chart">
            <h3 class="font-heading text-xl text-sage-700 mb-6 flex items-center gap-2">
                <x-icon name="bouquet" class="w-6 h-6 text-leaf-400" />
                Pemasukan per Kategori
            </h3>
            <canvas id="incomeCategoryChart" class="max-h-80"></canvas>
        </x-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Expense by Category -->
        <x-card variant="chart">
            <h3 class="font-heading text-xl text-sage-700 mb-6 flex items-center gap-2">
                <x-icon name="bouquet" class="w-6 h-6 text-coral-400" />
                Pengeluaran per Kategori
            </h3>
            <canvas id="expenseCategoryChart" class="max-h-80"></canvas>
        </x-card>

        <!-- Comparison Chart -->
        <x-card variant="chart">
            <h3 class="font-heading text-xl text-sage-700 mb-6 flex items-center gap-2">
                <x-icon name="tree" class="w-6 h-6 text-sage-400" />
                Perbandingan Pemasukan vs Pengeluaran
            </h3>
            <canvas id="comparisonChart" class="max-h-80"></canvas>
        </x-card>
    </div>

    <!-- Export Buttons -->
    <x-card>
        <h3 class="font-heading text-xl text-sage-700 mb-4 flex items-center gap-2">
            <x-icon name="flower" class="w-6 h-6 text-sage-400" />
            Export Laporan
        </h3>
        <div class="flex flex-wrap gap-4">
            <button onclick="window.print()" class="btn-flora-secondary">
                <x-icon name="flower" class="w-4 h-4" />
                Print Laporan
            </button>
            <button onclick="exportToPDF()" class="btn-flora-secondary">
                <x-icon name="flower" class="w-4 h-4" />
                Export PDF
            </button>
            <button onclick="exportToExcel()" class="btn-flora-secondary">
                <x-icon name="flower" class="w-4 h-4" />
                Export Excel
            </button>
        </div>
    </x-card>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Monthly Trend Chart
        const monthlyCtx = document.getElementById('monthlyTrendChart');
        if (monthlyCtx) {
            const monthlyData = @json($monthlyData);
            new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: monthlyData.map(d => d.month),
                    datasets: [{
                        label: 'Pemasukan',
                        data: monthlyData.map(d => d.income),
                        borderColor: '#6B8E23',
                        backgroundColor: 'rgba(107, 142, 35, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Pengeluaran',
                        data: monthlyData.map(d => d.expense),
                        borderColor: '#FF6B6B',
                        backgroundColor: 'rgba(255, 107, 107, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Income Category Chart
        const incomeCategoryCtx = document.getElementById('incomeCategoryChart');
        if (incomeCategoryCtx) {
            const incomeData = @json($incomeByCategory);
            new Chart(incomeCategoryCtx, {
                type: 'doughnut',
                data: {
                    labels: incomeData.map(d => d.kategori || 'Lainnya'),
                    datasets: [{
                        data: incomeData.map(d => d.total),
                        backgroundColor: [
                            '#87A96B',
                            '#A7D7C5',
                            '#6B8E23',
                            '#FFD700',
                            '#87CEEB'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        }

        // Expense Category Chart
        const expenseCategoryCtx = document.getElementById('expenseCategoryChart');
        if (expenseCategoryCtx) {
            const expenseData = @json($expenseByCategory);
            new Chart(expenseCategoryCtx, {
                type: 'doughnut',
                data: {
                    labels: expenseData.map(d => d.kategori || 'Lainnya'),
                    datasets: [{
                        data: expenseData.map(d => d.total),
                        backgroundColor: [
                            '#FF6B6B',
                            '#87CEEB',
                            '#87A96B',
                            '#8B7355',
                            '#E6E6FA',
                            '#A7D7C5',
                            '#6B8E23',
                            '#FFD700'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        }

        // Comparison Chart
        const comparisonCtx = document.getElementById('comparisonChart');
        if (comparisonCtx) {
            new Chart(comparisonCtx, {
                type: 'bar',
                data: {
                    labels: ['Pemasukan', 'Pengeluaran'],
                    datasets: [{
                        label: 'Jumlah',
                        data: [{{ $totalIncome ?? 0 }}, {{ $totalExpense ?? 0 }}],
                        backgroundColor: ['#6B8E23', '#FF6B6B']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        function exportToPDF() {
            alert('Fitur export PDF akan segera tersedia!');
        }

        function exportToExcel() {
            alert('Fitur export Excel akan segera tersedia!');
        }
    </script>
    @endpush
</x-app-layout>
