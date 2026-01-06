@props(['title' => 'Your garden is empty', 'description' => 'Plant your first seed to start growing your financial garden!', 'action' => null, 'actionLabel' => 'Plant Your First Seed'])
<div class="empty-state">
    <x-icon name="empty-garden" class="w-48 h-48 text-sage-300 mb-4 animate-float" />
    <h3 class="mt-4 font-heading text-2xl text-sage-600">{{ $title }}</h3>
    <p class="mt-2 text-base text-earth-600 max-w-md">{{ $description }}</p>
    @if($action)
        <div class="mt-6">
            <a href="{{ $action }}" class="btn-flora-primary inline-flex items-center gap-2">
                <x-icon name="add-seed" class="w-5 h-5" />
                {{ $actionLabel }}
            </a>
        </div>
    @endif
</div>
