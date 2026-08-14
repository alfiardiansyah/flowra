<x-guest-layout>
    <!-- Header Section -->
    <div class="mb-5 text-center">
        <h2 class="font-heading text-2xl sm:text-3xl font-bold text-sage-800 tracking-tight">
            Start Your Financial Garden
        </h2>
        <p class="text-xs sm:text-sm text-earth-600 mt-1">
            Create an account and begin growing your wealth naturally
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="flex items-center gap-1.5 text-xs font-semibold text-earth-700 mb-1.5">
                <x-icon name="flower" class="w-3.5 h-3.5 text-sage-500" />
                <span>Full Name</span>
            </label>
            <input id="name" 
                   class="flora-input text-sm py-2.5 px-3.5 @error('name') border-coral-400 focus:border-coral-500 focus:ring-coral-200 @enderror" 
                   type="text" 
                   name="name" 
                   value="{{ old('name') }}" 
                   required 
                   autofocus 
                   autocomplete="name"
                   placeholder="Your full name">
            <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs" />
        </div>

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
                   autocomplete="new-password"
                   placeholder="Create a strong password">
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="flex items-center gap-1.5 text-xs font-semibold text-earth-700 mb-1.5">
                <x-icon name="leaf" class="w-3.5 h-3.5 text-sage-500" />
                <span>Confirm Password</span>
            </label>
            <input id="password_confirmation" 
                   class="flora-input text-sm py-2.5 px-3.5"
                   type="password"
                   name="password_confirmation" 
                   required 
                   autocomplete="new-password"
                   placeholder="Confirm your password">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs" />
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" class="btn-flora-primary w-full justify-center py-3 text-sm font-semibold rounded-xl flex items-center gap-2">
                <x-icon name="add-seed" class="w-4 h-4 text-white" />
                <span>Register Account</span>
            </button>
        </div>

        <!-- Switch to Login Link -->
        <div class="pt-4 text-center border-t border-sage-100">
            <p class="text-xs text-earth-600">
                Already registered? 
                <a href="{{ route('login') }}" class="font-semibold text-sage-700 hover:text-sage-900 hover:underline transition-colors ml-0.5">
                    Log in here
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
