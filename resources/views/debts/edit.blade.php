<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-heading text-3xl text-sage-600 flex items-center gap-3">
                    <x-icon name="edit-leaf" class="w-8 h-8 text-sage-400" />
                    Edit Catatan {{ $debt->type === 'debt' ? 'Hutang' : 'Piutang' }}
                </h2>
                <p class="mt-1 text-earth-600 text-sm">Pihak terkait: {{ $debt->person_name }}</p>
            </div>
            <a href="{{ route('debts.index') }}" class="btn-flora-secondary text-sm">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <x-card class="max-w-2xl mx-auto">
        <form action="{{ route('debts.update', $debt) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 form-section pb-4">
                <div>
                    <label class="form-section-title text-sm mb-2">Nama Pihak Terkait</label>
                    <input type="text" name="person_name" value="{{ old('person_name', $debt->person_name) }}" required class="flora-input">
                </div>

                <div>
                    <label class="form-section-title text-sm mb-2">Total Nominal Pinjaman (Rp)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-lg font-bold text-sage-600">Rp</span>
                        <input type="number" name="amount" value="{{ old('amount', $debt->amount) }}" step="0.01" min="{{ $debt->paid_amount }}" required class="flora-input pl-12 text-lg font-bold">
                    </div>
                    <p class="text-[11px] text-earth-500 mt-1">Sudah terbayar: Rp {{ number_format($debt->paid_amount, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 form-section pb-4">
                <div>
                    <label class="form-section-title text-sm mb-2">Tanggal Mulai Pinjam</label>
                    <input type="date" name="date" value="{{ old('date', $debt->date ? $debt->date->format('Y-m-d') : '') }}" required class="flora-input">
                </div>

                <div>
                    <label class="form-section-title text-sm mb-2">Target Jatuh Tempo</label>
                    <input type="date" name="due_date" value="{{ old('due_date', $debt->due_date ? $debt->due_date->format('Y-m-d') : '') }}" class="flora-input">
                </div>
            </div>

            <div class="form-section pb-4 space-y-4">
                <div>
                    <label class="form-section-title text-sm mb-2">Rekening Asosiasi</label>
                    <select name="account_id" class="flora-input">
                        <option value="">-- Tanpa Rekening Khusus --</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ old('account_id', $debt->account_id) == $acc->id ? 'selected' : '' }}>
                                {{ $acc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-section-title text-sm mb-2">Catatan</label>
                    <textarea name="notes" rows="2" class="flora-input text-sm">{{ old('notes', $debt->notes) }}</textarea>
                </div>
            </div>

            <div class="flex gap-3 justify-between items-center pt-4 border-t border-sage-200">
                <form action="{{ route('debts.destroy', $debt) }}" method="POST" onsubmit="return confirm('Hapus catatan ini beserta seluruh riwayat pembayarannya?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-coral-600 hover:underline">Hapus Catatan</button>
                </form>

                <div class="flex gap-3">
                    <a href="{{ route('debts.index') }}" class="btn-flora-secondary">Batal</a>
                    <button type="submit" class="btn-flora-primary flex items-center gap-2">
                        <x-icon name="sprout" class="w-4 h-4 text-white" />
                        <span>Perbarui Catatan</span>
                    </button>
                </div>
            </div>
        </form>
    </x-card>
</x-app-layout>
