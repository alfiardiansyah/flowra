<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Flowra — Kelola Keuanganmu!</title>
        <link rel="icon" type="image/png" href="{{ asset('images/icons/sprout.png') }}">
        <link rel="shortcut icon" href="{{ asset('images/icons/sprout.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&family=Satisfy&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        <style>[x-cloak] { display: none !important; }</style>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased botanical-bg text-earth-800" x-data="{ mobileMenuOpen: false }">
        <div class="min-h-screen flex">
            <!-- Sidebar Navigation (Desktop) -->
            <aside class="flora-sidebar hidden lg:flex flex-col justify-between w-64 fixed left-0 top-0 bottom-0 z-40 text-white select-none">
                <!-- Top Brand Header -->
                <div class="px-5 py-4 border-b border-white/10 flex items-center justify-between flex-shrink-0">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group">
                        <div class="w-8 h-8 rounded-xl bg-white/15 backdrop-blur-md flex items-center justify-center p-1.5 border border-white/20 shadow-sm group-hover:scale-105 transition-transform duration-200">
                            <img src="{{ asset('images/icons/logo.png') }}" alt="Flowra" class="w-full h-full object-contain">
                        </div>
                        <div>
                            <div class="font-heading text-lg font-bold text-white tracking-wide flex items-center gap-1.5 leading-tight">
                                <span>Flowra</span>
                                <span class="w-1.5 h-1.5 rounded-full bg-mint-300"></span>
                            </div>
                            <div class="text-[9px] text-white/70 font-medium tracking-wider uppercase">Financial Garden</div>
                        </div>
                    </a>
                </div>

                <!-- Scrollable Navigation Area -->
                <div class="flex-1 overflow-y-auto px-3.5 py-3 space-y-4 custom-scrollbar">
                    <!-- Quick Add Action CTA -->
                    <div>
                        <button @click="$dispatch('open-quick-transaction')" 
                                class="w-full bg-white text-sage-800 hover:bg-cream-50 font-semibold py-2.5 px-3 rounded-xl shadow-sm hover:shadow-md flex items-center justify-center gap-2 text-xs transition-all duration-200 hover:scale-[1.02] active:scale-[0.98] border border-white/40 group">
                            <x-icon name="add-seed" class="w-4 h-4 text-leaf-600 group-hover:scale-110 transition-transform" />
                            <span class="font-bold tracking-wide">+ Catat Transaksi</span>
                        </button>
                    </div>

                    <nav class="space-y-1">
                        <!-- Group: Menu Utama -->
                        <div class="px-2.5 pb-1 pt-1">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-white/40">Menu Utama</span>
                        </div>

                        <a href="{{ route('dashboard') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition-all duration-150 relative group {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white font-semibold shadow-sm backdrop-blur-sm border border-white/25' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                            @if(request()->routeIs('dashboard'))
                                <span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-mint-300 rounded-r"></span>
                            @endif
                            <x-icon name="tree" class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-white/80 group-hover:text-white' }}" />
                            <span class="truncate">Dashboard</span>
                        </a>

                        <a href="{{ route('transactions.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition-all duration-150 relative group {{ request()->routeIs('transactions.*') ? 'bg-white/20 text-white font-semibold shadow-sm backdrop-blur-sm border border-white/25' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                            @if(request()->routeIs('transactions.*'))
                                <span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-mint-300 rounded-r"></span>
                            @endif
                            <x-icon name="flower-bloom" class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('transactions.*') ? 'text-white' : 'text-white/80 group-hover:text-white' }}" />
                            <span class="truncate">Semua Transaksi</span>
                        </a>

                        <a href="{{ route('accounts.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition-all duration-150 relative group {{ request()->routeIs('accounts.*') ? 'bg-white/20 text-white font-semibold shadow-sm backdrop-blur-sm border border-white/25' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                            @if(request()->routeIs('accounts.*'))
                                <span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-mint-300 rounded-r"></span>
                            @endif
                            <x-icon name="cash-leaf" class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('accounts.*') ? 'text-white' : 'text-white/80 group-hover:text-white' }}" />
                            <span class="truncate">Rekening & Dompet</span>
                        </a>

                        <!-- Group: Perencanaan -->
                        <div class="px-2.5 pb-1 pt-2.5">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-white/40">Perencanaan</span>
                        </div>

                        <a href="{{ route('budgets.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition-all duration-150 relative group {{ request()->routeIs('budgets.*') ? 'bg-white/20 text-white font-semibold shadow-sm backdrop-blur-sm border border-white/25' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                            @if(request()->routeIs('budgets.*'))
                                <span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-mint-300 rounded-r"></span>
                            @endif
                            <x-icon name="sprout" class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('budgets.*') ? 'text-white' : 'text-white/80 group-hover:text-white' }}" />
                            <span class="truncate">Anggaran (Budget)</span>
                        </a>

                        <a href="{{ route('debts.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition-all duration-150 relative group {{ request()->routeIs('debts.*') ? 'bg-white/20 text-white font-semibold shadow-sm backdrop-blur-sm border border-white/25' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                            @if(request()->routeIs('debts.*'))
                                <span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-mint-300 rounded-r"></span>
                            @endif
                            <x-icon name="leaf" class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('debts.*') ? 'text-white' : 'text-white/80 group-hover:text-white' }}" />
                            <span class="truncate">Hutang & Piutang</span>
                        </a>

                        <a href="{{ route('recurring.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition-all duration-150 relative group {{ request()->routeIs('recurring.*') ? 'bg-white/20 text-white font-semibold shadow-sm backdrop-blur-sm border border-white/25' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                            @if(request()->routeIs('recurring.*'))
                                <span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-mint-300 rounded-r"></span>
                            @endif
                            <x-icon name="leaf-wind" class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('recurring.*') ? 'text-white' : 'text-white/80 group-hover:text-white' }}" />
                            <span class="truncate">Transaksi Rutin</span>
                        </a>

                        <!-- Group: Analisis & Data -->
                        <div class="px-2.5 pb-1 pt-2.5">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-white/40">Analisis & Data</span>
                        </div>

                        <a href="{{ route('categories.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition-all duration-150 relative group {{ request()->routeIs('categories.*') ? 'bg-white/20 text-white font-semibold shadow-sm backdrop-blur-sm border border-white/25' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                            @if(request()->routeIs('categories.*'))
                                <span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-mint-300 rounded-r"></span>
                            @endif
                            <x-icon name="bouquet" class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('categories.*') ? 'text-white' : 'text-white/80 group-hover:text-white' }}" />
                            <span class="truncate">Kategori</span>
                        </a>

                        <a href="{{ route('reports.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition-all duration-150 relative group {{ request()->routeIs('reports.*') ? 'bg-white/20 text-white font-semibold shadow-sm backdrop-blur-sm border border-white/25' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                            @if(request()->routeIs('reports.*'))
                                <span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-mint-300 rounded-r"></span>
                            @endif
                            <x-icon name="cherry-blossom" class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('reports.*') ? 'text-white' : 'text-white/80 group-hover:text-white' }}" />
                            <span class="truncate">Laporan Keuangan</span>
                        </a>
                    </nav>
                </div>

                <!-- User Profile & Settings Card (Bottom Fixed) -->
                <div class="p-3 border-t border-white/15 bg-black/15 backdrop-blur-md flex-shrink-0">
                    <div class="p-2 rounded-xl bg-white/10 border border-white/15 flex items-center justify-between gap-2 shadow-inner">
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 min-w-0 flex-1 group hover:opacity-95 transition-opacity">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-mint-200 via-sage-300 to-leaf-400 text-sage-900 font-bold text-xs flex items-center justify-center shadow-sm flex-shrink-0 border border-white/40">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div class="truncate min-w-0">
                                <div class="font-semibold text-xs text-white truncate group-hover:text-mint-200 transition-colors">{{ Auth::user()->name }}</div>
                                <div class="text-[10px] text-white/60 truncate">Profil & Pengaturan</div>
                            </div>
                        </a>

                        <div class="flex items-center gap-0.5 flex-shrink-0">
                            <a href="{{ route('profile.edit') }}" 
                               title="Pengaturan Profil" 
                               class="p-1.5 rounded-lg text-white/70 hover:text-white hover:bg-white/15 transition-all {{ request()->routeIs('profile.*') ? 'bg-white/25 text-white' : '' }}">
                                <x-icon name="user" class="w-4 h-4" />
                            </a>

                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" 
                                        title="Keluar (Log Out)" 
                                        onclick="event.preventDefault(); this.closest('form').submit();"
                                        class="p-1.5 rounded-lg text-white/70 hover:text-coral-200 hover:bg-coral-500/20 transition-all">
                                    <x-icon name="logout" class="w-4 h-4" />
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content Area -->
            <div class="flex-1 lg:ml-64 flex flex-col min-h-screen min-w-0 max-w-full overflow-x-hidden">
                <!-- Top Navigation (Mobile) -->
                <nav class="lg:hidden bg-white/95 backdrop-blur-md border-b border-sage-200 sticky top-0 z-30 px-4 py-3 flex items-center justify-between shadow-sm">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-sage-500 flex items-center justify-center p-1 border border-sage-400 shadow-sm">
                            <img src="{{ asset('images/icons/logo.png') }}" alt="Flowra" class="w-full h-full object-contain">
                        </div>
                        <span class="font-heading text-lg font-bold text-sage-800 tracking-wide">Flowra</span>
                    </a>
                    <div class="flex items-center gap-2">
                        <button @click="$dispatch('open-quick-transaction')" class="px-2.5 py-1.5 rounded-lg bg-flora-gradient text-white shadow-sm flex items-center gap-1 text-xs font-semibold">
                            <x-icon name="add-seed" class="w-3.5 h-3.5 text-white" />
                            <span>Catat</span>
                        </button>
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 rounded-lg text-sage-600 hover:bg-sage-50">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>
                </nav>

                <!-- Mobile Menu Drawer -->
                <div x-show="mobileMenuOpen" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     class="lg:hidden bg-white border-b border-sage-200 p-4 shadow-lg sticky top-[53px] z-20"
                     style="display: none;">
                    <!-- User Profile Strip in Mobile -->
                    <div class="flex items-center justify-between pb-3 mb-3 border-b border-sage-100">
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-sage-600 text-white font-bold text-xs flex items-center justify-center">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-semibold text-xs text-earth-800">{{ Auth::user()->name }}</div>
                                <div class="text-[10px] text-earth-500">{{ Auth::user()->email }}</div>
                            </div>
                        </a>
                        <a href="{{ route('profile.edit') }}" class="text-xs font-semibold text-sage-600 hover:underline">
                            Profil →
                        </a>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <a href="{{ route('dashboard') }}" class="p-2 rounded-lg hover:bg-sage-50 flex items-center gap-2 text-earth-700 {{ request()->routeIs('dashboard') ? 'bg-sage-100 text-sage-800 font-semibold' : '' }}">
                            <x-icon name="tree" class="w-4 h-4" /> Dashboard
                        </a>
                        <a href="{{ route('transactions.index') }}" class="p-2 rounded-lg hover:bg-sage-50 flex items-center gap-2 text-earth-700 {{ request()->routeIs('transactions.*') ? 'bg-sage-100 text-sage-800 font-semibold' : '' }}">
                            <x-icon name="flower-bloom" class="w-4 h-4" /> Transaksi
                        </a>
                        <a href="{{ route('accounts.index') }}" class="p-2 rounded-lg hover:bg-sage-50 flex items-center gap-2 text-earth-700 {{ request()->routeIs('accounts.*') ? 'bg-sage-100 text-sage-800 font-semibold' : '' }}">
                            <x-icon name="cash-leaf" class="w-4 h-4" /> Rekening
                        </a>
                        <a href="{{ route('budgets.index') }}" class="p-2 rounded-lg hover:bg-sage-50 flex items-center gap-2 text-earth-700 {{ request()->routeIs('budgets.*') ? 'bg-sage-100 text-sage-800 font-semibold' : '' }}">
                            <x-icon name="sprout" class="w-4 h-4" /> Anggaran
                        </a>
                        <a href="{{ route('debts.index') }}" class="p-2 rounded-lg hover:bg-sage-50 flex items-center gap-2 text-earth-700 {{ request()->routeIs('debts.*') ? 'bg-sage-100 text-sage-800 font-semibold' : '' }}">
                            <x-icon name="leaf" class="w-4 h-4" /> Hutang/Piutang
                        </a>
                        <a href="{{ route('recurring.index') }}" class="p-2 rounded-lg hover:bg-sage-50 flex items-center gap-2 text-earth-700 {{ request()->routeIs('recurring.*') ? 'bg-sage-100 text-sage-800 font-semibold' : '' }}">
                            <x-icon name="leaf-wind" class="w-4 h-4" /> Rutin
                        </a>
                        <a href="{{ route('categories.index') }}" class="p-2 rounded-lg hover:bg-sage-50 flex items-center gap-2 text-earth-700 {{ request()->routeIs('categories.*') ? 'bg-sage-100 text-sage-800 font-semibold' : '' }}">
                            <x-icon name="bouquet" class="w-4 h-4" /> Kategori
                        </a>
                        <a href="{{ route('reports.index') }}" class="p-2 rounded-lg hover:bg-sage-50 flex items-center gap-2 text-earth-700 {{ request()->routeIs('reports.*') ? 'bg-sage-100 text-sage-800 font-semibold' : '' }}">
                            <x-icon name="cherry-blossom" class="w-4 h-4" /> Laporan
                        </a>
                    </div>

                    <div class="mt-3 pt-3 border-t border-sage-100 flex items-center justify-between text-xs">
                        <a href="{{ route('profile.edit') }}" class="text-sage-700 hover:text-sage-900 font-medium flex items-center gap-1.5">
                            <x-icon name="user" class="w-3.5 h-3.5" /> Pengaturan Akun
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-coral-600 hover:text-coral-800 font-medium flex items-center gap-1">
                                <x-icon name="logout" class="w-3.5 h-3.5" /> Log Out
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white/80 backdrop-blur-sm border-b border-sage-100 shadow-sm">
                        <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Global Alerts -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 w-full">
                    @if(session('success'))
                        <div class="flora-alert flora-alert-success success-bloom mb-4">
                            <x-icon name="sprout" class="w-5 h-5 text-leaf-600 flex-shrink-0" />
                            <div class="text-sm font-medium">{{ session('success') }}</div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="flora-alert flora-alert-error error-wilt mb-4">
                            <x-icon name="delete-wilt" class="w-5 h-5 text-coral-600 flex-shrink-0" />
                            <div class="text-sm font-medium">{{ session('error') }}</div>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="flora-alert flora-alert-error error-wilt mb-4">
                            <x-icon name="delete-wilt" class="w-5 h-5 text-coral-600 flex-shrink-0" />
                            <div class="text-sm font-medium">
                                <div class="font-bold mb-1">Terjadi kesalahan pada input data:</div>
                                <ul class="list-disc list-inside text-xs space-y-0.5">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="flora-alert flora-alert-info mb-4">
                            <x-icon name="flower" class="w-5 h-5 text-sky-600 flex-shrink-0" />
                            <div class="text-sm font-medium">{{ session('info') }}</div>
                        </div>
                    @endif
                </div>

                <!-- Page Main Slot -->
                <main class="flex-1 p-3 sm:p-6 lg:p-8 max-w-7xl mx-auto w-full min-w-0 pb-24 lg:pb-8">
                    {{ $slot }}
                </main>

                <!-- Bottom Navigation (Mobile) -->
                <nav class="lg:hidden bottom-nav z-40">
                    <div class="grid grid-cols-5 items-center py-2 px-1 text-center">
                        <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 py-1 {{ request()->routeIs('dashboard') ? 'text-sage-600 font-bold' : 'text-earth-400' }}">
                            <x-icon name="tree" class="w-5 h-5" />
                            <span class="text-[10px]">Home</span>
                        </a>
                        <a href="{{ route('transactions.index') }}" class="flex flex-col items-center gap-1 py-1 {{ request()->routeIs('transactions.*') ? 'text-sage-600 font-bold' : 'text-earth-400' }}">
                            <x-icon name="flower-bloom" class="w-5 h-5" />
                            <span class="text-[10px]">Transaksi</span>
                        </a>
                        <div class="flex justify-center -mt-5">
                            <button @click="$dispatch('open-quick-transaction')" 
                                    class="w-12 h-12 bg-flora-gradient rounded-full shadow-flora-lg flex items-center justify-center text-white text-xl hover:scale-110 active:scale-95 transition-transform duration-200">
                                <x-icon name="plus" class="w-6 h-6 text-white" />
                            </button>
                        </div>
                        <a href="{{ route('budgets.index') }}" class="flex flex-col items-center gap-1 py-1 {{ request()->routeIs('budgets.*') ? 'text-sage-600 font-bold' : 'text-earth-400' }}">
                            <x-icon name="sprout" class="w-5 h-5" />
                            <span class="text-[10px]">Anggaran</span>
                        </a>
                        <a href="{{ route('accounts.index') }}" class="flex flex-col items-center gap-1 py-1 {{ request()->routeIs('accounts.*') ? 'text-sage-600 font-bold' : 'text-earth-400' }}">
                            <x-icon name="cash-leaf" class="w-5 h-5" />
                            <span class="text-[10px]">Rekening</span>
                        </a>
                    </div>
                </nav>
            </div>
        </div>

        <!-- Global Quick Transaction Modal Component -->
        <x-quick-transaction-modal :accounts="$globalAccounts ?? collect()" :categories="$globalCategories ?? collect()" />

        <!-- Botanical Page Transition Loading Screen -->
        <div id="flora-global-loader" class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-cream-50/90 backdrop-blur-md transition-opacity duration-300 opacity-0 pointer-events-none select-none">
            <div class="relative flex items-center justify-center w-28 h-28">
                <!-- Glowing Soft Pulsing Background Aura -->
                <div class="absolute w-20 h-20 rounded-full bg-mint-200/40 animate-pulse"></div>

                <!-- Outer Clockwise Gentle Gradient Spinner Ring (Slow 3.8s) -->
                <div class="absolute w-24 h-24 rounded-full border-4 border-transparent border-t-sage-600 border-r-leaf-500 border-b-mint-400 animate-spin-slow"></div>

                <!-- Inner Counter-Clockwise Dotted Botanical Halo Ring (Slow 5.5s) -->
                <div class="absolute w-16 h-16 rounded-full border-2 border-dashed border-sage-400/70 animate-spin-reverse-slow"></div>

                <!-- Center Sprout Logo Icon -->
                <div class="relative z-10 w-10 h-10 rounded-2xl bg-white/80 backdrop-blur-sm p-1.5 shadow-sm border border-sage-200/70 flex items-center justify-center">
                    <img src="{{ asset('images/icons/sprout.png') }}" alt="Flowra Sprout" class="w-full h-full object-contain animate-bounce">
                </div>
            </div>

            <!-- Flowra Brand Text & Status Message -->
            <div class="text-center mt-5">
                <div class="font-heading text-lg font-bold text-sage-800 tracking-wide flex items-center justify-center gap-1.5 leading-tight">
                    <span>Flowra</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-leaf-500 animate-pulse"></span>
                </div>
                <p class="text-xs text-earth-600 font-medium tracking-wide mt-1.5 animate-pulse">Menyiapkan kebun keuangan Anda...</p>
            </div>
        </div>

        <style>
            @keyframes spin-slow {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
            @keyframes spin-reverse-slow {
                from { transform: rotate(360deg); }
                to { transform: rotate(0deg); }
            }
            .animate-spin-slow {
                animation: spin-slow 3.8s linear infinite;
            }
            .animate-spin-reverse-slow {
                animation: spin-reverse-slow 5.5s linear infinite;
            }
        </style>

        <script>
            (function() {
                const loader = document.getElementById('flora-global-loader');
                let hideTimeout = null;

                function showLoader() {
                    if (!loader) return;
                    loader.classList.remove('opacity-0', 'pointer-events-none');
                    loader.classList.add('opacity-100', 'pointer-events-auto');

                    // Safety fallback: auto-hide after 6 seconds in case navigation is interrupted
                    clearTimeout(hideTimeout);
                    hideTimeout = setTimeout(hideLoader, 6000);
                }

                function hideLoader() {
                    if (!loader) return;
                    loader.classList.remove('opacity-100', 'pointer-events-auto');
                    loader.classList.add('opacity-0', 'pointer-events-none');
                }

                // Hide loader when page is loaded or restored from browser BFCache
                window.addEventListener('pageshow', hideLoader);
                window.addEventListener('DOMContentLoaded', hideLoader);

                // Attach navigation listener to all internal links
                document.addEventListener('click', function(e) {
                    const link = e.target.closest('a');
                    if (!link) return;

                    const href = link.getAttribute('href');
                    const target = link.getAttribute('target');

                    // Ignore hash links, javascript: void, external links, downloads, or target="_blank"
                    if (!href || 
                        href.startsWith('#') || 
                        href.startsWith('javascript:') || 
                        target === '_blank' || 
                        link.hasAttribute('download') ||
                        link.hasAttribute('data-no-loader')) {
                        return;
                    }

                    // Only trigger for same-origin links
                    if (link.hostname === window.location.hostname) {
                        showLoader();
                    }
                });

                // Attach to form submissions
                document.addEventListener('submit', function(e) {
                    const form = e.target;
                    if (form && !form.hasAttribute('data-no-loader')) {
                        showLoader();
                    }
                });
            })();
        </script>

        <!-- Chart.js Library -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

        <!-- Stacked Scripts -->
        @stack('scripts')
    </body>
</html>
