<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="font-heading text-3xl text-sage-600 mb-2">Start Your Financial Garden</h2>
        <p class="text-earth-600">Create an account and begin growing your wealth naturally</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-earth-700 mb-2 flex items-center gap-2">
                <x-icon name="flower" class="w-4 h-4 text-sage-400" />
                Name
            </label>
            <input id="name" 
                   class="flora-input @error('name') border-coral-400 @enderror" 
                   type="text" 
                   name="name" 
                   value="{{ old('name') }}" 
                   required 
                   autofocus 
                   autocomplete="name"
                   placeholder="Your full name">
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

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
                   autocomplete="username"
                   placeholder="your.email@example.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-earth-700 mb-2 flex items-center gap-2">
                <x-icon name="leaf" class="w-4 h-4 text-sage-400" />
                Password
            </label>
            <input id="password" 
                   class="flora-input @error('password') border-coral-400 @enderror"
                   type="password"
                   name="password" 
                   required 
                   autocomplete="new-password"
                   placeholder="Create a strong password">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-earth-700 mb-2 flex items-center gap-2">
                <x-icon name="leaf" class="w-4 h-4 text-sage-400" />
                Confirm Password
            </label>
            <input id="password_confirmation" 
                   class="flora-input"
                   type="password"
                   name="password_confirmation" 
                   required 
                   autocomplete="new-password"
                   placeholder="Confirm your password">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between pt-4">
            <a href="{{ route('login') }}" class="text-sm text-earth-600 hover:text-sage-600">
                Already registered? <span class="font-medium text-sage-600">Log in</span>
            </a>
            <button type="submit" class="btn-flora-primary">
                <x-icon name="sprout" class="w-5 h-5" />
                Register
            </button>
        </div>
    </form>
</x-guest-layout>
