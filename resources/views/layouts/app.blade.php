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

    {{-- 네비게이션 --}}
    @include('layouts.navigation')

    {{-- 플래시 메시지 --}}
    @include('partials.flash')

    {{-- 본문 --}}
    <main class="relative z-0 flex-1">
      @hasSection('content')
          @yield('content')
      @else
          {{ $slot ?? '' }}
      @endif
    </main>


    {{-- 공용 Attendees 모달 (레이아웃에 1번만 배치) --}}
    <div id="attendees-modal"
        class="fixed inset-0 z-[60] hidden"
        role="dialog" aria-modal="true" aria-labelledby="attendees-modal-title" tabindex="-1">
      <div class="absolute inset-0 bg-black/40" data-close-att></div>
      <div class="absolute inset-0 flex items-start justify-center p-4">
        <div class="w-full max-w-3xl bg-white rounded shadow-lg mt-10">
          <div class="flex items-center justify-between border-b px-4 py-3">
            <h2 id="attendees-modal-title" class="font-semibold">Attendees</h2>
            <button type="button" class="text-gray-500 hover:text-gray-700"
                    data-close-att aria-label="Close">&times;</button>
          </div>
          <div id="attendees-modal-body" class="p-4">Loading...</div>
          <div class="flex justify-end border-t px-4 py-3">
            <button type="button" class="px-4 py-2 bg-gray-200 rounded" data-close-att>Close</button>
          </div>
        </div>
      </div>
    </div>


    @stack('modals')   {{-- 모달 DOM 렌더 --}}
    @stack('scripts')  {{-- 대시보드에서 push한 스크립트 렌더 --}}

  </div>

<script>
document.addEventListener('click', async (e) => {
  const btn = e.target.closest('.js-open-attendees');
  if (!btn) return;

  const modal = document.getElementById('attendees-modal');
  const body  = document.getElementById('attendees-modal-body');
  if (!modal || !body) {
    console.error('❌ attendees-modal DOM not found');
    return;
  }

  body.innerHTML = 'Loading...';
  modal.classList.remove('hidden');

  try {
    const res = await fetch(btn.dataset.url, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin'  
    });
    if (!res.ok) throw new Error('HTTP ' + res.status);
    body.innerHTML = await res.text();
  } catch (err) {
    console.error(err);
    body.innerHTML = '<div class="text-red-600">Failed to load attendees.</div>';
  }
});

// 닫기 (오버레이, 버튼, ESC)
document.addEventListener('click', (e) => {
  if (e.target.closest('[data-close-att]')) {
    document.getElementById('attendees-modal')?.classList.add('hidden');
  }
});
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    document.getElementById('attendees-modal')?.classList.add('hidden');
  }
});
</script>

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
