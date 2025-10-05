<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Event App')</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
  <div class="min-h-screen flex flex-col">

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

    {{-- ✅ 네비게이션 --}}
    @include('layouts.navigation')

    {{-- ✅ 플래시 메시지 --}}
    @include('partials.flash')

    {{-- ✅ 본문 --}}
    <main class="relative z-0 flex-1">
      {{ $slot ?? '' }}
      @yield('content')
    </main>

    {{-- ✅ 모달 스택 --}}
    @stack('modals')

  </div>

  <script>
    // 플래시 메시지 자동 사라짐
    window.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('.js-flash').forEach((el) => {
        const ms = parseInt(el.getAttribute('data-timeout') || '3000', 10);
        setTimeout(() => {
          el.style.opacity = '0';
          el.style.transform = 'translateY(-4px)';
          setTimeout(() => el.remove(), 300);
        }, ms);
      });
    });
  </script>
</body>
</html>
