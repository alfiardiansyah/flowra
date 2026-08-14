<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-md" style="background-color: {{ $account->color }}25;">
                    <x-icon :name="$account->icon" class="w-7 h-7" />
                </div>
                <div>
                    <h2 class="font-heading text-3xl text-sage-700 font-semibold">{{ $account->name }}</h2>
                    <p class="text-earth-600 text-xs mt-0.5">
                        {{ $account->type_name }} @if($account->account_number) • No. {{ $account->account_number }} @endif
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <form action="{{ route('accounts.recalculate', $account) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="btn-flora-secondary text-xs flex items-center gap-1.5" title="Sinkronkan & hitung ulang saldo dari seluruh mutasi">
                        <x-icon name="refresh" class="w-3.5 h-3.5" />
                        <span>Hitung Ulang Saldo</span>
                    </button>
                </form>
                <a href="{{ route('accounts.edit', $account) }}" class="btn-flora-secondary text-xs flex items-center gap-1.5">
                    <x-icon name="edit-leaf" class="w-3.5 h-3.5" />
                    <span>Edit</span>
                </a>
                <a href="{{ route('accounts.index') }}" class="btn-flora-secondary text-xs">
                    ← Semua Rekening
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Account Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-card class="p-5 border-l-4" style="border-left-color: {{ $account->color }}">
            <div class="text-xs text-earth-500 font-medium">Saldo Saat Ini</div>
            <div class="text-2xl font-bold text-sage-700 mt-1">
                Rp {{ number_format($account->current_balance, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-earth-400 mt-1">Saldo Awal: Rp {{ number_format($account->opening_balance, 0, ',', '.') }}</div>
        </x-card>

        <x-card class="p-5 border-l-4 border-l-leaf-400">
            <div class="text-xs text-earth-500 font-medium">Total Pemasukan</div>
            <div class="text-2xl font-bold text-leaf-600 mt-1">
                Rp {{ number_format($totalIn, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-earth-400 mt-1">Akumulasi uang masuk</div>
        </x-card>

        <x-card class="p-5 border-l-4 border-l-coral-400">
            <div class="text-xs text-earth-500 font-medium">Total Pengeluaran</div>
            <div class="text-2xl font-bold text-coral-600 mt-1">
                Rp {{ number_format($totalOut, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-earth-400 mt-1">Akumulasi uang keluar</div>
        </x-card>

        <x-card class="p-5 border-l-4 border-l-sky-400">
            <div class="text-xs text-earth-500 font-medium">Mutasi Transfer</div>
            <div class="text-sm font-bold text-sky-600 mt-1">
                Masuk: +Rp {{ number_format($transfersIn, 0, ',', '.') }}
            </div>
            <div class="text-xs font-bold text-coral-500 mt-0.5">
                Keluar: -Rp {{ number_format($transfersOut, 0, ',', '.') }}
            </div>
        </x-card>
    </div>

    <!-- Transaction History Table for This Account -->
    <x-card>
        <div class="flex items-center justify-between mb-6 pb-3 border-b border-sage-100">
            <h3 class="font-heading text-lg text-sage-700 flex items-center gap-2">
                <x-icon name="flower-bloom" class="w-5 h-5 text-sage-500" />
                Mutasi Rekening {{ $account->name }}
            </h3>
            <button @click="$dispatch('open-quick-transaction', { accountId: '{{ $account->id }}' })" class="btn-flora-primary text-xs flex items-center gap-1.5">
                <x-icon name="plus" class="w-3.5 h-3.5 text-white" />
                <span>+ Catat Mutasi</span>
            </button>
        </div>

        @if($transactions->count() > 0)
            <div class="overflow-x-auto">
                <table class="flora-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th>Jenis / Kategori</th>
                            <th>Tujuan / Asal</th>
                            <th class="text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $tx)
                            @php
                                $isIncomingTransfer = ($tx->type === 'transfer' && $tx->destination_account_id === $account->id);
                                $isOutgoingTransfer = ($tx->type === 'transfer' && $tx->account_id === $account->id);
                            @endphp
                            <tr>
                                <td class="text-xs text-earth-600 font-medium">
                                    {{ $tx->date ? $tx->date->format('d M Y') : '-' }}
                                </td>
                                <td>
                                    <div class="font-semibold text-earth-800 text-sm">{{ $tx->description }}</div>
                                    @if($tx->notes)
                                        <div class="text-xs text-earth-500 italic">{{ $tx->notes }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if($tx->type === 'income')
                                        <span class="flora-badge flora-badge-success text-[11px]">{{ $tx->category->name ?? 'Pemasukan' }}</span>
                                    @elseif($tx->type === 'expense')
                                        <span class="flora-badge flora-badge-danger text-[11px]">{{ $tx->category->name ?? 'Pengeluaran' }}</span>
                                    @else
                                        <span class="flora-badge flora-badge-info text-[11px]">
                                            {{ $isIncomingTransfer ? 'Transfer Masuk' : 'Transfer Keluar' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-xs text-earth-600">
                                    @if($tx->type === 'transfer')
                                        @if($isIncomingTransfer)
                                            Dari: <span class="font-semibold text-earth-800">{{ $tx->account->name ?? '-' }}</span>
                                        @else
                                            Ke: <span class="font-semibold text-earth-800">{{ $tx->destinationAccount->name ?? '-' }}</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-right font-bold text-sm">
                                    @if($tx->type === 'income' || $isIncomingTransfer)
                                        <span class="text-leaf-600">+ Rp {{ number_format($tx->amount, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-coral-600">- Rp {{ number_format($tx->amount, 0, ',', '.') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flora-pagination mt-6">
                {{ $transactions->links() }}
            </div>
        @else
            <x-empty-state 
                title="Belum Ada Riwayat Transaksi" 
                description="Belum ada catatan mutasi transaksi pada rekening ini."
                :action="route('transactions.create', ['account_id' => $account->id])"
                action-label="+ Catat Transaksi di Rekening Ini" />
        @endif
    </x-card>
</x-app-layout>
