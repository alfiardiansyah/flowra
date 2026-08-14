<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-heading text-3xl text-sage-600 flex items-center gap-3">
                    <x-icon name="edit-leaf" class="w-8 h-8 text-sage-400" />
                    Edit Kategori
                </h2>
                <p class="mt-1 text-earth-600 text-sm">Kategori: {{ $category->name }}</p>
            </div>
            <a href="{{ route('categories.index') }}" class="btn-flora-secondary text-sm">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <x-card class="max-w-2xl mx-auto">
        <form action="{{ route('categories.update', $category) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="form-section pb-4">
                <label class="form-section-title text-sm mb-2">Nama Kategori</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" required class="flora-input">
            </div>

            <div class="form-section pb-4">
                <label class="form-section-title text-sm mb-2">Kategori Induk</label>
                <select name="parent_id" class="flora-input">
                    <option value="">-- Jadikan Kategori Utama (Tanpa Induk) --</option>
                    @foreach($parentCategories as $parent)
                        <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                            {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 form-section pb-4">
                <div>
                    <label class="form-section-title text-sm mb-2">Ikon Kategori</label>
                    <select name="icon" class="flora-input text-sm">
                        <option value="flower" {{ old('icon', $category->icon) === 'flower' ? 'selected' : '' }}>Bunga</option>
                        <option value="sprout" {{ old('icon', $category->icon) === 'sprout' ? 'selected' : '' }}>Tunas</option>
                        <option value="tree" {{ old('icon', $category->icon) === 'tree' ? 'selected' : '' }}>Pohon</option>
                        <option value="sunflower" {{ old('icon', $category->icon) === 'sunflower' ? 'selected' : '' }}>Bunga Matahari</option>
                        <option value="cherry-blossom" {{ old('icon', $category->icon) === 'cherry-blossom' ? 'selected' : '' }}>Sakura</option>
                        <option value="oak-tree" {{ old('icon', $category->icon) === 'oak-tree' ? 'selected' : '' }}>Pohon Ek</option>
                        <option value="wildflower" {{ old('icon', $category->icon) === 'wildflower' ? 'selected' : '' }}>Bunga Liar</option>
                        <option value="apple" {{ old('icon', $category->icon) === 'apple' ? 'selected' : '' }}>Apel</option>
                        <option value="leaf-wind" {{ old('icon', $category->icon) === 'leaf-wind' ? 'selected' : '' }}>Daun Angin</option>
                        <option value="shopping-leaf" {{ old('icon', $category->icon) === 'shopping-leaf' ? 'selected' : '' }}>Belanja</option>
                        <option value="cactus" {{ old('icon', $category->icon) === 'cactus' ? 'selected' : '' }}>Kaktus</option>
                        <option value="medical-leaf" {{ old('icon', $category->icon) === 'medical-leaf' ? 'selected' : '' }}>Kesehatan</option>
                        <option value="book-sprout" {{ old('icon', $category->icon) === 'book-sprout' ? 'selected' : '' }}>Buku</option>
                        <option value="mixed-leaves" {{ old('icon', $category->icon) === 'mixed-leaves' ? 'selected' : '' }}>Lain-lain</option>
                    </select>
                </div>

                <div>
                    <label class="form-section-title text-sm mb-2">Warna Identitas</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="color" value="{{ old('color', $category->color) }}" class="w-12 h-10 p-1 rounded-xl border border-sage-200 cursor-pointer">
                        <span class="text-xs text-earth-500">Pilih warna penanda</span>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 justify-between items-center pt-4 border-t border-sage-200">
                <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-coral-600 hover:underline">Hapus Kategori</button>
                </form>

                <div class="flex gap-3">
                    <a href="{{ route('categories.index') }}" class="btn-flora-secondary">Batal</a>
                    <button type="submit" class="btn-flora-primary flex items-center gap-2">
                        <x-icon name="sprout" class="w-4 h-4 text-white" />
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </div>
        </form>
    </x-card>
</x-app-layout>
