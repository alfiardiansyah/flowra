<x-guest-layout>
    <!-- Header Section -->
    <div class="mb-5 text-center">
        <h2 class="font-heading text-2xl sm:text-3xl font-bold text-sage-800 tracking-tight">
            Verify Your Email
        </h2>
        <p class="text-xs sm:text-sm text-earth-600 mt-1">
            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-xs text-leaf-600 bg-mint-50 p-3 rounded-xl border border-mint-200 text-center">
            A new verification link has been sent to the email address you provided during registration.
        </div>
    @endif

    <div class="space-y-4 pt-2">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-flora-primary w-full justify-center py-3 text-sm font-semibold rounded-xl flex items-center gap-2">
                <x-icon name="sprout" class="w-4 h-4 text-white" />
                <span>Resend Verification Email</span>
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="text-center pt-2">
            @csrf
            <button type="submit" class="text-xs font-semibold text-earth-500 hover:text-coral-600 hover:underline transition-colors">
                Log Out
            </button>
        </form>
    </div>
</x-guest-layout>
