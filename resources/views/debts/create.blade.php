<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-heading text-3xl text-sage-600 flex items-center gap-3">
                    <x-icon name="leaf" class="w-8 h-8 text-sage-400" />
                    Tambah Catatan Hutang / Piutang
                </h2>
                <p class="mt-1 text-earth-600 text-sm">Catat kewajiban atau tagihan kepada pihak lain</p>
            </div>
            <a href="{{ route('debts.index') }}" class="btn-flora-secondary text-sm">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <x-card class="max-w-2xl mx-auto" x-data="{ type: '{{ old('type', $type ?? 'debt') }}' }">
        <form action="{{ route('debts.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Type Selection -->
            <div class="form-section pb-4">
                <label class="form-section-title text-sm mb-3">Jenis Catatan</label>
                <div class="grid grid-cols-2 gap-4 p-1.5 bg-sage-50 rounded-2xl border border-sage-200">
                    <button type="button" @click="type = 'debt'" :class="type === 'debt' ? 'bg-coral-400 text-white shadow-md font-semibold' : 'text-earth-600'" class="py-3 px-4 rounded-xl text-sm flex items-center justify-center gap-2 transition-all">
                        <x-icon name="falling-leaves" class="w-5 h-5" />
                        <span>Hutang (Saya Berhutang)</span>
                    </button>
                    <button type="button" @click="type = 'receivable'" :class="type === 'receivable' ? 'bg-leaf-400 text-white shadow-md font-semibold' : 'text-earth-600'" class="py-3 px-4 rounded-xl text-sm flex items-center justify-center gap-2 transition-all">
                        <x-icon name="sprout" class="w-5 h-5" />
                        <span>Piutang (Orang Lain Berhutang)</span>
                    </button>
                </div>
                <input type="hidden" name="type" :value="type">
            </div>

            <!-- Person Name & Amount -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 form-section pb-4">
                <div>
                    <label class="form-section-title text-sm mb-2" x-text="type === 'debt' ? 'Nama Pemberi Pinjaman / Kreditor' : 'Nama Peminjam / Debitor'"></label>
                    <input type="text" name="person_name" value="{{ old('person_name') }}" required placeholder="Contoh: Budi Santoso, Bank ABC" class="flora-input">
                    @error('person_name')
                        <p class="mt-1 text-xs text-coral-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="form-section-title text-sm mb-2">Total Nominal Pinjaman (Rp)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-lg font-bold text-sage-600">Rp</span>
                        <input type="number" name="amount" value="{{ old('amount') }}" step="0.01" min="1" required placeholder="0" class="flora-input pl-12 text-lg font-bold">
                    </div>
                    @error('amount')
                        <p class="mt-1 text-xs text-coral-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Dates -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 form-section pb-4">
                <div>
                    <label class="form-section-title text-sm mb-2">Tanggal Mulai Pinjam</label>
                    <input type="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" required class="flora-input">
                    @error('date')
                        <p class="mt-1 text-xs text-coral-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="form-section-title text-sm mb-2">Target Jatuh Tempo (Opsional)</label>
                    <input type="date" name="due_date" value="{{ old('due_date') }}" class="flora-input">
                    @error('due_date')
                        <p class="mt-1 text-xs text-coral-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Account & Notes -->
            <div class="form-section pb-4 space-y-4">
                <div>
                    <label class="form-section-title text-sm mb-2" x-text="type === 'debt' ? 'Rekening Penerima Uang Pinjaman' : 'Rekening Sumber Uang Pinjaman'"></label>
                    <select name="account_id" class="flora-input">
                        <option value="">-- Tanpa Mutasi Rekening Otomatis --</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ old('account_id') == $acc->id ? 'selected' : '' }}>
                                {{ $acc->name }} (Saldo saat ini: Rp {{ number_format($acc->current_balance, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-earth-500 mt-1" x-text="type === 'debt' ? 'Jika dipilih, saldo rekening akan bertambah sejumlah pinjaman yang Anda terima (pemasukan kas).' : 'Jika dipilih, saldo rekening akan berkurang sejumlah pinjaman yang Anda berikan (pengeluaran kas).'"></p>
                </div>

                <div>
                    <label class="form-section-title text-sm mb-2">Catatan Tambahan (Opsional)</label>
                    <textarea name="notes" rows="2" placeholder="Catatan perjanjian, bunga (jika ada), atau tujuan..." class="flora-input text-sm">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 justify-end pt-4 border-t border-sage-200">
                <a href="{{ route('debts.index') }}" class="btn-flora-secondary">Batal</a>
                <button type="submit" class="btn-flora-primary flex items-center gap-2">
                    <x-icon name="sprout" class="w-4 h-4 text-white" />
                    <span>Simpan Catatan</span>
                </button>
            </div>
        </form>
    </x-card>
</x-app-layout>
