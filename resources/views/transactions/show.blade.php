<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-heading text-2xl text-sage-600 flex items-center gap-2.5">
                <x-icon name="flower-bloom" class="w-7 h-7 text-sage-400" />
                Detail Transaksi #{{ $transaction->id }}
            </h2>
            <a href="{{ route('transactions.index') }}" class="btn-flora-secondary text-xs py-2 px-3 flex items-center gap-1">
                <span>← Kembali ke Transaksi</span>
            </a>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto py-6">
        <x-card class="p-6 relative border-l-4" style="border-left-color: {{ $transaction->type === 'income' ? '#87A96B' : ($transaction->type === 'expense' ? '#FF6B6B' : '#38BDF8') }};">
            <div class="flex items-start justify-between pb-4 border-b border-sage-100 mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-xl {{ $transaction->type === 'income' ? 'bg-mint-100 text-leaf-600' : ($transaction->type === 'expense' ? 'bg-coral-100 text-coral-600' : 'bg-sky-100 text-sky-600') }}">
                        @if($transaction->type === 'income')
                            <x-icon :name="$transaction->category->icon ?? 'sprout'" class="w-6 h-6" />
                        @elseif($transaction->type === 'expense')
                            <x-icon :name="$transaction->category->icon ?? 'falling-leaves'" class="w-6 h-6" />
                        @else
                            <x-icon name="transfer" class="w-6 h-6" />
                        @endif
                    </div>
                    <div>
                        <span class="flora-badge text-[10px] py-0.5 px-2 mb-1 uppercase font-bold {{ $transaction->type === 'income' ? 'flora-badge-success' : ($transaction->type === 'expense' ? 'flora-badge-danger' : 'flora-badge-info') }}">
                            {{ $transaction->type === 'income' ? 'Pemasukan' : ($transaction->type === 'expense' ? 'Pengeluaran' : 'Transfer') }}
                        </span>
                        <h3 class="font-heading text-xl font-bold text-earth-800 mt-0.5">{{ $transaction->description }}</h3>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold {{ $transaction->type === 'income' ? 'text-leaf-600' : ($transaction->type === 'expense' ? 'text-coral-600' : 'text-sky-600') }}">
                        {{ $transaction->formatted_amount }}
                    </div>
                    <div class="text-xs text-earth-500 mt-1">{{ $transaction->date ? $transaction->date->format('d MMMM Y') : '-' }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs mb-6">
                <div class="p-3 rounded-xl bg-sage-50/70 border border-sage-100">
                    <span class="text-earth-500 block text-[10px] uppercase font-bold mb-1">Kategori</span>
                    <span class="font-semibold text-earth-800 flex items-center gap-1.5">
                        <x-icon :name="$transaction->category->icon ?? 'flower'" class="w-4 h-4 text-sage-600" />
                        <span>{{ $transaction->category->name ?? ($transaction->type === 'transfer' ? 'Transfer' : 'Umum') }}</span>
                    </span>
                </div>

                <div class="p-3 rounded-xl bg-sage-50/70 border border-sage-100">
                    <span class="text-earth-500 block text-[10px] uppercase font-bold mb-1">Rekening / Dompet</span>
                    <span class="font-semibold text-earth-800 flex items-center gap-1.5">
                        <x-icon :name="$transaction->account->icon ?? 'cash-leaf'" class="w-4 h-4 text-sage-600" />
                        <span>
                            @if($transaction->type === 'transfer')
                                {{ $transaction->account->name ?? '-' }} → {{ $transaction->destinationAccount->name ?? '-' }}
                            @else
                                {{ $transaction->account->name ?? '-' }}
                            @endif
                        </span>
                    </span>
                </div>
            </div>

            @if($transaction->notes)
                <div class="mb-6 p-3.5 rounded-xl bg-earth-50/60 border border-earth-100 text-xs">
                    <span class="text-earth-500 block text-[10px] uppercase font-bold mb-1">Catatan Tambahan</span>
                    <p class="text-earth-800 italic leading-relaxed">{{ $transaction->notes }}</p>
                </div>
            @endif

            <div class="flex items-center justify-between pt-4 border-t border-sage-100 text-xs">
                <div class="text-earth-400 text-[11px]">
                    Dicatat: {{ $transaction->created_at ? $transaction->created_at->format('d M Y, H:i') : '-' }}
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('transactions.edit', $transaction) }}" class="btn-flora-secondary text-xs py-1.5 px-3 flex items-center gap-1">
                        <x-icon name="edit-leaf" class="w-3.5 h-3.5" />
                        <span>Edit</span>
                    </a>
                    <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-flora-secondary text-xs py-1.5 px-3 text-coral-600 hover:bg-coral-50 flex items-center gap-1">
                            <x-icon name="delete-wilt" class="w-3.5 h-3.5" />
                            <span>Hapus</span>
                        </button>
                    </form>
                </div>
            </div>
        </x-card>
    </div>
</x-app-layout>
