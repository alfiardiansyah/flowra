<div x-data="{ 
    open: false, 
    loading: false,
    transaction: null,
    fetchDetails(id) {
        this.open = true;
        this.loading = true;
        this.transaction = null;
        fetch('/transactions/' + id, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => {
            if (!res.ok) throw new Error('Akses tidak diizinkan atau data tidak ditemukan.');
            return res.json();
        })
        .then(data => {
            this.loading = false;
            if (data.success) {
                this.transaction = data.transaction;
            }
        })
        .catch(err => {
            this.loading = false;
            alert(err.message || 'Gagal memuat detail transaksi');
            this.open = false;
        });
    }
}"
@open-transaction-detail.window="fetchDetails($event.detail.id)"
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

        <div class="flora-card max-w-lg w-full bg-white shadow-2xl relative border-2 border-sage-200 p-6" @click.stop>
            <div class="flex items-center justify-between pb-4 border-b border-sage-100 mb-4">
                <div class="flex items-center gap-2">
                    <x-icon name="flower-bloom" class="w-6 h-6 text-sage-500" />
                    <h3 class="font-heading text-xl text-sage-700">Rincian Transaksi</h3>
                </div>
                <button type="button" @click.stop.prevent="open = false" aria-label="Tutup" class="text-earth-400 hover:text-earth-600 p-1.5 rounded-lg hover:bg-sage-100 transition-colors cursor-pointer relative z-20">
                    <svg class="w-6 h-6 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Loading Spinner -->
            <div x-show="loading" class="py-12 flex flex-col items-center justify-center text-earth-500">
                <svg class="animate-spin h-8 w-8 text-sage-600 mb-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-xs">Memuat data transaksi...</span>
            </div>

            <!-- Transaction Details Body -->
            <template x-if="!loading && transaction">
                <div class="space-y-4">
                    <div class="flex items-start justify-between p-3.5 rounded-xl bg-sage-50/70 border border-sage-100">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md"
                                  :class="{
                                      'bg-mint-100 text-leaf-700': transaction.type === 'income',
                                      'bg-coral-100 text-coral-700': transaction.type === 'expense',
                                      'bg-sky-100 text-sky-700': transaction.type === 'transfer'
                                  }" x-text="transaction.type_label">
                            </span>
                            <h4 class="font-heading text-lg font-bold text-earth-800 mt-1" x-text="transaction.description"></h4>
                            <div class="text-xs text-earth-500 mt-0.5" x-text="transaction.date"></div>
                        </div>

                        <div class="text-right">
                            <div class="text-xl font-bold" 
                                 :class="{
                                     'text-leaf-600': transaction.type === 'income',
                                     'text-coral-600': transaction.type === 'expense',
                                     'text-sky-600': transaction.type === 'transfer'
                                 }" x-text="transaction.formatted_amount">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="p-3 rounded-lg border border-sage-100 bg-white">
                            <span class="text-[10px] text-earth-400 font-bold uppercase block mb-1">Kategori</span>
                            <div class="font-semibold text-earth-800" x-text="transaction.category_name"></div>
                        </div>
                        <div class="p-3 rounded-lg border border-sage-100 bg-white">
                            <span class="text-[10px] text-earth-400 font-bold uppercase block mb-1">Rekening</span>
                            <div class="font-semibold text-earth-800" x-text="transaction.type === 'transfer' ? (transaction.account_name + ' → ' + transaction.destination_account_name) : transaction.account_name"></div>
                        </div>
                    </div>

                    <template x-if="transaction.notes">
                        <div class="p-3 rounded-lg border border-earth-100 bg-earth-50/50 text-xs">
                            <span class="text-[10px] text-earth-400 font-bold uppercase block mb-0.5">Catatan</span>
                            <p class="text-earth-700 italic" x-text="transaction.notes"></p>
                        </div>
                    </template>

                    <div class="text-[10px] text-earth-400 pt-1">
                        Dicatat pada: <span x-text="transaction.created_at"></span>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-4 border-t border-sage-100">
                        <button type="button" @click="open = false" class="btn-flora-secondary text-xs py-1.5 px-3">Tutup</button>
                        <a :href="transaction.edit_url" class="btn-flora-secondary text-xs py-1.5 px-3 text-sage-700 flex items-center gap-1">
                            <x-icon name="edit-leaf" class="w-3.5 h-3.5" />
                            <span>Edit</span>
                        </a>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
