@props(['accounts' => []])
<div x-data="{ 
    open: false, 
    type: 'debt', 
    amount: '',
    personName: '',
    accountId: '',
    date: '{{ now()->format('Y-m-d') }}',
    dueDate: '',
    notes: '',
    showAdvanced: false,
    setType(newType) {
        this.type = newType;
    }
}"
@open-debt-modal.window="open = true; if ($event.detail?.type) type = $event.detail.type;"
@keydown.escape.window="open = false"
class="relative z-50">
    <!-- Backdrop -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-earth-900/60 backdrop-blur-sm"
         @click="open = false"
         style="display: none;"></div>

    <!-- Modal Content -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         class="fixed inset-0 z-10 overflow-y-auto flex items-center justify-center p-4 sm:p-6"
         style="display: none;">

        <div class="flora-card max-w-lg w-full bg-white shadow-2xl relative border-2 border-sage-200" @click.stop>
            <div class="flex items-center justify-between pb-4 border-b border-sage-100 mb-5">
                <div class="flex items-center gap-2">
                    <x-icon name="leaf" class="w-6 h-6 text-sage-500" />
                    <h3 class="font-heading text-xl text-sage-700">Catat Hutang & Piutang</h3>
                </div>
                <button type="button" @click.stop.prevent="open = false" aria-label="Tutup" class="text-earth-400 hover:text-earth-600 p-1.5 rounded-lg hover:bg-sage-100 transition-colors cursor-pointer relative z-20">
                    <svg class="w-6 h-6 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('debts.store') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Type Toggle -->
                <div class="grid grid-cols-2 gap-2 p-1.5 bg-sage-50 rounded-xl border border-sage-200">
                    <button type="button" 
                            @click="setType('debt')"
                            :class="type === 'debt' ? 'bg-coral-500 text-white shadow-md font-semibold' : 'text-earth-600 hover:text-earth-800'"
                            class="py-2.5 px-3 rounded-lg text-xs sm:text-sm transition-all duration-200 flex items-center justify-center gap-1.5">
                        <x-icon name="falling-leaves" class="w-4 h-4" />
                        <span>Hutang (Saya Berhutang)</span>
                    </button>
                    <button type="button" 
                            @click="setType('receivable')"
                            :class="type === 'receivable' ? 'bg-leaf-500 text-white shadow-md font-semibold' : 'text-earth-600 hover:text-earth-800'"
                            class="py-2.5 px-3 rounded-lg text-xs sm:text-sm transition-all duration-200 flex items-center justify-center gap-1.5">
                        <x-icon name="sprout" class="w-4 h-4" />
                        <span>Piutang (Dipinjam Orang)</span>
                    </button>
                </div>
                <input type="hidden" name="type" :value="type">

                <!-- Person Name -->
                <div>
                    <label class="block text-xs font-semibold text-earth-700 uppercase tracking-wider mb-1" x-text="type === 'debt' ? 'Hutang Kepada (Nama Orang / Bank)' : 'Piutang Dari (Nama Peminjam)'"></label>
                    <input type="text" 
                           name="person_name" 
                           x-model="personName"
                           required 
                           :placeholder="type === 'debt' ? 'Contoh: Budi, Bank BCA, Ayah' : 'Contoh: Andi, Rina, Rekan Kerja'" 
                           class="flora-input text-sm font-semibold">
                </div>

                <!-- Amount -->
                <div>
                    <label class="block text-xs font-semibold text-earth-700 uppercase tracking-wider mb-1">Nominal (Rp)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-lg font-bold text-sage-600">Rp</span>
                        <input type="number" 
                               name="amount" 
                               x-model="amount"
                               placeholder="0" 
                               step="0.01" 
                               min="1" 
                               required 
                               class="flora-input pl-12 text-xl font-bold text-earth-800 focus:border-sage-400">
                    </div>
                </div>

                <!-- Account Association (Optional) -->
                <div>
                    <label class="block text-xs font-semibold text-earth-700 uppercase tracking-wider mb-1">
                        <span x-text="type === 'debt' ? 'Rekening Penerima Uang (Opsional)' : 'Rekening Sumber Uang (Opsional)'"></span>
                    </label>
                    <select name="account_id" x-model="accountId" class="flora-input text-sm">
                        <option value="">-- Tanpa Penyesuaian Saldo Rekening --</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }} (Rp {{ number_format($acc->current_balance, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-earth-500 mt-1" x-text="type === 'debt' ? 'Jika dipilih, saldo rekening ini akan bertambah saat pinjaman diterima.' : 'Jika dipilih, saldo rekening ini akan berkurang saat memberi pinjaman.'"></p>
                </div>

                <!-- Date & Due Date -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-earth-700 uppercase tracking-wider mb-1">Tanggal Pinjam</label>
                        <input type="date" name="date" x-model="date" required class="flora-input text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-earth-700 uppercase tracking-wider mb-1">Jatuh Tempo (Opsional)</label>
                        <input type="date" name="due_date" x-model="dueDate" class="flora-input text-sm">
                    </div>
                </div>

                <!-- Progressive Disclosure: Notes -->
                <div class="pt-1">
                    <button type="button" 
                            @click="showAdvanced = !showAdvanced" 
                            class="text-xs font-medium text-sage-600 hover:text-sage-700 flex items-center gap-1">
                        <span x-text="showAdvanced ? '− Sembunyikan Catatan' : '+ Tambah Catatan Detail'"></span>
                    </button>

                    <div x-show="showAdvanced" class="mt-2 space-y-2 pt-2 border-t border-sage-100" style="display: none;">
                        <div>
                            <label class="block text-xs font-semibold text-earth-700 mb-1">Catatan / Keterangan Tambahan</label>
                            <textarea name="notes" x-model="notes" rows="2" placeholder="Catatan detail pinjaman..." class="flora-input text-sm"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-3 justify-end pt-4 border-t border-sage-100">
                    <button type="button" @click="open = false" class="btn-flora-secondary text-sm py-2 px-4">Batal</button>
                    <button type="submit" class="btn-flora-primary text-sm py-2 px-6 flex items-center gap-2">
                        <x-icon name="sprout" class="w-4 h-4 text-white" />
                        <span>Simpan Catatan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
