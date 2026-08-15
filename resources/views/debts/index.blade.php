<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-heading text-3xl text-sage-600 flex items-center gap-3">
                    <x-icon name="leaf" class="w-8 h-8 text-sage-400" />
                    Hutang & Piutang
                </h2>
                <p class="mt-1 text-earth-600 text-sm">Pantau kewajiban pinjaman dan tagihan kepada orang lain dengan rapi</p>
            </div>
            <div>
                <button @click="$dispatch('open-debt-modal', { type: 'debt' })" class="btn-flora-primary text-sm flex items-center gap-2 shadow-sm py-2 px-4">
                    <x-icon name="leaf" class="w-4 h-4 text-white" />
                    <span>+ Catat Hutang & Piutang</span>
                </button>
            </div>
        </div>
    </x-slot>

    <!-- Outer Alpine Container for Long-Press, Action Sheet & Modals -->
    <div x-data="{
        actionSheetOpen: false,
        activeDebt: null,

        paymentModalOpen: false,
        deleteModalOpen: false,

        pressTimer: null,
        isLongPress: false,

        getDebtFromEl(el) {
            try {
                const targetEl = (el && el.hasAttribute && el.hasAttribute('data-debt')) ? el : el.closest('[data-debt]');
                if (!targetEl) return null;
                const b64 = targetEl.getAttribute('data-debt');
                if (!b64) return null;
                return JSON.parse(atob(b64));
            } catch(e) {
                console.error('Error parsing debt base64 data:', e);
                return null;
            }
        },

        startPress(el) {
            const debt = this.getDebtFromEl(el);
            if (!debt) return;
            this.isLongPress = false;
            clearTimeout(this.pressTimer);
            this.pressTimer = setTimeout(() => {
                this.isLongPress = true;
                if (navigator.vibrate) {
                    try { navigator.vibrate(40); } catch(e) {}
                }
                this.openActionSheet(debt);
            }, 450);
        },

        cancelPress() {
            clearTimeout(this.pressTimer);
        },

        handleCardClick(el, e) {
            if (e && e.target && e.target.closest('a, button')) {
                return;
            }
            const debt = this.getDebtFromEl(el);
            if (!debt) return;
            if (!this.isLongPress) {
                window.location.href = debt.show_url;
            }
            this.isLongPress = false;
        },

        openActionSheet(debt) {
            if (!debt) return;
            this.activeDebt = debt;
            this.actionSheetOpen = true;
        },

        openPaymentModal(debt) {
            if (!debt) return;
            this.activeDebt = debt;
            this.actionSheetOpen = false;
            this.paymentModalOpen = true;
        },

        confirmDelete(debt) {
            if (!debt) return;
            this.activeDebt = debt;
            this.actionSheetOpen = false;
            this.deleteModalOpen = true;
        }
    }">

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-8">
            <x-card variant="summary" class="border-l-4 border-l-coral-400">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs text-earth-500 font-medium">Sisa Hutang Saya (Kewajiban)</div>
                        <div class="text-2xl font-bold text-coral-600 mt-1">
                            Rp {{ number_format($totalDebt, 0, ',', '.') }}
                        </div>
                        <p class="text-xs text-earth-500 mt-1">Uang yang harus Anda kembalikan ke orang lain</p>
                    </div>
                    <x-icon name="falling-leaves" class="w-8 h-8 text-coral-400 opacity-80" />
                </div>
            </x-card>

            <x-card variant="summary" class="border-l-4 border-l-leaf-400">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs text-earth-500 font-medium">Sisa Piutang Saya (Tagihan)</div>
                        <div class="text-2xl font-bold text-leaf-600 mt-1">
                            Rp {{ number_format($totalReceivable, 0, ',', '.') }}
                        </div>
                        <p class="text-xs text-earth-500 mt-1">Uang orang lain yang dipinjam dari Anda</p>
                    </div>
                    <x-icon name="sprout" class="w-8 h-8 text-leaf-500 opacity-80" />
                </div>
            </x-card>
        </div>

        <!-- Type Tabs -->
        <div class="flex gap-2 mb-6 overflow-x-auto pb-1">
            <a href="{{ route('debts.index', ['type' => 'all']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition-all whitespace-nowrap {{ $type === 'all' ? 'bg-sage-600 text-white shadow-sm' : 'bg-white text-earth-600 hover:bg-sage-50 border border-sage-200' }}">
                Semua Catatan
            </a>
            <a href="{{ route('debts.index', ['type' => 'debt']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition-all whitespace-nowrap {{ $type === 'debt' ? 'bg-coral-500 text-white shadow-sm' : 'bg-white text-earth-600 hover:bg-coral-50 hover:text-coral-600 border border-sage-200' }}">
                Hutang (Saya Berhutang)
            </a>
            <a href="{{ route('debts.index', ['type' => 'receivable']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition-all whitespace-nowrap {{ $type === 'receivable' ? 'bg-leaf-500 text-white shadow-sm' : 'bg-white text-earth-600 hover:bg-mint-50 hover:text-leaf-600 border border-sage-200' }}">
                Piutang (Orang Lain Berhutang)
            </a>
        </div>

        <!-- Debts Grid -->
        @if($items->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($items as $debt)
                    @php
                        $debtDataData = [
                            'id' => $debt->id,
                            'type' => $debt->type,
                            'person_name' => $debt->person_name,
                            'amount_formatted' => 'Rp ' . number_format($debt->amount, 0, ',', '.'),
                            'remaining_formatted' => 'Rp ' . number_format($debt->remaining_amount, 0, ',', '.'),
                            'raw_remaining' => $debt->remaining_amount,
                            'paid_formatted' => 'Rp ' . number_format($debt->paid_amount, 0, ',', '.'),
                            'status' => $debt->status,
                            'show_url' => route('debts.show', $debt),
                            'edit_url' => route('debts.edit', $debt),
                            'delete_url' => route('debts.destroy', $debt),
                            'payment_url' => route('debts.payment', $debt),
                        ];
                        $debtB64 = base64_encode(json_encode($debtDataData));
                    @endphp

                    <div class="flora-card p-5 sm:p-6 border-l-4 transition-all duration-300 hover:shadow-flora-lg hover:-translate-y-1 relative cursor-pointer select-none active:scale-[0.99] flex flex-col justify-between {{ $debt->status === 'paid' ? 'opacity-70 bg-gray-50' : '' }}"
                         style="border-left-color: {{ $debt->type === 'debt' ? '#FF6B6B' : '#6B8E23' }};"
                         data-debt="{{ $debtB64 }}"
                         @touchstart="startPress($el)"
                         @touchend="cancelPress()"
                         @touchmove="cancelPress()"
                         @mousedown="startPress($el)"
                         @mouseup="cancelPress()"
                         @mouseleave="cancelPress()"
                         @contextmenu.prevent
                         @click="handleCardClick($el, $event)">
                        
                        <div>
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <span class="flora-badge {{ $debt->status_badge_class }} text-[10px] py-0.5 px-2 mb-1">
                                        {{ $debt->status === 'paid' ? 'LUNAS' : ($debt->status === 'partially_paid' ? 'DIBAYAR SEBAGIAN' : 'BELUM DIBAYAR') }}
                                    </span>
                                    <h3 class="font-heading text-lg font-bold text-earth-800 leading-tight">{{ $debt->person_name }}</h3>
                                    <div class="text-xs text-earth-500 font-medium">
                                        {{ $debt->type === 'debt' ? 'Hutang kepada' : 'Dipinjam oleh' }} {{ $debt->person_name }}
                                    </div>
                                </div>
                            </div>

                            <!-- Amount & Remaining -->
                            <div class="my-3">
                                <div class="text-xs text-earth-500 font-medium">Sisa Nominal</div>
                                <div class="text-2xl font-bold {{ $debt->type === 'debt' ? 'text-coral-600' : 'text-leaf-600' }}">
                                    Rp {{ number_format($debt->remaining_amount, 0, ',', '.') }}
                                </div>
                                <div class="text-[11px] text-earth-500 mt-0.5">
                                    Total: Rp {{ number_format($debt->amount, 0, ',', '.') }} • Terbayar: Rp {{ number_format($debt->paid_amount, 0, ',', '.') }}
                                </div>
                            </div>

                            <!-- Mini Progress Bar -->
                            <div class="w-full bg-sage-100 rounded-full h-2 overflow-hidden my-3">
                                <div class="h-2 rounded-full transition-all duration-300"
                                     style="width: {{ $debt->percentage_paid }}%; background-color: {{ $debt->type === 'debt' ? '#FF6B6B' : '#6B8E23' }};"></div>
                            </div>

                            <!-- Due Date Info -->
                            <div class="text-xs text-earth-600 flex items-center justify-between py-1">
                                <span>Tanggal Pinjam:</span>
                                <span class="font-medium text-earth-800">{{ $debt->date ? $debt->date->format('d M Y') : '-' }}</span>
                            </div>
                            <div class="text-xs text-earth-600 flex items-center justify-between py-1">
                                <span>Jatuh Tempo:</span>
                                <span class="font-semibold {{ $debt->due_date && $debt->due_date->isPast() && $debt->status !== 'paid' ? 'text-coral-600 font-bold' : 'text-earth-800' }}">
                                    {{ $debt->due_date ? $debt->due_date->format('d M Y') : 'Tidak ditentukan' }}
                                </span>
                            </div>
                        </div>

                        <!-- Actions Bottom -->
                        <div class="pt-4 border-t border-sage-100 mt-3 flex items-center justify-between">
                            <span class="text-xs font-semibold text-sage-600 hover:text-sage-800 flex items-center gap-1">
                                <span>Rincian Pembayaran ({{ $debt->payments->count() }})</span>
                                <x-icon name="chevron-right" class="w-3 h-3" />
                            </span>

                            @if($debt->status !== 'paid')
                                <div @click.stop>
                                    <button type="button" 
                                            @click="openPaymentModal(getDebtFromEl($el))" 
                                            class="btn-flora-primary text-xs py-1.5 px-3 shadow-sm cursor-pointer">
                                        + Bayar / Cicil
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="flora-card p-8 sm:p-12 text-center max-w-lg mx-auto">
                <x-icon name="leaf" class="w-12 h-12 text-sage-400 mx-auto mb-3" />
                <h3 class="font-heading text-xl font-bold text-sage-700 mb-1">Belum Ada Catatan Hutang / Piutang</h3>
                <p class="text-xs sm:text-sm text-earth-600 mb-6">Catat pinjaman yang Anda berikan atau terima untuk melacak tanggal jatuh tempo dan pembayarannya.</p>
                <div>
                    <button @click="$dispatch('open-debt-modal', { type: 'debt' })" class="btn-flora-primary text-xs sm:text-sm py-2.5 px-6 flex items-center gap-2 mx-auto shadow-sm">
                        <x-icon name="leaf" class="w-4 h-4 text-white" />
                        <span>+ Catat Hutang & Piutang Baru</span>
                    </button>
                </div>
            </div>
        @endif

        <!-- ================= 1. MOBILE CONTEXT ACTION SHEET (Popup Tekan Lama) ================= -->
        <div x-show="actionSheetOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div x-show="actionSheetOpen" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-earth-900/60 backdrop-blur-sm transition-opacity" @click="actionSheetOpen = false"></div>

            <div class="flex min-h-full items-end justify-center p-0 text-center sm:items-center sm:p-4">
                <div x-show="actionSheetOpen"
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-t-3xl sm:rounded-3xl bg-white text-left shadow-2xl transition-all w-full sm:max-w-sm p-5 border border-sage-100">

                    <div x-show="activeDebt">
                        <!-- Pull bar indicator for mobile -->
                        <div class="w-12 h-1 bg-sage-200 rounded-full mx-auto mb-4 sm:hidden"></div>

                        <div class="text-center pb-3 border-b border-sage-100 mb-4">
                            <div class="text-xs text-earth-500 font-medium">Pilih Aksi Pinjaman</div>
                            <div class="font-bold text-earth-900 text-base mt-0.5" x-text="activeDebt ? activeDebt.person_name : ''"></div>
                            <div class="text-xs font-bold mt-0.5"
                                 :class="activeDebt && activeDebt.type === 'debt' ? 'text-coral-600' : 'text-leaf-600'"
                                 x-text="activeDebt ? activeDebt.remaining_formatted : ''"></div>
                        </div>

                        <div class="space-y-2">
                            <!-- Option 1: View Details / Show -->
                            <a :href="activeDebt ? activeDebt.show_url : '#'" 
                               class="w-full p-3.5 rounded-2xl bg-sage-50 hover:bg-sage-100 text-sage-800 text-xs font-bold flex items-center justify-between transition-colors">
                                <span class="flex items-center gap-2.5">
                                    <span class="text-base">👁️</span>
                                    <span>Lihat Rincian & Riwayat</span>
                                </span>
                                <span class="text-sage-400">→</span>
                            </a>

                            <!-- Option 2: Payment (if not paid) -->
                            <div x-show="activeDebt && activeDebt.status !== 'paid'">
                                <button type="button" 
                                        @click="openPaymentModal(activeDebt)"
                                        class="w-full p-3.5 rounded-2xl bg-mint-50 hover:bg-mint-100 text-leaf-800 text-xs font-bold flex items-center justify-between transition-colors">
                                    <span class="flex items-center gap-2.5">
                                        <x-icon name="sprout" class="w-4 h-4 text-leaf-600" />
                                        <span>Bayar / Cicil Pinjaman</span>
                                    </span>
                                    <span class="text-leaf-400">→</span>
                                </button>
                            </div>

                            <!-- Option 3: Edit Record -->
                            <a :href="activeDebt ? activeDebt.edit_url : '#'" 
                               class="w-full p-3.5 rounded-2xl bg-sky-50 hover:bg-sky-100 text-sky-800 text-xs font-bold flex items-center justify-between transition-colors">
                                <span class="flex items-center gap-2.5">
                                    <x-icon name="edit-leaf" class="w-4 h-4 text-sky-600" />
                                    <span>Edit Catatan Pinjaman</span>
                                </span>
                                <span class="text-sky-400">→</span>
                            </a>

                            <!-- Option 4: Delete Record -->
                            <button type="button" 
                                    @click="confirmDelete(activeDebt)"
                                    class="w-full p-3.5 rounded-2xl bg-coral-50 hover:bg-coral-100 text-coral-700 text-xs font-bold flex items-center justify-between transition-colors">
                                <span class="flex items-center gap-2.5">
                                    <x-icon name="delete-wilt" class="w-4 h-4 text-coral-600" />
                                    <span>Hapus Catatan Pinjaman</span>
                                </span>
                                <span class="text-coral-400">→</span>
                            </button>
                        </div>

                        <button type="button" @click="actionSheetOpen = false" class="w-full text-center text-xs font-semibold text-earth-500 hover:text-earth-700 mt-4 py-2">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= 2. UNIVERSAL PAYMENT MODAL ================= -->
        <div x-show="paymentModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4" role="dialog" aria-modal="true">
            <div x-show="paymentModalOpen" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-earth-900/60 backdrop-blur-sm transition-opacity" @click="paymentModalOpen = false"></div>

            <div x-show="paymentModalOpen"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="flora-card max-w-md w-full bg-white shadow-2xl relative z-10 p-6 border border-sage-200">

                <div x-show="activeDebt">
                    <div class="flex items-center justify-between pb-3 border-b border-sage-100 mb-3">
                        <h3 class="font-heading text-lg font-semibold text-sage-700">
                            Catat Pembayaran <span x-text="activeDebt ? activeDebt.person_name : ''"></span>
                        </h3>
                        <button type="button" @click="paymentModalOpen = false" class="text-earth-400 hover:text-earth-600 p-1 rounded-lg hover:bg-sage-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <p class="text-xs text-earth-600 mb-4">
                        Sisa kewajiban yang harus diselesaikan: <span class="font-bold text-earth-800" x-text="activeDebt ? activeDebt.remaining_formatted : ''"></span>
                    </p>

                    <form :action="activeDebt ? activeDebt.payment_url : '#'" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-earth-700 mb-1">Nominal Pembayaran (Rp)</label>
                            <input type="number" name="amount" :value="activeDebt ? activeDebt.raw_remaining : ''" :max="activeDebt ? activeDebt.raw_remaining : ''" min="1" step="0.01" required class="flora-input text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-earth-700 mb-1">Sumber / Tujuan Rekening</label>
                            <select name="account_id" required class="flora-input text-sm">
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->name }} (Rp {{ number_format($acc->current_balance, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-earth-700 mb-1">Tanggal</label>
                            <input type="date" name="date" value="{{ now()->format('Y-m-d') }}" required class="flora-input text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-earth-700 mb-1">Catatan (Opsional)</label>
                            <input type="text" name="notes" placeholder="Contoh: Cicilan ke-1" class="flora-input text-sm">
                        </div>

                        <div class="flex gap-2 justify-end pt-3 border-t border-sage-100">
                            <button type="button" @click="paymentModalOpen = false" class="btn-flora-secondary text-xs py-1.5 px-3">Batal</button>
                            <button type="submit" class="btn-flora-primary text-xs py-1.5 px-4 shadow-sm">Simpan Pembayaran</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ================= 3. DELETE CONFIRMATION MODAL ================= -->
        <div x-show="deleteModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div x-show="deleteModalOpen" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-earth-900/60 backdrop-blur-sm transition-opacity" @click="deleteModalOpen = false"></div>

            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="deleteModalOpen"
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md p-6 border border-sage-100">

                    <form :action="activeDebt ? activeDebt.delete_url : '#'" method="POST">
                        @csrf
                        @method('DELETE')

                        <div class="flex items-start gap-4 mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-coral-100 text-coral-600 flex items-center justify-center shrink-0">
                                <x-icon name="delete-wilt" class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-lg font-bold font-heading text-earth-900">Konfirmasi Hapus Catatan</h3>
                                <p class="text-xs text-earth-600 mt-1">
                                    Apakah Anda yakin ingin menghapus catatan pinjaman <span class="font-bold text-earth-800" x-text="activeDebt ? activeDebt.person_name : ''"></span> sebesar <span class="font-bold text-coral-600" x-text="activeDebt ? activeDebt.amount_formatted : ''"></span>?
                                </p>
                            </div>
                        </div>

                        <div class="bg-coral-50/70 border border-coral-200 p-3 rounded-2xl text-[11px] text-coral-800 mb-5">
                            💡 Seluruh riwayat pembayaran terkait akan dihapus dan transaksi keuangan terkait akan dibatalkan.
                        </div>

                        <div class="flex justify-end gap-2 pt-2 border-t border-sage-100">
                            <button type="button" @click="deleteModalOpen = false" class="btn-flora-secondary text-xs">
                                Batal
                            </button>
                            <button type="submit" class="btn-flora-primary text-xs !bg-coral-600 hover:!bg-coral-700 !border-coral-600 shadow-sm flex items-center gap-1 cursor-pointer">
                                <x-icon name="delete-wilt" class="w-3.5 h-3.5 text-white" />
                                <span>Ya, Hapus Catatan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <!-- Popup Modal Catat Hutang & Piutang Component -->
    <x-debt-modal :accounts="$accounts" />
</x-app-layout>
