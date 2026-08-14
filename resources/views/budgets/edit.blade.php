<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-heading text-3xl text-sage-600 flex items-center gap-3">
                    <x-icon name="edit-leaf" class="w-8 h-8 text-sage-400" />
                    Edit Anggaran {{ $budget->category->name ?? 'Kategori' }}
                </h2>
                <p class="mt-1 text-earth-600 text-sm">Bulan {{ \Carbon\Carbon::parse($budget->month . '-01')->locale('id')->isoFormat('MMMM Y') }}</p>
            </div>
            <a href="{{ route('budgets.index', ['month' => $budget->month]) }}" class="btn-flora-secondary text-sm">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <x-card class="max-w-2xl mx-auto">
        <form action="{{ route('budgets.update', $budget) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Amount -->
            <div class="form-section pb-4">
                <label class="form-section-title text-sm mb-2">Batas Nominal Anggaran (Rp)</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xl font-bold text-sage-600">Rp</span>
                    <input type="number" name="amount" value="{{ old('amount', $budget->amount) }}" step="0.01" min="1" required class="flora-input pl-12 text-lg font-bold">
                </div>
                <div class="text-xs text-earth-500 mt-2">
                    Saat ini sudah terpakai: <span class="font-bold text-earth-800">Rp {{ number_format($budget->spent_amount, 0, ',', '.') }}</span>
                </div>
                @error('amount')
                    <p class="mt-1 text-xs text-coral-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notes -->
            <div class="form-section pb-4">
                <label class="form-section-title text-sm mb-2">Catatan Tambahan (Opsional)</label>
                <textarea name="notes" rows="2" class="flora-input text-sm">{{ old('notes', $budget->notes) }}</textarea>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 justify-end pt-4 border-t border-sage-200">
                <a href="{{ route('budgets.index', ['month' => $budget->month]) }}" class="btn-flora-secondary">Batal</a>
                <button type="submit" class="btn-flora-primary flex items-center gap-2">
                    <x-icon name="sprout" class="w-4 h-4 text-white" />
                    <span>Perbarui Anggaran</span>
                </button>
            </div>
        </form>
    </x-card>
</x-app-layout>
