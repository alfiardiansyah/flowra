<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-heading text-3xl text-sage-600 flex items-center gap-3">
                    <x-icon name="cash-leaf" class="w-8 h-8 text-sage-400" />
                    Tambah Rekening Baru
                </h2>
                <p class="mt-1 text-earth-600 text-sm">Daftarkan akun keuangan baru untuk melacak saldo secara akurat</p>
            </div>
            <a href="{{ route('accounts.index') }}" class="btn-flora-secondary text-sm">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <x-card class="max-w-2xl mx-auto">
        <form action="{{ route('accounts.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Account Name -->
            <div class="form-section pb-4">
                <label class="form-section-title text-sm mb-2">Nama Rekening / Dompet</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: BCA Utama, Dompet Saku, GoPay" class="flora-input">
                @error('name')
                    <p class="mt-1 text-xs text-coral-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Type -->
            <div class="form-section pb-4">
                <label class="form-section-title text-sm mb-2">Jenis Rekening</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @php
                        $types = [
                            'cash' => ['label' => 'Tunai / Cash', 'icon' => 'cash-leaf', 'color' => '#87A96B'],
                            'bank' => ['label' => 'Bank Account', 'icon' => 'bank-bca', 'color' => '#5DADE2'],
                            'ewallet' => ['label' => 'E-Wallet', 'icon' => 'e-wallet', 'color' => '#B19CD9'],
                            'savings' => ['label' => 'Tabungan', 'icon' => 'tree', 'color' => '#6B8E23'],
                            'credit_card' => ['label' => 'Kartu Kredit', 'icon' => 'falling-leaves', 'color' => '#FF6B6B'],
                            'investment' => ['label' => 'Investasi', 'icon' => 'oak-tree', 'color' => '#FFD700'],
                        ];
                    @endphp
                    @foreach($types as $key => $item)
                        <label class="relative cursor-pointer">
                            <input type="radio" name="type" value="{{ $key }}" {{ old('type', 'bank') === $key ? 'checked' : '' }} class="peer sr-only">
                            <div class="p-3.5 rounded-xl border-2 border-sage-200 peer-checked:border-sage-500 peer-checked:bg-sage-50/70 hover:border-sage-300 flex flex-col items-center text-center gap-1.5 transition-all">
                                <x-icon :name="$item['icon']" class="w-6 h-6" />
                                <span class="text-xs font-semibold text-earth-800">{{ $item['label'] }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('type')
                    <p class="mt-1 text-xs text-coral-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Opening Balance -->
            <div class="form-section pb-4">
                <label class="form-section-title text-sm mb-2">Saldo Awal (Rp)</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xl font-bold text-sage-600">Rp</span>
                    <input type="number" name="opening_balance" value="{{ old('opening_balance', 0) }}" step="0.01" min="0" required class="flora-input pl-12 text-lg font-bold">
                </div>
                <p class="text-xs text-earth-500 mt-1">Saldo yang ada pada saat pertama kali rekening didaftarkan ke Flowra.</p>
                @error('opening_balance')
                    <p class="mt-1 text-xs text-coral-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Account Number -->
            <div class="form-section pb-4">
                <label class="form-section-title text-sm mb-2">Nomor Rekening / HP (Opsional)</label>
                <input type="text" name="account_number" value="{{ old('account_number') }}" placeholder="Contoh: 1234567890" class="flora-input font-mono text-sm">
                @error('account_number')
                    <p class="mt-1 text-xs text-coral-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Visual Styling: Color & Icon -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 form-section pb-4">
                <div>
                    <label class="form-section-title text-sm mb-2">Warna Identitas</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="color" value="{{ old('color', '#87A96B') }}" class="w-12 h-10 p-1 rounded-xl border border-sage-200 cursor-pointer">
                        <span class="text-xs text-earth-500">Pilih warna untuk kartu rekening</span>
                    </div>
                </div>

                <div>
                    <label class="form-section-title text-sm mb-2">Ikon</label>
                    <select name="icon" class="flora-input text-sm">
                        <option value="bank-bca" {{ old('icon') === 'bank-bca' ? 'selected' : '' }}>BCA</option>
                        <option value="bank-mandiri" {{ old('icon') === 'bank-mandiri' ? 'selected' : '' }}>Mandiri</option>
                        <option value="bank-bri" {{ old('icon') === 'bank-bri' ? 'selected' : '' }}>BRI</option>
                        <option value="cash-leaf" {{ old('icon', 'cash-leaf') === 'cash-leaf' ? 'selected' : '' }}>Dompet / Tunai</option>
                        <option value="e-wallet" {{ old('icon') === 'e-wallet' ? 'selected' : '' }}>E-Wallet</option>
                        <option value="tree" {{ old('icon') === 'tree' ? 'selected' : '' }}>Pohon Tabungan</option>
                        <option value="oak-tree" {{ old('icon') === 'oak-tree' ? 'selected' : '' }}>Investasi</option>
                    </select>
                </div>
            </div>

            <!-- Notes -->
            <div class="form-section pb-4">
                <label class="form-section-title text-sm mb-2">Catatan Tambahan (Opsional)</label>
                <textarea name="notes" rows="2" placeholder="Catatan kecil mengenai rekening ini..." class="flora-input text-sm">{{ old('notes') }}</textarea>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 justify-end pt-4 border-t border-sage-200">
                <a href="{{ route('accounts.index') }}" class="btn-flora-secondary">Batal</a>
                <button type="submit" class="btn-flora-primary flex items-center gap-2">
                    <x-icon name="sprout" class="w-4 h-4 text-white" />
                    <span>Simpan Rekening</span>
                </button>
            </div>
        </form>
    </x-card>
</x-app-layout>
