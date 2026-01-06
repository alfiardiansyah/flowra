<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-heading text-3xl text-sage-600 flex items-center gap-3">
                    <x-icon name="falling-leaves" class="w-8 h-8 text-coral-400" />
                    Track Your Spending Leaves
                </h2>
                <p class="mt-1 text-earth-600">Record a new expense in your garden</p>
            </div>
            <a href="{{ route('expenses.index') }}" class="btn-flora-secondary">
                Kembali
            </a>
        </div>
    </x-slot>

    <x-card class="max-w-3xl mx-auto">
        <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Nominal -->
            <div class="form-section">
                <label class="form-section-title">
                    <x-icon name="tree" class="w-5 h-5" />
                    Nominal Pengeluaran
                </label>
                <input type="number" name="nominal" id="nominal" 
                       value="{{ old('nominal') }}" 
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
                            'Makanan' => ['icon' => 'apple', 'color' => '#FF7A5C'],
                            'Transport' => ['icon' => 'leaf-wind', 'color' => '#5DADE2'],
                            'Belanja' => ['icon' => 'shopping-leaf', 'color' => '#6B8E23'],
                            'Tagihan' => ['icon' => 'cactus', 'color' => '#8B7355'],
                            'Hiburan' => ['icon' => 'bouquet', 'color' => '#B19CD9'],
                            'Kesehatan' => ['icon' => 'medical-leaf', 'color' => '#9BCF53'],
                            'Pendidikan' => ['icon' => 'book-sprout', 'color' => '#87A96B'],
                            'Lainnya' => ['icon' => 'mixed-leaves', 'color' => '#8B7355']
                        ];
                    @endphp
                    @foreach($categories as $key => $cat)
                        <label class="relative">
                            <input type="radio" name="kategori" value="{{ $key }}" 
                                   {{ old('kategori') == $key ? 'checked' : '' }}
                                   class="peer sr-only"
                                   onchange="updateCategoryStyle(this)">
                            <div class="flex flex-col items-center gap-2 p-3 rounded-xl border-2 border-gray-300 cursor-pointer transition-all duration-200 hover:border-gray-400" 
                                 style="background-color: transparent;"
                                 id="cat-{{ $key }}">
                                <x-icon :name="$cat['icon']" class="w-6 h-6" style="color: {{ $cat['color'] }}" />
                                <span class="text-xs font-medium text-earth-700 text-center">{{ $key }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('kategori')
                    <p class="mt-1 text-sm text-coral-600">{{ $message }}</p>
                @enderror
            </div>

            <script>
            function updateCategoryStyle(input) {
                const categories = document.querySelectorAll('[id^="cat-"]');
                const colorMap = {
                    'Makanan': '#FF7A5C',
                    'Transport': '#5DADE2',
                    'Belanja': '#6B8E23',
                    'Tagihan': '#8B7355',
                    'Hiburan': '#B19CD9',
                    'Kesehatan': '#9BCF53',
                    'Pendidikan': '#87A96B',
                    'Lainnya': '#8B7355'
                };
                
                categories.forEach(cat => {
                    cat.style.borderColor = '#D1D5DB';
                    cat.style.backgroundColor = 'transparent';
                });
                
                if (input.checked) {
                    const selectedDiv = document.getElementById('cat-' + input.value);
                    if (selectedDiv) {
                        selectedDiv.style.borderColor = colorMap[input.value];
                        selectedDiv.style.backgroundColor = colorMap[input.value] + '15';
                    }
                }
            }
            
            // Initialize on page load
            document.addEventListener('DOMContentLoaded', function() {
                const checkedInput = document.querySelector('input[name="kategori"]:checked');
                if (checkedInput) {
                    updateCategoryStyle(checkedInput);
                }
            });
            </script>

            <!-- Keterangan -->
            <div class="form-section">
                <label class="form-section-title">
                    <x-icon name="flower" class="w-5 h-5" />
                    Keterangan
                </label>
                <textarea name="keterangan" id="keterangan" rows="3"
                          class="flora-input @error('keterangan') border-coral-400 @enderror"
                          placeholder="Tambahkan keterangan (opsional)">{{ old('keterangan') }}</textarea>
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
                       value="{{ old('tanggal', now()->format('Y-m-d')) }}"
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
                    <option value="BCA" {{ old('metode_pembayaran') == 'BCA' ? 'selected' : '' }}>BCA</option>
                    <option value="Mandiri" {{ old('metode_pembayaran') == 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
                    <option value="BRI" {{ old('metode_pembayaran') == 'BRI' ? 'selected' : '' }}>BRI</option>
                    <option value="Cash" {{ old('metode_pembayaran') == 'Cash' ? 'selected' : '' }}>Cash</option>
                    <option value="E-Wallet" {{ old('metode_pembayaran') == 'E-Wallet' ? 'selected' : '' }}>E-Wallet</option>
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
                    <x-icon name="falling-leaves" class="w-5 h-5" />
                    Record Expense
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
