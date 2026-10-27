@php
  $tabs = [
    ['Home', 'house-simple', '/', true],
    ['Learn', 'books', 'learn-categories.html', false],
    ['Rewards', 'gift', 'rewards-dashboard.html', false],
    ['Ranking', 'trophy', 'leaderboard.html', false],
    ['Profile', 'user-circle', 'profile.html', false],
  ];
@endphp

<nav class="tabbar mt-auto" aria-label="Primary">
  <div class="tabbar-inner">
    @foreach ($tabs as [$label, $icon, $href, $active])
      <a class="tab {{ $active ? 'active' : '' }}" href="{{ $href }}">
        <span class="tab-ico"><i class="ph{{ $active ? '-fill' : '' }} ph-{{ $icon }} text-xl"></i></span>{{ $label }}
      </a>
    @endforeach
  </div>
</nav>
