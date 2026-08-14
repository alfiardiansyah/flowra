@props(['name' => 'flower', 'class' => 'w-6 h-6', 'style' => ''])
@php
    $pngIconMap = [
        'leaf' => 'leaf.png',
        'tree' => 'tree.png',
        'sprout' => 'sprout.png',
        'flower' => 'flower.png',
        'sunflower' => 'sunflower.png',
        'cherry-blossom' => 'cherry-blossom.png',
        'oak-tree' => 'oak-tree.png',
        'wildflower' => 'wildflower.png',
        'bouquet' => 'bouquet.png',
        'apple' => 'apple.png',
        'leaf-wind' => 'leaf-wind.png',
        'shopping-leaf' => 'shopping-leaf.png',
        'cactus' => 'cactus.png',
        'medical-leaf' => 'medical-leaf.png',
        'book-sprout' => 'book-sprout.png',
        'mixed-leaves' => 'mixed-leaves.png',
        'falling-leaves' => 'falling-leaves.png',
        'flower-bloom' => 'flower-bloom.png',
        'bank-bca' => 'bank-bca.png',
        'bank-mandiri' => 'bank-mandiri.png',
        'bank-bri' => 'bank-bri.png',
        'cash-leaf' => 'cash-leaf.png',
        'e-wallet' => 'e-wallet.png',
        'edit-leaf' => 'edit-leaf.png',
        'delete-wilt' => 'delete-wilt.png',
        'add-seed' => 'add-seed.png',
        'empty-garden' => 'empty-garden.png',
    ];
@endphp

@if(isset($pngIconMap[$name]))
    <img src="{{ asset('images/icons/' . $pngIconMap[$name]) }}" alt="{{ ucfirst(str_replace('-', ' ', $name)) }}" class="{{ $class }} object-contain inline-block" style="{{ $style }}">
@elseif($name === 'transfer' || $name === 'exchange' || $name === 'arrows-right-left')
    <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="{{ $style }}"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
@elseif($name === 'wallet')
    <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="{{ $style }}"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
@elseif($name === 'credit-card')
    <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="{{ $style }}"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
@elseif($name === 'calendar')
    <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="{{ $style }}"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
@elseif($name === 'clock')
    <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="{{ $style }}"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
@elseif($name === 'refresh' || $name === 'arrow-path')
    <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="{{ $style }}"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
@elseif($name === 'download')
    <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="{{ $style }}"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
@elseif($name === 'printer')
    <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="{{ $style }}"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
@elseif($name === 'search')
    <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="{{ $style }}"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
@elseif($name === 'check')
    <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="{{ $style }}"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
@elseif($name === 'plus')
    <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="{{ $style }}"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
@elseif($name === 'chevron-right')
    <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="{{ $style }}"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
@elseif($name === 'chevron-down')
    <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="{{ $style }}"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
@elseif($name === 'user')
    <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="{{ $style }}"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
@elseif($name === 'settings' || $name === 'cog')
    <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="{{ $style }}"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
@elseif($name === 'logout')
    <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="{{ $style }}"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
@else
    <img src="{{ asset('images/icons/flower.png') }}" alt="{{ ucfirst(str_replace('-', ' ', $name)) }}" class="{{ $class }} object-contain inline-block" style="{{ $style }}">
@endif
