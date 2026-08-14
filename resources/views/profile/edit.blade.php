<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-heading text-3xl text-sage-600 flex items-center gap-3">
                    <x-icon name="flower" class="w-8 h-8 text-sage-400" />
                    Pengaturan Akun & Profil
                </h2>
                <p class="mt-1 text-earth-600 text-sm">Kelola informasi pribadi, keamanan kata sandi, dan data finansial Anda</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6 max-w-4xl mx-auto">
        <!-- 1. Update Profile Information -->
        <x-card class="p-6 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </x-card>

        <!-- 2. Update Password -->
        <x-card class="p-6 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </x-card>

        <!-- 3. Reset All Financial Data (Start Fresh) -->
        <x-card class="p-6 sm:p-8 border-l-4 border-l-amber-500">
            <div class="max-w-xl">
                @include('profile.partials.reset-financial-data-form')
            </div>
        </x-card>

        <!-- 4. Delete Entire User Account -->
        <x-card class="p-6 sm:p-8 border-l-4 border-l-coral-500">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </x-card>
    </div>
</x-app-layout>
