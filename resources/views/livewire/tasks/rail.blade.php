<aside class="rail">
    <div class="avatar" title="{{ $avatarLabel }}"></div>

    @foreach ($primaryButtons as $index => $button)
        <button class="btn" title="{{ $button['title'] ?? '' }}" wire:key="rail-primary-{{ $index }}">
            <i data-lucide="{{ $button['icon'] ?? '' }}"></i>
        </button>
    @endforeach

    <div class="spacer"></div>

    @foreach ($secondaryButtons as $index => $button)
        <button class="btn" title="{{ $button['title'] ?? '' }}" wire:key="rail-secondary-{{ $index }}">
            <i data-lucide="{{ $button['icon'] ?? '' }}"></i>
        </button>
    @endforeach
</aside>
