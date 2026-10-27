<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <title>{{ $title ?? 'Home' }} · Kidzio</title>
  <meta name="theme-color" content="#FFF8F1" />
  <link rel="manifest" href="/kidzio/manifest.json" />
  <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/regular/style.css" />
  <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/fill/style.css" />
  <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700;800&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet" />
  <link rel="icon" type="image/png" href="/kidzio/assets/images/icon.png" />

  {{-- Pre-paint theme script (prevents flash of wrong theme). --}}
  <script>
    (function () {
      var saved = localStorage.getItem('theme');
      var theme = saved || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
      document.documentElement.classList.toggle('dark', theme === 'dark');
      document.querySelector('meta[name="theme-color"]').setAttribute('content', theme === 'dark' ? '#0F0B22' : '#FFF8F1');
    })();
  </script>
  <link href="/kidzio/assets/css/index.css" rel="stylesheet">
</head>
<body>
  <main class="device-frame min-h-screen flex flex-col">
    {{ $slot }}
    @unless ($hideTabbar ?? false)
      <x-kidzio.tabbar />
    @endunless
  </main>

  <script src="/kidzio/assets/js/app.js"></script>
  <script defer src="/kidzio/index.js"></script>
</body>
</html>
