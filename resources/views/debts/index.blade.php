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
            <div class="flex items-center gap-2">
                <a href="{{ route('debts.create', ['type' => 'debt']) }}" class="btn-flora-secondary text-sm flex items-center gap-1.5">
                    <x-icon name="falling-leaves" class="w-4 h-4 text-coral-500" />
                    <span>+ Catat Hutang</span>
                </a>
                <a href="{{ route('debts.create', ['type' => 'receivable']) }}" class="btn-flora-primary text-sm flex items-center gap-1.5">
                    <x-icon name="sprout" class="w-4 h-4 text-white" />
                    <span>+ Catat Piutang</span>
                </a>
            </div>
        </div>
    </x-slot>

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
    <div class="flex gap-2 mb-6">
        <a href="{{ route('debts.index', ['type' => 'all']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-semibold transition-all {{ $type === 'all' ? 'bg-sage-600 text-white shadow-sm' : 'bg-white text-earth-600 hover:bg-sage-50 border border-sage-200' }}">
            Semua Catatan
        </a>
        <a href="{{ route('debts.index', ['type' => 'debt']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-semibold transition-all {{ $type === 'debt' ? 'bg-coral-500 text-white shadow-sm' : 'bg-white text-earth-600 hover:bg-coral-50 hover:text-coral-600 border border-sage-200' }}">
            Hutang (Saya Berhutang)
        </a>
        <a href="{{ route('debts.index', ['type' => 'receivable']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-semibold transition-all {{ $type === 'receivable' ? 'bg-leaf-500 text-white shadow-sm' : 'bg-white text-earth-600 hover:bg-mint-50 hover:text-leaf-600 border border-sage-200' }}">
            Piutang (Orang Lain Berhutang)
        </a>
    </div>

    <!-- Debts Grid -->
    @if($items->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($items as $debt)
                <div class="flora-card p-6 border-l-4 transition-all hover:shadow-flora-lg flex flex-col justify-between {{ $debt->status === 'paid' ? 'opacity-70 bg-gray-50' : '' }}"
                     style="border-left-color: {{ $debt->type === 'debt' ? '#FF6B6B' : '#6B8E23' }};">
                    <div>
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <span class="flora-badge {{ $debt->status_badge_class }} text-[10px] py-0.5 px-2 mb-1">
                                    {{ $debt->status === 'paid' ? 'LUNAS' : ($debt->status === 'partially_paid' ? 'DIBAYAR SEBAGIAN' : 'BELUM DIBAYAR') }}
                                </span>
                                <h3 class="font-heading text-lg font-bold text-earth-800">{{ $debt->person_name }}</h3>
                                <div class="text-xs text-earth-500 font-medium">
                                    {{ $debt->type === 'debt' ? 'Hutang kepada' : 'Dipinjam oleh' }} {{ $debt->person_name }}
                                </div>
                            </div>

                            <div class="flex items-center gap-1">
                                <a href="{{ route('debts.edit', $debt) }}" class="p-1 text-sage-600 hover:text-sage-800 rounded hover:bg-sage-50" title="Edit">
                                    <x-icon name="edit-leaf" class="w-4 h-4" />
                                </a>
                            </div>
                        </div>

                        <!-- Amount & Remaining -->
                        <div class="my-3">
                            <div class="text-xs text-earth-500">Sisa Nominal</div>
                            <div class="text-xl font-bold {{ $debt->type === 'debt' ? 'text-coral-600' : 'text-leaf-600' }}">
                                Rp {{ number_format($debt->remaining_amount, 0, ',', '.') }}
                            </div>
                            <div class="text-[11px] text-earth-500 mt-0.5">
                                Total: Rp {{ number_format($debt->amount, 0, ',', '.') }} • Terbayar: Rp {{ number_format($debt->paid_amount, 0, ',', '.') }}
                            </div>
                        </div>

                        <!-- Mini Progress -->
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
                        <a href="{{ route('debts.show', $debt) }}" class="text-xs font-semibold text-sage-600 hover:underline">
                            Rincian Pembayaran ({{ $debt->payments->count() }})
                        </a>

                        @if($debt->status !== 'paid')
                            <button @click="$dispatch('open-payment-modal-{{ $debt->id }}')" 
                                    class="btn-flora-primary text-xs py-1.5 px-3">
                                + Bayar / Cicil
                            </button>
                        @endif
                    </div>

                    <!-- Payment Modal for this debt item -->
                    <div x-data="{ open: false }" 
                         @open-payment-modal-{{ $debt->id }}.window="open = true" 
                         x-show="open" 
                         style="display: none;" 
                         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
                        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="open = false"></div>
                        <div class="flora-card max-w-md w-full bg-white shadow-2xl relative z-10 p-6 border border-sage-200">
                            <h3 class="font-heading text-lg font-semibold text-sage-700 mb-2">
                                Catat Pembayaran {{ $debt->type === 'debt' ? 'Hutang ke' : 'Piutang dari' }} {{ $debt->person_name }}
                            </h3>
                            <p class="text-xs text-earth-600 mb-4">Sisa yang harus diselesaikan: <span class="font-bold text-earth-800">Rp {{ number_format($debt->remaining_amount, 0, ',', '.') }}</span></p>

                            <form action="{{ route('debts.payment', $debt) }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-xs font-semibold text-earth-700 mb-1">Nominal Pembayaran (Rp)</label>
                                    <input type="number" name="amount" value="{{ $debt->remaining_amount }}" max="{{ $debt->remaining_amount }}" min="1" step="0.01" required class="flora-input text-sm">
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
                                    <button type="button" @click="open = false" class="btn-flora-secondary text-xs py-1.5 px-3">Batal</button>
                                    <button type="submit" class="btn-flora-primary text-xs py-1.5 px-4">Simpan Pembayaran</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <x-empty-state 
            title="Belum Ada Catatan Hutang / Piutang" 
            description="Catat pinjaman yang Anda berikan atau terima untuk melacak tanggal jatuh tempo dan pembayarannya."
            :action="route('debts.create')"
            action-label="+ Catat Hutang / Piutang Baru" />
    @endif
</x-app-layout>
