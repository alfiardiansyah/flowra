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

    <div x-data="{ 
        showDeleteModal: false, 
        deleteAction: '{{ $otherAccounts->count() > 0 ? 'reassign' : 'cascade' }}', 
        targetAccountId: '{{ $otherAccounts->first()->id ?? '' }}' 
    }">
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
                    <button type="button" 
                            @click="showDeleteModal = true"
                            class="text-xs font-semibold text-coral-600 hover:text-coral-800 hover:underline flex items-center gap-1">
                        <x-icon name="trash" class="w-3.5 h-3.5" />
                        <span>Hapus Rekening</span>
                    </button>

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

        <!-- Delete Account Confirmation Modal -->
        <div x-show="showDeleteModal" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <!-- Backdrop -->
            <div x-show="showDeleteModal" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-earth-900/60 backdrop-blur-sm transition-opacity" 
                 @click="showDeleteModal = false"></div>

            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="showDeleteModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg p-6 border border-sage-100">

                    <form action="{{ route('accounts.destroy', $account) }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <div class="flex items-start gap-4 mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-coral-100 text-coral-600 flex items-center justify-center shrink-0">
                                <x-icon name="trash" class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-xl font-bold font-heading text-earth-900" id="modal-title">
                                    Hapus Rekening "{{ $account->name }}"
                                </h3>
                                <p class="text-xs text-earth-600 mt-1">
                                    @if($transactionCount == 0)
                                        Rekening ini <span class="font-semibold text-leaf-700">belum memiliki transaksi</span>.
                                    @else
                                        Rekening ini memiliki <span class="font-bold text-coral-600">{{ $transactionCount }} transaksi</span> terikat.
                                    @endif
                                </p>
                            </div>
                        </div>

                        @if($transactionCount == 0)
                            <div class="bg-sage-50 p-4 rounded-2xl border border-sage-200 mb-6 text-xs text-earth-700">
                                <p>Apakah Anda yakin ingin menghapus rekening ini? Karena belum ada transaksi terkait, akun ini akan langsung dihapus permanen dari sistem.</p>
                            </div>
                        @else
                            <div class="space-y-3 mb-6">
                                <label class="text-xs font-semibold text-earth-800 block">Pilih Tindakan Penghapusan:</label>

                                @if($otherAccounts->count() > 0)
                                    <!-- Option 1: Reassign -->
                                    <div class="p-3.5 rounded-2xl border transition-all cursor-pointer"
                                         :class="deleteAction === 'reassign' ? 'border-sage-500 bg-sage-50/70 ring-2 ring-sage-200' : 'border-sage-200 bg-white hover:bg-sage-50/30'"
                                         @click="deleteAction = 'reassign'">
                                        <div class="flex items-start gap-3">
                                            <input type="radio" name="action" value="reassign" x-model="deleteAction" class="mt-0.5 text-sage-600 focus:ring-sage-500">
                                            <div class="flex-1 text-xs">
                                                <div class="font-bold text-earth-800">Pindahkan Transaksi ke Rekening Lain & Hapus Rekening</div>
                                                <div class="text-earth-600 text-[11px] mt-0.5">Seluruh {{ $transactionCount }} transaksi akan dipindahkan ke rekening tujuan. Keuangan Anda tidak akan hilang.</div>

                                                <div x-show="deleteAction === 'reassign'" class="mt-3">
                                                    <label class="block text-[11px] font-semibold text-sage-800 mb-1">Pilih Rekening Tujuan Pemindahan:</label>
                                                    <select name="target_account_id" x-model="targetAccountId" class="flora-input text-xs py-2 px-3">
                                                        @foreach($otherAccounts as $other)
                                                            <option value="{{ $other->id }}">{{ $other->name }} (Saldo: Rp {{ number_format($other->current_balance, 0, ',', '.') }})</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Option 2: Cascade Delete -->
                                <div class="p-3.5 rounded-2xl border transition-all cursor-pointer"
                                     :class="deleteAction === 'cascade' ? 'border-coral-500 bg-coral-50/50 ring-2 ring-coral-200' : 'border-sage-200 bg-white hover:bg-coral-50/20'"
                                     @click="deleteAction = 'cascade'">
                                    <div class="flex items-start gap-3">
                                        <input type="radio" name="action" value="cascade" x-model="deleteAction" class="mt-0.5 text-coral-600 focus:ring-coral-500">
                                        <div class="text-xs">
                                            <div class="font-bold text-coral-700">Hapus Rekening Beserta Seluruh Transaksinya Permanen</div>
                                            <div class="text-earth-600 text-[11px] mt-0.5">PERINGATAN: Akun dan seluruh {{ $transactionCount }} transaksi terkait akan dihapus secara permanen dari database.</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Option 3: Deactivate / Archive -->
                                <div class="p-3.5 rounded-2xl border transition-all cursor-pointer"
                                     :class="deleteAction === 'deactivate' ? 'border-earth-400 bg-earth-50 ring-2 ring-earth-200' : 'border-sage-200 bg-white hover:bg-earth-50/30'"
                                     @click="deleteAction = 'deactivate'">
                                    <div class="flex items-start gap-3">
                                        <input type="radio" name="action" value="deactivate" x-model="deleteAction" class="mt-0.5 text-earth-600 focus:ring-earth-500">
                                        <div class="text-xs">
                                            <div class="font-bold text-earth-800">Nonaktifkan Rekening Saja (Arsip)</div>
                                            <div class="text-earth-600 text-[11px] mt-0.5">Rekening akan disembunyikan dari transaksi baru, tetapi data transaksi sejarah tetap utuh.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-end gap-3 pt-3 border-t border-sage-100">
                            <button type="button" 
                                    @click="showDeleteModal = false"
                                    class="btn-flora-secondary text-xs">
                                Batal
                            </button>

                            <button type="submit" 
                                    class="btn-flora-primary text-xs !bg-coral-600 hover:!bg-coral-700 !border-coral-600 flex items-center gap-1.5 shadow-md">
                                <x-icon name="trash" class="w-3.5 h-3.5 text-white" />
                                <span>Konfirmasi Hapus</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
