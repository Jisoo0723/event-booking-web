@extends('layouts.app')
@section('title', 'Events')

@section('content')
@php
  $q = request('q');
  $category = request('category', 'All');
@endphp

<div class="max-w-6xl mx-auto p-6">
  <div class="mb-6">
    <h1 class="text-2xl md:text-3xl font-bold flex items-center gap-2">
      <span>🌟</span> Upcoming Events
    </h1>
  </div>

  {{-- 검색 폼 (AJAX) --}}
  <form id="events-search-form" class="mb-5 flex gap-2">
    <input
      type="text"
      name="q"
      value="{{ $q }}"
      placeholder="Search events"
      class="border rounded-lg px-3 py-2 w-full md:w-96 focus:outline-none focus:ring focus:ring-blue-200"
    >
    <button class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
      Search
    </button>
  </form>

  {{-- 카테고리 탭 (AJAX) --}}
  @if(!empty($categories))
    <div class="mb-4 flex flex-wrap gap-2" id="events-cats">
      @foreach($categories as $c)
        @php $active = ($category === $c); @endphp
        <a href="#"
           data-cat="{{ $c }}"
           class="text-sm px-3 py-1.5 rounded-full border {{ $active ? 'bg-blue-600 text-white border-blue-600' : 'text-gray-700 border-gray-300 hover:bg-gray-100' }}">
           {{ $c }}
        </a>
      @endforeach
    </div>
  @endif

  {{-- 목록 컨테이너 (AJAX로 교체됨) --}}
  <div id="events-list">
    @include('partials.events_cards', ['events' => $events])
  </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
  const listEl   = document.getElementById('events-list');
  const catsEl   = document.getElementById('events-cats');
  const formEl   = document.getElementById('events-search-form');
  let currentCat = '{{ $category }}';

  async function loadList(params){
    const url = new URL('{{ route('events.filter') }}', window.location.origin);
    const q = params.q?.trim?.() ?? '';
    if (q) url.searchParams.set('q', q);
    if (params.category && params.category !== 'All') url.searchParams.set('category', params.category);
    if (params.page) url.searchParams.set('page', params.page);

    const res  = await fetch(url, { headers: { 'X-Requested-With':'XMLHttpRequest' }});
    const html = await res.text();
    listEl.innerHTML = html;

    // 주소창 동기화 (공유/새로고침 안전)
    const pageUrl = new URL('{{ route('events.index') }}', window.location.origin);
    if (q) pageUrl.searchParams.set('q', q);
    if (params.category && params.category !== 'All') pageUrl.searchParams.set('category', params.category);
    if (params.page) pageUrl.searchParams.set('page', params.page);
    window.history.replaceState({}, '', pageUrl);

    wirePagination(); // 새롭게 렌더된 페이징에 바인딩
  }

  function setActiveCat(btn){
    if (!catsEl) return;
    catsEl.querySelectorAll('[data-cat]').forEach(a=>{
      a.classList.remove('bg-blue-600','text-white','border-blue-600');
      a.classList.add('text-gray-700','border-gray-300');
    });
    btn.classList.add('bg-blue-600','text-white','border-blue-600');
    btn.classList.remove('text-gray-700','border-gray-300');
  }

  // 카테고리 클릭
  if (catsEl) {
    catsEl.addEventListener('click', (e)=>{
      const a = e.target.closest('[data-cat]');
      if (!a) return;
      e.preventDefault();
      currentCat = a.dataset.cat || 'All';
      setActiveCat(a);
      const q = (formEl?.querySelector('[name="q"]')?.value || '').trim();
      loadList({ category: currentCat, q, page: 1 });
    });
  }

  // 검색 폼
  if (formEl) {
    formEl.addEventListener('submit', (e)=>{
      e.preventDefault();
      const q = (formEl.querySelector('[name="q"]').value || '').trim();
      loadList({ category: currentCat, q, page: 1 });
    });
  }

  // 페이징 위임
  function wirePagination(){
    const pager = listEl.querySelector('nav[role="navigation"]') || listEl.querySelector('nav');
    if (!pager) return;
    pager.addEventListener('click', async (e)=>{
      const a = e.target.closest('a[href]');
      if (!a) return;
      e.preventDefault();
      const page = (new URL(a.href)).searchParams.get('page') || 1;
      const q = (formEl?.querySelector('[name="q"]')?.value || '').trim();
      loadList({ category: currentCat, q, page });
    }, { passive:false });
  }

  // 첫 바인딩
  wirePagination();
})();
</script>
@endpush
