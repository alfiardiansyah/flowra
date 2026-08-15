<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-heading text-3xl text-sage-600 flex items-center gap-3">
                    <x-icon name="cash-leaf" class="w-8 h-8 text-sage-400" />
                    Rekening & Dompet
                </h2>
                <p class="mt-1 text-earth-600 text-sm">Kelola semua sumber dana keuangan Anda (Bank, E-Wallet, Tunai, Tabungan)</p>
            </div>
            <div class="flex items-center gap-2">
                <button @click="$dispatch('open-quick-transaction', { type: 'transfer' })" class="btn-flora-secondary text-sm flex items-center gap-2">
                    <x-icon name="transfer" class="w-4 h-4" />
                    <span>Transfer Antar Rekening</span>
                </button>
                <a href="{{ route('accounts.create') }}" class="btn-flora-primary flex items-center gap-2 text-sm">
                    <x-icon name="add-seed" class="w-4 h-4 text-white" />
                    <span>+ Rekening Baru</span>
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Outer Alpine Container for Long-Press & Action Sheet -->
    <div x-data="{
        actionSheetOpen: false,
        activeAcc: null,

        pressTimer: null,
        isLongPress: false,

        getAccFromEl(el) {
            try {
                const targetEl = (el && el.hasAttribute && el.hasAttribute('data-acc')) ? el : el.closest('[data-acc]');
                if (!targetEl) return null;
                const b64 = targetEl.getAttribute('data-acc');
                if (!b64) return null;
                return JSON.parse(atob(b64));
            } catch(e) {
                console.error('Error parsing account base64 data:', e);
                return null;
            }
        },

        startPress(el) {
            const acc = this.getAccFromEl(el);
            if (!acc) return;
            this.isLongPress = false;
            clearTimeout(this.pressTimer);
            this.pressTimer = setTimeout(() => {
                this.isLongPress = true;
                if (navigator.vibrate) {
                    try { navigator.vibrate(40); } catch(e) {}
                }
                this.openActionSheet(acc);
            }, 450);
        },

        cancelPress() {
            clearTimeout(this.pressTimer);
        },

        handleCardClick(el) {
            const acc = this.getAccFromEl(el);
            if (!acc) return;
            if (!this.isLongPress) {
                window.location.href = acc.show_url;
            }
            this.isLongPress = false;
        },

        openActionSheet(acc) {
            if (!acc) return;
            this.activeAcc = acc;
            this.actionSheetOpen = true;
        }
    }">

        <!-- Net Worth Summary Banner -->
        <div class="flora-card bg-flora-gradient text-white p-6 sm:p-8 rounded-3xl mb-8 shadow-flora-lg relative overflow-hidden">
            <div class="absolute right-4 -bottom-6 opacity-15">
                <x-icon name="tree" class="w-48 h-48 text-white" />
            </div>
            <div class="relative z-10">
                <span class="text-xs font-bold uppercase tracking-widest text-white/80">Total Kekayaan Bersih (Net Worth)</span>
                <div class="text-3xl sm:text-4xl font-bold text-white mt-2 mb-2 font-heading">
                    Rp {{ number_format($totalNetWorth, 0, ',', '.') }}
                </div>
                <p class="text-xs text-white/90">
                    Terdistribusi di <span class="font-bold underline">{{ $activeCount }} rekening aktif</span> Anda.
                </p>
            </div>
        </div>

        <!-- Accounts Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($accounts as $acc)
                @php
                    $accDataData = [
                        'id' => $acc->id,
                        'name' => $acc->name,
                        'type_name' => $acc->type_name,
                        'balance_formatted' => 'Rp ' . number_format($acc->current_balance, 0, ',', '.'),
                        'account_number' => $acc->account_number,
                        'show_url' => route('accounts.show', $acc),
                        'edit_url' => route('accounts.edit', $acc),
                        'recalculate_url' => route('accounts.recalculate', $acc),
                    ];
                    $accB64 = base64_encode(json_encode($accDataData));
                @endphp

                <div class="flora-card p-5 sm:p-6 border-t-4 transition-all duration-300 hover:shadow-flora-lg hover:-translate-y-1 relative cursor-pointer select-none active:scale-[0.99] {{ !$acc->is_active ? 'opacity-60 bg-gray-50' : '' }}"
                     style="border-top-color: {{ $acc->color }}"
                     data-acc="{{ $accB64 }}"
                     @touchstart="startPress($el)"
                     @touchend="cancelPress()"
                     @touchmove="cancelPress()"
                     @mousedown="startPress($el)"
                     @mouseup="cancelPress()"
                     @mouseleave="cancelPress()"
                     @contextmenu.prevent
                     @click="handleCardClick($el)">
                    
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 shadow-sm" style="background-color: {{ $acc->color }}20;">
                                <x-icon :name="$acc->icon" class="w-7 h-7" />
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-heading text-base sm:text-lg text-earth-800 font-semibold truncate leading-tight">{{ $acc->name }}</h3>
                                <span class="flora-badge flora-badge-info text-[10px] py-0.5 px-2 mt-1 inline-block">
                                    {{ $acc->type_name }}
                                </span>
                            </div>
                        </div>

                        <!-- Prominent Edit Action Button -->
                        <div class="flex items-center gap-1 shrink-0 ml-2" @click.stop>
                            <a href="{{ route('accounts.edit', $acc) }}" 
                               class="px-2.5 py-1.5 rounded-xl bg-sage-100/80 hover:bg-sage-200 text-sage-800 text-xs font-semibold flex items-center gap-1.5 transition-colors shadow-2xs" 
                               title="Edit Rekening">
                                <x-icon name="edit-leaf" class="w-3.5 h-3.5 text-sage-700" />
                                <span>Edit</span>
                            </a>
                        </div>
                    </div>

                    <div class="my-4">
                        <div class="text-xs text-earth-500 mb-0.5 font-medium">Saldo Saat Ini</div>
                        <div class="text-2xl font-bold text-sage-700">
                            Rp {{ number_format($acc->current_balance, 0, ',', '.') }}
                        </div>
                        @if($acc->account_number)
                            <div class="text-xs text-earth-500 mt-1 font-mono">No. {{ $acc->account_number }}</div>
                        @endif
                    </div>

                    <div class="pt-4 border-t border-sage-100 flex items-center justify-between text-xs">
                        <div class="text-earth-500">
                            Saldo Awal: Rp {{ number_format($acc->opening_balance, 0, ',', '.') }}
                        </div>
                        <span class="font-semibold text-sage-600 hover:text-sage-800 flex items-center gap-1">
                            <span>Rincian & Mutasi</span>
                            <x-icon name="chevron-right" class="w-3 h-3" />
                        </span>
                    </div>
                </div>
            @endforeach

            <!-- Add Account Card -->
            <a href="{{ route('accounts.create') }}" 
               class="flora-card border-2 border-dashed border-sage-300 hover:border-sage-500 flex flex-col items-center justify-center p-8 text-center text-sage-600 hover:bg-sage-50/50 transition-all duration-300 min-h-[200px]">
                <div class="w-12 h-12 rounded-full bg-sage-100 flex items-center justify-center mb-3">
                    <x-icon name="plus" class="w-6 h-6 text-sage-600" />
                </div>
                <div class="font-heading font-semibold text-base text-earth-800">Tambah Rekening Baru</div>
                <p class="text-xs text-earth-500 mt-1 max-w-xs">Tambahkan rekening bank, dompet digital, atau uang tunai</p>
            </a>
        </div>

        <!-- ================= MOBILE CONTEXT ACTION SHEET (Popup Tekan Lama) ================= -->
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

                    <div x-show="activeAcc">
                        <!-- Pull bar indicator -->
                        <div class="w-12 h-1 bg-sage-200 rounded-full mx-auto mb-4 sm:hidden"></div>

                        <div class="text-center pb-3 border-b border-sage-100 mb-4">
                            <div class="text-xs text-earth-500 font-medium">Pilih Aksi Rekening</div>
                            <div class="font-bold text-earth-900 text-base mt-0.5" x-text="activeAcc ? activeAcc.name : ''"></div>
                            <div class="text-xs font-bold text-sage-700 mt-0.5" x-text="activeAcc ? activeAcc.balance_formatted : ''"></div>
                        </div>

                        <div class="space-y-2">
                            <!-- Option 1: View Details / Show -->
                            <a :href="activeAcc ? activeAcc.show_url : '#'" 
                               class="w-full p-3.5 rounded-2xl bg-sage-50 hover:bg-sage-100 text-sage-800 text-xs font-bold flex items-center justify-between transition-colors">
                                <span class="flex items-center gap-2.5">
                                    <span class="text-base">👁️</span>
                                    <span>Lihat Rincian & Mutasi</span>
                                </span>
                                <span class="text-sage-400">→</span>
                            </a>

                            <!-- Option 2: Edit Account -->
                            <a :href="activeAcc ? activeAcc.edit_url : '#'" 
                               class="w-full p-3.5 rounded-2xl bg-sky-50 hover:bg-sky-100 text-sky-800 text-xs font-bold flex items-center justify-between transition-colors">
                                <span class="flex items-center gap-2.5">
                                    <x-icon name="edit-leaf" class="w-4 h-4 text-sky-600" />
                                    <span>Edit / Pengaturan Rekening</span>
                                </span>
                                <span class="text-sky-400">→</span>
                            </a>

                            <!-- Option 3: Recalculate Balance -->
                            <form :action="activeAcc ? activeAcc.recalculate_url : '#'" method="POST" class="w-full">
                                @csrf
                                <button type="submit" 
                                        class="w-full p-3.5 rounded-2xl bg-mint-50 hover:bg-mint-100 text-leaf-800 text-xs font-bold flex items-center justify-between transition-colors">
                                    <span class="flex items-center gap-2.5">
                                        <x-icon name="refresh" class="w-4 h-4 text-leaf-600" />
                                        <span>Hitung Ulang Saldo (Sync)</span>
                                    </span>
                                    <span class="text-leaf-400">→</span>
                                </button>
                            </form>

                            <!-- Option 4: Delete Account -->
                            <a :href="activeAcc ? activeAcc.edit_url : '#'" 
                               class="w-full p-3.5 rounded-2xl bg-coral-50 hover:bg-coral-100 text-coral-700 text-xs font-bold flex items-center justify-between transition-colors">
                                <span class="flex items-center gap-2.5">
                                    <x-icon name="delete-wilt" class="w-4 h-4 text-coral-600" />
                                    <span>Hapus / Nonaktifkan Rekening</span>
                                </span>
                                <span class="text-coral-400">→</span>
                            </a>
                        </div>

                        <button type="button" @click="actionSheetOpen = false" class="w-full text-center text-xs font-semibold text-earth-500 hover:text-earth-700 mt-4 py-2">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
