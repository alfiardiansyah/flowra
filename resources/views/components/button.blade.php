@props(['type' => 'button', 'variant' => 'primary', 'icon' => null])
@php
    $classes = match($variant) {
        'primary' => 'btn-flora-primary',
        'secondary' => 'btn-flora-secondary',
        'danger' => 'btn-flora-danger',
        default => 'btn-flora-primary',
    };
    if ($icon) {
        $classes .= ' flex items-center gap-2';
    }
@endphp
<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        <x-icon :name="$icon" class="w-5 h-5" />
    @endif
    {{ $slot }}
</button>
