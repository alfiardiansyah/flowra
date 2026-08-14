<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-heading text-3xl text-sage-600 flex items-center gap-3">
                    <x-icon name="edit-leaf" class="w-8 h-8 text-sage-400" />
                    Edit Transaksi
                </h2>
                <p class="mt-1 text-earth-600 text-sm">Perbarui rincian transaksi Anda</p>
            </div>
            <a href="{{ route('transactions.index') }}" class="btn-flora-secondary text-sm">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <x-card class="max-w-2xl mx-auto" x-data="{ 
        type: '{{ old('type', $transaction->type) }}',
        showAdvanced: {{ $transaction->notes || $transaction->attachment ? 'true' : 'false' }}
    }">
        <form action="{{ route('transactions.update', $transaction) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- 1. Transaction Type Toggle -->
            <div class="form-section pb-4">
                <label class="form-section-title text-sm mb-3">Jenis Transaksi</label>
                <div class="grid grid-cols-3 gap-3 p-1.5 bg-sage-50 rounded-2xl border border-sage-200">
                    <button type="button" 
                            @click="type = 'expense'"
                            :class="type === 'expense' ? 'bg-coral-400 text-white shadow-md font-semibold' : 'text-earth-600 hover:text-earth-800'"
                            class="py-3 px-4 rounded-xl text-sm transition-all duration-200 flex items-center justify-center gap-2">
                        <x-icon name="falling-leaves" class="w-5 h-5" />
                        <span>Pengeluaran</span>
                    </button>
                    <button type="button" 
                            @click="type = 'income'"
                            :class="type === 'income' ? 'bg-leaf-400 text-white shadow-md font-semibold' : 'text-earth-600 hover:text-earth-800'"
                            class="py-3 px-4 rounded-xl text-sm transition-all duration-200 flex items-center justify-center gap-2">
                        <x-icon name="sprout" class="w-5 h-5" />
                        <span>Pemasukan</span>
                    </button>
                    <button type="button" 
                            @click="type = 'transfer'"
                            :class="type === 'transfer' ? 'bg-sky-400 text-white shadow-md font-semibold' : 'text-earth-600 hover:text-earth-800'"
                            class="py-3 px-4 rounded-xl text-sm transition-all duration-200 flex items-center justify-center gap-2">
                        <x-icon name="transfer" class="w-5 h-5" />
                        <span>Transfer</span>
                    </button>
                </div>
                <input type="hidden" name="type" :value="type">
            </div>

            <!-- 2. Nominal / Amount -->
            <div class="form-section pb-4">
                <label class="form-section-title text-sm mb-2">
                    <x-icon name="tree" class="w-4 h-4 text-sage-500" />
                    Nominal Transaksi (Rp)
                </label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-2xl font-bold text-sage-600">Rp</span>
                    <input type="number" 
                           name="amount" 
                           value="{{ old('amount', $transaction->amount) }}"
                           step="0.01" 
                           min="0.01" 
                           required 
                           class="flora-input pl-14 text-2xl font-bold text-earth-800 focus:border-sage-400">
                </div>
                @error('amount')
                    <p class="mt-1 text-xs text-coral-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- 3. Category (For Income / Expense) -->
            <div x-show="type !== 'transfer'" class="form-section pb-4">
                <label class="form-section-title text-sm mb-2">
                    <x-icon name="bouquet" class="w-4 h-4 text-sage-500" />
                    Kategori
                </label>
                <select name="category_id" :disabled="type === 'transfer'" class="flora-input">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" 
                                x-show="type === '{{ $cat->type }}' || '{{ $cat->type }}' === 'both'"
                                {{ old('category_id', $transaction->category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="mt-1 text-xs text-coral-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- 4. Accounts -->
            <div class="form-section pb-4">
                <div class="grid" :class="type === 'transfer' ? 'grid-cols-1 sm:grid-cols-2 gap-4' : 'grid-cols-1'">
                    <div>
                        <label class="form-section-title text-sm mb-2">
                            <x-icon name="cash-leaf" class="w-4 h-4 text-sage-500" />
                            <span x-text="type === 'transfer' ? 'Dari Rekening (Sumber)' : 'Rekening / Dompet'"></span>
                        </label>
                        <select name="account_id" required class="flora-input">
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}" {{ old('account_id', $transaction->account_id) == $acc->id ? 'selected' : '' }}>
                                    {{ $acc->name }} (Saldo: Rp {{ number_format($acc->current_balance, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                        @error('account_id')
                            <p class="mt-1 text-xs text-coral-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-show="type === 'transfer'">
                        <label class="form-section-title text-sm mb-2">
                            <x-icon name="transfer" class="w-4 h-4 text-sky-500" />
                            <span>Ke Rekening (Tujuan)</span>
                        </label>
                        <select name="destination_account_id" :disabled="type !== 'transfer'" class="flora-input">
                            <option value="">-- Pilih Rekening Tujuan --</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}" {{ old('destination_account_id', $transaction->destination_account_id) == $acc->id ? 'selected' : '' }}>
                                    {{ $acc->name }} (Saldo: Rp {{ number_format($acc->current_balance, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                        @error('destination_account_id')
                            <p class="mt-1 text-xs text-coral-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- 5. Date & Description -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 form-section pb-4">
                <div>
                    <label class="form-section-title text-sm mb-2">
                        <x-icon name="calendar" class="w-4 h-4 text-sage-500" />
                        Tanggal
                    </label>
                    <input type="date" name="date" value="{{ old('date', $transaction->date ? $transaction->date->format('Y-m-d') : '') }}" required class="flora-input">
                    @error('date')
                        <p class="mt-1 text-xs text-coral-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="form-section-title text-sm mb-2">
                        <x-icon name="flower" class="w-4 h-4 text-sage-500" />
                        Keterangan
                    </label>
                    <input type="text" name="description" value="{{ old('description', $transaction->description) }}" required class="flora-input">
                    @error('description')
                        <p class="mt-1 text-xs text-coral-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- 6. Progressive Disclosure: Advanced Options -->
            <div>
                <button type="button" 
                        @click="showAdvanced = !showAdvanced" 
                        class="text-sm font-semibold text-sage-600 hover:text-sage-700 flex items-center gap-1.5">
                    <span x-text="showAdvanced ? '− Sembunyikan Opsi Lanjutan' : '+ Opsi Lanjutan (Catatan Tambahan & Upload Bukti)'"></span>
                </button>

                <div x-show="showAdvanced" class="mt-4 space-y-4 pt-4 border-t border-sage-200" style="{{ $transaction->notes || $transaction->attachment ? '' : 'display: none;' }}">
                    <div>
                        <label class="block text-xs font-semibold text-earth-700 mb-1">Catatan Tambahan</label>
                        <textarea name="notes" rows="3" placeholder="Tambahkan rincian detail..." class="flora-input">{{ old('notes', $transaction->notes) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-earth-700 mb-1">Bukti Struk / Transaksi</label>
                        @if($transaction->attachment)
                            <div class="mb-3 flex items-center gap-3 p-3 bg-sage-50 rounded-xl">
                                <img src="{{ asset('storage/' . $transaction->attachment) }}" alt="Attachment" class="w-16 h-16 object-cover rounded-lg border">
                                <div>
                                    <div class="text-xs font-medium text-earth-800">Bukti Terlampir</div>
                                    <a href="{{ asset('storage/' . $transaction->attachment) }}" target="_blank" class="text-xs text-sage-600 hover:underline">Lihat Gambar Asli</a>
                                </div>
                            </div>
                        @endif
                        <div class="border-2 border-dashed border-sage-300 rounded-xl p-5 text-center hover:border-sage-400 transition-colors">
                            <input type="file" name="attachment" id="attachment" accept="image/*" class="hidden" onchange="previewTxImage(this)">
                            <label for="attachment" class="cursor-pointer">
                                <x-icon name="add-seed" class="w-8 h-8 text-sage-400 mx-auto mb-2" />
                                <p class="text-xs text-earth-600">Klik untuk mengganti foto struk / bukti transfer (PNG, JPG maks 3MB)</p>
                            </label>
                            <div id="txImagePreview" class="mt-3 hidden">
                                <img id="txPreviewImg" src="" alt="Preview" class="max-w-xs max-h-48 mx-auto rounded-lg shadow-sm">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 justify-end pt-6 border-t border-sage-200">
                <a href="{{ route('transactions.index') }}" class="btn-flora-secondary">
                    Batal
                </a>
                <button type="submit" class="btn-flora-primary flex items-center gap-2">
                    <x-icon name="sprout" class="w-5 h-5 text-white" />
                    <span>Perbarui Transaksi</span>
                </button>
            </div>
        </form>
    </x-card>

    @push('scripts')
    <script>
        function previewTxImage(input) {
            const preview = document.getElementById('txImagePreview');
            const previewImg = document.getElementById('txPreviewImg');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.classList.add('hidden');
            }
        }
    </script>
    @endpush
</x-app-layout>
