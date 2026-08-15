<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-heading text-3xl text-sage-600 flex items-center gap-3">
                    <x-icon name="bouquet" class="w-8 h-8 text-sage-400" />
                    Kategori Keuangan
                </h2>
                <p class="mt-1 text-earth-600 text-sm">Kelola kategori pengeluaran dan pemasukan untuk pengelompokan transaksi yang fleksibel</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" @click="$dispatch('open-cat-modal', { type: 'expense' })" class="btn-flora-secondary text-sm flex items-center gap-1.5 shadow-xs cursor-pointer">
                    <x-icon name="falling-leaves" class="w-4 h-4 text-coral-500" />
                    <span>+ Kategori Pengeluaran</span>
                </button>
                <button type="button" @click="$dispatch('open-cat-modal', { type: 'income' })" class="btn-flora-primary text-sm flex items-center gap-1.5 shadow-xs cursor-pointer">
                    <x-icon name="sprout" class="w-4 h-4 text-white" />
                    <span>+ Kategori Pemasukan</span>
                </button>
            </div>
        </div>
    </x-slot>

    <!-- Outer Alpine Controller for Management, Modals & Live Filter -->
    <div x-data="{
        modalOpen: false,
        deleteModalOpen: false,
        
        modalMode: 'create', // 'create' or 'edit'
        modalTitle: 'Tambah Kategori Baru',
        modalActionUrl: '{{ route('categories.store') }}',
        modalType: 'expense',
        modalParentId: '',
        modalName: '',
        modalIcon: 'folder',
        modalColor: '#6B8E23',

        activeCategory: null,
        searchQuery: '',
        activeTab: 'all', // 'all', 'expense', 'income'

        presetIcons: [
            'folder', 'leaf', 'sprout', 'falling-leaves', 'bouquet', 'flower', 
            'flower-bloom', 'cash-leaf', 'tree', 'add-seed', 'edit-leaf', 'delete-wilt'
        ],

        presetColors: [
            '#6B8E23', '#FF6B6B', '#3B82F6', '#10B981', '#F59E0B', 
            '#8B5CF6', '#EC4899', '#14B8A6', '#64748B', '#D97706'
        ],

        getCatFromEl(el) {
            try {
                const targetEl = (el && el.hasAttribute && el.hasAttribute('data-cat')) ? el : el.closest('[data-cat]');
                if (!targetEl) return null;
                const b64 = targetEl.getAttribute('data-cat');
                if (!b64) return null;
                return JSON.parse(atob(b64));
            } catch(e) {
                console.error('Error parsing category base64 data:', e);
                return null;
            }
        },

        openCreateModal(type = 'expense', parentId = '') {
            this.modalMode = 'create';
            this.modalTitle = parentId ? 'Tambah Subkategori Baru' : 'Tambah Kategori Utama Baru';
            this.modalActionUrl = '{{ route('categories.store') }}';
            this.modalType = type;
            this.modalParentId = parentId ? String(parentId) : '';
            this.modalName = '';
            this.modalIcon = 'leaf';
            this.modalColor = type === 'expense' ? '#FF6B6B' : '#6B8E23';
            this.modalOpen = true;
        },

        openEditModal(cat) {
            if (!cat) return;
            this.activeCategory = cat;
            this.modalMode = 'edit';
            this.modalTitle = 'Edit Kategori ' + cat.name;
            this.modalActionUrl = cat.update_url;
            this.modalType = cat.type;
            this.modalParentId = cat.parent_id ? String(cat.parent_id) : '';
            this.modalName = cat.name;
            this.modalIcon = cat.icon || 'leaf';
            this.modalColor = cat.color || '#6B8E23';
            this.modalOpen = true;
        },

        openDeleteModal(cat) {
            if (!cat) return;
            this.activeCategory = cat;
            this.deleteModalOpen = true;
        },

        matchesSearch(name) {
            if (!this.searchQuery.trim()) return true;
            return name.toLowerCase().includes(this.searchQuery.toLowerCase().trim());
        }
    }" 
    @open-cat-modal.window="openCreateModal($event.detail.type || 'expense', $event.detail.parentId || '')">

        <!-- Search & Filter Header Toolbar -->
        <div class="flora-card p-4 mb-8 bg-white/80 backdrop-blur-md border border-sage-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <!-- Filter Tabs -->
            <div class="flex items-center gap-1.5 w-full sm:w-auto">
                <button type="button" @click="activeTab = 'all'" 
                        :class="activeTab === 'all' ? 'bg-sage-600 text-white shadow-xs' : 'bg-sage-50 text-earth-700 hover:bg-sage-100'"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition-colors cursor-pointer flex-1 sm:flex-none text-center">
                    Semua Kategori
                </button>
                <button type="button" @click="activeTab = 'expense'" 
                        :class="activeTab === 'expense' ? 'bg-coral-600 text-white shadow-xs' : 'bg-coral-50 text-coral-800 hover:bg-coral-100'"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition-colors cursor-pointer flex-1 sm:flex-none text-center">
                    Pengeluaran
                </button>
                <button type="button" @click="activeTab = 'income'" 
                        :class="activeTab === 'income' ? 'bg-leaf-600 text-white shadow-xs' : 'bg-leaf-50 text-leaf-800 hover:bg-leaf-100'"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition-colors cursor-pointer flex-1 sm:flex-none text-center">
                    Pemasukan
                </button>
            </div>

            <!-- Search Input -->
            <div class="relative w-full sm:w-72">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-sage-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" x-model="searchQuery" placeholder="Cari nama kategori..." class="flora-input text-xs pl-9 py-2 border-sage-200 focus:border-sage-500">
                <button x-show="searchQuery" @click="searchQuery = ''" type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs text-earth-400 hover:text-earth-600">
                    ✕
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Expense Categories Section -->
            <div x-show="activeTab === 'all' || activeTab === 'expense'">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-coral-200">
                    <h3 class="font-heading text-xl text-coral-700 flex items-center gap-2">
                        <x-icon name="falling-leaves" class="w-5 h-5 text-coral-500" />
                        <span>Kategori Pengeluaran</span>
                    </h3>
                    <span class="text-xs text-earth-500 font-bold bg-coral-50 text-coral-700 py-1 px-3 rounded-full border border-coral-200">
                        {{ $expenseCategories->count() }} Kategori Utama
                    </span>
                </div>

                <div class="space-y-4">
                    @foreach($expenseCategories as $cat)
                        @php
                            $catDataData = [
                                'id' => $cat->id,
                                'name' => $cat->name,
                                'type' => $cat->type,
                                'parent_id' => $cat->parent_id,
                                'icon' => $cat->icon,
                                'color' => $cat->color,
                                'is_default' => $cat->is_default,
                                'transaction_count' => $cat->transactions_count,
                                'update_url' => route('categories.update', $cat),
                                'delete_url' => route('categories.destroy', $cat),
                            ];
                            $catB64 = base64_encode(json_encode($catDataData));
                        @endphp

                        <div class="flora-card p-5 border-l-4 transition-all hover:shadow-flora-lg relative"
                             style="border-left-color: {{ $cat->color }};"
                             data-cat="{{ $catB64 }}"
                             x-show="matchesSearch('{{ addslashes($cat->name) }}') || {{ json_encode($cat->children->pluck('name')->toArray()) }}.some(child => matchesSearch(child))">
                            
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0 shadow-2xs" style="background-color: {{ $cat->color }}20;">
                                        <x-icon :name="$cat->icon" class="w-6 h-6" />
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-heading text-base font-bold text-earth-800">{{ $cat->name }}</h4>
                                            @if($cat->is_default)
                                                <span class="text-[10px] font-semibold text-sage-600 bg-sage-100 py-0.5 px-2 rounded-md">Bawaan</span>
                                            @else
                                                <span class="text-[10px] font-semibold text-sky-600 bg-sky-100 py-0.5 px-2 rounded-md">Kustom</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-earth-500 font-medium mt-0.5 flex items-center gap-2">
                                            <span>{{ $cat->children->count() }} Subkategori</span>
                                            <span>•</span>
                                            <span class="text-coral-600 font-semibold">{{ $cat->transactions_count }} Transaksi</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center gap-1 shrink-0">
                                    <button type="button" 
                                            @click="openCreateModal('expense', '{{ $cat->id }}')" 
                                            class="text-[11px] font-bold text-sage-700 hover:text-sage-900 bg-sage-100 hover:bg-sage-200 py-1.5 px-2.5 rounded-xl transition-colors cursor-pointer" 
                                            title="Tambah Subkategori">
                                        + Subkategori
                                    </button>

                                    <button type="button" 
                                            @click="openEditModal(getCatFromEl($el))" 
                                            class="p-1.5 text-sage-600 hover:text-sage-900 hover:bg-sage-100 rounded-xl transition-colors cursor-pointer" 
                                            title="Edit Kategori">
                                        <x-icon name="edit-leaf" class="w-4 h-4" />
                                    </button>

                                    <button type="button" 
                                            @click="openDeleteModal(getCatFromEl($el))" 
                                            class="p-1.5 text-coral-600 hover:text-coral-800 hover:bg-coral-100 rounded-xl transition-colors cursor-pointer" 
                                            title="Hapus Kategori">
                                        <x-icon name="delete-wilt" class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>

                            <!-- Subcategories Interactive Pills -->
                            @if($cat->children->count() > 0)
                                <div class="flex flex-wrap gap-1.5 pt-3 border-t border-sage-100 mt-2">
                                    @foreach($cat->children as $child)
                                        @php
                                            $childDataData = [
                                                'id' => $child->id,
                                                'name' => $child->name,
                                                'type' => $child->type,
                                                'parent_id' => $child->parent_id,
                                                'icon' => $child->icon,
                                                'color' => $child->color,
                                                'is_default' => $child->is_default,
                                                'transaction_count' => $child->transactions_count,
                                                'update_url' => route('categories.update', $child),
                                                'delete_url' => route('categories.destroy', $child),
                                            ];
                                            $childB64 = base64_encode(json_encode($childDataData));
                                        @endphp

                                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs bg-sage-50/80 text-earth-800 border border-sage-200/80 font-medium"
                                             data-cat="{{ $childB64 }}"
                                             x-show="matchesSearch('{{ addslashes($child->name) }}')">
                                            <span>{{ $child->name }}</span>
                                            <span class="text-[10px] text-earth-400">({{ $child->transactions_count }})</span>

                                            <button type="button" @click="openEditModal(getCatFromEl($el))" class="text-sage-500 hover:text-sage-700 ml-1 cursor-pointer">
                                                <x-icon name="edit-leaf" class="w-3 h-3" />
                                            </button>
                                            <button type="button" @click="openDeleteModal(getCatFromEl($el))" class="text-coral-500 hover:text-coral-700 cursor-pointer">
                                                <x-icon name="delete-wilt" class="w-3 h-3" />
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Income Categories Section -->
            <div x-show="activeTab === 'all' || activeTab === 'income'">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-leaf-200">
                    <h3 class="font-heading text-xl text-leaf-700 flex items-center gap-2">
                        <x-icon name="sprout" class="w-5 h-5 text-leaf-500" />
                        <span>Kategori Pemasukan</span>
                    </h3>
                    <span class="text-xs text-earth-500 font-bold bg-leaf-50 text-leaf-700 py-1 px-3 rounded-full border border-leaf-200">
                        {{ $incomeCategories->count() }} Kategori Utama
                    </span>
                </div>

                <div class="space-y-4">
                    @foreach($incomeCategories as $cat)
                        @php
                            $catDataData = [
                                'id' => $cat->id,
                                'name' => $cat->name,
                                'type' => $cat->type,
                                'parent_id' => $cat->parent_id,
                                'icon' => $cat->icon,
                                'color' => $cat->color,
                                'is_default' => $cat->is_default,
                                'transaction_count' => $cat->transactions_count,
                                'update_url' => route('categories.update', $cat),
                                'delete_url' => route('categories.destroy', $cat),
                            ];
                            $catB64 = base64_encode(json_encode($catDataData));
                        @endphp

                        <div class="flora-card p-5 border-l-4 transition-all hover:shadow-flora-lg relative"
                             style="border-left-color: {{ $cat->color }};"
                             data-cat="{{ $catB64 }}"
                             x-show="matchesSearch('{{ addslashes($cat->name) }}') || {{ json_encode($cat->children->pluck('name')->toArray()) }}.some(child => matchesSearch(child))">
                            
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0 shadow-2xs" style="background-color: {{ $cat->color }}20;">
                                        <x-icon :name="$cat->icon" class="w-6 h-6" />
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-heading text-base font-bold text-earth-800">{{ $cat->name }}</h4>
                                            @if($cat->is_default)
                                                <span class="text-[10px] font-semibold text-sage-600 bg-sage-100 py-0.5 px-2 rounded-md">Bawaan</span>
                                            @else
                                                <span class="text-[10px] font-semibold text-sky-600 bg-sky-100 py-0.5 px-2 rounded-md">Kustom</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-earth-500 font-medium mt-0.5 flex items-center gap-2">
                                            <span>{{ $cat->children->count() }} Subkategori</span>
                                            <span>•</span>
                                            <span class="text-leaf-600 font-semibold">{{ $cat->transactions_count }} Transaksi</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center gap-1 shrink-0">
                                    <button type="button" 
                                            @click="openCreateModal('income', '{{ $cat->id }}')" 
                                            class="text-[11px] font-bold text-sage-700 hover:text-sage-900 bg-sage-100 hover:bg-sage-200 py-1.5 px-2.5 rounded-xl transition-colors cursor-pointer" 
                                            title="Tambah Subkategori">
                                        + Subkategori
                                    </button>

                                    <button type="button" 
                                            @click="openEditModal(getCatFromEl($el))" 
                                            class="p-1.5 text-sage-600 hover:text-sage-900 hover:bg-sage-100 rounded-xl transition-colors cursor-pointer" 
                                            title="Edit Kategori">
                                        <x-icon name="edit-leaf" class="w-4 h-4" />
                                    </button>

                                    <button type="button" 
                                            @click="openDeleteModal(getCatFromEl($el))" 
                                            class="p-1.5 text-coral-600 hover:text-coral-800 hover:bg-coral-100 rounded-xl transition-colors cursor-pointer" 
                                            title="Hapus Kategori">
                                        <x-icon name="delete-wilt" class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>

                            <!-- Subcategories Interactive Pills -->
                            @if($cat->children->count() > 0)
                                <div class="flex flex-wrap gap-1.5 pt-3 border-t border-sage-100 mt-2">
                                    @foreach($cat->children as $child)
                                        @php
                                            $childDataData = [
                                                'id' => $child->id,
                                                'name' => $child->name,
                                                'type' => $child->type,
                                                'parent_id' => $child->parent_id,
                                                'icon' => $child->icon,
                                                'color' => $child->color,
                                                'is_default' => $child->is_default,
                                                'transaction_count' => $child->transactions_count,
                                                'update_url' => route('categories.update', $child),
                                                'delete_url' => route('categories.destroy', $child),
                                            ];
                                            $childB64 = base64_encode(json_encode($childDataData));
                                        @endphp

                                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs bg-sage-50/80 text-earth-800 border border-sage-200/80 font-medium"
                                             data-cat="{{ $childB64 }}"
                                             x-show="matchesSearch('{{ addslashes($child->name) }}')">
                                            <span>{{ $child->name }}</span>
                                            <span class="text-[10px] text-earth-400">({{ $child->transactions_count }})</span>

                                            <button type="button" @click="openEditModal(getCatFromEl($el))" class="text-sage-500 hover:text-sage-700 ml-1 cursor-pointer">
                                                <x-icon name="edit-leaf" class="w-3 h-3" />
                                            </button>
                                            <button type="button" @click="openDeleteModal(getCatFromEl($el))" class="text-coral-500 hover:text-coral-700 cursor-pointer">
                                                <x-icon name="delete-wilt" class="w-3 h-3" />
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- ================= 1. CREATE / EDIT CATEGORY MODAL ================= -->
        <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4" role="dialog" aria-modal="true">
            <div x-show="modalOpen" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-earth-900/60 backdrop-blur-sm transition-opacity" @click="modalOpen = false"></div>

            <div x-show="modalOpen"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="flora-card max-w-lg w-full bg-white shadow-2xl relative z-10 p-6 border border-sage-200">

                <div class="flex items-center justify-between pb-3 border-b border-sage-100 mb-4">
                    <h3 class="font-heading text-xl font-bold text-sage-800" x-text="modalTitle"></h3>
                    <button type="button" @click="modalOpen = false" class="text-earth-400 hover:text-earth-600 p-1 rounded-lg hover:bg-sage-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form :action="modalActionUrl" method="POST" class="space-y-4">
                    @csrf
                    <template x-if="modalMode === 'edit'">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <!-- Type Selection -->
                    <div>
                        <label class="block text-xs font-semibold text-earth-700 mb-1">Tipe Kategori</label>
                        <select name="type" x-model="modalType" required class="flora-input text-sm">
                            <option value="expense">Pengeluaran (Expense)</option>
                            <option value="income">Pemasukan (Income)</option>
                        </select>
                    </div>

                    <!-- Parent Category Select (for Subcategory) -->
                    <div>
                        <label class="block text-xs font-semibold text-earth-700 mb-1">Induk Kategori (Opsional / Kosongkan Jika Utama)</label>
                        <select name="parent_id" x-model="modalParentId" class="flora-input text-sm">
                            <option value="">-- Kategori Utama (Tanpa Induk) --</option>
                            @foreach($allCategories->whereNull('parent_id') as $parent)
                                <option value="{{ $parent->id }}">
                                    {{ $parent->type === 'expense' ? 'Pengeluaran' : 'Pemasukan' }} — {{ $parent->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Category Name -->
                    <div>
                        <label class="block text-xs font-semibold text-earth-700 mb-1">Nama Kategori</label>
                        <input type="text" name="name" x-model="modalName" placeholder="Contoh: Makanan, Transportasi, Gaji" required class="flora-input text-sm">
                    </div>

                    <!-- Color Picker -->
                    <div>
                        <label class="block text-xs font-semibold text-earth-700 mb-1">Warna Label Kategori</label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="color" x-model="modalColor" class="w-10 h-10 rounded-xl border border-sage-200 p-1 cursor-pointer shrink-0">
                            <div class="flex flex-wrap gap-1.5">
                                <template x-for="c in presetColors" :key="c">
                                    <button type="button" @click="modalColor = c" 
                                            class="w-6 h-6 rounded-lg transition-transform hover:scale-110 cursor-pointer border border-black/10"
                                            :style="'background-color: ' + c"></button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Icon Selection -->
                    <div>
                        <label class="block text-xs font-semibold text-earth-700 mb-1">Ikon Botani</label>
                        <input type="hidden" name="icon" x-model="modalIcon">
                        <div class="grid grid-cols-6 gap-2 p-3 bg-sage-50/70 border border-sage-200 rounded-2xl max-h-36 overflow-y-auto">
                            <template x-for="iconName in presetIcons" :key="iconName">
                                <button type="button" @click="modalIcon = iconName" 
                                        :class="modalIcon === iconName ? 'bg-sage-600 text-white shadow-xs scale-105' : 'bg-white text-earth-700 hover:bg-sage-100'"
                                        class="p-2.5 rounded-xl flex items-center justify-center transition-all cursor-pointer">
                                    <span x-text="iconName" class="text-[10px] font-mono truncate max-w-full"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <div class="flex gap-2 justify-end pt-3 border-t border-sage-100">
                        <button type="button" @click="modalOpen = false" class="btn-flora-secondary text-xs py-2 px-4">Batal</button>
                        <button type="submit" class="btn-flora-primary text-xs py-2 px-5 shadow-sm">Simpan Kategori</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ================= 2. DELETE CONFIRMATION MODAL ================= -->
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

                    <form :action="activeCategory ? activeCategory.delete_url : '#'" method="POST">
                        @csrf
                        @method('DELETE')

                        <div class="flex items-start gap-4 mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-coral-100 text-coral-600 flex items-center justify-center shrink-0">
                                <x-icon name="delete-wilt" class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-xl font-bold font-heading text-earth-900">
                                    Hapus Kategori "<span x-text="activeCategory ? activeCategory.name : ''"></span>"
                                </h3>
                                <p class="text-xs text-earth-600 mt-1">
                                    Terikat pada <span class="font-bold text-coral-600" x-text="activeCategory ? activeCategory.transaction_count : 0"></span> catatan transaksi.
                                </p>
                            </div>
                        </div>

                        <template x-if="activeCategory && activeCategory.transaction_count > 0">
                            <div class="space-y-3 mb-5">
                                <label class="text-xs font-semibold text-earth-800 block">Pilih Tindakan Penghapusan:</label>

                                <!-- Option 1: Reassign -->
                                <div class="p-3.5 rounded-2xl border border-sage-200 bg-white hover:bg-sage-50/50 transition-all cursor-pointer">
                                    <div class="flex items-start gap-3">
                                        <input type="radio" name="action" value="reassign" checked class="mt-0.5 text-sage-600 focus:ring-sage-500">
                                        <div class="text-xs w-full">
                                            <div class="font-bold text-earth-800">Pindahkan Transaksi ke Kategori Lain</div>
                                            <div class="text-earth-600 text-[11px] mt-0.5">Seluruh transaksi akan dipindahkan agar catatan keuangan Anda tetap rapi.</div>
                                            <div class="mt-2">
                                                <select name="target_category_id" class="flora-input text-xs py-1.5 px-3">
                                                    @foreach($allCategories as $otherCat)
                                                        <option value="{{ $otherCat->id }}">{{ $otherCat->name }} ({{ $otherCat->type }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Option 2: Unassign / Cascade -->
                                <div class="p-3.5 rounded-2xl border border-coral-200 bg-coral-50/40 transition-all cursor-pointer">
                                    <div class="flex items-start gap-3">
                                        <input type="radio" name="action" value="cascade" class="mt-0.5 text-coral-600 focus:ring-coral-500">
                                        <div class="text-xs">
                                            <div class="font-bold text-coral-700">Kosongkan Kategori Transaksi & Hapus Kategori</div>
                                            <div class="text-earth-600 text-[11px] mt-0.5">Kategori transaksi terkait akan diubah menjadi "Tanpa Kategori".</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template x-if="!activeCategory || activeCategory.transaction_count === 0">
                            <div class="bg-sage-50 p-4 rounded-2xl border border-sage-200 mb-5 text-xs text-earth-700">
                                Kategori ini belum memiliki transaksi terkait dan dapat dihapus secara permanen dari sistem.
                            </div>
                        </template>

                        <div class="flex justify-end gap-2 pt-3 border-t border-sage-100">
                            <button type="button" @click="deleteModalOpen = false" class="btn-flora-secondary text-xs py-2 px-4">
                                Batal
                            </button>
                            <button type="submit" class="btn-flora-primary text-xs !bg-coral-600 hover:!bg-coral-700 !border-coral-600 shadow-sm flex items-center gap-1 cursor-pointer py-2 px-5">
                                <x-icon name="delete-wilt" class="w-3.5 h-3.5 text-white" />
                                <span>Ya, Hapus Kategori</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
