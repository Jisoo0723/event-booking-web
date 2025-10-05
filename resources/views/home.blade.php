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

  {{-- 이벤트 카드 그리드 (events.index와 동일 마크업) --}}
  @if($events->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 items-stretch">
      @foreach($events as $event)
        <div class="h-full rounded-xl border bg-white shadow-sm p-4 flex flex-col">
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
