@props(['name','class'=>'w-6 h-6'])
@php
    $iconMap = [
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
    $iconFile = $iconMap[$name] ?? 'default.png';
@endphp
<img src="{{ asset('images/icons/' . $iconFile) }}" alt="{{ ucfirst(str_replace('-', ' ', $name)) }}" class="{{ $class }}">
