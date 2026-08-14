<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-heading text-3xl text-sage-600 flex items-center gap-3">
                    <x-icon name="edit-leaf" class="w-8 h-8 text-sage-400" />
                    Edit Pengeluaran
                </h2>
                <p class="mt-1 text-earth-600 text-sm">Perbarui catatan pengeluaran Anda</p>
            </div>
            <a href="{{ route('expenses.index') }}" class="btn-flora-secondary text-sm">
                ← Kembali
            </a>
        </div>
    </x-slot>

    @php
        $amount = $expense->amount ?? $expense->nominal;
        $desc = $expense->description ?? $expense->keterangan;
        $catId = $expense->category_id ?? null;
        $catName = $expense->category->name ?? $expense->kategori;
        $date = $expense->date ? $expense->date->format('Y-m-d') : ($expense->tanggal ? $expense->tanggal->format('Y-m-d') : '');
        $accId = $expense->account_id ?? null;
        $proof = $expense->attachment ?? $expense->bukti_pembayaran;
    @endphp

    <x-card class="max-w-2xl mx-auto">
        <form action="{{ route('expenses.update', $expense) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Nominal -->
            <div class="form-section pb-4">
                <label class="form-section-title text-sm mb-2">Nominal Pengeluaran (Rp)</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xl font-bold text-coral-600">Rp</span>
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
                    <label class="form-section-title text-sm mb-2">Metode / Rekening</label>
                    <select name="account_id" class="flora-input">
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ old('account_id', $accId) == $acc->id ? 'selected' : '' }}>
                                {{ $acc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Bukti Pembayaran -->
            <div class="form-section pb-4">
                <label class="form-section-title text-sm mb-2">Bukti Pembayaran (Opsional)</label>
                @if($proof)
                    <div class="mb-3 flex items-center gap-3 p-3 bg-sage-50 rounded-xl">
                        <img src="{{ asset('storage/' . $proof) }}" alt="Bukti" class="w-16 h-16 object-cover rounded-lg border">
                        <a href="{{ asset('storage/' . $proof) }}" target="_blank" class="text-xs text-sage-600 hover:underline">Lihat Gambar Bukti</a>
                    </div>
                @endif
                <input type="file" name="bukti_pembayaran" accept="image/*" class="flora-input text-xs">
            </div>

            <!-- Actions -->
            <div class="flex gap-3 justify-end pt-4 border-t border-sage-200">
                <a href="{{ route('expenses.index') }}" class="btn-flora-secondary">Batal</a>
                <button type="submit" class="btn-flora-primary flex items-center gap-2">
                    <x-icon name="sprout" class="w-4 h-4 text-white" />
                    <span>Perbarui Pengeluaran</span>
                </button>
            </div>
        </form>
    </x-card>
</x-app-layout>
