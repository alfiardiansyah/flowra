<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-heading text-3xl text-sage-600 flex items-center gap-3">
                    <x-icon name="edit-leaf" class="w-8 h-8 text-sage-400" />
                    Edit Pemasukan
                </h2>
                <p class="mt-1 text-earth-600 text-sm">Perbarui catatan pemasukan Anda</p>
            </div>
            <a href="{{ route('incomes.index') }}" class="btn-flora-secondary text-sm">
                ← Kembali
            </a>
        </div>
    </x-slot>

    @php
        $amount = $income->amount ?? $income->nominal;
        $desc = $income->description ?? $income->keterangan;
        $catId = $income->category_id ?? null;
        $catName = $income->category->name ?? $income->kategori;
        $date = $income->date ? $income->date->format('Y-m-d') : ($income->tanggal ? $income->tanggal->format('Y-m-d') : '');
        $accId = $income->account_id ?? null;
    @endphp

    <x-card class="max-w-2xl mx-auto">
        <form action="{{ route('incomes.update', $income) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Nominal -->
            <div class="form-section pb-4">
                <label class="form-section-title text-sm mb-2">Nominal Pemasukan (Rp)</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xl font-bold text-sage-600">Rp</span>
                    <input type="number" name="nominal" value="{{ old('nominal', $amount) }}" step="0.01" min="0.01" required class="flora-input pl-12 text-lg font-bold">
                </div>
            </div>

            <!-- Kategori -->
            <div class="form-section pb-4">
                <label class="form-section-title text-sm mb-2">Kategori</label>
                <select name="category_id" class="flora-input">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $catId) == $cat->id || old('kategori', $catName) == $cat->name ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Keterangan -->
            <div class="form-section pb-4">
                <label class="form-section-title text-sm mb-2">Keterangan</label>
                <input type="text" name="keterangan" value="{{ old('keterangan', $desc) }}" class="flora-input">
            </div>

            <!-- Tanggal & Rekening -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 form-section pb-4">
                <div>
                    <label class="form-section-title text-sm mb-2">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', $date) }}" required class="flora-input">
                </div>

                <div>
                    <label class="form-section-title text-sm mb-2">Rekening Penerima</label>
                    <select name="account_id" class="flora-input">
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ old('account_id', $accId) == $acc->id ? 'selected' : '' }}>
                                {{ $acc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            <!-- Actions -->
            <div class="flex gap-3 justify-end pt-4 border-t border-sage-200">
                <a href="{{ route('incomes.index') }}" class="btn-flora-secondary">Batal</a>
                <button type="submit" class="btn-flora-primary flex items-center gap-2">
                    <x-icon name="sprout" class="w-4 h-4 text-white" />
                    <span>Perbarui Pemasukan</span>
                </button>
            </div>
        </form>
    </x-card>
</x-app-layout>
