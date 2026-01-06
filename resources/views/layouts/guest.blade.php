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
    <body class="font-sans text-earth-800 antialiased botanical-bg">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute top-0 left-0 w-64 h-64 bg-sage-200/20 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-mint-200/20 rounded-full blur-3xl translate-x-1/2 translate-y-1/2"></div>
            
            <div class="relative z-10 mb-8">
                <a href="/" class="block">
                    <x-flowra-logo class="h-16" />
                </a>
                <p class="text-center mt-2 font-accent text-sage-600 text-lg">Grow Your Wealth Naturally</p>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white/90 backdrop-blur-md shadow-flora-lg overflow-hidden sm:rounded-2xl relative z-10">
                <!-- Decorative corner elements -->
                <div class="absolute top-0 right-0 w-32 h-32 opacity-5">
                    <x-icon name="flower" class="w-full h-full text-sage-400" />
                </div>
                <div class="absolute bottom-0 left-0 w-24 h-24 opacity-5">
                    <x-icon name="leaf" class="w-full h-full text-mint-400" />
                </div>
                <div class="relative z-10">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
