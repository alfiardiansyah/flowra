<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-heading text-3xl text-sage-600 flex items-center gap-3">
                    <x-icon name="cash-leaf" class="w-8 h-8 text-sage-400" />
                    Rekening & Dompet
                </h2>
                <p class="mt-1 text-earth-600 text-sm">Kelola semua sumber dana keuangan Anda (Bank, E-Wallet, Tunai, Tabungan)</p>
            </div>
            <div class="flex items-center gap-2">
                <button @click="$dispatch('open-quick-transaction', { type: 'transfer' })" class="btn-flora-secondary text-sm flex items-center gap-2">
                    <x-icon name="transfer" class="w-4 h-4" />
                    <span>Transfer Antar Rekening</span>
                </button>
                <a href="{{ route('accounts.create') }}" class="btn-flora-primary flex items-center gap-2 text-sm">
                    <x-icon name="add-seed" class="w-4 h-4 text-white" />
                    <span>+ Rekening Baru</span>
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Net Worth Summary Banner -->
    <div class="flora-card bg-flora-gradient text-white p-6 sm:p-8 rounded-3xl mb-8 shadow-flora-lg relative overflow-hidden">
        <div class="absolute right-4 -bottom-6 opacity-15">
            <x-icon name="tree" class="w-48 h-48 text-white" />
        </div>
        <div class="relative z-10">
            <span class="text-xs font-bold uppercase tracking-widest text-white/80">Total Kekayaan Bersih (Net Worth)</span>
            <div class="text-3xl sm:text-4xl font-bold text-white mt-2 mb-2 font-heading">
                Rp {{ number_format($totalNetWorth, 0, ',', '.') }}
            </div>
            <p class="text-xs text-white/90">
                Terdistribusi di <span class="font-bold underline">{{ $activeCount }} rekening aktif</span> Anda.
            </p>
        </div>
    </div>

    <!-- Accounts Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($accounts as $acc)
            <div class="flora-card p-6 border-t-4 transition-all duration-300 hover:shadow-flora-lg hover:-translate-y-1 relative {{ !$acc->is_active ? 'opacity-60 bg-gray-50' : '' }}"
                 style="border-top-color: {{ $acc->color }}">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-sm" style="background-color: {{ $acc->color }}20;">
                            <x-icon :name="$acc->icon" class="w-7 h-7" />
                        </div>
                        <div>
                            <h3 class="font-heading text-lg text-earth-800 font-semibold">{{ $acc->name }}</h3>
                            <span class="flora-badge flora-badge-info text-[10px] py-0.5 px-2 mt-0.5">
                                {{ $acc->type_name }}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-1">
                        <a href="{{ route('accounts.edit', $acc) }}" class="p-1.5 text-sage-600 hover:text-sage-800 rounded-lg hover:bg-sage-50" title="Edit Rekening">
                            <x-icon name="edit-leaf" class="w-4 h-4" />
                        </a>
                    </div>
                </div>

                <div class="my-4">
                    <div class="text-xs text-earth-500 mb-0.5 font-medium">Saldo Saat Ini</div>
                    <div class="text-2xl font-bold text-sage-700">
                        Rp {{ number_format($acc->current_balance, 0, ',', '.') }}
                    </div>
                    @if($acc->account_number)
                        <div class="text-xs text-earth-500 mt-1 font-mono">No. {{ $acc->account_number }}</div>
                    @endif
                </div>

                <div class="pt-4 border-t border-sage-100 flex items-center justify-between text-xs">
                    <div class="text-earth-500">
                        Saldo Awal: Rp {{ number_format($acc->opening_balance, 0, ',', '.') }}
                    </div>
                    <a href="{{ route('accounts.show', $acc) }}" class="font-semibold text-sage-600 hover:text-sage-800 hover:underline flex items-center gap-1">
                        <span>Rincian</span>
                        <x-icon name="chevron-right" class="w-3 h-3" />
                    </a>
                </div>
            </div>
        @endforeach

        <!-- Add Account Card -->
        <a href="{{ route('accounts.create') }}" 
           class="flora-card border-2 border-dashed border-sage-300 hover:border-sage-500 flex flex-col items-center justify-center p-8 text-center text-sage-600 hover:bg-sage-50/50 transition-all duration-300 min-h-[200px]">
            <div class="w-12 h-12 rounded-full bg-sage-100 flex items-center justify-center mb-3">
                <x-icon name="plus" class="w-6 h-6 text-sage-600" />
            </div>
            <div class="font-heading font-semibold text-base text-earth-800">Tambah Rekening Baru</div>
            <p class="text-xs text-earth-500 mt-1 max-w-xs">Tambahkan rekening bank, dompet digital, atau uang tunai</p>
        </a>
    </div>
</x-app-layout>
