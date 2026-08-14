<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-heading text-3xl text-sage-600 flex items-center gap-3">
                    <x-icon name="sprout" class="w-8 h-8 text-leaf-400 animate-grow" />
                    Pemasukan
                </h2>
                <p class="mt-1 text-earth-600 text-sm">Kelola seluruh benih pemasukan keuangan Anda</p>
            </div>
            <a href="{{ route('incomes.create') }}" class="btn-flora-primary text-sm flex items-center gap-2">
                <x-icon name="add-seed" class="w-4 h-4 text-white" />
                <span>+ Tambah Pemasukan</span>
            </a>
        </div>
    </x-slot>

    <!-- Filters -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('incomes.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-earth-700 mb-1">Cari Keterangan</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Ketik kata kunci..." 
                       class="flora-input text-xs py-2">
            </div>
            <div class="min-w-[150px]">
                <label class="block text-xs font-medium text-earth-700 mb-1">Kategori</label>
                <select name="kategori" class="flora-input text-xs py-2">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->name }}" {{ request('kategori') == $cat->name ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-flora-primary text-xs py-2 px-4 flex items-center gap-1">
                <x-icon name="search" class="w-3.5 h-3.5 text-white" />
                <span>Filter</span>
            </button>
            @if(request()->hasAny(['search', 'kategori']))
                <a href="{{ route('incomes.index') }}" class="btn-flora-secondary text-xs py-2 px-3">
                    Reset
                </a>
            @endif
        </form>
    </x-card>

    <!-- Income List -->
    @if($incomes->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            @foreach($incomes as $income)
                @php
                    $amount = $income->amount ?? $income->nominal;
                    $desc = $income->description ?? $income->keterangan ?? 'Pemasukan';
                    $catName = $income->category->name ?? $income->kategori ?? 'Lainnya';
                    $date = $income->date ?? $income->tanggal;
                    $accName = $income->account->name ?? $income->nama_bank ?? 'Tunai';
                    $proof = $income->attachment ?? $income->bukti_transfer;
                    $icon = $income->category->icon ?? 'sunflower';
                @endphp
                <x-card variant="transaction" class="income p-5">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-2xl bg-mint-100 flex items-center justify-center">
                                <x-icon :name="$icon" class="w-6 h-6 text-leaf-600" />
                            </div>
                            <div>
                                <div class="font-semibold text-earth-800 text-sm">{{ $catName }}</div>
                                <div class="text-xs text-earth-500">{{ $date ? $date->format('d M Y') : '-' }}</div>
                            </div>
                        </div>
                        <div class="flex gap-1">
                            <a href="{{ route('incomes.edit', $income) }}" class="p-1.5 rounded-lg text-sage-600 hover:bg-sage-50" title="Edit">
                                <x-icon name="edit-leaf" class="w-4 h-4" />
                            </a>
                            <form action="{{ route('incomes.destroy', $income) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pemasukan ini?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg text-coral-500 hover:bg-coral-50" title="Hapus">
                                    <x-icon name="delete-wilt" class="w-4 h-4" />
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="text-2xl font-bold text-leaf-600 mb-1">
                            + Rp {{ number_format($amount, 0, ',', '.') }}
                        </div>
                        @if($desc)
                            <p class="text-xs text-earth-600">{{ $desc }}</p>
                        @endif
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-sage-100 text-xs">
                        <div class="flex items-center gap-1.5 text-earth-600 font-medium">
                            <x-icon name="cash-leaf" class="w-3.5 h-3.5 text-sage-500" />
                            <span>{{ $accName }}</span>
                        </div>
                        @if($proof)
                            <a href="{{ asset('storage/' . $proof) }}" target="_blank" class="text-sage-600 hover:underline font-semibold">
                                Lihat Bukti
                            </a>
                        @endif
                    </div>
                </x-card>
            @endforeach
        </div>

        <div class="flora-pagination">
            {{ $incomes->links() }}
        </div>
    @else
        <x-empty-state 
            title="Belum Ada Pemasukan" 
            description="Tanam benih pemasukan pertama Anda untuk mulai menumbuhkan kekayaan finansial."
            :action="route('incomes.create')"
            action-label="+ Tambah Pemasukan" />
    @endif
</x-app-layout>
