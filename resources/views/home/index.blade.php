@extends('layouts.app')
@section('title', 'Home')

@section('content')
<div class="mx-auto max-w-6xl px-6 py-8">

  {{-- 헤더 --}}
  <div class="mb-6">
    <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-gray-900">
      🔥 Trending Events
    </h1>
    <p class="mt-2 text-gray-600">Check out the most popular events right now.</p>
  </div>

  {{-- 검색 (홈에서는 결과를 /events로 보냄) --}}
  <form action="{{ route('events.index') }}" method="GET" class="mb-6">
    <div class="relative">
      <input type="text" name="q" placeholder="Search events"
             class="w-full rounded-xl border-0 ring-1 ring-black/10 px-4 py-3 pl-11 text-[15px] placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-[#2973EB]">
      <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400"
           viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd"
              d="M9 3.5a5.5 5.5 0 104.09 9.31l3.55 3.55a1 1 0 001.42-1.42l-3.55-3.55A5.5 5.5 0 009 3.5zM5 9a4 4 0 118 0A4 4 0 015 9z"
              clip-rule="evenodd"/>
      </svg>
    </div>
  </form>

  {{-- 그리드 --}}
  @if($events->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
      @foreach($events as $event)
        <x-event-card :event="$event" />
      @endforeach
    </div>

    {{-- 페이지네이션 --}}
    <div class="mt-8 flex justify-center">
      {{ $events->onEachSide(1)->links() }}
    </div>
  @else
    <div class="rounded-lg border border-dashed p-10 text-center text-gray-600">
      No trending events yet.
    </div>
  @endif
</div>
@endsection
