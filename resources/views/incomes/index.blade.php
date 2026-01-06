<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-heading text-3xl text-sage-600 flex items-center gap-3">
                    <x-icon name="sprout" class="w-8 h-8 text-leaf-400 animate-grow" />
                    Pemasukan
                </h2>
                <p class="mt-1 text-earth-600">Plant your income seeds and watch them grow</p>
            </div>
            <a href="{{ route('incomes.create') }}" class="btn-flora-primary">
                <x-icon name="add-seed" class="w-5 h-5" />
                Tambah Pemasukan
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
        <form method="GET" action="{{ route('incomes.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-earth-700 mb-2">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari pemasukan..." 
                       class="flora-input">
            </div>
            <div class="min-w-[150px]">
                <label class="block text-sm font-medium text-earth-700 mb-2">Kategori</label>
                <select name="kategori" class="flora-input">
                    <option value="">Semua Kategori</option>
                    <option value="Gaji" {{ request('kategori') == 'Gaji' ? 'selected' : '' }}>Gaji</option>
                    <option value="Bonus" {{ request('kategori') == 'Bonus' ? 'selected' : '' }}>Bonus</option>
                    <option value="Investasi" {{ request('kategori') == 'Investasi' ? 'selected' : '' }}>Investasi</option>
                    <option value="Freelance" {{ request('kategori') == 'Freelance' ? 'selected' : '' }}>Freelance</option>
                    <option value="Lainnya" {{ request('kategori') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>
            <button type="submit" class="btn-flora-secondary">
                <x-icon name="flower" class="w-4 h-4" />
                Filter
            </button>
            @if(request()->hasAny(['search', 'kategori']))
                <a href="{{ route('incomes.index') }}" class="btn-flora-secondary">
                    Reset
                </a>
            @endif
        </form>
    </x-card>

    <!-- Income List -->
    @if($incomes->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            @foreach($incomes as $income)
                <x-card variant="transaction" class="income">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full bg-leaf-100 flex items-center justify-center">
                                @php
                                    $iconMap = [
                                        'Gaji' => 'sunflower',
                                        'Bonus' => 'cherry-blossom',
                                        'Investasi' => 'oak-tree',
                                        'Freelance' => 'wildflower',
                                        'Lainnya' => 'bouquet'
                                    ];
                                    $icon = $iconMap[$income->kategori] ?? 'bouquet';
                                @endphp
                                <x-icon :name="$icon" class="w-6 h-6 text-leaf-600" />
                            </div>
                            <div>
                                <div class="font-semibold text-earth-800">{{ $income->kategori ?? 'Lainnya' }}</div>
                                <div class="text-sm text-earth-600">{{ $income->tanggal ? $income->tanggal->format('d M Y') : '-' }}</div>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('incomes.edit', $income) }}" class="block p-2 rounded-lg text-sage-600 hover:bg-sage-50 hover:text-sage-700 transition-colors cursor-pointer z-10" title="Edit">
                                <x-icon name="edit-leaf" class="w-5 h-5" />
                            </a>
                            <form action="{{ route('incomes.destroy', $income) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pemasukan ini?')" style="display: inline-block; z-index: 10;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="padding: 0.5rem; border-radius: 0.5rem; color: #FF7A5C; cursor: pointer; background: transparent; border: none; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#FFE8E3'; this.style.color='#FF5C3A';" onmouseout="this.style.backgroundColor='transparent'; this.style.color='#FF7A5C';" title="Hapus">
                                    <x-icon name="delete-wilt" class="w-5 h-5" />
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="text-2xl font-bold text-leaf-600 mb-1">
                            Rp {{ number_format($income->nominal, 0, ',', '.') }}
                        </div>
                        @if($income->keterangan)
                            <p class="text-sm text-earth-600">{{ $income->keterangan }}</p>
                        @endif
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-sage-100">
                        <div class="flex items-center gap-2 text-sm text-earth-500">
                            @if($income->nama_bank)
                                @php
                                    $bankIcons = [
                                        'BCA' => 'bank-bca',
                                        'Mandiri' => 'bank-mandiri',
                                        'BRI' => 'bank-bri',
                                        'Cash' => 'cash-leaf',
                                        'E-Wallet' => 'e-wallet'
                                    ];
                                    $bankIcon = $bankIcons[$income->nama_bank] ?? 'cash-leaf';
                                @endphp
                                <x-icon :name="$bankIcon" class="w-4 h-4" />
                                <span>{{ $income->nama_bank }}</span>
                            @endif
                        </div>
                        @if($income->bukti_transfer)
                            <a href="{{ asset('storage/' . $income->bukti_transfer) }}" target="_blank" class="text-xs text-sage-600 hover:text-sage-700">
                                Lihat Bukti
                            </a>
                        @endif
                    </div>
                </x-card>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="flora-pagination">
            {{ $incomes->links() }}
        </div>
    @else
        <x-empty-state 
            title="Belum ada pemasukan" 
            description="Plant your first income seed to start growing your financial garden!"
            :action="route('incomes.create')"
            action-label="Plant Your First Income Seed" />
    @endif

    <!-- Floating Action Button (Mobile) -->
    <a href="{{ route('incomes.create') }}" class="fab lg:hidden">
        <x-icon name="add-seed" class="w-8 h-8" />
    </a>
</x-app-layout>
