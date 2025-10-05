@extends('layouts.app')
@section('title', 'Events')

@section('content')
<div class="max-w-6xl mx-auto p-6">
  <h1 class="text-2xl md:text-3xl font-semibold mb-6">Upcoming Events</h1>

  {{-- 검색 폼 --}}
  <form method="GET" action="{{ route('events.index') }}" class="mb-5 flex gap-2">
    <input
      type="text"
      name="q"
      value="{{ request('q') }}"
      placeholder="Search events"
      class="border rounded-lg px-3 py-2 w-full md:w-96 focus:outline-none focus:ring focus:ring-blue-200"
    >
    @if(request('category'))
      <input type="hidden" name="category" value="{{ request('category') }}">
    @endif
    <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
      Search
    </button>
  </form>

  {{-- 카테고리 탭 (홈 페이지에서는 안 보이게) --}}
  @if(!empty($categories))
    <div class="mb-4 d-flex flex-wrap gap-2">
        @foreach($categories as $c)
            @php $active = (request('category', 'All') === $c) ? 'btn-primary' : 'btn-light'; @endphp
            <a class="btn {{ $active }} btn-sm"
                href="{{ route('events.index', array_filter(['category' => $c !== 'All' ? $c : null, 'q' => $q])) }}">
                {{ $c }}
            </a>
        @endforeach
    </div>
  @endif

  {{-- 이벤트 목록 --}}
  @if($events->count())
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
      @foreach($events as $event)
        <div class="bg-white border rounded-xl p-4 shadow-sm flex flex-col gap-3">
          {{-- 카테고리 뱃지 + 날짜 --}}
          <div class="flex items-start justify-between">
            <span class="text-sm text-gray-500">
              {{ $event->event_date->format('M d, h:i A') }}
            </span>
            @if(!empty($event->category))
              <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                {{ $event->category }}
              </span>
            @endif
          </div>

          <a href="{{ route('events.show', $event) }}"
             class="font-semibold text-lg hover:underline">
            {{ $event->title }}
          </a>

          <div class="text-gray-600 text-sm">@ {{ $event->location }}</div>

          @if($event->description)
            <p class="text-gray-700 text-sm line-clamp-3">{{ $event->description }}</p>
          @endif

          <div class="text-sm text-gray-700">
            Capacity: {{ $event->capacity }}
            · Booked: {{ $event->bookings_count ?? 0 }}
            · Remaining: {{ max(0, (int)$event->capacity - (int)($event->bookings_count ?? 0)) }}
          </div>

          <div class="mt-1">
            <a class="inline-flex justify-center items-center w-full px-3 py-2 border rounded-lg
                       text-blue-700 border-blue-200 hover:bg-blue-50"
               href="{{ route('events.show', $event) }}">
              View Details
            </a>
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-8">
      {{ $events->onEachSide(1)->links() }}
    </div>
  @else
    <div class="bg-white border rounded p-6 text-gray-600">
      No events found.
    </div>
  @endif
</div>
@endsection
