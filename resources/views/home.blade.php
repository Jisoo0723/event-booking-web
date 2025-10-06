{{-- resources/views/home.blade.php --}}
@extends('layouts.app')
@section('title', 'Home')

@section('content')
<div class="max-w-6xl mx-auto p-6">
  {{-- 홈 헤더 (Trending) --}}
  <div class="mb-6">
    <h1 class="text-2xl font-bold flex items-center gap-2">
      <span>🔥</span> Trending Events
    </h1>
    <p class="text-gray-600 mt-1">Check out the most popular events right now.</p>
  </div>

  {{-- 검색/카테고리 = 홈에선 제거 (원하면 남겨도 됨) --}}
  {{-- <form ...> ... </form> --}}
  {{-- 카테고리 탭 ... --}}

  {{-- 카테고리 버튼 --}}
  <div class="mb-4 flex flex-wrap gap-2" id="home-cats">
    @foreach($categories as $c)
      <button
        class="cat-btn text-sm px-3 py-1.5 rounded-full border {{ ($category ?? 'All')===$c ? 'bg-blue-600 text-white border-blue-600' : 'text-gray-700 border-gray-300 hover:bg-gray-100' }}"
        data-cat="{{ $c }}">
        {{ $c }}
      </button>
    @endforeach
  </div>

  {{-- 이벤트 카드 컨테이너 (초기 렌더) --}}
  <div id="home-events">
    @include('partials.events_cards', ['events' => $events])
  </div>

  {{-- AJAX 필터 스크립트 --}}
  <script>
    document.getElementById('home-cats').addEventListener('click', async (e) => {
      const btn = e.target.closest('.cat-btn');
      if (!btn) return;

      const cat = btn.dataset.cat;
      const url = new URL('{{ route('home.filter') }}', window.location.origin);
      if (cat && cat !== 'All') url.searchParams.set('category', cat);

      const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      const html = await res.text();
      document.getElementById('home-events').innerHTML = html;

      // 버튼 스타일 토글
      document.querySelectorAll('.cat-btn').forEach(b => {
        b.classList.remove('bg-blue-600','text-white','border-blue-600');
        b.classList.add('text-gray-700','border-gray-300');
      });
      btn.classList.add('bg-blue-600','text-white','border-blue-600');
      btn.classList.remove('text-gray-700','border-gray-300');
    });
  </script>

</div>
@endsection
