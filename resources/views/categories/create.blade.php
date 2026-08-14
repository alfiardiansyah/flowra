<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-heading text-3xl text-sage-600 flex items-center gap-3">
                    <x-icon name="bouquet" class="w-8 h-8 text-sage-400" />
                    Tambah Kategori Baru
                </h2>
                <p class="mt-1 text-earth-600 text-sm">Buat kategori atau subkategori untuk pengelompokan transaksi</p>
            </div>
            <a href="{{ route('categories.index') }}" class="btn-flora-secondary text-sm">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <x-card class="max-w-2xl mx-auto">
        <form action="{{ route('categories.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Type Selection -->
            <div class="form-section pb-4">
                <label class="form-section-title text-sm mb-3">Jenis Kategori</label>
                <div class="grid grid-cols-2 gap-4 p-1.5 bg-sage-50 rounded-2xl border border-sage-200">
                    <label class="relative cursor-pointer">
                        <input type="radio" name="type" value="expense" {{ old('type', $type ?? 'expense') === 'expense' ? 'checked' : '' }} class="peer sr-only">
                        <div class="py-2.5 px-4 rounded-xl text-center text-sm font-semibold peer-checked:bg-coral-400 peer-checked:text-white text-earth-600 transition-all flex items-center justify-center gap-2">
                            <x-icon name="falling-leaves" class="w-4 h-4" /> Pengeluaran
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="type" value="income" {{ old('type', $type ?? 'expense') === 'income' ? 'checked' : '' }} class="peer sr-only">
                        <div class="py-2.5 px-4 rounded-xl text-center text-sm font-semibold peer-checked:bg-leaf-400 peer-checked:text-white text-earth-600 transition-all flex items-center justify-center gap-2">
                            <x-icon name="sprout" class="w-4 h-4" /> Pemasukan
                        </div>
                    </label>
                </div>
            </div>

            <!-- Category Name -->
            <div class="form-section pb-4">
                <label class="form-section-title text-sm mb-2">Nama Kategori</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Belanja Online, Kopi & Camilan" class="flora-input">
                @error('name')
                    <p class="mt-1 text-xs text-coral-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Parent Category (Optional Hierarchical) -->
            <div class="form-section pb-4">
                <label class="form-section-title text-sm mb-2">Kategori Induk (Kosongkan jika ini Kategori Utama)</label>
                <select name="parent_id" class="flora-input">
                    <option value="">-- Jadikan Kategori Utama (Tanpa Induk) --</option>
                    @foreach($parentCategories as $parent)
                        <option value="{{ $parent->id }}" {{ old('parent_id', $parentId) == $parent->id ? 'selected' : '' }}>
                            {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Icon & Color -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 form-section pb-4">
                <div>
                    <label class="form-section-title text-sm mb-2">Ikon Kategori</label>
                    <select name="icon" class="flora-input text-sm">
                        <option value="flower" {{ old('icon') === 'flower' ? 'selected' : '' }}>Bunga (Flower)</option>
                        <option value="sprout" {{ old('icon') === 'sprout' ? 'selected' : '' }}>Tunas (Sprout)</option>
                        <option value="tree" {{ old('icon') === 'tree' ? 'selected' : '' }}>Pohon (Tree)</option>
                        <option value="sunflower" {{ old('icon') === 'sunflower' ? 'selected' : '' }}>Bunga Matahari</option>
                        <option value="cherry-blossom" {{ old('icon') === 'cherry-blossom' ? 'selected' : '' }}>Sakura</option>
                        <option value="oak-tree" {{ old('icon') === 'oak-tree' ? 'selected' : '' }}>Pohon Ek</option>
                        <option value="wildflower" {{ old('icon') === 'wildflower' ? 'selected' : '' }}>Bunga Liar</option>
                        <option value="apple" {{ old('icon') === 'apple' ? 'selected' : '' }}>Apel (Makanan)</option>
                        <option value="leaf-wind" {{ old('icon') === 'leaf-wind' ? 'selected' : '' }}>Daun Angin (Transport)</option>
                        <option value="shopping-leaf" {{ old('icon') === 'shopping-leaf' ? 'selected' : '' }}>Belanja</option>
                        <option value="cactus" {{ old('icon') === 'cactus' ? 'selected' : '' }}>Kaktus (Tagihan)</option>
                        <option value="medical-leaf" {{ old('icon') === 'medical-leaf' ? 'selected' : '' }}>Kesehatan</option>
                        <option value="book-sprout" {{ old('icon') === 'book-sprout' ? 'selected' : '' }}>Buku (Pendidikan)</option>
                        <option value="mixed-leaves" {{ old('icon') === 'mixed-leaves' ? 'selected' : '' }}>Lain-lain</option>
                    </select>
                </div>

                <div>
                    <label class="form-section-title text-sm mb-2">Warna Identitas</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="color" value="{{ old('color', '#87A96B') }}" class="w-12 h-10 p-1 rounded-xl border border-sage-200 cursor-pointer">
                        <span class="text-xs text-earth-500">Pilih warna penanda</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 justify-end pt-4 border-t border-sage-200">
                <a href="{{ route('categories.index') }}" class="btn-flora-secondary">Batal</a>
                <button type="submit" class="btn-flora-primary flex items-center gap-2">
                    <x-icon name="sprout" class="w-4 h-4 text-white" />
                    <span>Simpan Kategori</span>
                </button>
            </div>
        </form>
    </x-card>
</x-app-layout>
