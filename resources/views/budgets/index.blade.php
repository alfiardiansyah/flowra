<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-heading text-3xl text-sage-600 flex items-center gap-3">
                    <x-icon name="sprout" class="w-8 h-8 text-leaf-400 animate-grow" />
                    Anggaran Bulanan
                </h2>
                <p class="mt-1 text-earth-600 text-sm">Rencanakan batas pengeluaran per kategori untuk menjaga kesehatan finansial</p>
            </div>

            <!-- Month Selector -->
            <div class="flex items-center gap-2">
                @php
                    $currentCarbon = \Carbon\Carbon::parse($month . '-01');
                    $prevMonth = $currentCarbon->copy()->subMonth()->format('Y-m');
                    $nextMonth = $currentCarbon->copy()->addMonth()->format('Y-m');
                @endphp
                <a href="{{ route('budgets.index', ['month' => $prevMonth]) }}" class="p-2 rounded-xl bg-white border border-sage-200 text-earth-600 hover:bg-sage-50 text-xs font-semibold">
                    ← {{ $currentCarbon->copy()->subMonth()->locale('id')->isoFormat('MMM') }}
                </a>
                <span class="px-4 py-2 bg-sage-100 text-sage-800 rounded-xl text-xs font-bold font-heading">
                    {{ $currentCarbon->locale('id')->isoFormat('MMMM Y') }}
                </span>
                <a href="{{ route('budgets.index', ['month' => $nextMonth]) }}" class="p-2 rounded-xl bg-white border border-sage-200 text-earth-600 hover:bg-sage-50 text-xs font-semibold">
                    {{ $currentCarbon->copy()->addMonth()->locale('id')->isoFormat('MMM') }} →
                </a>

                <a href="{{ route('budgets.create', ['month' => $month]) }}" class="btn-flora-primary text-xs flex items-center gap-1.5 ml-2">
                    <x-icon name="add-seed" class="w-4 h-4 text-white" />
                    <span>+ Buat Anggaran</span>
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Overall Budget Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
        <x-card variant="summary">
            <div class="text-xs text-earth-500 font-medium">Total Anggaran (Bulan Ini)</div>
            <div class="text-2xl font-bold text-sage-700 mt-1">
                Rp {{ number_format($budgetData['total_budget'], 0, ',', '.') }}
            </div>
            <p class="text-xs text-earth-500 mt-1">Batas maksimal belanja yang direncanakan</p>
        </x-card>

        <x-card variant="summary">
            <div class="text-xs text-earth-500 font-medium">Realisasi Belanja</div>
            <div class="text-2xl font-bold {{ $budgetData['is_over_budget'] ? 'text-coral-600' : 'text-earth-800' }} mt-1">
                Rp {{ number_format($budgetData['total_spent'], 0, ',', '.') }}
            </div>
            <div class="text-xs mt-1 font-semibold" style="color: {{ $budgetData['is_over_budget'] ? '#FF6B6B' : ($budgetData['total_percentage'] >= 80 ? '#ccac00' : '#6b8e4f') }}">
                Terpakai {{ $budgetData['total_percentage'] }}%
            </div>
        </x-card>

        <x-card variant="summary">
            <div class="text-xs text-earth-500 font-medium">Sisa Anggaran Aman</div>
            <div class="text-2xl font-bold {{ $budgetData['total_remaining'] > 0 ? 'text-leaf-600' : 'text-coral-600' }} mt-1">
                Rp {{ number_format($budgetData['total_remaining'], 0, ',', '.') }}
            </div>
            <p class="text-xs text-earth-500 mt-1">Sisa dana belanja yang dapat digunakan</p>
        </x-card>
    </div>

    <!-- Copy from previous month option -->
    @if(count($budgetData['items']) === 0 && $hasPrevBudgets)
        <div class="flora-card bg-mint-50/80 border border-mint-200 p-4 rounded-2xl mb-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <x-icon name="sprout" class="w-8 h-8 text-leaf-600" />
                <div>
                    <h4 class="font-semibold text-sm text-earth-800">Salin Anggaran Bulan Lalu?</h4>
                    <p class="text-xs text-earth-600">Anda dapat menduplikasi target anggaran dari bulan {{ \Carbon\Carbon::parse($prevMonth . '-01')->locale('id')->isoFormat('MMMM Y') }} secara otomatis.</p>
                </div>
            </div>
            <form action="{{ route('budgets.copy-previous') }}" method="POST">
                @csrf
                <input type="hidden" name="month" value="{{ $month }}">
                <button type="submit" class="btn-flora-primary text-xs py-2 px-4 whitespace-nowrap">
                    Salin Anggaran Bulan Lalu
                </button>
            </form>
        </div>
    @endif

    <!-- Category Budgets Grid -->
    @if(!empty($budgetData['items']) && count($budgetData['items']) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($budgetData['items'] as $item)
                @php $budget = $item['budget']; @endphp
                <div class="flora-card p-6 border-l-4 transition-all hover:shadow-flora-lg"
                     style="border-left-color: {{ $item['status_color'] }}">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-sage-100">
                                <x-icon :name="$item['category']->icon ?? 'flower'" class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="font-heading text-base font-semibold text-earth-800">{{ $item['category']->name ?? 'Kategori' }}</h3>
                                @if($item['is_over'])
                                    <span class="flora-badge flora-badge-danger text-[10px] py-0.5 px-2">Melebihi Anggaran!</span>
                                @elseif($item['percentage'] >= 80)
                                    <span class="flora-badge flora-badge-info text-[10px] py-0.5 px-2">Hampir Habis ({{ $item['percentage'] }}%)</span>
                                @else
                                    <span class="flora-badge flora-badge-success text-[10px] py-0.5 px-2">Terkendali</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-1">
                            <a href="{{ route('budgets.edit', $budget) }}" class="p-1.5 text-sage-600 hover:text-sage-800 rounded-lg hover:bg-sage-50" title="Edit Anggaran">
                                <x-icon name="edit-leaf" class="w-4 h-4" />
                            </a>
                            <form action="{{ route('budgets.destroy', $budget) }}" method="POST" onsubmit="return confirm('Hapus anggaran kategori ini?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-coral-500 hover:text-coral-700 rounded-lg hover:bg-coral-50" title="Hapus">
                                    <x-icon name="delete-wilt" class="w-4 h-4" />
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="my-4">
                        <div class="flex items-center justify-between text-xs mb-1.5 font-medium">
                            <span class="text-earth-600">
                                Terpakai: <span class="font-bold text-earth-800">Rp {{ number_format($item['spent'], 0, ',', '.') }}</span>
                            </span>
                            <span class="font-bold" style="color: {{ $item['status_color'] }}">{{ $item['percentage'] }}%</span>
                        </div>
                        <div class="w-full bg-sage-100 rounded-full h-3 overflow-hidden">
                            <div class="h-3 rounded-full transition-all duration-500"
                                 style="width: {{ min(100, $item['percentage']) }}%; background-color: {{ $item['status_color'] }};"></div>
                        </div>
                    </div>

                    <!-- Numbers Footer -->
                    <div class="pt-3 border-t border-sage-100 flex items-center justify-between text-xs">
                        <div class="text-earth-500">
                            Batas: <span class="font-semibold text-earth-700">Rp {{ number_format($item['amount'], 0, ',', '.') }}</span>
                        </div>
                        <div class="font-semibold {{ $item['remaining'] > 0 ? 'text-leaf-600' : 'text-coral-600' }}">
                            Sisa: Rp {{ number_format($item['remaining'], 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <x-empty-state 
            title="Belum Ada Anggaran Dibuat" 
            description="Tentukan batas anggaran pengeluaran untuk bulan ini agar pengeluaran Anda terkontrol dengan baik."
            :action="route('budgets.create', ['month' => $month])"
            action-label="+ Buat Anggaran Pertama" />
    @endif
</x-app-layout>
