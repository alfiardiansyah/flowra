<x-guest-layout>
    <div class="mb-6 text-center">
        <x-icon name="sprout" class="w-16 h-16 text-sage-400 mx-auto mb-4 animate-grow" />
        <h2 class="font-heading text-3xl text-sage-600 mb-2">Reset Password</h2>
        <p class="text-earth-600">Create a new password for your account</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

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
                   value="{{ old('email', $request->email) }}" 
                   required 
                   autofocus 
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
                   placeholder="Enter new password">
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
                   placeholder="Confirm new password">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end pt-4">
            <button type="submit" class="btn-flora-primary">
                <x-icon name="sprout" class="w-5 h-5" />
                Reset Password
            </button>
        </div>
    </form>
</x-guest-layout>
