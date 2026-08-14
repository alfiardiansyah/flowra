<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-heading text-3xl text-sage-600 flex items-center gap-3">
                    <x-icon name="tree" class="w-8 h-8 text-sage-400 animate-leaf-sway" />
                    Selamat Datang, {{ Auth::user()->name }}!
                </h2>
                <p class="mt-1 text-earth-600 text-sm">Ikhtisar taman keuangan Anda hari ini</p>
            </div>
            <div class="flex items-center gap-3">
                <button @click="$dispatch('open-quick-transaction')" class="btn-flora-primary flex items-center gap-2 text-sm">
                    <x-icon name="add-seed" class="w-4 h-4 text-white" />
                    <span>+ Catat Transaksi</span>
                </button>
                <div class="hidden md:flex items-center gap-2 text-sage-500 bg-white/70 px-3 py-2 rounded-xl border border-sage-200 text-xs font-medium">
                    <x-icon name="flower" class="w-4 h-4" />
                    <span>{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <!-- Summary Cards Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <!-- Total Net Worth / Kekayaan Bersih -->
        <x-card variant="summary" class="relative">
            <div class="absolute top-0 right-0 w-24 h-24 opacity-10">
                <x-icon name="tree" class="w-full h-full text-sage-400" />
            </div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold uppercase tracking-wider text-sage-700">Total Kekayaan Bersih</span>
                    <x-icon name="tree" class="w-6 h-6 text-sage-500" />
                </div>
                <div class="text-2xl font-bold text-sage-700 mb-1">
                    Rp {{ number_format($totalNetWorth ?? 0, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-earth-500 flex flex-wrap items-center gap-x-2 gap-y-0.5 mt-0.5">
                    <span>Saldo: Rp {{ number_format($totalAccountBalance ?? 0, 0, ',', '.') }}</span>
                    @if(($totalReceivable ?? 0) > 0)
                        <span class="text-leaf-600 font-medium">+ Piutang: Rp {{ number_format($totalReceivable, 0, ',', '.') }}</span>
                    @endif
                    @if(($totalDebt ?? 0) > 0)
                        <span class="text-coral-600 font-medium">- Hutang: Rp {{ number_format($totalDebt, 0, ',', '.') }}</span>
                    @endif
                </div>
            </div>
        </x-card>

        <!-- Pemasukan Bulan Ini -->
        <x-card variant="summary" class="relative">
            <div class="absolute top-0 right-0 w-20 h-20 opacity-10">
                <x-icon name="sprout" class="w-full h-full text-leaf-400" />
            </div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold uppercase tracking-wider text-leaf-700">Pemasukan Bulan Ini</span>
                    <x-icon name="sprout" class="w-6 h-6 text-leaf-500 animate-grow" />
                </div>
                <div class="text-2xl font-bold text-leaf-600 mb-1">
                    Rp {{ number_format($thisMonthIncome ?? 0, 0, ',', '.') }}
                </div>
                <p class="text-xs text-earth-500">Periode {{ now()->format('M Y') }}</p>
            </div>
        </x-card>

        <!-- Pengeluaran Bulan Ini -->
        <x-card variant="summary" class="relative">
            <div class="absolute top-0 right-0 w-20 h-20 opacity-10">
                <x-icon name="falling-leaves" class="w-full h-full text-coral-400" />
            </div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold uppercase tracking-wider text-coral-700">Pengeluaran Bulan Ini</span>
                    <x-icon name="falling-leaves" class="w-6 h-6 text-coral-500" />
                </div>
                <div class="text-2xl font-bold text-coral-600 mb-1">
                    Rp {{ number_format($thisMonthExpense ?? 0, 0, ',', '.') }}
                </div>
                <p class="text-xs text-earth-500">
                    @if($expenseDiffPercent > 0)
                        <span class="text-coral-500 font-medium">↑ {{ $expenseDiffPercent }}%</span> dibanding bln lalu
                    @elseif($expenseDiffPercent < 0)
                        <span class="text-leaf-600 font-medium">↓ {{ abs($expenseDiffPercent) }}%</span> dibanding bln lalu
                    @else
                        Sama dgn bulan lalu
                    @endif
                </p>
            </div>
        </x-card>

        <!-- Arus Kas Bersih (Net Cash Flow) -->
        <x-card variant="summary" class="relative">
            <div class="absolute top-0 right-0 w-20 h-20 opacity-10">
                <x-icon name="flower-bloom" class="w-full h-full text-sky-400" />
            </div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold uppercase tracking-wider text-sky-700">Arus Kas Bersih</span>
                    <x-icon name="flower-bloom" class="w-6 h-6 text-sky-500" />
                </div>
                <div class="text-2xl font-bold {{ $netCashFlow >= 0 ? 'text-leaf-600' : 'text-coral-600' }} mb-1">
                    {{ $netCashFlow >= 0 ? '+' : '' }} Rp {{ number_format($netCashFlow ?? 0, 0, ',', '.') }}
                </div>
                <p class="text-xs text-earth-500">Surplus / Defisit bulan ini</p>
            </div>
        </x-card>
    </div>

    <!-- Accounts Quick Grid -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h3 class="font-heading text-lg text-sage-700 flex items-center gap-2">
                    <x-icon name="cash-leaf" class="w-5 h-5 text-sage-500" />
                    Rekening & Dompet Saya
                </h3>
                <p class="text-xs text-earth-500 mt-0.5">Diurutkan berdasarkan saldo terbesar</p>
            </div>
            <a href="{{ route('accounts.index') }}" class="text-xs font-semibold text-sage-600 hover:text-sage-800 hover:underline flex items-center gap-1">
                <span>Kelola Semua Rekening ({{ $accounts->count() }})</span>
                <span>→</span>
            </a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3.5">
            @foreach($accounts->take(4) as $acc)
                <a href="{{ route('accounts.show', $acc) }}" 
                   class="flora-card p-3.5 hover:shadow-flora-lg hover:-translate-y-0.5 transition-all duration-200 block border-l-4"
                   style="border-left-color: {{ $acc->color }}">
                    <div class="flex items-center justify-between mb-1.5">
                        <x-icon :name="$acc->icon" class="w-5 h-5" />
                        <span class="text-[10px] text-earth-400 font-medium uppercase truncate">{{ $acc->type }}</span>
                    </div>
                    <div class="font-semibold text-xs text-earth-800 truncate mb-1" title="{{ $acc->name }}">{{ $acc->name }}</div>
                    <div class="text-xs font-bold text-sage-700">Rp {{ number_format($acc->current_balance, 0, ',', '.') }}</div>
                </a>
            @endforeach

            @if($accounts->count() > 4)
                <a href="{{ route('accounts.index') }}" 
                   class="flora-card p-3.5 border border-sage-200 bg-sage-50/50 hover:bg-sage-100/70 hover:shadow-sm flex flex-col items-center justify-center text-center text-sage-700 transition-all duration-200">
                    <span class="text-xs font-bold">+{{ $accounts->count() - 4 }} Rekening Lainnya</span>
                    <span class="text-[10px] text-earth-500 mt-0.5">Lihat Selengkapnya →</span>
                </a>
            @else
                <a href="{{ route('accounts.create') }}" 
                   class="flora-card p-3.5 border-2 border-dashed border-sage-300 hover:border-sage-400 flex flex-col items-center justify-center text-center text-sage-600 hover:bg-sage-50/50 transition-all duration-200">
                    <x-icon name="plus" class="w-4 h-4 mb-1" />
                    <span class="text-xs font-medium">+ Tambah Rekening</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- 7 Days Trend -->
        <x-card variant="chart" class="lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-heading text-lg text-sage-700 flex items-center gap-2">
                    <x-icon name="flower-bloom" class="w-5 h-5 text-sage-400" />
                    Transaksi 7 Hari Terakhir
                </h3>
                <span class="text-xs text-earth-500">Pemasukan vs Pengeluaran</span>
            </div>
            <div style="position: relative; height: 260px;">
                <canvas id="dashboardTrendChart"></canvas>
            </div>
        </x-card>

        <!-- Category Doughnut -->
        <x-card variant="chart">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-heading text-lg text-sage-700 flex items-center gap-2">
                    <x-icon name="bouquet" class="w-5 h-5 text-sage-400" />
                    Pengeluaran Bulan Ini
                </h3>
            </div>
            <div style="position: relative; height: 260px;">
                @if($expenseByCategory->count() > 0)
                    <canvas id="dashboardCategoryChart"></canvas>
                @else
                    <div class="h-full flex flex-col items-center justify-center text-center p-4">
                        <x-icon name="sprout" class="w-12 h-12 text-sage-300 mb-2" />
                        <p class="text-xs text-earth-500">Belum ada data pengeluaran bulan ini</p>
                    </div>
                @endif
            </div>
        </x-card>
    </div>

    <!-- Budget & Obligations Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Budget Progress Section -->
        <x-card class="lg:col-span-2">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-sage-100">
                <div>
                    <h3 class="font-heading text-lg text-sage-700 flex items-center gap-2">
                        <x-icon name="sprout" class="w-5 h-5 text-sage-500" />
                        Anggaran Bulan Ini
                    </h3>
                    <p class="text-xs text-earth-500 mt-0.5">
                        Terpakai: Rp {{ number_format($budgetSummary['total_spent'], 0, ',', '.') }} dari Rp {{ number_format($budgetSummary['total_budget'], 0, ',', '.') }} ({{ $budgetSummary['total_percentage'] }}%)
                    </p>
                </div>
                <a href="{{ route('budgets.index') }}" class="text-xs font-medium text-sage-600 hover:text-sage-700 hover:underline">
                    Atur Anggaran →
                </a>
            </div>

            @if(!empty($budgetSummary['items']) && count($budgetSummary['items']) > 0)
                <div class="space-y-4">
                    @foreach(collect($budgetSummary['items'])->take(4) as $item)
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <div class="font-medium text-earth-800 flex items-center gap-2">
                                    <x-icon :name="$item['category']->icon ?? 'flower'" class="w-4 h-4" />
                                    <span>{{ $item['category']->name ?? 'Kategori' }}</span>
                                    @if($item['is_over'])
                                        <span class="flora-badge flora-badge-danger text-[10px] py-0 px-1.5">Over Budget</span>
                                    @endif
                                </div>
                                <div class="text-earth-600">
                                    <span class="font-semibold text-earth-800">Rp {{ number_format($item['spent'], 0, ',', '.') }}</span> / Rp {{ number_format($item['amount'], 0, ',', '.') }}
                                    <span class="text-xs font-bold" style="color: {{ $item['status_color'] }}">({{ $item['percentage'] }}%)</span>
                                </div>
                            </div>
                            <!-- Vine Progress Bar -->
                            <div class="w-full bg-sage-100 rounded-full h-2.5 overflow-hidden">
                                <div class="h-2.5 rounded-full transition-all duration-500" 
                                     style="width: {{ min(100, $item['percentage']) }}%; background-color: {{ $item['status_color'] }};"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-6 text-center">
                    <p class="text-sm text-earth-600 mb-3">Anda belum menetapkan anggaran untuk bulan ini.</p>
                    <a href="{{ route('budgets.create') }}" class="btn-flora-secondary text-xs inline-flex items-center gap-1.5">
                        <x-icon name="sprout" class="w-4 h-4" />
                        <span>Mulai Buat Anggaran</span>
                    </a>
                </div>
            @endif
        </x-card>

        <!-- Upcoming Bills & Pending Debts -->
        <div class="space-y-6">
            <!-- Upcoming Recurring Bills -->
            <x-card>
                <div class="flex items-center justify-between mb-3 pb-2 border-b border-sage-100">
                    <h4 class="font-heading text-sm text-sage-700 flex items-center gap-1.5">
                        <x-icon name="leaf-wind" class="w-4 h-4 text-sage-500" />
                        <span>Tagihan & Rutin Mendatang</span>
                    </h4>
                    <a href="{{ route('recurring.index') }}" class="text-[11px] text-sage-600 hover:underline">Semua</a>
                </div>

                @if($upcomingRecurring->count() > 0)
                    <div class="space-y-2.5">
                        @foreach($upcomingRecurring->take(3) as $rec)
                            <div class="flex items-center justify-between p-2 rounded-lg bg-sage-50/70 text-xs">
                                <div>
                                    <div class="font-medium text-earth-800">{{ $rec->description }}</div>
                                    <div class="text-[10px] text-earth-500">{{ $rec->next_run_date->format('d M Y') }} • {{ $rec->account->name ?? '' }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold {{ $rec->type === 'income' ? 'text-leaf-600' : 'text-coral-600' }}">
                                        {{ $rec->type === 'income' ? '+' : '-' }} Rp {{ number_format($rec->amount, 0, ',', '.') }}
                                    </div>
                                    <form action="{{ route('recurring.post-now', $rec) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-[10px] text-sage-600 hover:text-sage-800 font-semibold hover:underline">
                                            Catat Sekarang
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-earth-500 py-3 text-center">Tidak ada tagihan mendesak dalam 14 hari ke depan.</p>
                @endif
            </x-card>

            <!-- Pending Debts / Receivables -->
            <x-card>
                <div class="flex items-center justify-between mb-3 pb-2 border-b border-sage-100">
                    <h4 class="font-heading text-sm text-sage-700 flex items-center gap-1.5">
                        <x-icon name="leaf" class="w-4 h-4 text-sage-500" />
                        <span>Hutang & Piutang</span>
                    </h4>
                    <a href="{{ route('debts.index') }}" class="text-[11px] text-sage-600 hover:underline">Semua</a>
                </div>

                @if($pendingDebts->count() > 0)
                    <div class="space-y-2.5">
                        @foreach($pendingDebts->take(3) as $debt)
                            <div class="flex items-center justify-between p-2 rounded-lg bg-sage-50/70 text-xs">
                                <div>
                                    <span class="flora-badge {{ $debt->type === 'debt' ? 'flora-badge-danger' : 'flora-badge-success' }} text-[9px] py-0 px-1 mb-0.5">
                                        {{ $debt->type === 'debt' ? 'Hutang' : 'Piutang' }}
                                    </span>
                                    <div class="font-medium text-earth-800">{{ $debt->person_name }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-earth-800">
                                        Sisa: Rp {{ number_format($debt->remaining_amount, 0, ',', '.') }}
                                    </div>
                                    <div class="text-[10px] text-earth-500">
                                        {{ $debt->due_date ? 'Jatuh tempo ' . $debt->due_date->format('d M') : 'Tanpa batas' }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-earth-500 py-3 text-center">Semua catatan hutang/piutang lunas atau belum ada.</p>
                @endif
            </x-card>
        </div>
    </div>

    <!-- Recent Transactions Section -->
    <x-card>
        <div class="flex items-center justify-between mb-6 pb-3 border-b border-sage-100">
            <div>
                <h3 class="font-heading text-xl text-sage-700 flex items-center gap-2">
                    <x-icon name="flower" class="w-6 h-6 text-sage-400" />
                    Transaksi Terbaru
                </h3>
                <p class="text-xs text-earth-500 mt-0.5">Aktivitas keuangan terakhir di kebun Anda</p>
            </div>
            <div class="flex items-center gap-3">
                <button @click="$dispatch('open-quick-transaction')" class="btn-flora-secondary text-xs">
                    + Tambah
                </button>
                <a href="{{ route('transactions.index') }}" class="text-xs font-medium text-sage-600 hover:text-sage-700 hover:underline">
                    Lihat Semua Transaksi →
                </a>
            </div>
        </div>

        @if($recent && $recent->count() > 0)
            <div class="space-y-3">
                @foreach($recent as $transaction)
                    <div class="transaction-card {{ $transaction->type }} p-4 rounded-xl flex items-center justify-between hover:bg-sage-50/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 {{ $transaction->type === 'income' ? 'bg-mint-100 text-leaf-600' : ($transaction->type === 'expense' ? 'bg-coral-100 text-coral-600' : 'bg-sky-100 text-sky-600') }}">
                                @if($transaction->type === 'income')
                                    <x-icon :name="$transaction->category->icon ?? 'sprout'" class="w-5 h-5 text-leaf-600" />
                                @elseif($transaction->type === 'expense')
                                    <x-icon :name="$transaction->category->icon ?? 'falling-leaves'" class="w-5 h-5 text-coral-600" />
                                @else
                                    <x-icon name="transfer" class="w-5 h-5 text-sky-600" />
                                @endif
                            </div>
                            <div>
                                <div class="font-semibold text-sm text-earth-800">{{ $transaction->description }}</div>
                                <div class="text-xs text-earth-500 flex items-center gap-2 mt-0.5">
                                    <span>{{ $transaction->category->name ?? ($transaction->type === 'transfer' ? 'Transfer Antar Rekening' : 'Umum') }}</span>
                                    <span>•</span>
                                    <span>{{ $transaction->date ? $transaction->date->format('d M Y') : '-' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="text-right">
                            <div class="font-bold text-sm {{ $transaction->type === 'income' ? 'text-leaf-600' : ($transaction->type === 'expense' ? 'text-coral-600' : 'text-sky-600') }}">
                                {{ $transaction->formatted_amount }}
                            </div>
                            <div class="text-[11px] text-earth-500 mt-0.5">
                                @if($transaction->type === 'transfer')
                                    {{ $transaction->account->name ?? '' }} → {{ $transaction->destinationAccount->name ?? '' }}
                                @else
                                    {{ $transaction->account->name ?? '' }}
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <x-empty-state 
                title="Taman Keuangan Masih Kosong" 
                description="Mulai menanam benih keuangan pertama Anda untuk memantau pemasukan dan pengeluaran secara teratur."
                action="javascript:void(0)"
                @click="$dispatch('open-quick-transaction')"
                action-label="+ Catat Transaksi Pertama" />
        @endif
    </x-card>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Trend Chart
            const trendCtx = document.getElementById('dashboardTrendChart');
            if (trendCtx) {
                const rawTrend = @json($last7Days ?? []);
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: rawTrend.map(d => d.label || ''),
                        datasets: [
                            {
                                label: 'Pemasukan',
                                data: rawTrend.map(d => parseFloat(d.income) || 0),
                                borderColor: '#6B8E23',
                                backgroundColor: 'rgba(107, 142, 35, 0.12)',
                                tension: 0.35,
                                fill: true
                            },
                            {
                                label: 'Pengeluaran',
                                data: rawTrend.map(d => parseFloat(d.expense) || 0),
                                borderColor: '#FF6B6B',
                                backgroundColor: 'rgba(255, 107, 107, 0.12)',
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

            // Category Doughnut Chart
            const catCtx = document.getElementById('dashboardCategoryChart');
            if (catCtx) {
                const rawCats = @json($expenseByCategory ?? []);
                if (rawCats && rawCats.length > 0) {
                    new Chart(catCtx, {
                        type: 'doughnut',
                        data: {
                            labels: rawCats.map(d => d.kategori || 'Lainnya'),
                            datasets: [{
                                data: rawCats.map(d => parseFloat(d.total) || 0),
                                backgroundColor: rawCats.map(d => d.color || '#FF6B6B')
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
