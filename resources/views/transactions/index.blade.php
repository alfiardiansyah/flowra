<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-heading text-2xl sm:text-3xl text-sage-600 flex items-center gap-2.5">
                    <x-icon name="flower-bloom" class="w-7 h-7 sm:w-8 sm:h-8 text-sage-400" />
                    Semua Transaksi
                </h2>
                <p class="mt-0.5 text-earth-600 text-xs sm:text-sm">Catatan riwayat pemasukan, pengeluaran, dan transfer antar rekening</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('transactions.create') }}" class="btn-flora-primary w-full sm:w-auto flex items-center justify-center gap-2 text-xs sm:text-sm py-2.5 px-4 shadow-sm">
                    <x-icon name="add-seed" class="w-4 h-4 text-white" />
                    <span>+ Tambah Transaksi</span>
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Outer Alpine.js Component for Modal & Long-Press Handling -->
    <div x-data="{
        detailModalOpen: false,
        activeTx: null,

        actionSheetOpen: false,
        actionTx: null,

        deleteModalOpen: false,
        deleteTx: null,

        pressTimer: null,
        isLongPress: false,

        getTxFromEl(el) {
            try {
                const targetEl = (el && el.hasAttribute && el.hasAttribute('data-tx')) ? el : el.closest('[data-tx]');
                if (!targetEl) return null;
                const b64 = targetEl.getAttribute('data-tx');
                if (!b64) return null;
                return JSON.parse(atob(b64));
            } catch(e) {
                console.error('Error parsing transaction base64 data:', e);
                return null;
            }
        },

        startPress(el) {
            const tx = this.getTxFromEl(el);
            if (!tx) return;
            this.isLongPress = false;
            clearTimeout(this.pressTimer);
            this.pressTimer = setTimeout(() => {
                this.isLongPress = true;
                if (navigator.vibrate) {
                    try { navigator.vibrate(40); } catch(e) {}
                }
                this.openActionSheet(tx);
            }, 450);
        },

        cancelPress() {
            clearTimeout(this.pressTimer);
        },

        handleCardClick(el) {
            const tx = this.getTxFromEl(el);
            if (!tx) return;
            if (!this.isLongPress) {
                this.openDetailModal(tx);
            }
            this.isLongPress = false;
        },

        openDetailModalFromEl(el) {
            const tx = this.getTxFromEl(el);
            if (tx) this.openDetailModal(tx);
        },

        confirmDeleteFromEl(el) {
            const tx = this.getTxFromEl(el);
            if (tx) this.confirmDelete(tx);
        },

        openDetailModal(tx) {
            if (!tx) return;
            this.activeTx = tx;
            this.actionSheetOpen = false;
            this.detailModalOpen = true;
        },

        openActionSheet(tx) {
            if (!tx) return;
            this.actionTx = tx;
            this.actionSheetOpen = true;
        },

        confirmDelete(tx) {
            if (!tx) return;
            this.deleteTx = tx;
            this.actionSheetOpen = false;
            this.detailModalOpen = false;
            this.deleteModalOpen = true;
        }
    }">

        <!-- Quick Stats for Current Filter -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-5 sm:mb-6">
            <div class="flora-card p-3.5 sm:p-4 flex items-center justify-between border-l-4 border-l-leaf-400">
                <div>
                    <div class="text-[11px] sm:text-xs text-earth-500 font-medium">Total Pemasukan (Filter)</div>
                    <div class="text-base sm:text-lg font-bold text-leaf-600 mt-0.5">Rp {{ number_format($totalIncomes, 0, ',', '.') }}</div>
                </div>
                <x-icon name="sprout" class="w-6 h-6 sm:w-7 sm:h-7 text-leaf-500 opacity-80 flex-shrink-0" />
            </div>
            <div class="flora-card p-3.5 sm:p-4 flex items-center justify-between border-l-4 border-l-coral-400">
                <div>
                    <div class="text-[11px] sm:text-xs text-earth-500 font-medium">Total Pengeluaran (Filter)</div>
                    <div class="text-base sm:text-lg font-bold text-coral-600 mt-0.5">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</div>
                </div>
                <x-icon name="falling-leaves" class="w-6 h-6 sm:w-7 sm:h-7 text-coral-500 opacity-80 flex-shrink-0" />
            </div>
            <div class="flora-card p-3.5 sm:p-4 flex items-center justify-between border-l-4 border-l-sky-400">
                <div>
                    <div class="text-[11px] sm:text-xs text-earth-500 font-medium">Selisih Bersih</div>
                    <div class="text-base sm:text-lg font-bold {{ $netAmount >= 0 ? 'text-leaf-600' : 'text-coral-600' }} mt-0.5">
                        {{ $netAmount >= 0 ? '+' : '' }} Rp {{ number_format($netAmount, 0, ',', '.') }}
                    </div>
                </div>
                <x-icon name="flower-bloom" class="w-6 h-6 sm:w-7 sm:h-7 text-sky-500 opacity-80 flex-shrink-0" />
            </div>
        </div>

        <!-- Live Filter Bar -->
        <x-card class="mb-5 sm:mb-6 p-4 sm:p-5" x-data="{
            submitTimeout: null,
            triggerLiveFilter() {
                clearTimeout(this.submitTimeout);
                this.submitTimeout = setTimeout(() => {
                    $refs.filterForm.submit();
                }, 400);
            }
        }">
            <form method="GET" action="{{ route('transactions.index') }}" x-ref="filterForm" class="space-y-4">
                @if(request('type'))
                    <input type="hidden" name="type" value="{{ request('type') }}">
                @endif

                <div class="flex items-center justify-between pb-2 border-b border-sage-100 gap-2">
                    <div class="flex items-center gap-1.5 overflow-x-auto -mx-1 px-1 custom-scrollbar scrollbar-none whitespace-nowrap">
                        <a href="{{ route('transactions.index', array_merge(request()->except('type', 'page'), ['type' => ''])) }}" 
                           class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all duration-200 flex-shrink-0 {{ !request('type') ? 'bg-sage-600 text-white shadow-sm' : 'bg-sage-50 text-earth-600 hover:bg-sage-100' }}">
                            Semua Jenis
                        </a>
                        <a href="{{ route('transactions.index', array_merge(request()->except('type', 'page'), ['type' => 'expense'])) }}" 
                           class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all duration-200 flex-shrink-0 {{ request('type') === 'expense' ? 'bg-coral-500 text-white shadow-sm' : 'bg-sage-50 text-earth-600 hover:bg-coral-50 hover:text-coral-600' }}">
                            Pengeluaran
                        </a>
                        <a href="{{ route('transactions.index', array_merge(request()->except('type', 'page'), ['type' => 'income'])) }}" 
                           class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all duration-200 flex-shrink-0 {{ request('type') === 'income' ? 'bg-leaf-500 text-white shadow-sm' : 'bg-sage-50 text-earth-600 hover:bg-mint-50 hover:text-leaf-600' }}">
                            Pemasukan
                        </a>
                        <a href="{{ route('transactions.index', array_merge(request()->except('type', 'page'), ['type' => 'transfer'])) }}" 
                           class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all duration-200 flex-shrink-0 {{ request('type') === 'transfer' ? 'bg-sky-500 text-white shadow-sm' : 'bg-sage-50 text-earth-600 hover:bg-sky-50 hover:text-sky-600' }}">
                            Transfer
                        </a>
                    </div>

                    @if(request()->hasAny(['search', 'type', 'account_id', 'category_id', 'from', 'to']))
                        <a href="{{ route('transactions.index') }}" class="btn-flora-secondary text-xs py-1.5 px-3 flex-shrink-0 flex items-center gap-1" title="Reset Filter">
                            <x-icon name="delete-wilt" class="w-3.5 h-3.5 text-coral-500" />
                            <span>Reset Filter</span>
                        </a>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                    <div>
                        <label class="block text-[11px] sm:text-xs font-medium text-earth-700 mb-1">Cari Keterangan / Catatan</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   @input="triggerLiveFilter()" 
                                   x-init="if ($el.value) { $el.focus(); $el.setSelectionRange($el.value.length, $el.value.length); }"
                                   placeholder="Ketik untuk mencari instan..." 
                                   class="flora-input text-xs py-2 pr-8">
                            <x-icon name="search" class="w-3.5 h-3.5 text-sage-400 absolute right-2.5 top-2.5 pointer-events-none" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] sm:text-xs font-medium text-earth-700 mb-1">Rekening</label>
                        <select name="account_id" @change="$refs.filterForm.submit()" class="flora-input text-xs py-2">
                            <option value="">Semua Rekening</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}" {{ request('account_id') == $acc->id ? 'selected' : '' }}>
                                    {{ $acc->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] sm:text-xs font-medium text-earth-700 mb-1">Kategori</label>
                        <select name="category_id" @change="$refs.filterForm.submit()" class="flora-input text-xs py-2">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }} ({{ $cat->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] sm:text-xs font-medium text-earth-700 mb-1">Dari Tanggal</label>
                        <input type="date" name="from" value="{{ request('from') }}" @change="$refs.filterForm.submit()" class="flora-input text-xs py-2">
                    </div>
                </div>
            </form>
        </x-card>

        <!-- Transactions List Container -->
        @if($transactions->count() > 0)
            <!-- 1. Mobile Android Card List View (Block on Mobile, Hidden on Desktop) -->
            <div class="block md:hidden space-y-3 mb-6">
                @foreach($transactions as $tx)
                    @php
                        $txDataData = [
                            'id' => $tx->id,
                            'type' => $tx->type,
                            'type_name' => $tx->type === 'income' ? 'Pemasukan' : ($tx->type === 'expense' ? 'Pengeluaran' : 'Transfer'),
                            'description' => $tx->description,
                            'notes' => $tx->notes,
                            'amount_formatted' => $tx->formatted_amount,
                            'raw_amount' => number_format($tx->amount, 0, ',', '.'),
                            'date_formatted' => $tx->date ? $tx->date->format('d M Y') : '-',
                            'date_full' => $tx->date ? $tx->date->format('l, d F Y') : '-',
                            'category_name' => $tx->category->name ?? ($tx->type === 'income' ? 'Pemasukan' : ($tx->type === 'expense' ? 'Pengeluaran' : 'Transfer')),
                            'category_icon' => $tx->category->icon ?? ($tx->type === 'income' ? 'sprout' : ($tx->type === 'expense' ? 'falling-leaves' : 'leaf-wind')),
                            'account_name' => $tx->account->name ?? '-',
                            'account_icon' => $tx->account->icon ?? 'cash-leaf',
                            'destination_account_name' => $tx->destinationAccount->name ?? null,
                            'edit_url' => route('transactions.edit', $tx),
                            'delete_url' => route('transactions.destroy', $tx),
                        ];
                        $txB64 = base64_encode(json_encode($txDataData));
                    @endphp

                    <div class="flora-card p-3.5 rounded-2xl bg-white border border-sage-200/80 shadow-sm flex flex-col gap-2.5 cursor-pointer active:scale-[0.99] transition-all select-none relative"
                         data-tx="{{ $txB64 }}"
                         @touchstart="startPress($el)"
                         @touchend="cancelPress()"
                         @touchmove="cancelPress()"
                         @mousedown="startPress($el)"
                         @mouseup="cancelPress()"
                         @mouseleave="cancelPress()"
                         @contextmenu.prevent
                         @click="handleCardClick($el)">
                        
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 shadow-inner {{ $tx->type === 'income' ? 'bg-mint-100 text-leaf-700' : ($tx->type === 'expense' ? 'bg-coral-100 text-coral-700' : 'bg-sky-100 text-sky-700') }}">
                                    <x-icon :name="$tx->category->icon ?? ($tx->type === 'income' ? 'sprout' : ($tx->type === 'expense' ? 'falling-leaves' : 'leaf-wind'))" class="w-5 h-5" />
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-earth-800 text-sm truncate leading-snug">{{ $tx->description }}</div>
                                    <div class="text-[11px] text-earth-500 flex items-center gap-1.5 mt-0.5">
                                        <span>{{ $tx->date ? $tx->date->format('d M Y') : '-' }}</span>
                                        <span>•</span>
                                        <span class="font-medium text-sage-700">{{ $tx->type === 'income' ? ($tx->category->name ?? 'Pemasukan') : ($tx->type === 'expense' ? ($tx->category->name ?? 'Pengeluaran') : 'Transfer') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="text-right flex-shrink-0">
                                <div class="font-bold text-sm {{ $tx->type === 'income' ? 'text-leaf-600' : ($tx->type === 'expense' ? 'text-coral-600' : 'text-sky-600') }}">
                                    {{ $tx->formatted_amount }}
                                </div>
                            </div>
                        </div>

                        <!-- Account & Single Tap Indicator Footer -->
                        <div class="pt-2 border-t border-sage-100 flex items-center justify-between text-xs">
                            <div class="text-earth-600 font-medium flex items-center gap-1">
                                @if($tx->type === 'transfer')
                                    <span>{{ $tx->account->name ?? '-' }}</span>
                                    <span class="text-sky-500 font-bold">→</span>
                                    <span>{{ $tx->destinationAccount->name ?? '-' }}</span>
                                @else
                                    <x-icon :name="$tx->account->icon ?? 'cash-leaf'" class="w-3.5 h-3.5 text-sage-500" />
                                    <span>{{ $tx->account->name ?? '-' }}</span>
                                @endif
                            </div>

                            <div class="flex items-center gap-1.5 text-[11px] text-sage-600 font-medium">
                                @if($tx->attachment)
                                    <span title="Ada Bukti Lampiran">📎</span>
                                @endif
                                <span class="flex items-center gap-1 bg-sage-50 px-2 py-0.5 rounded-lg border border-sage-200/60">
                                    <span>Rincian</span>
                                    <span>→</span>
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- 2. Desktop Table View (Clean Single Detail Button, Row Clickable) -->
            <div class="hidden md:block flora-card p-0 overflow-hidden shadow-flora mb-6">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-sage-50/90 border-b border-sage-100 text-earth-700 text-xs font-semibold uppercase tracking-wider">
                            <th class="py-3 px-4 w-32">Tanggal</th>
                            <th class="py-3 px-4">Keterangan</th>
                            <th class="py-3 px-4 w-36">Kategori</th>
                            <th class="py-3 px-4 w-40">Rekening</th>
                            <th class="py-3 px-4 w-36 text-right">Nominal</th>
                            <th class="py-3 px-4 w-28 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sage-100 text-xs text-earth-700">
                        @foreach($transactions as $tx)
                            @php
                                $txDataData = [
                                    'id' => $tx->id,
                                    'type' => $tx->type,
                                    'type_name' => $tx->type === 'income' ? 'Pemasukan' : ($tx->type === 'expense' ? 'Pengeluaran' : 'Transfer'),
                                    'description' => $tx->description,
                                    'notes' => $tx->notes,
                                    'amount_formatted' => $tx->formatted_amount,
                                    'raw_amount' => number_format($tx->amount, 0, ',', '.'),
                                    'date_formatted' => $tx->date ? $tx->date->format('d M Y') : '-',
                                    'date_full' => $tx->date ? $tx->date->format('l, d F Y') : '-',
                                    'category_name' => $tx->category->name ?? ($tx->type === 'income' ? 'Pemasukan' : ($tx->type === 'expense' ? 'Pengeluaran' : 'Transfer')),
                                    'category_icon' => $tx->category->icon ?? ($tx->type === 'income' ? 'sprout' : ($tx->type === 'expense' ? 'falling-leaves' : 'leaf-wind')),
                                    'account_name' => $tx->account->name ?? '-',
                                    'account_icon' => $tx->account->icon ?? 'cash-leaf',
                                    'destination_account_name' => $tx->destinationAccount->name ?? null,
                                    'edit_url' => route('transactions.edit', $tx),
                                    'delete_url' => route('transactions.destroy', $tx),
                                ];
                                $txB64 = base64_encode(json_encode($txDataData));
                            @endphp
                            <tr class="hover:bg-mint-50/60 transition-colors cursor-pointer" 
                                data-tx="{{ $txB64 }}"
                                @click="openDetailModalFromEl($el)">
                                <td class="py-3 px-4 font-medium text-earth-600 whitespace-nowrap">
                                    {{ $tx->date ? $tx->date->format('d M Y') : '-' }}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-semibold text-earth-800 text-xs leading-snug line-clamp-1">{{ $tx->description }}</div>
                                    @if($tx->notes)
                                        <div class="text-[11px] text-earth-500 italic truncate max-w-[240px] mt-0.5">{{ $tx->notes }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="flora-badge {{ $tx->type_badge_class }} text-[10px] py-0.5 px-2">
                                        @if($tx->type === 'income')
                                            {{ $tx->category->name ?? 'Pemasukan' }}
                                        @elseif($tx->type === 'expense')
                                            {{ $tx->category->name ?? 'Pengeluaran' }}
                                        @else
                                            Transfer
                                        @endif
                                    </span>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    @if($tx->type === 'transfer')
                                        <div class="text-[11px] font-medium text-earth-700 flex items-center gap-1">
                                            <span>{{ $tx->account->name ?? '-' }}</span>
                                            <span class="text-sky-500 font-bold">→</span>
                                            <span>{{ $tx->destinationAccount->name ?? '-' }}</span>
                                        </div>
                                    @else
                                        <div class="text-[11px] font-medium text-earth-700 flex items-center gap-1.5">
                                            <x-icon :name="$tx->account->icon ?? 'cash-leaf'" class="w-3.5 h-3.5 text-sage-500" />
                                            <span class="truncate max-w-[130px]">{{ $tx->account->name ?? '-' }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right font-bold text-xs {{ $tx->type === 'income' ? 'text-leaf-600' : ($tx->type === 'expense' ? 'text-coral-600' : 'text-sky-600') }} whitespace-nowrap">
                                    {{ $tx->formatted_amount }}
                                </td>
                                <!-- Clean Single Detail Action Button -->
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <button type="button" 
                                            @click.stop="openDetailModalFromEl($el)"
                                            class="px-2.5 py-1 rounded-xl bg-sage-50 hover:bg-sage-100 text-sage-700 font-semibold text-xs transition-colors inline-flex items-center gap-1 border border-sage-200/60 shadow-2xs cursor-pointer"
                                            title="Lihat Detail Rincian & Aksi">
                                        <span>👁️ Detail</span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flora-pagination mt-4 sm:mt-6">
                {{ $transactions->links() }}
            </div>
        @else
            <x-empty-state 
                title="Tidak Ada Transaksi Ditemukan" 
                description="Coba ubah filter pencarian Anda atau tambahkan transaksi baru ke kebun Anda."
                :action="route('transactions.create')"
                action-label="+ Tambah Transaksi Sekarang" />
        @endif

        <!-- ================= MODALS & ACTION SHEETS ================= -->

        <!-- 1. POP-UP RINCIAN TRANSAKSI (Detail Modal - Contains Edit & Delete Actions) -->
        <div x-show="detailModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div x-show="detailModalOpen" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-earth-900/60 backdrop-blur-sm transition-opacity" @click="detailModalOpen = false"></div>

            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="detailModalOpen"
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md p-6 border border-sage-100">

                    <div x-show="activeTx">
                        <!-- Header & Type Badge -->
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-sage-100">
                            <span class="flora-badge text-xs font-semibold py-1 px-3"
                                  :class="{
                                      'bg-mint-100 text-leaf-700': activeTx && activeTx.type === 'income',
                                      'bg-coral-100 text-coral-700': activeTx && activeTx.type === 'expense',
                                      'bg-sky-100 text-sky-700': activeTx && activeTx.type === 'transfer'
                                  }">
                                <span x-text="activeTx ? activeTx.type_name : ''"></span>
                            </span>
                            <button type="button" @click="detailModalOpen = false" class="text-earth-400 hover:text-earth-600 text-xl font-bold p-1">✕</button>
                        </div>

                        <!-- Amount Banner -->
                        <div class="text-center py-4 px-6 rounded-2xl mb-5"
                             :class="{
                                 'bg-mint-50 border border-mint-200': activeTx && activeTx.type === 'income',
                                 'bg-coral-50 border border-coral-200': activeTx && activeTx.type === 'expense',
                                 'bg-sky-50 border border-sky-200': activeTx && activeTx.type === 'transfer'
                             }">
                            <div class="text-xs text-earth-500 font-medium">Nominal Transaksi</div>
                            <div class="text-3xl font-bold font-heading mt-1"
                                 :class="{
                                     'text-leaf-600': activeTx && activeTx.type === 'income',
                                     'text-coral-600': activeTx && activeTx.type === 'expense',
                                     'text-sky-600': activeTx && activeTx.type === 'transfer'
                                 }"
                                 x-text="activeTx ? activeTx.amount_formatted : ''"></div>
                        </div>

                        <!-- Transaction Details Grid -->
                        <div class="space-y-3 text-xs mb-6">
                            <div class="flex justify-between py-2 border-b border-sage-100">
                                <span class="text-earth-500">Keterangan</span>
                                <span class="font-bold text-earth-800 text-right max-w-[200px]" x-text="activeTx ? activeTx.description : ''"></span>
                            </div>

                            <div class="flex justify-between py-2 border-b border-sage-100">
                                <span class="text-earth-500">Kategori</span>
                                <span class="font-semibold text-sage-700" x-text="activeTx ? activeTx.category_name : ''"></span>
                            </div>

                            <div class="flex justify-between py-2 border-b border-sage-100">
                                <span class="text-earth-500">Tanggal</span>
                                <span class="font-semibold text-earth-800" x-text="activeTx ? activeTx.date_full : ''"></span>
                            </div>

                            <div class="flex justify-between py-2 border-b border-sage-100">
                                <span class="text-earth-500" x-text="activeTx && activeTx.type === 'transfer' ? 'Dari Rekening' : 'Sumber Rekening'"></span>
                                <span class="font-semibold text-earth-800" x-text="activeTx ? activeTx.account_name : ''"></span>
                            </div>

                            <div x-show="activeTx && activeTx.type === 'transfer'" class="flex justify-between py-2 border-b border-sage-100">
                                <span class="text-earth-500">Ke Rekening Tujuan</span>
                                <span class="font-semibold text-sky-700" x-text="activeTx ? activeTx.destination_account_name : ''"></span>
                            </div>

                            <div x-show="activeTx && activeTx.notes" class="py-2 border-b border-sage-100">
                                <span class="text-earth-500 block mb-1">Catatan Tambahan:</span>
                                <p class="text-earth-700 italic bg-sage-50 p-2.5 rounded-xl border border-sage-200" x-text="activeTx ? activeTx.notes : ''"></p>
                            </div>
                        </div>

                        <!-- Modal Action Footer (Prominent Edit & Delete Buttons) -->
                        <div class="flex items-center justify-end gap-2 pt-4 border-t border-sage-100">
                            <a :href="activeTx ? activeTx.edit_url : '#'" class="btn-flora-secondary text-xs font-semibold flex items-center gap-1.5 py-2 px-4 shadow-2xs">
                                <x-icon name="edit-leaf" class="w-4 h-4 text-sky-600" />
                                <span>Edit Transaksi</span>
                            </a>
                            <button type="button" @click="confirmDelete(activeTx)" class="btn-flora-primary text-xs font-semibold !bg-coral-600 hover:!bg-coral-700 !border-coral-600 flex items-center gap-1.5 py-2 px-4 shadow-2xs cursor-pointer">
                                <x-icon name="delete-wilt" class="w-4 h-4 text-white" />
                                <span>Hapus Transaksi</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. MOBILE CONTEXT ACTION SHEET (Popup Pilihan Aksi di HP saat Tekan Lama) -->
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

                    <div x-show="actionTx">
                        <!-- Pull bar indicator for mobile -->
                        <div class="w-12 h-1 bg-sage-200 rounded-full mx-auto mb-4 sm:hidden"></div>

                        <div class="text-center pb-3 border-b border-sage-100 mb-3">
                            <div class="text-xs text-earth-500 font-medium">Pilih Aksi Transaksi</div>
                            <div class="font-bold text-earth-800 text-sm mt-0.5 truncate" x-text="actionTx ? actionTx.description : ''"></div>
                            <div class="text-xs font-bold mt-0.5"
                                 :class="{
                                     'text-leaf-600': actionTx && actionTx.type === 'income',
                                     'text-coral-600': actionTx && actionTx.type === 'expense',
                                     'text-sky-600': actionTx && actionTx.type === 'transfer'
                                 }"
                                 x-text="actionTx ? actionTx.amount_formatted : ''"></div>
                        </div>

                        <div class="space-y-2">
                            <button type="button" 
                                    @click="openDetailModal(actionTx)"
                                    class="w-full p-3.5 rounded-2xl bg-sage-50 hover:bg-sage-100 text-sage-800 text-xs font-bold flex items-center justify-between transition-colors">
                                <span class="flex items-center gap-2.5">
                                    <span class="text-base">👁️</span>
                                    <span>Lihat Rincian Transaksi</span>
                                </span>
                                <span class="text-sage-400">→</span>
                            </button>

                            <a :href="actionTx ? actionTx.edit_url : '#'" 
                               class="w-full p-3.5 rounded-2xl bg-sky-50 hover:bg-sky-100 text-sky-800 text-xs font-bold flex items-center justify-between transition-colors">
                                <span class="flex items-center gap-2.5">
                                    <x-icon name="edit-leaf" class="w-4 h-4 text-sky-600" />
                                    <span>Edit Transaksi</span>
                                </span>
                                <span class="text-sky-400">→</span>
                            </a>

                            <button type="button" 
                                    @click="confirmDelete(actionTx)"
                                    class="w-full p-3.5 rounded-2xl bg-coral-50 hover:bg-coral-100 text-coral-700 text-xs font-bold flex items-center justify-between transition-colors">
                                <span class="flex items-center gap-2.5">
                                    <x-icon name="delete-wilt" class="w-4 h-4 text-coral-600" />
                                    <span>Hapus Transaksi</span>
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

        <!-- 3. RELIABLE DELETE CONFIRMATION MODAL -->
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

                    <form :action="deleteTx ? deleteTx.delete_url : '#'" method="POST">
                        @csrf
                        @method('DELETE')

                        <div class="flex items-start gap-4 mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-coral-100 text-coral-600 flex items-center justify-center shrink-0">
                                <x-icon name="delete-wilt" class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-lg font-bold font-heading text-earth-900">Konfirmasi Hapus Transaksi</h3>
                                <p class="text-xs text-earth-600 mt-1">
                                    Apakah Anda yakin ingin menghapus transaksi <span class="font-bold text-earth-800" x-text="deleteTx ? deleteTx.description : ''"></span> sebesar <span class="font-bold text-coral-600" x-text="deleteTx ? deleteTx.amount_formatted : ''"></span>?
                                </p>
                            </div>
                        </div>

                        <div class="bg-coral-50/70 border border-coral-200 p-3 rounded-2xl text-[11px] text-coral-800 mb-5">
                            💡 Saldo rekening <span class="font-semibold" x-text="deleteTx ? deleteTx.account_name : ''"></span> akan dihitung ulang secara otomatis setelah transaksi dihapus.
                        </div>

                        <div class="flex justify-end gap-2 pt-2 border-t border-sage-100">
                            <button type="button" @click="deleteModalOpen = false" class="btn-flora-secondary text-xs">
                                Batal
                            </button>
                            <button type="submit" class="btn-flora-primary text-xs !bg-coral-600 hover:!bg-coral-700 !border-coral-600 shadow-sm flex items-center gap-1 cursor-pointer">
                                <x-icon name="delete-wilt" class="w-3.5 h-3.5 text-white" />
                                <span>Ya, Hapus Transaksi</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
