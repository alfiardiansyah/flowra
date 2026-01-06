<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-heading text-3xl text-sage-600 flex items-center gap-3">
                    <x-icon name="tree" class="w-8 h-8 text-sage-400 animate-leaf-sway" />
                    Welcome back, {{ Auth::user()->name }}!
                </h2>
                <p class="mt-1 text-earth-600">Here's your financial garden overview</p>
            </div>
            <div class="hidden md:flex items-center gap-2 text-sage-500">
                <x-icon name="flower" class="w-5 h-5" />
                <span class="text-sm">{{ now()->format('l, F d, Y') }}</span>
            </div>
        </div>
    </x-slot>

    @if(session('success'))
        <x-alert type="success" dismissible class="mb-6">
            {{ session('success') }}
        </x-alert>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Saldo -->
        <x-card variant="summary" class="md:col-span-2 lg:col-span-1 relative">
            <div class="absolute top-0 right-0 w-24 h-24 opacity-10">
                <x-icon name="tree" class="w-full h-full text-sage-400" />
            </div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-heading text-lg text-sage-700">Total Saldo</h3>
                    <x-icon name="tree" class="w-8 h-8 text-sage-400" />
                </div>
                <div class="text-2xl font-bold text-sage-600 mb-2" x-data="{ count: 0, target: {{ $totalSaldo ?? 0 }} }" 
                     x-init="() => { let duration = 2000; let steps = 60; let increment = target / steps; let timer = setInterval(() => { count += increment; if (count >= target) { count = target; clearInterval(timer); } }, duration / steps); }">
                    Rp <span x-text="new Intl.NumberFormat('id-ID').format(Math.floor(count))"></span>
                </div>
                <p class="text-sm text-earth-600">Your financial foundation</p>
            </div>
        </x-card>

        <!-- Pemasukan Bulan Ini -->
        <x-card variant="summary" class="relative">
            <div class="absolute top-0 right-0 w-20 h-20 opacity-10">
                <x-icon name="sprout" class="w-full h-full text-leaf-400" />
            </div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-heading text-base text-leaf-700">Pemasukan</h3>
                    <x-icon name="sprout" class="w-6 h-6 text-leaf-400 animate-grow" />
                </div>
                <div class="text-2xl font-bold text-leaf-600 mb-2">
                    Rp {{ number_format($thisMonthIncome ?? 0, 0, ',', '.') }}
                </div>
                <p class="text-xs text-earth-600">Bulan ini</p>
            </div>
        </x-card>

        <!-- Pengeluaran Bulan Ini -->
        <x-card variant="summary" class="relative">
            <div class="absolute top-0 right-0 w-20 h-20 opacity-10">
                <x-icon name="falling-leaves" class="w-full h-full text-coral-400" />
            </div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-heading text-base text-coral-700">Pengeluaran</h3>
                    <x-icon name="falling-leaves" class="w-6 h-6 text-coral-400" />
                </div>
                <div class="text-2xl font-bold text-coral-600 mb-2">
                    Rp {{ number_format($thisMonthExpense ?? 0, 0, ',', '.') }}
                </div>
                <p class="text-xs text-earth-600">Bulan ini</p>
            </div>
        </x-card>

        <!-- Total Transaksi -->
        <x-card variant="summary" class="relative">
            <div class="absolute top-0 right-0 w-20 h-20 opacity-10">
                <x-icon name="flower-bloom" class="w-full h-full text-sky-400" />
            </div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-heading text-base text-sky-700">Transaksi</h3>
                    <x-icon name="flower-bloom" class="w-6 h-6 text-sky-400" />
                </div>
                <div class="text-2xl font-bold text-sky-600 mb-2">
                    {{ ($recent->count() ?? 0) + ($incomeByCategory->count() ?? 0) + ($expenseByCategory->count() ?? 0) }}
                </div>
                <p class="text-xs text-earth-600">Total kategori</p>
            </div>
        </x-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Chart: Last 7 Days -->
        <x-card variant="chart" class="lg:col-span-2">
            <h3 class="font-heading text-xl text-sage-700 mb-6 flex items-center gap-2">
                <x-icon name="flower-bloom" class="w-6 h-6 text-sage-400" />
                Transaksi 7 Hari Terakhir
            </h3>
            <div style="position: relative; height: 300px;">
                <canvas id="transactionChart"></canvas>
            </div>
        </x-card>

        <!-- Category Pie Chart -->
        <x-card variant="chart">
            <h3 class="font-heading text-xl text-sage-700 mb-6 flex items-center gap-2">
                <x-icon name="bouquet" class="w-6 h-6 text-sage-400" />
                Pengeluaran per Kategori
            </h3>
            <div style="position: relative; height: 300px;">
                <canvas id="categoryChart"></canvas>
            </div>
        </x-card>
    </div>

    <!-- Recent Transactions -->
    <x-card>
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-heading text-xl text-sage-700 flex items-center gap-2">
                <x-icon name="flower" class="w-6 h-6 text-sage-400" />
                Transaksi Terbaru
            </h3>
            <div class="flex gap-2">
                <a href="{{ route('incomes.create') }}" class="btn-flora-secondary text-sm">
                    <x-icon name="add-seed" class="w-4 h-4" />
                    Tambah Pemasukan
                </a>
                <a href="{{ route('expenses.create') }}" class="btn-flora-secondary text-sm">
                    <x-icon name="add-seed" class="w-4 h-4" />
                    Tambah Pengeluaran
                </a>
            </div>
        </div>

        @if($recent && $recent->count() > 0)
            <div class="space-y-3">
                @foreach($recent as $transaction)
                    @if($transaction instanceof \App\Models\Income)
                        <div class="transaction-card income p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-full bg-leaf-100 flex items-center justify-center">
                                        <x-icon name="sprout" class="w-6 h-6 text-leaf-600" />
                                    </div>
                                    <div>
                                        <div class="font-semibold text-earth-800">{{ $transaction->keterangan ?? 'Pemasukan' }}</div>
                                        <div class="text-sm text-earth-600">{{ $transaction->kategori ?? 'Lainnya' }} • {{ $transaction->tanggal ? $transaction->tanggal->format('d M Y') : '-' }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-leaf-600">+ Rp {{ number_format($transaction->nominal, 0, ',', '.') }}</div>
                                    <div class="text-xs text-earth-500">{{ $transaction->nama_bank ?? '' }}</div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="transaction-card expense p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-full bg-coral-100 flex items-center justify-center">
                                        <x-icon name="falling-leaves" class="w-6 h-6 text-coral-600" />
                                    </div>
                                    <div>
                                        <div class="font-semibold text-earth-800">{{ $transaction->keterangan ?? 'Pengeluaran' }}</div>
                                        <div class="text-sm text-earth-600">{{ $transaction->kategori ?? 'Lainnya' }} • {{ $transaction->tanggal ? $transaction->tanggal->format('d M Y') : '-' }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-coral-600">- Rp {{ number_format($transaction->nominal, 0, ',', '.') }}</div>
                                    <div class="text-xs text-earth-500">{{ $transaction->metode_pembayaran ?? '' }}</div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            <x-empty-state 
                title="Belum ada transaksi" 
                description="Mulai menanam benih keuangan pertama Anda untuk melihat transaksi di sini."
                :action="route('incomes.create')"
                action-label="Tambah Transaksi Pertama" />
        @endif
    </x-card>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Transaction Chart (Last 7 Days)
            const transactionCtx = document.getElementById('transactionChart');
            if (transactionCtx) {
                const transactionData = @json($last7Days ?? []);
                
                // Ensure we have data, even if empty
                const labels = transactionData && transactionData.length > 0 
                    ? transactionData.map(d => d.label || '') 
                    : [];
                const incomeData = transactionData && transactionData.length > 0 
                    ? transactionData.map(d => parseFloat(d.income) || 0) 
                    : [];
                const expenseData = transactionData && transactionData.length > 0 
                    ? transactionData.map(d => parseFloat(d.expense) || 0) 
                    : [];
                
                new Chart(transactionCtx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Pemasukan',
                            data: incomeData,
                            borderColor: '#6B8E23',
                            backgroundColor: 'rgba(107, 142, 35, 0.1)',
                            tension: 0.4,
                            fill: true
                        }, {
                            label: 'Pengeluaran',
                            data: expenseData,
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
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': Rp ' + 
                                            new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Category Pie Chart
            const categoryCtx = document.getElementById('categoryChart');
            if (categoryCtx) {
                const categoryData = @json($expenseByCategory ?? []);
                
                // Ensure we have data
                const categoryLabels = categoryData && categoryData.length > 0 
                    ? categoryData.map(d => d.kategori || 'Lainnya') 
                    : [];
                const categoryTotals = categoryData && categoryData.length > 0 
                    ? categoryData.map(d => parseFloat(d.total) || 0) 
                    : [];
                
                // Generate colors dynamically if we have more categories than predefined colors
                const baseColors = [
                    '#87A96B',
                    '#A7D7C5',
                    '#6B8E23',
                    '#FF6B6B',
                    '#FFD700',
                    '#87CEEB',
                    '#E6E6FA',
                    '#DDA0DD',
                    '#98D8C8',
                    '#F7DC6F'
                ];
                const backgroundColor = categoryLabels.map((_, index) => 
                    baseColors[index % baseColors.length]
                );
                
                new Chart(categoryCtx, {
                    type: 'doughnut',
                    data: {
                        labels: categoryLabels,
                        datasets: [{
                            data: categoryTotals,
                            backgroundColor: backgroundColor
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return label + ': Rp ' + 
                                            new Intl.NumberFormat('id-ID').format(value) + 
                                            ' (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
