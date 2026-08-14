<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-heading text-2xl sm:text-3xl text-sage-600 flex items-center gap-2.5">
                    <x-icon name="flower-bloom" class="w-7 h-7 sm:w-8 sm:h-8 text-sage-400" />
                    Semua Transaksi
                </h2>
                <p class="mt-0.5 text-earth-600 text-xs sm:text-sm">Catatan riwayat pemasukan, pengeluaran, dan transfer antar rekening</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('transactions.create') }}" class="btn-flora-primary w-full sm:w-auto flex items-center justify-center gap-2 text-xs sm:text-sm py-2.5 px-4 shadow-sm">
                    <x-icon name="add-seed" class="w-4 h-4 text-white" />
                    <span>+ Tambah Transaksi</span>
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Quick Stats for Current Filter -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-5 sm:mb-6">
        <div class="flora-card p-3.5 sm:p-4 flex items-center justify-between border-l-4 border-l-leaf-400">
            <div>
                <div class="text-[11px] sm:text-xs text-earth-500 font-medium">Total Pemasukan (Filter)</div>
                <div class="text-base sm:text-lg font-bold text-leaf-600 mt-0.5">Rp {{ number_format($totalIncomes, 0, ',', '.') }}</div>
            </div>
            <x-icon name="sprout" class="w-6 h-6 sm:w-7 sm:h-7 text-leaf-500 opacity-80 flex-shrink-0" />
        </div>
        <div class="flora-card p-3.5 sm:p-4 flex items-center justify-between border-l-4 border-l-coral-400">
            <div>
                <div class="text-[11px] sm:text-xs text-earth-500 font-medium">Total Pengeluaran (Filter)</div>
                <div class="text-base sm:text-lg font-bold text-coral-600 mt-0.5">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</div>
            </div>
            <x-icon name="falling-leaves" class="w-6 h-6 sm:w-7 sm:h-7 text-coral-500 opacity-80 flex-shrink-0" />
        </div>
        <div class="flora-card p-3.5 sm:p-4 flex items-center justify-between border-l-4 border-l-sky-400">
            <div>
                <div class="text-[11px] sm:text-xs text-earth-500 font-medium">Selisih Bersih</div>
                <div class="text-base sm:text-lg font-bold {{ $netAmount >= 0 ? 'text-leaf-600' : 'text-coral-600' }} mt-0.5">
                    {{ $netAmount >= 0 ? '+' : '' }} Rp {{ number_format($netAmount, 0, ',', '.') }}
                </div>
            </div>
            <x-icon name="flower-bloom" class="w-6 h-6 sm:w-7 sm:h-7 text-sky-500 opacity-80 flex-shrink-0" />
        </div>
    </div>

    <!-- Live Filter & Search Bar -->
    <x-card class="mb-5 sm:mb-6 p-4 sm:p-5" x-data="{
        submitTimeout: null,
        triggerLiveFilter() {
            clearTimeout(this.submitTimeout);
            this.submitTimeout = setTimeout(() => {
                $refs.filterForm.submit();
            }, 400);
        }
    }">
        <form method="GET" action="{{ route('transactions.index') }}" x-ref="filterForm" class="space-y-4">
            @if(request('type'))
                <input type="hidden" name="type" value="{{ request('type') }}">
            @endif

            <!-- Type Tabs (Horizontal Scroll on Mobile/Android) -->
            <div class="flex items-center justify-between pb-2 border-b border-sage-100 gap-2">
                <div class="flex items-center gap-1.5 overflow-x-auto -mx-1 px-1 custom-scrollbar scrollbar-none whitespace-nowrap">
                    <a href="{{ route('transactions.index', array_merge(request()->except('type', 'page'), ['type' => ''])) }}" 
                       class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all duration-200 flex-shrink-0 {{ !request('type') ? 'bg-sage-600 text-white shadow-sm' : 'bg-sage-50 text-earth-600 hover:bg-sage-100' }}">
                        Semua Jenis
                    </a>
                    <a href="{{ route('transactions.index', array_merge(request()->except('type', 'page'), ['type' => 'expense'])) }}" 
                       class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all duration-200 flex-shrink-0 {{ request('type') === 'expense' ? 'bg-coral-500 text-white shadow-sm' : 'bg-sage-50 text-earth-600 hover:bg-coral-50 hover:text-coral-600' }}">
                        Pengeluaran
                    </a>
                    <a href="{{ route('transactions.index', array_merge(request()->except('type', 'page'), ['type' => 'income'])) }}" 
                       class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all duration-200 flex-shrink-0 {{ request('type') === 'income' ? 'bg-leaf-500 text-white shadow-sm' : 'bg-sage-50 text-earth-600 hover:bg-mint-50 hover:text-leaf-600' }}">
                        Pemasukan
                    </a>
                    <a href="{{ route('transactions.index', array_merge(request()->except('type', 'page'), ['type' => 'transfer'])) }}" 
                       class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all duration-200 flex-shrink-0 {{ request('type') === 'transfer' ? 'bg-sky-500 text-white shadow-sm' : 'bg-sage-50 text-earth-600 hover:bg-sky-50 hover:text-sky-600' }}">
                        Transfer
                    </a>
                </div>

                @if(request()->hasAny(['search', 'type', 'account_id', 'category_id', 'from', 'to']))
                    <a href="{{ route('transactions.index') }}" class="btn-flora-secondary text-xs py-1.5 px-3 flex-shrink-0 flex items-center gap-1" title="Reset Filter">
                        <x-icon name="delete-wilt" class="w-3.5 h-3.5 text-coral-500" />
                        <span>Reset Filter</span>
                    </a>
                @endif
            </div>

            <!-- Live Filter Inputs Grid (4 Equal Columns) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                <div>
                    <label class="block text-[11px] sm:text-xs font-medium text-earth-700 mb-1">Cari Keterangan / Catatan</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               @input="triggerLiveFilter()" 
                               x-init="if ($el.value) { $el.focus(); $el.setSelectionRange($el.value.length, $el.value.length); }"
                               placeholder="Ketik untuk mencari instan..." 
                               class="flora-input text-xs py-2 pr-8">
                        <x-icon name="search" class="w-3.5 h-3.5 text-sage-400 absolute right-2.5 top-2.5 pointer-events-none" />
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] sm:text-xs font-medium text-earth-700 mb-1">Rekening</label>
                    <select name="account_id" @change="$refs.filterForm.submit()" class="flora-input text-xs py-2">
                        <option value="">Semua Rekening</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ request('account_id') == $acc->id ? 'selected' : '' }}>
                                {{ $acc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] sm:text-xs font-medium text-earth-700 mb-1">Kategori</label>
                    <select name="category_id" @change="$refs.filterForm.submit()" class="flora-input text-xs py-2">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }} ({{ $cat->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] sm:text-xs font-medium text-earth-700 mb-1">Dari Tanggal</label>
                    <input type="date" name="from" value="{{ request('from') }}" @change="$refs.filterForm.submit()" class="flora-input text-xs py-2">
                </div>
            </div>
        </form>
    </x-card>

    <!-- Transactions List Container -->
    @if($transactions->count() > 0)
        <!-- 1. Mobile Android Card List View (Block on Mobile, Hidden on Desktop) -->
        <div class="block md:hidden space-y-3 mb-6">
            @foreach($transactions as $tx)
                <div class="flora-card p-3.5 rounded-2xl bg-white border border-sage-200/80 shadow-sm flex flex-col gap-2.5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 shadow-inner {{ $tx->type === 'income' ? 'bg-mint-100 text-leaf-700' : ($tx->type === 'expense' ? 'bg-coral-100 text-coral-700' : 'bg-sky-100 text-sky-700') }}">
                                <x-icon :name="$tx->category->icon ?? ($tx->type === 'income' ? 'sprout' : ($tx->type === 'expense' ? 'falling-leaves' : 'leaf-wind'))" class="w-5 h-5" />
                            </div>
                            <div class="min-w-0">
                                <div class="font-bold text-earth-800 text-sm truncate leading-snug">{{ $tx->description }}</div>
                                <div class="text-[11px] text-earth-500 flex items-center gap-1.5 mt-0.5">
                                    <span>{{ $tx->date ? $tx->date->format('d M Y') : '-' }}</span>
                                    <span>•</span>
                                    <span class="font-medium text-sage-700">{{ $tx->type === 'income' ? ($tx->category->name ?? 'Pemasukan') : ($tx->type === 'expense' ? ($tx->category->name ?? 'Pengeluaran') : 'Transfer') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="text-right flex-shrink-0">
                            <div class="font-bold text-sm {{ $tx->type === 'income' ? 'text-leaf-600' : ($tx->type === 'expense' ? 'text-coral-600' : 'text-sky-600') }}">
                                {{ $tx->formatted_amount }}
                            </div>
                        </div>
                    </div>

                    <!-- Account & Action Footer -->
                    <div class="pt-2 border-t border-sage-100 flex items-center justify-between text-xs">
                        <div class="text-earth-600 font-medium flex items-center gap-1">
                            @if($tx->type === 'transfer')
                                <span>{{ $tx->account->name ?? '-' }}</span>
                                <span class="text-sky-500 font-bold">→</span>
                                <span>{{ $tx->destinationAccount->name ?? '-' }}</span>
                            @else
                                <x-icon :name="$tx->account->icon ?? 'cash-leaf'" class="w-3.5 h-3.5 text-sage-500" />
                                <span>{{ $tx->account->name ?? '-' }}</span>
                            @endif
                        </div>

                        <div class="flex items-center gap-1">
                            @if($tx->attachment)
                                <a href="{{ asset('storage/' . $tx->attachment) }}" target="_blank" class="p-1.5 text-sage-600 hover:text-sage-800 rounded-lg hover:bg-sage-50 active:bg-sage-100" title="Lihat Bukti">
                                    <x-icon name="document-text" class="w-4 h-4" />
                                </a>
                            @endif
                            <a href="{{ route('transactions.edit', $tx) }}" class="p-1.5 text-sage-600 hover:text-sage-800 rounded-lg hover:bg-sage-50 active:bg-sage-100" title="Edit">
                                <x-icon name="edit-leaf" class="w-4 h-4" />
                            </a>
                            <form action="{{ route('transactions.destroy', $tx) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus transaksi ini? Saldo rekening akan disesuaikan kembali.')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-coral-500 hover:text-coral-700 rounded-lg hover:bg-coral-50 active:bg-coral-100" title="Hapus">
                                    <x-icon name="delete-wilt" class="w-4 h-4" />
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- 2. Desktop Table View (Hidden on Mobile, Block on Desktop) -->
        <div class="hidden md:block flora-card p-0 overflow-hidden shadow-flora mb-6">
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
                                <td class="text-xs font-medium text-earth-600 whitespace-nowrap">
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
                                <td class="text-right font-bold text-sm {{ $tx->type === 'income' ? 'text-leaf-600' : ($tx->type === 'expense' ? 'text-coral-600' : 'text-sky-600') }} whitespace-nowrap">
                                    {{ $tx->formatted_amount }}
                                </td>
                                <td class="text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        @if($tx->attachment)
                                            <a href="{{ asset('storage/' . $tx->attachment) }}" target="_blank" class="p-1.5 text-sage-600 hover:text-sage-800 rounded-lg hover:bg-sage-50" title="Lihat Bukti">
                                                <x-icon name="document-text" class="w-4 h-4" />
                                            </a>
                                        @endif
                                        <a href="{{ route('transactions.edit', $tx) }}" class="p-1.5 text-sage-600 hover:text-sage-800 rounded-lg hover:bg-sage-50" title="Edit">
                                            <x-icon name="edit-leaf" class="w-4 h-4" />
                                        </a>
                                        <form action="{{ route('transactions.destroy', $tx) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus transaksi me ini? Saldo rekening akan disesuaikan kembali.')" class="inline">
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

        <div class="flora-pagination mt-4 sm:mt-6">
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
