<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-heading text-3xl text-sage-600 flex items-center gap-3">
                    <x-icon name="falling-leaves" class="w-8 h-8 text-coral-400" />
                    Track Your Spending Leaves
                </h2>
                <p class="mt-1 text-earth-600">Record a new expense in your garden</p>
            </div>
            <a href="{{ route('expenses.index') }}" class="btn-flora-secondary">
                Kembali
            </a>
        </div>
    </x-slot>

    <x-card class="max-w-3xl mx-auto">
        <form action="{{ route('expenses.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Nominal -->
            <div class="form-section">
                <label class="form-section-title">
                    <x-icon name="tree" class="w-5 h-5" />
                    Nominal Pengeluaran (Rp)
                </label>
                <input type="number" name="nominal" id="nominal" 
                       value="{{ old('nominal') }}" 
                       step="0.01" min="0.01"
                       required
                       class="flora-input text-lg font-bold text-earth-800 @error('nominal') border-coral-400 @enderror"
                       placeholder="Contoh: 25000">
                @error('nominal')
                    <p class="mt-1 text-sm text-coral-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Kategori -->
            <div class="form-section">
                <label class="form-section-title">
                    <x-icon name="bouquet" class="w-5 h-5" />
                    Kategori Pengeluaran
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    @php
                        $displayCats = $categories->count() > 0 ? $categories : collect([
                            (object)['id' => '', 'name' => 'Makanan', 'icon' => 'apple', 'color' => '#FF7A5C'],
                            (object)['id' => '', 'name' => 'Transport', 'icon' => 'leaf-wind', 'color' => '#5DADE2'],
                            (object)['id' => '', 'name' => 'Belanja', 'icon' => 'shopping-leaf', 'color' => '#6B8E23'],
                            (object)['id' => '', 'name' => 'Tagihan', 'icon' => 'cactus', 'color' => '#8B7355'],
                            (object)['id' => '', 'name' => 'Hiburan', 'icon' => 'bouquet', 'color' => '#B19CD9'],
                            (object)['id' => '', 'name' => 'Kesehatan', 'icon' => 'medical-leaf', 'color' => '#9BCF53'],
                            (object)['id' => '', 'name' => 'Pendidikan', 'icon' => 'book-sprout', 'color' => '#87A96B'],
                            (object)['id' => '', 'name' => 'Lainnya', 'icon' => 'mixed-leaves', 'color' => '#8B7355']
                        ]);
                    @endphp
                    @foreach($displayCats as $cat)
                        <label class="relative cursor-pointer">
                            <input type="radio" 
                                   name="category_id" 
                                   value="{{ $cat->id ?: '' }}" 
                                   {{ (old('category_id') == $cat->id || old('kategori') == $cat->name || $loop->first) ? 'checked' : '' }}
                                   class="peer sr-only"
                                   data-catname="{{ $cat->name }}"
                                   onchange="updateCatStyle(this)">
                            <div class="flex flex-col items-center gap-2 p-3.5 rounded-xl border-2 border-sage-200 peer-checked:border-coral-500 peer-checked:bg-coral-50/50 hover:border-sage-400 transition-all duration-200 text-center">
                                <x-icon :name="$cat->icon ?? 'flower'" class="w-7 h-7" style="color: {{ $cat->color ?? '#FF7A5C' }}" />
                                <span class="text-xs font-semibold text-earth-700">{{ $cat->name }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
                <input type="hidden" name="kategori" id="hidden_kategori" value="{{ old('kategori', $displayCats->first()->name ?? 'Makanan') }}">
                @error('category_id')
                    <p class="mt-1 text-sm text-coral-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Rekening / Dompet Sumber Pengeluaran -->
            <div class="form-section">
                <label class="form-section-title">
                    <x-icon name="cash-leaf" class="w-5 h-5" />
                    Sumber Rekening / Dompet
                </label>
                <select name="account_id" id="account_id" class="flora-input @error('account_id') border-coral-400 @enderror">
                    @forelse($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ old('account_id') == $acc->id ? 'selected' : '' }}>
                            {{ $acc->name }} (Saldo saat ini: Rp {{ number_format($acc->current_balance, 0, ',', '.') }})
                        </option>
                    @empty
                        <option value="">Dompet Tunai</option>
                    @endforelse
                </select>
                @error('account_id')
                    <p class="mt-1 text-sm text-coral-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Keterangan -->
            <div class="form-section">
                <label class="form-section-title">
                    <x-icon name="flower" class="w-5 h-5" />
                    Keterangan Pengeluaran
                </label>
                <input type="text" name="keterangan" id="keterangan" 
                       value="{{ old('keterangan') }}"
                       class="flora-input @error('keterangan') border-coral-400 @enderror"
                       placeholder="Contoh: Makan Siang, Bensin Motor, dll">
                @error('keterangan')
                    <p class="mt-1 text-sm text-coral-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tanggal -->
            <div class="form-section">
                <label class="form-section-title">
                    <x-icon name="flower-bloom" class="w-5 h-5" />
                    Tanggal Pengeluaran
                </label>
                <input type="date" name="tanggal" id="tanggal" 
                       value="{{ old('tanggal', now()->format('Y-m-d')) }}"
                       class="flora-input @error('tanggal') border-coral-400 @enderror">
                @error('tanggal')
                    <p class="mt-1 text-sm text-coral-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex gap-4 justify-end pt-6 border-t border-sage-200">
                <a href="{{ route('expenses.index') }}" class="btn-flora-secondary">
                    Batal
                </a>
                <button type="submit" class="btn-flora-primary flex items-center gap-2">
                    <x-icon name="sprout" class="w-5 h-5 text-white" />
                    <span>Simpan Pengeluaran</span>
                </button>
            </div>
        </form>
    </x-card>

    @push('scripts')
    <script>
        function updateCatStyle(input) {
            const hiddenKategori = document.getElementById('hidden_kategori');
            if (hiddenKategori && input.dataset.catname) {
                hiddenKategori.value = input.dataset.catname;
            }
        }
    </script>
    @endpush
</x-app-layout>
