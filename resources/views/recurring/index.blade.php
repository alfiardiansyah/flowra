<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-heading text-3xl text-sage-600 flex items-center gap-3">
                    <x-icon name="leaf-wind" class="w-8 h-8 text-sage-400" />
                    Transaksi Rutin & Berulang
                </h2>
                <p class="mt-1 text-earth-600 text-sm">Kelola tagihan berkala, langganan, cicilan, dan gaji bulanan</p>
            </div>
            <a href="{{ route('recurring.create') }}" class="btn-flora-primary flex items-center gap-2 text-sm">
                <x-icon name="add-seed" class="w-4 h-4 text-white" />
                <span>+ Transaksi Rutin Baru</span>
            </a>
        </div>
    </x-slot>

    <!-- Stats Banner -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
        <x-card variant="summary" class="border-l-4 border-l-sage-500">
            <div class="text-xs text-earth-500 font-medium">Jadwal Aktif</div>
            <div class="text-2xl font-bold text-sage-700 mt-1">{{ $activeCount }} Transaksi Rutin</div>
            <p class="text-xs text-earth-500 mt-1">Langganan, sewa, tagihan listrik/air, & gaji</p>
        </x-card>

        <x-card variant="summary" class="border-l-4 border-l-coral-400">
            <div class="text-xs text-earth-500 font-medium">Estimasi Kewajiban Rutin Bulanan</div>
            <div class="text-2xl font-bold text-coral-600 mt-1">
                Rp {{ number_format($totalMonthlyObligation, 0, ',', '.') }}
            </div>
            <p class="text-xs text-earth-500 mt-1">Total pengeluaran rutin per bulan</p>
        </x-card>
    </div>

    <!-- Recurring Items Table/Cards -->
    @if($recurringTransactions->count() > 0)
        <div class="flora-card p-0 overflow-hidden shadow-flora">
            <div class="overflow-x-auto">
                <table class="flora-table">
                    <thead>
                        <tr>
                            <th>Keterangan</th>
                            <th>Frekuensi</th>
                            <th>Jadwal Berikutnya</th>
                            <th>Rekening & Kategori</th>
                            <th class="text-right">Nominal</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recurringTransactions as $rec)
                            <tr class="{{ !$rec->is_active ? 'opacity-60 bg-gray-50' : '' }}">
                                <td>
                                    <div class="font-semibold text-earth-800 text-sm">{{ $rec->description }}</div>
                                    @if($rec->auto_record)
                                        <span class="text-[10px] text-sage-600 font-medium flex items-center gap-1 mt-0.5">
                                            ⚡ Auto-Record Aktif
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="flora-badge flora-badge-info text-[11px]">
                                        {{ $rec->frequency_label }}
                                    </span>
                                </td>
                                <td class="text-xs font-semibold {{ $rec->next_run_date->isPast() ? 'text-coral-600 font-bold' : 'text-earth-700' }}">
                                    {{ $rec->next_run_date ? $rec->next_run_date->format('d M Y') : '-' }}
                                    @if($rec->next_run_date && $rec->next_run_date->isPast())
                                        <div class="text-[10px] text-coral-500">Jatuh tempo!</div>
                                    @endif
                                </td>
                                <td class="text-xs text-earth-600">
                                    <div>{{ $rec->account->name ?? '-' }}</div>
                                    <div class="text-[11px] text-earth-500">{{ $rec->category->name ?? ($rec->type === 'transfer' ? 'Transfer' : '-') }}</div>
                                </td>
                                <td class="text-right font-bold text-sm {{ $rec->type === 'income' ? 'text-leaf-600' : 'text-coral-600' }}">
                                    {{ $rec->type === 'income' ? '+' : '-' }} Rp {{ number_format($rec->amount, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('recurring.toggle', $rec) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="flora-badge {{ $rec->is_active ? 'flora-badge-success' : 'bg-gray-200 text-gray-700' }} text-[10px] cursor-pointer hover:opacity-80 transition-opacity" title="Klik untuk menjeda/mengaktifkan">
                                            {{ $rec->is_active ? 'Aktif' : 'Dijeda' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <form action="{{ route('recurring.post-now', $rec) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="btn-flora-secondary text-[11px] py-1 px-2.5 font-semibold" title="Catat transaksi untuk jadwal saat ini dan majukan jadwal berikutnya">
                                                Catat Sekarang
                                            </button>
                                        </form>
                                        <a href="{{ route('recurring.edit', $rec) }}" class="p-1.5 text-sage-600 hover:text-sage-800 rounded-lg hover:bg-sage-50" title="Edit">
                                            <x-icon name="edit-leaf" class="w-4 h-4" />
                                        </a>
                                        <form action="{{ route('recurring.destroy', $rec) }}" method="POST" onsubmit="return confirm('Hapus jadwal transaksi rutin ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-coral-500 hover:text-coral-700 rounded-lg hover:bg-coral-50" title="Hapus">
                                                <x-icon name="delete-wilt" class="w-4 h-4" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <x-empty-state 
            title="Belum Ada Transaksi Rutin" 
            description="Daftarkan pembayaran langganan, tagihan rutin bulanan, atau jadwal gaji untuk pengingat otomatis."
            :action="route('recurring.create')"
            action-label="+ Tambah Transaksi Rutin" />
    @endif
</x-app-layout>
