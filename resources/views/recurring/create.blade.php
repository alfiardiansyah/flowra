<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-heading text-3xl text-sage-600 flex items-center gap-3">
                    <x-icon name="leaf-wind" class="w-8 h-8 text-sage-400" />
                    Tambah Transaksi Rutin
                </h2>
                <p class="mt-1 text-earth-600 text-sm">Daftarkan tagihan berkala atau pendapatan rutin</p>
            </div>
            <a href="{{ route('recurring.index') }}" class="btn-flora-secondary text-sm">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <x-card class="max-w-2xl mx-auto" x-data="{ type: 'expense' }">
        <form action="{{ route('recurring.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Type -->
            <div class="form-section pb-4">
                <label class="form-section-title text-sm mb-3">Jenis Transaksi Rutin</label>
                <div class="grid grid-cols-3 gap-3 p-1.5 bg-sage-50 rounded-2xl border border-sage-200">
                    <button type="button" @click="type = 'expense'" :class="type === 'expense' ? 'bg-coral-400 text-white shadow-md font-semibold' : 'text-earth-600'" class="py-2.5 px-3 rounded-xl text-xs flex items-center justify-center gap-1.5 transition-all">
                        <x-icon name="falling-leaves" class="w-4 h-4" /> Pengeluaran Rutin
                    </button>
                    <button type="button" @click="type = 'income'" :class="type === 'income' ? 'bg-leaf-400 text-white shadow-md font-semibold' : 'text-earth-600'" class="py-2.5 px-3 rounded-xl text-xs flex items-center justify-center gap-1.5 transition-all">
                        <x-icon name="sprout" class="w-4 h-4" /> Pemasukan Rutin
                    </button>
                    <button type="button" @click="type = 'transfer'" :class="type === 'transfer' ? 'bg-sky-400 text-white shadow-md font-semibold' : 'text-earth-600'" class="py-2.5 px-3 rounded-xl text-xs flex items-center justify-center gap-1.5 transition-all">
                        <x-icon name="transfer" class="w-4 h-4" /> Transfer Rutin
                    </button>
                </div>
                <input type="hidden" name="type" :value="type">
            </div>

            <!-- Description & Amount -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 form-section pb-4">
                <div>
                    <label class="form-section-title text-sm mb-2">Nama / Keterangan Tagihan</label>
                    <input type="text" name="description" value="{{ old('description') }}" required placeholder="Contoh: Langganan Netflix, Sewa Kos, Gaji" class="flora-input">
                    @error('description')
                        <p class="mt-1 text-xs text-coral-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="form-section-title text-sm mb-2">Nominal (Rp)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-lg font-bold text-sage-600">Rp</span>
                        <input type="number" name="amount" value="{{ old('amount') }}" step="0.01" min="0.01" required placeholder="0" class="flora-input pl-12 text-lg font-bold">
                    </div>
                    @error('amount')
                        <p class="mt-1 text-xs text-coral-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Frequency & Start Date -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 form-section pb-4">
                <div>
                    <label class="form-section-title text-sm mb-2">Frekuensi Pengulangan</label>
                    <select name="frequency" required class="flora-input">
                        <option value="monthly" {{ old('frequency') === 'monthly' ? 'selected' : '' }}>Bulanan (Monthly)</option>
                        <option value="weekly" {{ old('frequency') === 'weekly' ? 'selected' : '' }}>Mingguan (Weekly)</option>
                        <option value="daily" {{ old('frequency') === 'daily' ? 'selected' : '' }}>Harian (Daily)</option>
                        <option value="yearly" {{ old('frequency') === 'yearly' ? 'selected' : '' }}>Tahunan (Yearly)</option>
                    </select>
                </div>

                <div>
                    <label class="form-section-title text-sm mb-2">Mulai Tanggal / Jatuh Tempo Pertama</label>
                    <input type="date" name="start_date" value="{{ old('start_date', now()->format('Y-m-d')) }}" required class="flora-input">
                    @error('start_date')
                        <p class="mt-1 text-xs text-coral-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Account & Category -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 form-section pb-4">
                <div>
                    <label class="form-section-title text-sm mb-2">Rekening Terkait</label>
                    <select name="account_id" required class="flora-input">
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ old('account_id') == $acc->id ? 'selected' : '' }}>
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
                            <option value="{{ $cat->id }}" x-show="type === '{{ $cat->type }}'">
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div x-show="type === 'transfer'">
                    <label class="form-section-title text-sm mb-2">Rekening Tujuan</label>
                    <select name="destination_account_id" class="flora-input">
                        <option value="">-- Pilih Rekening Tujuan --</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ old('destination_account_id') == $acc->id ? 'selected' : '' }}>
                                {{ $acc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Auto-record checkbox & Notes -->
            <div class="form-section pb-4 space-y-3">
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="auto_record" value="1" {{ old('auto_record') ? 'checked' : '' }} class="rounded border-sage-300 text-sage-600 focus:ring-sage-500">
                    <span class="text-xs font-semibold text-earth-700">Auto-Record (Otomatis catat transaksi saat jatuh tempo tiba)</span>
                </label>

                <div>
                    <label class="block text-xs font-semibold text-earth-700 mb-1">Catatan Tambahan (Opsional)</label>
                    <textarea name="notes" rows="2" placeholder="Catatan khusus..." class="flora-input text-sm">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 justify-end pt-4 border-t border-sage-200">
                <a href="{{ route('recurring.index') }}" class="btn-flora-secondary">Batal</a>
                <button type="submit" class="btn-flora-primary flex items-center gap-2">
                    <x-icon name="sprout" class="w-4 h-4 text-white" />
                    <span>Daftarkan Transaksi Rutin</span>
                </button>
            </div>
        </form>
    </x-card>
</x-app-layout>
