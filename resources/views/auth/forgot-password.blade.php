<x-guest-layout>
    <div class="mb-6 text-center">
        <x-icon name="flower" class="w-16 h-16 text-sage-400 mx-auto mb-4 animate-float" />
        <h2 class="font-heading text-3xl text-sage-600 mb-2">Forgot Password?</h2>
        <p class="text-earth-600">No problem! Enter your email and we'll send you a reset link</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-earth-700 mb-2 flex items-center gap-2">
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
                   placeholder="your.email@example.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between pt-4">
            <a href="{{ route('login') }}" class="text-sm text-earth-600 hover:text-sage-600">
                Back to login
            </a>
            <button type="submit" class="btn-flora-primary">
                <x-icon name="flower" class="w-5 h-5" />
                Email Password Reset Link
            </button>
        </div>
    </form>
</x-guest-layout>
