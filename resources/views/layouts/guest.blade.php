<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Flowra — {{ config('app.name', 'Grow Your Wealth Naturally') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Satisfy&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-earth-800 h-full min-h-screen relative selection:bg-sage-200 selection:text-sage-800 bg-earth-900">

    <!-- 1. Full-Screen Photographic Background -->
    <div class="fixed inset-0 z-0 bg-cover bg-center bg-no-repeat pointer-events-none"
         style="background-image: url('{{ asset('images/background.jpg') }}');">
    </div>

    <!-- 2. Subtle Natural Overlay Layer for Contrast & Elegance -->
    <div class="fixed inset-0 z-0 bg-gradient-to-b from-black/25 via-black/15 to-black/30 backdrop-blur-[2px] pointer-events-none"></div>

    <!-- 3. Centered Viewport Container (Horizontal & Vertical) -->
    <div class="relative z-10 min-h-screen w-full flex flex-col justify-center items-center p-4 sm:p-6 lg:p-8">
        
        <!-- Foreground Card Container -->
        <div class="w-full max-w-[420px] sm:max-w-[440px] transition-all duration-300">
            <div class="bg-white/92 sm:bg-white/95 backdrop-blur-xl rounded-3xl shadow-[0_20px_60px_-15px_rgba(0,0,0,0.35)] border border-white/80 p-6 sm:p-8 relative overflow-hidden">
                
                <!-- Flowra Branding Header -->
                <div class="flex flex-col items-center justify-center text-center mb-6">
                    <a href="{{ url('/') }}" class="inline-flex items-center justify-center gap-2.5 group mb-2" title="Kembali ke Beranda Flowra">
                        <div class="w-12 h-12 rounded-2xl bg-sage-100/90 flex items-center justify-center shadow-inner group-hover:scale-105 transition-transform duration-300">
                            <x-icon name="sprout" class="w-7 h-7 text-sage-600 animate-grow" />
                        </div>
                    </a>
                    <span class="font-heading text-2xl sm:text-3xl font-bold text-sage-800 tracking-tight leading-none">
                        Flowra
                    </span>
                    <p class="font-satisfy text-xs text-earth-500 tracking-wide mt-1">
                        Grow Your Wealth Naturally
                    </p>
                </div>

                <!-- Main Form Slot -->
                <div class="relative z-10">
                    {{ $slot }}
                </div>

            </div>

            <!-- Bottom Copyright / Tagline -->
            <div class="mt-5 text-center text-xs text-white/85 drop-shadow-sm font-medium tracking-wide">
                &copy; {{ date('Y') }} Flowra. Simple Personal Finance Garden.
            </div>
        </div>

    </div>

</body>
</html>
