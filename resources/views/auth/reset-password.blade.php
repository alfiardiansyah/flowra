<x-guest-layout>
    <!-- Header Section -->
    <div class="mb-5 text-center">
        <h2 class="font-heading text-2xl sm:text-3xl font-bold text-sage-800 tracking-tight">
            Reset Password
        </h2>
        <p class="text-xs sm:text-sm text-earth-600 mt-1">
            Create a new, secure password for your account
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

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
                   value="{{ old('email', $request->email) }}" 
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
                <span>New Password</span>
            </label>
            <input id="password" 
                   class="flora-input text-sm py-2.5 px-3.5 @error('password') border-coral-400 focus:border-coral-500 focus:ring-coral-200 @enderror" 
                   type="password" 
                   name="password" 
                   required 
                   autocomplete="new-password"
                   placeholder="Enter new password">
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="flex items-center gap-1.5 text-xs font-semibold text-earth-700 mb-1.5">
                <x-icon name="leaf" class="w-3.5 h-3.5 text-sage-500" />
                <span>Confirm New Password</span>
            </label>
            <input id="password_confirmation" 
                   class="flora-input text-sm py-2.5 px-3.5"
                   type="password"
                   name="password_confirmation" 
                   required 
                   autocomplete="new-password"
                   placeholder="Confirm new password">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs" />
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" class="btn-flora-primary w-full justify-center py-3 text-sm font-semibold rounded-xl flex items-center gap-2">
                <x-icon name="sprout" class="w-4 h-4 text-white" />
                <span>Reset Password</span>
            </button>
        </div>

        <!-- Back to Login Link -->
        <div class="pt-4 text-center border-t border-sage-100">
            <a href="{{ route('login') }}" class="text-xs font-semibold text-sage-700 hover:text-sage-900 hover:underline transition-colors">
                ← Back to login
            </a>
        </div>
    </form>
</x-guest-layout>
