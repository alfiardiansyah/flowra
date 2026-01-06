<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-heading text-3xl text-sage-600 flex items-center gap-3">
                    <x-icon name="edit-leaf" class="w-8 h-8 text-sage-400" />
                    Edit Pengeluaran
                </h2>
                <p class="mt-1 text-earth-600">Update your expense record</p>
            </div>
            <a href="{{ route('expenses.index') }}" class="btn-flora-secondary">
                Kembali
            </a>
        </div>
    </x-slot>

    <x-card class="max-w-3xl mx-auto">
        <form action="{{ route('expenses.update', $expense) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Nominal -->
            <div class="form-section">
                <label class="form-section-title">
                    <x-icon name="tree" class="w-5 h-5" />
                    Nominal Pengeluaran
                </label>
                <input type="number" name="nominal" id="nominal" 
                       value="{{ old('nominal', $expense->nominal) }}" 
                       step="0.01" min="0"
                       required
                       class="flora-input @error('nominal') border-coral-400 @enderror"
                       placeholder="Masukkan nominal">
                @error('nominal')
                    <p class="mt-1 text-sm text-coral-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Kategori -->
            <div class="form-section">
                <label class="form-section-title">
                    <x-icon name="bouquet" class="w-5 h-5" />
                    Kategori
                </label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @php
                        $categories = [
                            'Makanan' => ['icon' => 'apple', 'color' => 'coral'],
                            'Transport' => ['icon' => 'leaf-wind', 'color' => 'sky'],
                            'Belanja' => ['icon' => 'shopping-leaf', 'color' => 'sage'],
                            'Tagihan' => ['icon' => 'cactus', 'color' => 'earth'],
                            'Hiburan' => ['icon' => 'bouquet', 'color' => 'lavender'],
                            'Kesehatan' => ['icon' => 'medical-leaf', 'color' => 'mint'],
                            'Pendidikan' => ['icon' => 'book-sprout', 'color' => 'leaf'],
                            'Lainnya' => ['icon' => 'mixed-leaves', 'color' => 'earth']
                        ];
                        $currentKategori = old('kategori', $expense->kategori);
                    @endphp
                    @foreach($categories as $key => $cat)
                        <label class="relative">
                            <input type="radio" name="kategori" value="{{ $key }}" 
                                   {{ $currentKategori == $key ? 'checked' : '' }}
                                   class="peer sr-only">
                            <div class="flex flex-col items-center gap-2 p-3 rounded-xl border-2 border-sage-200 cursor-pointer transition-all peer-checked:border-{{ $cat['color'] }}-400 peer-checked:bg-{{ $cat['color'] }}-50 hover:border-{{ $cat['color'] }}-300">
                                <x-icon :name="$cat['icon']" class="w-6 h-6 text-{{ $cat['color'] }}-600" />
                                <span class="text-xs font-medium text-earth-700 text-center">{{ $key }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('kategori')
                    <p class="mt-1 text-sm text-coral-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Keterangan -->
            <div class="form-section">
                <label class="form-section-title">
                    <x-icon name="flower" class="w-5 h-5" />
                    Keterangan
                </label>
                <textarea name="keterangan" id="keterangan" rows="3"
                          class="flora-input @error('keterangan') border-coral-400 @enderror"
                          placeholder="Tambahkan keterangan (opsional)">{{ old('keterangan', $expense->keterangan) }}</textarea>
                @error('keterangan')
                    <p class="mt-1 text-sm text-coral-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tanggal -->
            <div class="form-section">
                <label class="form-section-title">
                    <x-icon name="flower-bloom" class="w-5 h-5" />
                    Tanggal
                </label>
                <input type="date" name="tanggal" id="tanggal" 
                       value="{{ old('tanggal', $expense->tanggal ? $expense->tanggal->format('Y-m-d') : now()->format('Y-m-d')) }}"
                       class="flora-input @error('tanggal') border-coral-400 @enderror">
                @error('tanggal')
                    <p class="mt-1 text-sm text-coral-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Metode Pembayaran -->
            <div class="form-section">
                <label class="form-section-title">
                    <x-icon name="cash-leaf" class="w-5 h-5" />
                    Metode Pembayaran
                </label>
                <select name="metode_pembayaran" id="metode_pembayaran" 
                        class="flora-input @error('metode_pembayaran') border-coral-400 @enderror">
                    <option value="">Pilih Metode</option>
                    <option value="BCA" {{ old('metode_pembayaran', $expense->metode_pembayaran) == 'BCA' ? 'selected' : '' }}>BCA</option>
                    <option value="Mandiri" {{ old('metode_pembayaran', $expense->metode_pembayaran) == 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
                    <option value="BRI" {{ old('metode_pembayaran', $expense->metode_pembayaran) == 'BRI' ? 'selected' : '' }}>BRI</option>
                    <option value="Cash" {{ old('metode_pembayaran', $expense->metode_pembayaran) == 'Cash' ? 'selected' : '' }}>Cash</option>
                    <option value="E-Wallet" {{ old('metode_pembayaran', $expense->metode_pembayaran) == 'E-Wallet' ? 'selected' : '' }}>E-Wallet</option>
                </select>
                @error('metode_pembayaran')
                    <p class="mt-1 text-sm text-coral-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Bukti Pembayaran -->
            <div class="form-section">
                <label class="form-section-title">
                    <x-icon name="flower" class="w-5 h-5" />
                    Bukti Pembayaran
                </label>
                @if($expense->bukti_pembayaran)
                    <div class="mb-4">
                        <p class="text-sm text-earth-600 mb-2">Bukti saat ini:</p>
                        <img src="{{ asset('storage/' . $expense->bukti_pembayaran) }}" alt="Current proof" class="max-w-xs rounded-lg shadow-md">
                    </div>
                @endif
                <div class="border-2 border-dashed border-sage-300 rounded-xl p-8 text-center hover:border-sage-400 transition-colors">
                    <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" 
                           accept="image/*"
                           class="hidden"
                           onchange="previewImage(this)">
                    <label for="bukti_pembayaran" class="cursor-pointer">
                        <x-icon name="add-seed" class="w-12 h-12 text-sage-400 mx-auto mb-3" />
                        <p class="text-earth-600 mb-2">Klik untuk upload atau drag & drop</p>
                        <p class="text-sm text-earth-500">PNG, JPG maksimal 2MB</p>
                    </label>
                    <div id="imagePreview" class="mt-4 hidden">
                        <img id="previewImg" src="" alt="Preview" class="max-w-xs mx-auto rounded-lg shadow-md">
                    </div>
                </div>
                @error('bukti_pembayaran')
                    <p class="mt-1 text-sm text-coral-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex gap-4 justify-end pt-6 border-t border-sage-200">
                <a href="{{ route('expenses.index') }}" class="btn-flora-secondary">
                    Batal
                </a>
                <button type="submit" class="btn-flora-primary">
                    <x-icon name="edit-leaf" class="w-5 h-5" />
                    Update Expense
                </button>
            </div>
        </form>
    </x-card>

    @push('scripts')
    <script>
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.classList.add('hidden');
            }
        }
    </script>
    @endpush
</x-app-layout>


