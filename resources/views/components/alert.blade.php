@props(['type' => 'success', 'dismissible' => false])
@php
    $classes = match($type) {
        'success' => 'flora-alert flora-alert-success',
        'error' => 'flora-alert flora-alert-error',
        'info' => 'flora-alert flora-alert-info',
        default => 'flora-alert flora-alert-info',
    };
@endphp
<div {{ $attributes->merge(['class' => $classes]) }} x-data="{ show: true }" x-show="show" x-transition>
    @if($type === 'success')
        <x-icon name="sprout" class="w-6 h-6 flex-shrink-0 animate-bloom" />
    @elseif($type === 'error')
        <x-icon name="falling-leaves" class="w-6 h-6 flex-shrink-0 animate-wilt" />
    @else
        <x-icon name="flower" class="w-6 h-6 flex-shrink-0" />
    @endif
    <div class="flex-1">
        {{ $slot }}
    </div>
    @if($dismissible)
        <button @click="show = false" class="text-current opacity-50 hover:opacity-100 transition-opacity">
            <img src="{{ asset('images/icons/close.png') }}" alt="Close" class="w-5 h-5">
        </button>
    @endif
</div>
