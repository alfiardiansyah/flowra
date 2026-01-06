<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-heading text-3xl text-sage-600 flex items-center gap-3">
                    <x-icon name="falling-leaves" class="w-8 h-8 text-coral-400" />
                    Pengeluaran
                </h2>
                <p class="mt-1 text-earth-600">Track your spending leaves</p>
            </div>
            <a href="{{ route('expenses.create') }}" class="btn-flora-primary">
                <x-icon name="add-seed" class="w-5 h-5" />
                Tambah Pengeluaran
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <x-alert type="success" dismissible class="mb-6">
            {{ session('success') }}
        </x-alert>
    @endif

    <!-- Filters -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('expenses.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-earth-700 mb-2">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari pengeluaran..." 
                       class="flora-input">
            </div>
            <div class="min-w-[150px]">
                <label class="block text-sm font-medium text-earth-700 mb-2">Kategori</label>
                <select name="kategori" class="flora-input">
                    <option value="">Semua Kategori</option>
                    <option value="Makanan" {{ request('kategori') == 'Makanan' ? 'selected' : '' }}>Makanan</option>
                    <option value="Transport" {{ request('kategori') == 'Transport' ? 'selected' : '' }}>Transport</option>
                    <option value="Belanja" {{ request('kategori') == 'Belanja' ? 'selected' : '' }}>Belanja</option>
                    <option value="Tagihan" {{ request('kategori') == 'Tagihan' ? 'selected' : '' }}>Tagihan</option>
                    <option value="Hiburan" {{ request('kategori') == 'Hiburan' ? 'selected' : '' }}>Hiburan</option>
                    <option value="Kesehatan" {{ request('kategori') == 'Kesehatan' ? 'selected' : '' }}>Kesehatan</option>
                    <option value="Pendidikan" {{ request('kategori') == 'Pendidikan' ? 'selected' : '' }}>Pendidikan</option>
                    <option value="Lainnya" {{ request('kategori') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>
            <button type="submit" class="btn-flora-secondary">
                <x-icon name="flower" class="w-4 h-4" />
                Filter
            </button>
            @if(request()->hasAny(['search', 'kategori']))
                <a href="{{ route('expenses.index') }}" class="btn-flora-secondary">
                    Reset
                </a>
            @endif
        </form>
    </x-card>

    <!-- Expense List -->
    @if($expenses->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            @foreach($expenses as $expense)
                <x-card variant="transaction" class="expense">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full bg-coral-100 flex items-center justify-center">
                                @php
                                    $iconMap = [
                                        'Makanan' => 'apple',
                                        'Transport' => 'leaf-wind',
                                        'Belanja' => 'shopping-leaf',
                                        'Tagihan' => 'cactus',
                                        'Hiburan' => 'bouquet',
                                        'Kesehatan' => 'medical-leaf',
                                        'Pendidikan' => 'book-sprout',
                                        'Lainnya' => 'mixed-leaves'
                                    ];
                                    $icon = $iconMap[$expense->kategori] ?? 'mixed-leaves';
                                @endphp
                                <x-icon :name="$icon" class="w-6 h-6 text-coral-600" />
                            </div>
                            <div>
                                <div class="font-semibold text-earth-800">{{ $expense->kategori ?? 'Lainnya' }}</div>
                                <div class="text-sm text-earth-600">{{ $expense->tanggal ? $expense->tanggal->format('d M Y') : '-' }}</div>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('expenses.edit', $expense) }}" class="block p-2 rounded-lg text-sage-600 hover:bg-sage-50 hover:text-sage-700 transition-colors cursor-pointer z-10" title="Edit">
                                <x-icon name="edit-leaf" class="w-5 h-5" />
                            </a>
                            <form action="{{ route('expenses.destroy', $expense) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pengeluaran ini?')" style="display: inline-block; z-index: 10;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="padding: 0.5rem; border-radius: 0.5rem; color: #FF7A5C; cursor: pointer; background: transparent; border: none; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#FFE8E3'; this.style.color='#FF5C3A';" onmouseout="this.style.backgroundColor='transparent'; this.style.color='#FF7A5C';" title="Hapus">
                                    <x-icon name="delete-wilt" class="w-5 h-5" />
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="text-2xl font-bold text-coral-600 mb-1">
                            Rp {{ number_format($expense->nominal, 0, ',', '.') }}
                        </div>
                        @if($expense->keterangan)
                            <p class="text-sm text-earth-600">{{ $expense->keterangan }}</p>
                        @endif
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-sage-100">
                        <div class="flex items-center gap-2 text-sm text-earth-500">
                            @if($expense->metode_pembayaran)
                                @php
                                    $paymentIcons = [
                                        'BCA' => 'bank-bca',
                                        'Mandiri' => 'bank-mandiri',
                                        'BRI' => 'bank-bri',
                                        'Cash' => 'cash-leaf',
                                        'E-Wallet' => 'e-wallet'
                                    ];
                                    $paymentIcon = $paymentIcons[$expense->metode_pembayaran] ?? 'cash-leaf';
                                @endphp
                                <x-icon :name="$paymentIcon" class="w-4 h-4" />
                                <span>{{ $expense->metode_pembayaran }}</span>
                            @endif
                        </div>
                        @if($expense->bukti_pembayaran)
                            <a href="{{ asset('storage/' . $expense->bukti_pembayaran) }}" target="_blank" class="text-xs text-coral-600 hover:text-coral-700">
                                Lihat Bukti
                            </a>
                        @endif
                    </div>
                </x-card>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="flora-pagination">
            {{ $expenses->links() }}
        </div>
    @else
        <x-empty-state 
            title="Belum ada pengeluaran" 
            description="Track your first spending leaf to start managing your expenses!"
            :action="route('expenses.create')"
            action-label="Track Your First Expense" />
    @endif

    <!-- Floating Action Button (Mobile) -->
    <a href="{{ route('expenses.create') }}" class="fab lg:hidden">
        <x-icon name="add-seed" class="w-8 h-8" />
    </a>
</x-app-layout>
