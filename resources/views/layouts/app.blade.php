<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Event App')</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
  <div class="min-h-screen bg-gray-100">

    @php
        $authRouteNames = [
            'login','register',
            'password.request','password.email','password.reset',
            'verification.notice','verification.verify'
        ];
        $current = optional(Route::current())->getName();

        if (!in_array($current, $authRouteNames) && session()->has('status')) {
            session()->flash('success', session('status'));
            session()->forget('status');
        }
    @endphp

    @include('layouts.navigation')   {{-- 네비 --}}

    @include('partials.flash')       {{-- success / error 플래시 --}}

    <script>
    // 자동 사라짐: 3초 후 페이드아웃 > 제거
    window.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('.js-flash').forEach((el) => {
        const ms = parseInt(el.getAttribute('data-timeout') || '3000', 10);
        setTimeout(() => {
          // fade-out 애니메이션
          el.style.opacity = '0';
          el.style.transform = 'translateY(-4px)';
          setTimeout(() => { el.remove(); }, 300); // 애니메이션 끝나면 제거
        }, ms);
      });
    });
  </script>

    <main>
      {{ $slot ?? '' }}
      @yield('content')
    </main>
  </div>
</body>
</html>
