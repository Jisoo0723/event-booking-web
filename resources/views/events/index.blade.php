@extends('layouts.app')
@section('title', 'Events')

@section('content')
@php
  $q = request('q');
  $category = request('category', 'All');   // 기본값 'All'
@endphp

<div class="max-w-6xl mx-auto p-6">
  <div class="mb-6">
    <h1 class="text-2xl md:text-3xl font-bold flex items-center gap-2">
      <span>🌟</span> Upcoming Events
    </h1>
  </div>

  {{-- 검색 폼 --}}
  <form method="GET" action="{{ route('events.index') }}" class="mb-5 flex gap-2">
    <input
      type="text"
      name="q"
      value="{{ $q }}"
      placeholder="Search events"
      class="border rounded-lg px-3 py-2 w-full md:w-96 focus:outline-none focus:ring focus:ring-blue-200"
    >
    @if($category !== 'All')
      <input type="hidden" name="category" value="{{ $category }}">
    @endif

    <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
      Search
    </button>
  </form>

  {{-- 카테고리 탭 --}}
  @if(!empty($categories))
    <div class="mb-4 flex flex-wrap gap-2">
      @foreach($categories as $c)
        @php $active = ($category === $c); @endphp
        <a
          {{-- All이면 category 파라미터 제거, 검색어는 유지 --}}
          href="{{ route('events.index', array_filter([
              'category' => $c !== 'All' ? $c : null,
              'q'        => $q,
          ])) }}"
          class="text-sm px-3 py-1.5 rounded-full border
                 {{ $active ? 'bg-blue-600 text-white border-blue-600'
                            : 'text-gray-700 border-gray-300 hover:bg-gray-100' }}">
          {{ $c }}
        </a>
      @endforeach
    </div>
  @endif

  {{-- 이벤트 목록 --}}
  @if($events->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 items-stretch">
      @foreach($events as $event)
        {{-- 배지 위치를 위해 relative 추가 --}}
        <div class="relative h-full rounded-xl border bg-white shadow-sm p-4 flex flex-col">

          {{-- 선택된 카테고리를 오른쪽 상단 뱃지로 노출 (있을 때만) --}}
          @if(!empty($event->category))
            <span class="absolute top-3 right-3 text-xs px-2 py-1 rounded-full border font-medium">
              {{ $event->category }}
            </span>
          @endif

          {{-- 상단 정보 --}}
          <div>
            <div class="text-xs text-gray-500">
              {{ $event->event_date->format('M d, g:i A') }}
            </div>

            <a href="{{ route('events.show', $event) }}"
               class="mt-1 block text-[15px] font-semibold text-gray-900 hover:underline">
              {{ $event->title }}
            </a>

            <div class="mt-1 flex items-center gap-1.5 text-sm text-gray-600">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2a7 7 0 017 7c0 5.25-7 11-7 11S5 14.25 5 9a7 7 0 017-7zm0 9.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z"/>
              </svg>
              <span class="truncate">{{ $event->location }}</span>
            </div>

            @if($event->description)
              <p class="mt-2 text-sm text-gray-700 line-clamp-3 min-h-[60px]">
                {{ $event->description }}
              </p>
            @else
              <div class="min-h-[60px]"></div>
            @endif
          </div>

          {{-- 하단 영역 --}}
          <div class="mt-auto pt-4">
            <div class="text-sm text-gray-600">
              Capacity: {{ $event->capacity }}
              · Booked: {{ $event->bookings_count ?? $event->bookings()->count() }}
              · Remaining: {{ $event->remainingSpots() }}
            </div>

            <a href="{{ route('events.show', $event) }}"
               class="mt-3 inline-flex w-full items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-800 hover:bg-gray-50">
              View Details
            </a>
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-8 flex flex-col items-center space-y-2">
      <div class="flex justify-center">
        {{ $events->onEachSide(1)->links() }}
      </div>
    </div>
  @else
    <div class="bg-white border rounded p-6 text-gray-600 text-center">
      No events found.
    </div>
  @endif
</div>
@endsection
