@props(['accounts' => [], 'categories' => []])
<div x-data="{ 
    open: false, 
    type: 'expense', 
    showAdvanced: false,
    amount: '',
    accountId: '{{ $accounts->first()->id ?? '' }}',
    destAccountId: '{{ $accounts->skip(1)->first()->id ?? $accounts->first()->id ?? '' }}',
    categoryId: '{{ $categories->where('type', 'expense')->first()->id ?? '' }}',
    date: '{{ now()->format('Y-m-d') }}',
    description: '',
    notes: '',
    setType(newType) {
        this.type = newType;
        if (newType === 'expense') {
            const firstExp = '{{ $categories->where('type', 'expense')->first()->id ?? '' }}';
            if (firstExp) this.categoryId = firstExp;
        } else if (newType === 'income') {
            const firstInc = '{{ $categories->where('type', 'income')->first()->id ?? '' }}';
            if (firstInc) this.categoryId = firstInc;
        }
    }
}"
@open-quick-transaction.window="open = true; setType($event.detail?.type || 'expense')"
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
                    <x-icon name="add-seed" class="w-6 h-6 text-sage-500" />
                    <h3 class="font-heading text-xl text-sage-700">Catat Transaksi Cepat</h3>
                </div>
                <button type="button" @click="open = false" class="text-earth-400 hover:text-earth-600 p-1 rounded-lg hover:bg-sage-50 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <!-- Transaction Type Toggle -->
                <div class="grid grid-cols-3 gap-2 p-1.5 bg-sage-50 rounded-xl border border-sage-200">
                    <button type="button" 
                            @click="setType('expense')"
                            :class="type === 'expense' ? 'bg-coral-400 text-white shadow-md font-semibold' : 'text-earth-600 hover:text-earth-800'"
                            class="py-2 px-3 rounded-lg text-sm transition-all duration-200 flex items-center justify-center gap-1.5">
                        <x-icon name="falling-leaves" class="w-4 h-4" />
                        <span>Pengeluaran</span>
                    </button>
                    <button type="button" 
                            @click="setType('income')"
                            :class="type === 'income' ? 'bg-leaf-400 text-white shadow-md font-semibold' : 'text-earth-600 hover:text-earth-800'"
                            class="py-2 px-3 rounded-lg text-sm transition-all duration-200 flex items-center justify-center gap-1.5">
                        <x-icon name="sprout" class="w-4 h-4" />
                        <span>Pemasukan</span>
                    </button>
                    <button type="button" 
                            @click="setType('transfer')"
                            :class="type === 'transfer' ? 'bg-sky-400 text-white shadow-md font-semibold' : 'text-earth-600 hover:text-earth-800'"
                            class="py-2 px-3 rounded-lg text-sm transition-all duration-200 flex items-center justify-center gap-1.5">
                        <x-icon name="transfer" class="w-4 h-4" />
                        <span>Transfer</span>
                    </button>
                </div>
                <input type="hidden" name="type" :value="type">

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
                               class="flora-input pl-12 text-xl font-bold text-earth-800 focus:border-sage-400"
                               autofocus>
                    </div>
                </div>

                <!-- Category (For Expense and Income) -->
                <div x-show="type !== 'transfer'">
                    <label class="block text-xs font-semibold text-earth-700 uppercase tracking-wider mb-1">Kategori</label>
                    <select name="category_id" x-model="categoryId" :disabled="type === 'transfer'" class="flora-input text-sm">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" 
                                    x-show="type === '{{ $cat->type }}' || '{{ $cat->type }}' === 'both'">
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Accounts -->
                <div class="grid" :class="type === 'transfer' ? 'grid-cols-2 gap-3' : 'grid-cols-1'">
                    <div>
                        <label class="block text-xs font-semibold text-earth-700 uppercase tracking-wider mb-1" x-text="type === 'transfer' ? 'Dari Rekening' : 'Rekening / Dompet'"></label>
                        <select name="account_id" x-model="accountId" required class="flora-input text-sm">
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->name }} (Rp {{ number_format($acc->current_balance, 0, ',', '.') }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div x-show="type === 'transfer'">
                        <label class="block text-xs font-semibold text-earth-700 uppercase tracking-wider mb-1">Ke Rekening</label>
                        <select name="destination_account_id" x-model="destAccountId" :disabled="type !== 'transfer'" class="flora-input text-sm">
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Date & Description -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-earth-700 uppercase tracking-wider mb-1">Tanggal</label>
                        <input type="date" name="date" x-model="date" required class="flora-input text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-earth-700 uppercase tracking-wider mb-1">Keterangan Singkat</label>
                        <input type="text" name="description" x-model="description" required placeholder="Contoh: Makan siang, Gaji, dll" class="flora-input text-sm">
                    </div>
                </div>

                <!-- Progressive Disclosure: Advanced Options -->
                <div class="pt-2">
                    <button type="button" 
                            @click="showAdvanced = !showAdvanced" 
                            class="text-xs font-medium text-sage-600 hover:text-sage-700 flex items-center gap-1">
                        <span x-text="showAdvanced ? '− Sembunyikan Opsi Lanjutan' : '+ Opsi Lanjutan (Catatan & Bukti)'"></span>
                    </button>

                    <div x-show="showAdvanced" class="mt-3 space-y-3 pt-2 border-t border-sage-100" style="display: none;">
                        <div>
                            <label class="block text-xs font-semibold text-earth-700 mb-1">Catatan Tambahan</label>
                            <textarea name="notes" x-model="notes" rows="2" placeholder="Catatan detail..." class="flora-input text-sm"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-earth-700 mb-1">Upload Bukti Transaksi (Opsional)</label>
                            <input type="file" name="attachment" accept="image/*" class="text-xs text-earth-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-sage-100 file:text-sage-700 hover:file:bg-sage-200">
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-3 justify-end pt-4 border-t border-sage-100">
                    <button type="button" @click="open = false" class="btn-flora-secondary text-sm py-2 px-4">Batal</button>
                    <button type="submit" class="btn-flora-primary text-sm py-2 px-6 flex items-center gap-2">
                        <x-icon name="sprout" class="w-4 h-4" />
                        <span>Simpan Transaksi</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
