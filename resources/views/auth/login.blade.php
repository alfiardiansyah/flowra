<x-guest-layout>
    <!-- Header Section - Lebih balanced spacing -->
    <div class="mb-8 text-center">
        <h2 class="font-heading text-3xl font-semibold text-sage-700 mb-3">Welcome Back!</h2>
        <p class="text-earth-600 text-base">Sign in to your financial garden</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-earth-700 mb-2 items-center gap-2">
                <x-icon name="flower" class="w-4 h-4 text-sage-400" />
                Email
            </label>
            <input id="email" 
                   class="flora-input @error('email') border-coral-400 @enderror" 
                   type="email" 
                   name="email" 
                   value="{{ old('email') }}" 
                   required 
                   autofocus 
                   autocomplete="username"
                   placeholder="your.email@example.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-earth-700 mb-2 items-center gap-2">
                <x-icon name="leaf" class="w-4 h-4 text-sage-400" />
                Password
            </label>
            <input id="password" 
                   class="flora-input @error('password') border-coral-400 @enderror"
                   type="password"
                   name="password" 
                   required 
                   autocomplete="current-password"
                   placeholder="Enter your password">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me & Forgot Password - Lebih balanced -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" 
                       type="checkbox" 
                       class="rounded border-sage-300 text-sage-600 shadow-sm focus:ring-sage-500 focus:ring-2 transition-colors" 
                       name="remember">
                <span class="ms-2 text-sm text-earth-600 group-hover:text-sage-600 transition-colors">Remember me</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-sage-600 hover:text-sage-700 font-medium transition-colors" href="{{ route('password.request') }}">
                    Forgot password?
                </a>
            @endif
        </div>

        <!-- Submit Button - Full width, lebih proporsional -->
        <div class="pt-6">
            <button type="submit" class="btn-flora-primary w-full justify-center py-3">
                <x-icon name="sprout" class="w-5 h-5" />
                <span>Log in</span>
            </button>
        </div>

        <!-- Sign Up Link - Separated dan lebih prominent -->
        <div class="pt-4 text-center border-t border-sage-100">
            <p class="text-sm text-earth-600">
                Don't have an account? 
                <a href="{{ route('register') }}" class="font-semibold text-sage-600 hover:text-sage-700 transition-colors">
                    Sign up here
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>