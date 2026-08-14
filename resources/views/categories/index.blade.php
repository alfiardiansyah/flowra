<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-heading text-3xl text-sage-600 flex items-center gap-3">
                    <x-icon name="bouquet" class="w-8 h-8 text-sage-400" />
                    Kategori Keuangan
                </h2>
                <p class="mt-1 text-earth-600 text-sm">Kelola kategori utama dan subkategori untuk pengelompokan transaksi</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('categories.create', ['type' => 'expense']) }}" class="btn-flora-secondary text-sm flex items-center gap-1.5">
                    <x-icon name="falling-leaves" class="w-4 h-4 text-coral-500" />
                    <span>+ Kategori Pengeluaran</span>
                </a>
                <a href="{{ route('categories.create', ['type' => 'income']) }}" class="btn-flora-primary text-sm flex items-center gap-1.5">
                    <x-icon name="sprout" class="w-4 h-4 text-white" />
                    <span>+ Kategori Pemasukan</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Expense Categories -->
        <div>
            <div class="flex items-center justify-between mb-4 pb-2 border-b border-coral-200">
                <h3 class="font-heading text-xl text-coral-700 flex items-center gap-2">
                    <x-icon name="falling-leaves" class="w-5 h-5 text-coral-500" />
                    Kategori Pengeluaran (Expense)
                </h3>
                <span class="text-xs text-earth-500 font-medium">{{ $expenseCategories->count() }} Kategori Utama</span>
            </div>

            <div class="space-y-4">
                @foreach($expenseCategories as $cat)
                    <div class="flora-card p-5 border-l-4 transition-all hover:shadow-flora" style="border-left-color: {{ $cat->color }};">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-coral-50">
                                    <x-icon :name="$cat->icon" class="w-6 h-6" />
                                </div>
                                <div>
                                    <h4 class="font-heading text-base font-bold text-earth-800">{{ $cat->name }}</h4>
                                    <div class="text-[11px] text-earth-500">
                                        {{ $cat->children->count() }} Subkategori
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-1">
                                <a href="{{ route('categories.create', ['parent_id' => $cat->id, 'type' => 'expense']) }}" 
                                   class="text-[11px] font-semibold text-sage-600 hover:text-sage-800 bg-sage-50 hover:bg-sage-100 py-1 px-2 rounded-lg" title="Tambah Subkategori">
                                    + Subkategori
                                </a>
                                @if(!$cat->is_default)
                                    <a href="{{ route('categories.edit', $cat) }}" class="p-1 text-sage-600 hover:text-sage-800 rounded hover:bg-sage-50" title="Edit">
                                        <x-icon name="edit-leaf" class="w-4 h-4" />
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Subcategories Pills -->
                        @if($cat->children->count() > 0)
                            <div class="flex flex-wrap gap-1.5 pt-2 border-t border-sage-100">
                                @foreach($cat->children as $child)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs bg-sage-50 text-earth-700 border border-sage-200">
                                        <span>{{ $child->name }}</span>
                                        @if(!$child->is_default)
                                            <a href="{{ route('categories.edit', $child) }}" class="text-sage-500 hover:text-sage-700 ml-0.5">
                                                <x-icon name="edit-leaf" class="w-3 h-3" />
                                            </a>
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Income Categories -->
        <div>
            <div class="flex items-center justify-between mb-4 pb-2 border-b border-leaf-200">
                <h3 class="font-heading text-xl text-leaf-700 flex items-center gap-2">
                    <x-icon name="sprout" class="w-5 h-5 text-leaf-500" />
                    Kategori Pemasukan (Income)
                </h3>
                <span class="text-xs text-earth-500 font-medium">{{ $incomeCategories->count() }} Kategori Utama</span>
            </div>

            <div class="space-y-4">
                @foreach($incomeCategories as $cat)
                    <div class="flora-card p-5 border-l-4 transition-all hover:shadow-flora" style="border-left-color: {{ $cat->color }};">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-mint-50">
                                    <x-icon :name="$cat->icon" class="w-6 h-6" />
                                </div>
                                <div>
                                    <h4 class="font-heading text-base font-bold text-earth-800">{{ $cat->name }}</h4>
                                    <div class="text-[11px] text-earth-500">
                                        {{ $cat->children->count() }} Subkategori
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-1">
                                <a href="{{ route('categories.create', ['parent_id' => $cat->id, 'type' => 'income']) }}" 
                                   class="text-[11px] font-semibold text-sage-600 hover:text-sage-800 bg-sage-50 hover:bg-sage-100 py-1 px-2 rounded-lg" title="Tambah Subkategori">
                                    + Subkategori
                                </a>
                                @if(!$cat->is_default)
                                    <a href="{{ route('categories.edit', $cat) }}" class="p-1 text-sage-600 hover:text-sage-800 rounded hover:bg-sage-50" title="Edit">
                                        <x-icon name="edit-leaf" class="w-4 h-4" />
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Subcategories Pills -->
                        @if($cat->children->count() > 0)
                            <div class="flex flex-wrap gap-1.5 pt-2 border-t border-sage-100">
                                @foreach($cat->children as $child)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs bg-sage-50 text-earth-700 border border-sage-200">
                                        <span>{{ $child->name }}</span>
                                        @if(!$child->is_default)
                                            <a href="{{ route('categories.edit', $child) }}" class="text-sage-500 hover:text-sage-700 ml-0.5">
                                                <x-icon name="edit-leaf" class="w-3 h-3" />
                                            </a>
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
