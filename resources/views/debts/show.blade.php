<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-heading text-3xl text-sage-600 flex items-center gap-3">
                    <x-icon name="leaf" class="w-8 h-8 text-sage-400" />
                    Rincian {{ $debt->type_label }}
                </h2>
                <p class="mt-1 text-earth-600 text-sm">Pihak Terkait: <span class="font-bold text-earth-800">{{ $debt->person_name }}</span></p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('debts.edit', $debt) }}" class="btn-flora-secondary text-xs flex items-center gap-1.5">
                    <x-icon name="edit-leaf" class="w-3.5 h-3.5" />
                    <span>Edit</span>
                </a>
                <a href="{{ route('debts.index') }}" class="btn-flora-secondary text-xs">
                    ← Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
        <x-card class="p-5 border-l-4" style="border-left-color: {{ $debt->type === 'debt' ? '#FF6B6B' : '#6B8E23' }}">
            <div class="text-xs text-earth-500 font-medium">Total Pokok Pinjaman</div>
            <div class="text-2xl font-bold text-earth-800 mt-1">
                Rp {{ number_format($debt->amount, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-earth-400 mt-1">Mulai: {{ $debt->date ? $debt->date->format('d M Y') : '-' }}</div>
        </x-card>

        <x-card class="p-5 border-l-4 border-l-leaf-400">
            <div class="text-xs text-earth-500 font-medium">Sudah Dibayar</div>
            <div class="text-2xl font-bold text-leaf-600 mt-1">
                Rp {{ number_format($debt->paid_amount, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-earth-400 mt-1">Kemajuan: {{ $debt->percentage_paid }}%</div>
        </x-card>

        <x-card class="p-5 border-l-4 border-l-coral-400">
            <div class="text-xs text-earth-500 font-medium">Sisa Kewajiban</div>
            <div class="text-2xl font-bold text-coral-600 mt-1">
                Rp {{ number_format($debt->remaining_amount, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-earth-400 mt-1">Jatuh Tempo: {{ $debt->due_date ? $debt->due_date->format('d M Y') : 'Tanpa batas' }}</div>
        </x-card>
    </div>

    <!-- Record Payment Card & Ledger -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Add Payment Box -->
        <x-card class="lg:col-span-1">
            <h3 class="font-heading text-lg text-sage-700 mb-4 pb-2 border-b border-sage-100 flex items-center gap-2">
                <x-icon name="sprout" class="w-5 h-5 text-sage-500" />
                Catat Pembayaran
            </h3>

            @if($debt->status === 'paid')
                <div class="p-4 bg-mint-50 border border-mint-200 rounded-xl text-center">
                    <x-icon name="flower-bloom" class="w-10 h-10 text-leaf-600 mx-auto mb-2" />
                    <div class="font-bold text-sm text-leaf-700">Kewajiban Sudah Lunas!</div>
                    <p class="text-xs text-earth-500 mt-1">Semua pinjaman telah selesai dibayarkan.</p>
                </div>
            @else
                <form action="{{ route('debts.payment', $debt) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-earth-700 mb-1">Nominal Pembayaran (Rp)</label>
                        <input type="number" name="amount" value="{{ old('amount', $debt->remaining_amount) }}" max="{{ $debt->remaining_amount }}" min="1" step="0.01" required class="flora-input text-sm">
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
                        <label class="block text-xs font-semibold text-earth-700 mb-1">Tanggal Bayar</label>
                        <input type="date" name="date" value="{{ now()->format('Y-m-d') }}" required class="flora-input text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-earth-700 mb-1">Catatan</label>
                        <input type="text" name="notes" placeholder="Contoh: Pembayaran cicilan ke-1" class="flora-input text-sm">
                    </div>

                    <button type="submit" class="btn-flora-primary w-full text-xs py-2.5 flex items-center justify-center gap-2">
                        <x-icon name="sprout" class="w-4 h-4 text-white" />
                        <span>Simpan Pembayaran</span>
                    </button>
                </form>
            @endif
        </x-card>

        <!-- Payment History Table -->
        <x-card class="lg:col-span-2">
            <h3 class="font-heading text-lg text-sage-700 mb-4 pb-2 border-b border-sage-100 flex items-center gap-2">
                <x-icon name="flower-bloom" class="w-5 h-5 text-sage-500" />
                Riwayat Angsuran & Pelunasan
            </h3>

            @if($debt->payments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="flora-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Rekening</th>
                                <th>Catatan</th>
                                <th class="text-right">Nominal</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($debt->payments as $pay)
                                <tr>
                                    <td class="text-xs text-earth-600 font-medium">
                                        {{ $pay->date ? $pay->date->format('d M Y') : '-' }}
                                    </td>
                                    <td class="text-xs text-earth-700 font-medium">
                                        {{ $pay->account->name ?? '-' }}
                                    </td>
                                    <td class="text-xs text-earth-600">
                                        {{ $pay->notes ?: '-' }}
                                    </td>
                                    <td class="text-right font-bold text-sm text-leaf-600">
                                        Rp {{ number_format($pay->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('debts.payments.destroy', $pay) }}" method="POST" onsubmit="return confirm('Hapus catatan pembayaran ini? Saldo rekening akan disesuaikan kembali.')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-coral-500 hover:text-coral-700 rounded-lg hover:bg-coral-50" title="Hapus">
                                                <x-icon name="delete-wilt" class="w-4 h-4" />
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-xs text-earth-500 py-6 text-center">Belum ada riwayat pembayaran yang tercatat.</p>
            @endif
        </x-card>
    </div>
</x-app-layout>
