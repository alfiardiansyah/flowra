<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>FLOWRA - {{ config('app.name', 'Grow Your Wealth Naturally') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&family=Satisfy&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased botanical-bg">
        <div class="min-h-screen flex">
            <!-- Sidebar Navigation (Desktop) -->
            <aside class="flora-sidebar hidden lg:block">
                <div class="p-6">
                    <x-flowra-logo class="mb-8" />
                    <nav class="space-y-2">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white shadow-lg' : 'text-white/80 hover:bg-white/10 hover:text-white' }} transition-all duration-200">
                            <x-icon name="tree" class="w-5 h-5" />
                            <span class="font-medium">Dashboard</span>
                        </a>
                        <a href="{{ route('incomes.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('incomes.*') ? 'bg-white/20 text-white shadow-lg' : 'text-white/80 hover:bg-white/10 hover:text-white' }} transition-all duration-200">
                            <x-icon name="sprout" class="w-5 h-5" />
                            <span class="font-medium">Pemasukan</span>
                        </a>
                        <a href="{{ route('expenses.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('expenses.*') ? 'bg-white/20 text-white shadow-lg' : 'text-white/80 hover:bg-white/10 hover:text-white' }} transition-all duration-200">
                            <x-icon name="falling-leaves" class="w-5 h-5" />
                            <span class="font-medium">Pengeluaran</span>
                        </a>
                        <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('reports.*') ? 'bg-white/20 text-white shadow-lg' : 'text-white/80 hover:bg-white/10 hover:text-white' }} transition-all duration-200">
                            <x-icon name="flower-bloom" class="w-5 h-5" />
                            <span class="font-medium">Laporan</span>
                        </a>
                    </nav>
                </div>
                <div class="bottom-0 left-0 right-0 p-6 border-t border-white/20">
                    <x-dropdown align="left" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-white/80 hover:bg-white/10 hover:text-white transition-all duration-200">
                                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center font-semibold">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <div class="flex-1 text-left">
                                    <div class="font-medium">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-white/60">{{ Auth::user()->email }}</div>
                                </div>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="flex-1 lg:ml-64">
                <!-- Top Navigation (Mobile) -->
                <nav class="lg:hidden bg-white/90 backdrop-blur-md border-b border-sage-200 sticky top-0 z-30">
                    <div class="px-4 py-3 flex items-center justify-between">
                        <x-flowra-logo class="h-8" />
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 rounded-lg text-sage-600 hover:bg-sage-50">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>
                </nav>

                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white/80 backdrop-blur-sm border-b border-sage-100 shadow-flora">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>

                <!-- Bottom Navigation (Mobile) -->
                <nav class="lg:hidden bottom-nav">
                    <div class="flex justify-around items-center py-2">
                        <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 px-4 py-2 rounded-xl {{ request()->routeIs('dashboard') ? 'text-sage-600' : 'text-earth-400' }} transition-colors">
                            <x-icon name="tree" class="w-6 h-6" />
                            <span class="text-xs font-medium">Dashboard</span>
                        </a>
                        <a href="{{ route('incomes.index') }}" class="flex flex-col items-center gap-1 px-4 py-2 rounded-xl {{ request()->routeIs('incomes.*') ? 'text-sage-600' : 'text-earth-400' }} transition-colors">
                            <x-icon name="sprout" class="w-6 h-6" />
                            <span class="text-xs font-medium">Pemasukan</span>
                        </a>
                        <a href="{{ route('expenses.index') }}" class="flex flex-col items-center gap-1 px-4 py-2 rounded-xl {{ request()->routeIs('expenses.*') ? 'text-sage-600' : 'text-earth-400' }} transition-colors">
                            <x-icon name="falling-leaves" class="w-6 h-6" />
                            <span class="text-xs font-medium">Pengeluaran</span>
                        </a>
                        <a href="{{ route('reports.index') }}" class="flex flex-col items-center gap-1 px-4 py-2 rounded-xl {{ request()->routeIs('reports.*') ? 'text-sage-600' : 'text-earth-400' }} transition-colors">
                            <x-icon name="flower-bloom" class="w-6 h-6" />
                            <span class="text-xs font-medium">Laporan</span>
                        </a>
                    </div>
                </nav>
            </div>
        </div>

        <!-- Chart.js Library -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        
        <!-- Stacked Scripts -->
        @stack('scripts')
    </body>
</html>
