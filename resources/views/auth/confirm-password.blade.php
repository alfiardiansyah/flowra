<x-guest-layout>
    <!-- Header Section -->
    <div class="mb-5 text-center">
        <h2 class="font-heading text-2xl sm:text-3xl font-bold text-sage-800 tracking-tight">
            Confirm Password
        </h2>
        <p class="text-xs sm:text-sm text-earth-600 mt-1">
            This is a secure area of the application. Please confirm your password before continuing.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
        @csrf

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
                   placeholder="Enter your password">
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" class="btn-flora-primary w-full justify-center py-3 text-sm font-semibold rounded-xl flex items-center gap-2">
                <x-icon name="sprout" class="w-4 h-4 text-white" />
                <span>Confirm Password</span>
            </button>
        </div>
    </form>
</x-guest-layout>
