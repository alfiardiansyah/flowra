@props(['class' => '', 'variant' => 'default'])
@php
    $baseClasses = 'flora-card';
    $variantClasses = match($variant) {
        'summary' => 'summary-card',
        'transaction' => 'transaction-card',
        'chart' => 'flora-chart',
        default => '',
    };
@endphp
<div {{ $attributes->merge(['class' => $baseClasses . ' ' . $variantClasses . ' ' . $class]) }}>
  {{ $slot }}
</div>
