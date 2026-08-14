<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-heading text-3xl text-sage-600 flex items-center gap-3">
                    <x-icon name="flower-bloom" class="w-8 h-8 text-sage-400" />
                    Semua Transaksi
                </h2>
                <p class="mt-1 text-earth-600 text-sm">Catatan riwayat pemasukan, pengeluaran, dan transfer antar rekening</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('transactions.create') }}" class="btn-flora-primary flex items-center gap-2 text-sm">
                    <x-icon name="add-seed" class="w-4 h-4 text-white" />
                    <span>+ Tambah Transaksi</span>
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Quick Stats for Current Filter -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="flora-card p-4 flex items-center justify-between border-l-4 border-l-leaf-400">
            <div>
                <div class="text-xs text-earth-500 font-medium">Total Pemasukan (Filter)</div>
                <div class="text-lg font-bold text-leaf-600 mt-0.5">Rp {{ number_format($totalIncomes, 0, ',', '.') }}</div>
            </div>
            <x-icon name="sprout" class="w-7 h-7 text-leaf-500 opacity-80" />
        </div>
        <div class="flora-card p-4 flex items-center justify-between border-l-4 border-l-coral-400">
            <div>
                <div class="text-xs text-earth-500 font-medium">Total Pengeluaran (Filter)</div>
                <div class="text-lg font-bold text-coral-600 mt-0.5">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</div>
            </div>
            <x-icon name="falling-leaves" class="w-7 h-7 text-coral-500 opacity-80" />
        </div>
        <div class="flora-card p-4 flex items-center justify-between border-l-4 border-l-sky-400">
            <div>
                <div class="text-xs text-earth-500 font-medium">Selisih Bersih</div>
                <div class="text-lg font-bold {{ $netAmount >= 0 ? 'text-leaf-600' : 'text-coral-600' }} mt-0.5">
                    {{ $netAmount >= 0 ? '+' : '' }} Rp {{ number_format($netAmount, 0, ',', '.') }}
                </div>
            </div>
            <x-icon name="flower-bloom" class="w-7 h-7 text-sky-500 opacity-80" />
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('transactions.index') }}" class="space-y-4">
            <!-- Type Tabs -->
            <div class="flex flex-wrap gap-2 pb-3 border-b border-sage-100">
                <a href="{{ route('transactions.index', array_merge(request()->except('type', 'page'), ['type' => ''])) }}" 
                   class="px-4 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 {{ !request('type') ? 'bg-sage-600 text-white shadow-sm' : 'bg-sage-50 text-earth-600 hover:bg-sage-100' }}">
                    Semua Jenis
                </a>
                <a href="{{ route('transactions.index', array_merge(request()->except('type', 'page'), ['type' => 'expense'])) }}" 
                   class="px-4 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 {{ request('type') === 'expense' ? 'bg-coral-500 text-white shadow-sm' : 'bg-sage-50 text-earth-600 hover:bg-coral-50 hover:text-coral-600' }}">
                    Pengeluaran
                </a>
                <a href="{{ route('transactions.index', array_merge(request()->except('type', 'page'), ['type' => 'income'])) }}" 
                   class="px-4 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 {{ request('type') === 'income' ? 'bg-leaf-500 text-white shadow-sm' : 'bg-sage-50 text-earth-600 hover:bg-mint-50 hover:text-leaf-600' }}">
                    Pemasukan
                </a>
                <a href="{{ route('transactions.index', array_merge(request()->except('type', 'page'), ['type' => 'transfer'])) }}" 
                   class="px-4 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 {{ request('type') === 'transfer' ? 'bg-sky-500 text-white shadow-sm' : 'bg-sage-50 text-earth-600 hover:bg-sky-50 hover:text-sky-600' }}">
                    Transfer
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3 items-end">
                <div>
                    <label class="block text-xs font-medium text-earth-700 mb-1">Cari Keterangan / Catatan</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik kata kunci..." class="flora-input text-xs py-2">
                </div>

                <div>
                    <label class="block text-xs font-medium text-earth-700 mb-1">Rekening</label>
                    <select name="account_id" class="flora-input text-xs py-2">
                        <option value="">Semua Rekening</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ request('account_id') == $acc->id ? 'selected' : '' }}>
                                {{ $acc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-earth-700 mb-1">Kategori</label>
                    <select name="category_id" class="flora-input text-xs py-2">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }} ({{ $cat->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-earth-700 mb-1">Dari Tanggal</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="flora-input text-xs py-2">
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="btn-flora-primary text-xs py-2 px-4 flex-1 flex items-center justify-center gap-1">
                        <x-icon name="search" class="w-3.5 h-3.5 text-white" />
                        <span>Filter</span>
                    </button>
                    @if(request()->hasAny(['search', 'type', 'account_id', 'category_id', 'from', 'to']))
                        <a href="{{ route('transactions.index') }}" class="btn-flora-secondary text-xs py-2 px-3 flex items-center justify-center" title="Reset Filter">
                            Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </x-card>

    <!-- Transactions List / Table -->
    @if($transactions->count() > 0)
        <div class="flora-card p-0 overflow-hidden shadow-flora">
            <div class="overflow-x-auto">
                <table class="flora-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th>Jenis & Kategori</th>
                            <th>Rekening</th>
                            <th class="text-right">Nominal</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $tx)
                            <tr>
                                <td class="text-xs font-medium text-earth-600">
                                    {{ $tx->date ? $tx->date->format('d M Y') : '-' }}
                                </td>
                                <td>
                                    <div class="font-semibold text-earth-800 text-sm">{{ $tx->description }}</div>
                                    @if($tx->notes)
                                        <div class="text-xs text-earth-500 italic truncate max-w-xs">{{ $tx->notes }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="flora-badge {{ $tx->type_badge_class }} text-[11px]">
                                        @if($tx->type === 'income')
                                            {{ $tx->category->name ?? 'Pemasukan' }}
                                        @elseif($tx->type === 'expense')
                                            {{ $tx->category->name ?? 'Pengeluaran' }}
                                        @else
                                            Transfer
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    @if($tx->type === 'transfer')
                                        <div class="text-xs font-medium text-earth-700 flex items-center gap-1">
                                            <span>{{ $tx->account->name ?? '-' }}</span>
                                            <span class="text-sky-500 font-bold">→</span>
                                            <span>{{ $tx->destinationAccount->name ?? '-' }}</span>
                                        </div>
                                    @else
                                        <div class="text-xs font-medium text-earth-700 flex items-center gap-1.5">
                                            <x-icon :name="$tx->account->icon ?? 'cash-leaf'" class="w-3.5 h-3.5" />
                                            <span>{{ $tx->account->name ?? '-' }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-right font-bold text-sm {{ $tx->type === 'income' ? 'text-leaf-600' : ($tx->type === 'expense' ? 'text-coral-600' : 'text-sky-600') }}">
                                    {{ $tx->formatted_amount }}
                                </td>
                                <td class="text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        @if($tx->attachment)
                                            <a href="{{ asset('storage/' . $tx->attachment) }}" target="_blank" class="p-1.5 text-sage-600 hover:text-sage-800 rounded-lg hover:bg-sage-50" title="Lihat Bukti">
                                                <x-icon name="document-text" class="w-4 h-4" />
                                            </a>
                                        @endif
                                        <a href="{{ route('transactions.edit', $tx) }}" class="p-1.5 text-sage-600 hover:text-sage-800 rounded-lg hover:bg-sage-50" title="Edit">
                                            <x-icon name="edit-leaf" class="w-4 h-4" />
                                        </a>
                                        <form action="{{ route('transactions.destroy', $tx) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus transaksi ini? Saldo rekening akan disesuaikan kembali.')" class="inline">
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

        <div class="flora-pagination mt-6">
            {{ $transactions->links() }}
        </div>
    @else
        <x-empty-state 
            title="Tidak Ada Transaksi Ditemukan" 
            description="Coba ubah filter pencarian Anda atau tambahkan transaksi baru ke kebun Anda."
            :action="route('transactions.create')"
            action-label="+ Tambah Transaksi Sekarang" />
    @endif
</x-app-layout>
