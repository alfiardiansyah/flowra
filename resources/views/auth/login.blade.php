<x-guest-layout>
    <!-- Header Section -->
    <div class="mb-5 text-center">
        <h2 class="font-heading text-2xl sm:text-3xl font-bold text-sage-800 tracking-tight">
            Welcome Back!
        </h2>
        <p class="text-xs sm:text-sm text-earth-600 mt-1">
            Sign in to your financial garden
        </p>
    </div>

    <!-- Session Status Alert -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="flex items-center gap-1.5 text-xs font-semibold text-earth-700 mb-1.5">
                <x-icon name="flower" class="w-3.5 h-3.5 text-sage-500" />
                <span>Email Address</span>
            </label>
            <input id="email" 
                   class="flora-input text-sm py-2.5 px-3.5 @error('email') border-coral-400 focus:border-coral-500 focus:ring-coral-200 @enderror" 
                   type="email" 
                   name="email" 
                   value="{{ old('email') }}" 
                   required 
                   autofocus 
                   autocomplete="username"
                   placeholder="your.email@example.com">
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="flex items-center gap-1.5 text-xs font-semibold text-earth-700 mb-1.5">
                <x-icon name="leaf" class="w-3.5 h-3.5 text-sage-500" />
                <span>Password</span>
            </label>
            <input id="password" 
                   class="flora-input text-sm py-2.5 px-3.5 @error('password') border-coral-400 focus:border-coral-500 focus:ring-coral-200 @enderror"
                   type="password"
                   name="password" 
                   required 
                   autocomplete="current-password"
                   placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group select-none">
                <input id="remember_me" 
                       type="checkbox" 
                       class="rounded border-sage-300 text-sage-600 shadow-sm focus:ring-sage-500 focus:ring-2 transition-colors cursor-pointer w-4 h-4" 
                       name="remember">
                <span class="ms-2 text-xs text-earth-600 group-hover:text-sage-700 transition-colors">
                    Remember me
                </span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-xs text-sage-600 hover:text-sage-800 font-medium hover:underline transition-colors" href="{{ route('password.request') }}">
                    Forgot password?
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" class="btn-flora-primary w-full justify-center py-3 text-sm font-semibold rounded-xl flex items-center gap-2">
                <x-icon name="sprout" class="w-4 h-4 text-white" />
                <span>Log in</span>
            </button>
        </div>

        <!-- Switch to Register Link -->
        <div class="pt-4 text-center border-t border-sage-100">
            <p class="text-xs text-earth-600">
                Don't have an account? 
                <a href="{{ route('register') }}" class="font-semibold text-sage-700 hover:text-sage-900 hover:underline transition-colors ml-0.5">
                    Sign up here
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>