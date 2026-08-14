<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-heading text-3xl text-sage-600 flex items-center gap-3">
                    <x-icon name="edit-leaf" class="w-8 h-8 text-sage-400" />
                    Edit Rekening
                </h2>
                <p class="mt-1 text-earth-600 text-sm">Perbarui informasi rekening {{ $account->name }}</p>
            </div>
            <a href="{{ route('accounts.index') }}" class="btn-flora-secondary text-sm">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <x-card class="max-w-2xl mx-auto">
        <form action="{{ route('accounts.update', $account) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Account Name -->
            <div class="form-section pb-4">
                <label class="form-section-title text-sm mb-2">Nama Rekening / Dompet</label>
                <input type="text" name="name" value="{{ old('name', $account->name) }}" required class="flora-input">
                @error('name')
                    <p class="mt-1 text-xs text-coral-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Type -->
            <div class="form-section pb-4">
                <label class="form-section-title text-sm mb-2">Jenis Rekening</label>
                <select name="type" class="flora-input">
                    <option value="cash" {{ old('type', $account->type) === 'cash' ? 'selected' : '' }}>Tunai / Cash</option>
                    <option value="bank" {{ old('type', $account->type) === 'bank' ? 'selected' : '' }}>Rekening Bank</option>
                    <option value="ewallet" {{ old('type', $account->type) === 'ewallet' ? 'selected' : '' }}>E-Wallet / Dompet Digital</option>
                    <option value="savings" {{ old('type', $account->type) === 'savings' ? 'selected' : '' }}>Tabungan</option>
                    <option value="credit_card" {{ old('type', $account->type) === 'credit_card' ? 'selected' : '' }}>Kartu Kredit</option>
                    <option value="investment" {{ old('type', $account->type) === 'investment' ? 'selected' : '' }}>Investasi</option>
                    <option value="other" {{ old('type', $account->type) === 'other' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>

            <!-- Opening Balance -->
            <div class="form-section pb-4">
                <label class="form-section-title text-sm mb-2">Saldo Awal (Rp)</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xl font-bold text-sage-600">Rp</span>
                    <input type="number" name="opening_balance" value="{{ old('opening_balance', $account->opening_balance) }}" step="0.01" required class="flora-input pl-12 text-lg font-bold">
                </div>
                <p class="text-xs text-earth-500 mt-1">Mengubah saldo awal akan otomatis menghitung ulang saldo berjalan.</p>
            </div>

            <!-- Account Number -->
            <div class="form-section pb-4">
                <label class="form-section-title text-sm mb-2">Nomor Rekening / HP</label>
                <input type="text" name="account_number" value="{{ old('account_number', $account->account_number) }}" class="flora-input font-mono text-sm">
            </div>

            <!-- Visual Styling: Color & Icon -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 form-section pb-4">
                <div>
                    <label class="form-section-title text-sm mb-2">Warna Identitas</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="color" value="{{ old('color', $account->color) }}" class="w-12 h-10 p-1 rounded-xl border border-sage-200 cursor-pointer">
                        <span class="text-xs text-earth-500">Pilih warna kartu</span>
                    </div>
                </div>

                <div>
                    <label class="form-section-title text-sm mb-2">Ikon</label>
                    <select name="icon" class="flora-input text-sm">
                        <option value="bank-bca" {{ old('icon', $account->icon) === 'bank-bca' ? 'selected' : '' }}>BCA</option>
                        <option value="bank-mandiri" {{ old('icon', $account->icon) === 'bank-mandiri' ? 'selected' : '' }}>Mandiri</option>
                        <option value="bank-bri" {{ old('icon', $account->icon) === 'bank-bri' ? 'selected' : '' }}>BRI</option>
                        <option value="cash-leaf" {{ old('icon', $account->icon) === 'cash-leaf' ? 'selected' : '' }}>Dompet / Tunai</option>
                        <option value="e-wallet" {{ old('icon', $account->icon) === 'e-wallet' ? 'selected' : '' }}>E-Wallet</option>
                        <option value="tree" {{ old('icon', $account->icon) === 'tree' ? 'selected' : '' }}>Pohon Tabungan</option>
                        <option value="oak-tree" {{ old('icon', $account->icon) === 'oak-tree' ? 'selected' : '' }}>Investasi</option>
                    </select>
                </div>
            </div>

            <!-- Status Checkbox -->
            <div class="form-section pb-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $account->is_active) ? 'checked' : '' }} class="rounded border-sage-300 text-sage-600 focus:ring-sage-500">
                    <span class="text-sm font-medium text-earth-700">Rekening Aktif (ditampilkan dalam pilihan transaksi)</span>
                </label>
            </div>

            <!-- Notes -->
            <div class="form-section pb-4">
                <label class="form-section-title text-sm mb-2">Catatan Tambahan</label>
                <textarea name="notes" rows="2" class="flora-input text-sm">{{ old('notes', $account->notes) }}</textarea>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 justify-between items-center pt-4 border-t border-sage-200">
                <form action="{{ route('accounts.destroy', $account) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus/menonaktifkan rekening ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs font-semibold text-coral-600 hover:text-coral-800 hover:underline">
                        Hapus Rekening
                    </button>
                </form>

                <div class="flex gap-3">
                    <a href="{{ route('accounts.index') }}" class="btn-flora-secondary">Batal</a>
                    <button type="submit" class="btn-flora-primary flex items-center gap-2">
                        <x-icon name="sprout" class="w-4 h-4 text-white" />
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </div>
        </form>
    </x-card>
</x-app-layout>
