@props(['label', 'value', 'unit' => null, 'href' => '#'])

<a href="{{ $href }}" class="stat items-start hover:ring-primary transition">
  <span class="stat-label flex items-center gap-1">{{ $label }}</span>
  <span class="stat-value">{{ $value }} @if ($unit)<span class="text-xs font-bold text-muted">{{ $unit }}</span>@endif</span>
</a>
