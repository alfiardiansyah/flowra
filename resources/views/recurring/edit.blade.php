<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-heading text-3xl text-sage-600 flex items-center gap-3">
                    <x-icon name="edit-leaf" class="w-8 h-8 text-sage-400" />
                    Edit Transaksi Rutin
                </h2>
                <p class="mt-1 text-earth-600 text-sm">Perbarui rincian jadwal {{ $recurring->description }}</p>
            </div>
            <a href="{{ route('recurring.index') }}" class="btn-flora-secondary text-sm">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <x-card class="max-w-2xl mx-auto" x-data="{ type: '{{ $recurring->type }}' }">
        <form action="{{ route('recurring.update', $recurring) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Description & Amount -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 form-section pb-4">
                <div>
                    <label class="form-section-title text-sm mb-2">Nama / Keterangan Tagihan</label>
                    <input type="text" name="description" value="{{ old('description', $recurring->description) }}" required class="flora-input">
                    @error('description')
                        <p class="mt-1 text-xs text-coral-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="form-section-title text-sm mb-2">Nominal (Rp)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-lg font-bold text-sage-600">Rp</span>
                        <input type="number" name="amount" value="{{ old('amount', $recurring->amount) }}" step="0.01" min="0.01" required class="flora-input pl-12 text-lg font-bold">
                    </div>
                    @error('amount')
                        <p class="mt-1 text-xs text-coral-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Frequency & Next Run Date -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 form-section pb-4">
                <div>
                    <label class="form-section-title text-sm mb-2">Frekuensi</label>
                    <select name="frequency" required class="flora-input">
                        <option value="monthly" {{ old('frequency', $recurring->frequency) === 'monthly' ? 'selected' : '' }}>Bulanan (Monthly)</option>
                        <option value="weekly" {{ old('frequency', $recurring->frequency) === 'weekly' ? 'selected' : '' }}>Mingguan (Weekly)</option>
                        <option value="daily" {{ old('frequency', $recurring->frequency) === 'daily' ? 'selected' : '' }}>Harian (Daily)</option>
                        <option value="yearly" {{ old('frequency', $recurring->frequency) === 'yearly' ? 'selected' : '' }}>Tahunan (Yearly)</option>
                    </select>
                </div>

                <div>
                    <label class="form-section-title text-sm mb-2">Jadwal Eksekusi Berikutnya</label>
                    <input type="date" name="next_run_date" value="{{ old('next_run_date', $recurring->next_run_date ? $recurring->next_run_date->format('Y-m-d') : '') }}" required class="flora-input">
                    @error('next_run_date')
                        <p class="mt-1 text-xs text-coral-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Account & Category -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 form-section pb-4">
                <div>
                    <label class="form-section-title text-sm mb-2">Rekening</label>
                    <select name="account_id" required class="flora-input">
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ old('account_id', $recurring->account_id) == $acc->id ? 'selected' : '' }}>
                                {{ $acc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div x-show="type !== 'transfer'">
                    <label class="form-section-title text-sm mb-2">Kategori</label>
                    <select name="category_id" class="flora-input">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $recurring->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Auto-record & Active check -->
            <div class="form-section pb-4 space-y-2">
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="auto_record" value="1" {{ old('auto_record', $recurring->auto_record) ? 'checked' : '' }} class="rounded border-sage-300 text-sage-600 focus:ring-sage-500">
                    <span class="text-xs font-semibold text-earth-700">Auto-Record Aktif</span>
                </label>
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $recurring->is_active) ? 'checked' : '' }} class="rounded border-sage-300 text-sage-600 focus:ring-sage-500">
                    <span class="text-xs font-semibold text-earth-700">Jadwal Aktif (tidak dijeda)</span>
                </label>
            </div>

            <!-- Notes -->
            <div class="form-section pb-4">
                <label class="form-section-title text-sm mb-2">Catatan</label>
                <textarea name="notes" rows="2" class="flora-input text-sm">{{ old('notes', $recurring->notes) }}</textarea>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 justify-end pt-4 border-t border-sage-200">
                <a href="{{ route('recurring.index') }}" class="btn-flora-secondary">Batal</a>
                <button type="submit" class="btn-flora-primary flex items-center gap-2">
                    <x-icon name="sprout" class="w-4 h-4 text-white" />
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </x-card>
</x-app-layout>
