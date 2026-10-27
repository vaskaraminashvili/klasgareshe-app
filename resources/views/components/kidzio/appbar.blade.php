@props(['back', 'eyebrow' => null, 'title'])

<header class="appbar safe-top">
  <a href="{{ $back }}" wire:navigate class="icon-btn" aria-label="Back"><i class="ph ph-caret-left"></i></a>
  <div class="grow">
    @if ($eyebrow)<p class="text-xs text-muted">{{ $eyebrow }}</p>@endif
    <h1 class="h-display text-lg leading-tight">{{ $title }}</h1>
  </div>
</header>
