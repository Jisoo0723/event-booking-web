@extends('layouts.app')
@section('title', 'Events')

@section('content')
<div class="max-w-6xl mx-auto p-6">
  <h1 class="text-2xl font-semibold mb-4">Events List</h1>

  {{-- 검색 폼 --}}
  <form method="GET" action="{{ route('events.index') }}" class="mb-4 flex gap-2">
  <input
    type="text"
    name="q"
    value="{{ request('q') }}"
    placeholder="Search events or location"
    class="border rounded px-3 py-2 w-full md:w-80"
  >
  <button type="submit" class="px-3 py-2 bg-blue-600 text-white rounded">
    Search
  </button>
  </form>

  {{-- 이벤트 목록 --}}
  @if($events->count())
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
      @foreach($events as $event)
        <div class="bg-white border rounded p-4 flex flex-col gap-2">
          <a href="{{ route('events.show', $event) }}" class="font-semibold text-lg hover:underline">
            {{ $event->title }}
          </a>

          <div class="text-gray-700 text-sm">
            <div>{{ $event->event_date->format('Y-m-d H:i') }} · {{ $event->location }}</div>
            <div>
              Capacity: {{ $event->capacity }}
              · Booked: {{ $event->bookings()->count() }}
              · Remaining: {{ $event->remainingSpots() }}
            </div>
          </div>

          @if($event->description)
            <p class="text-gray-700 line-clamp-3">{{ $event->description }}</p>
          @endif

          <div class="mt-2">
            <a class="text-indigo-600 hover:underline" href="{{ route('events.show', $event) }}">View details</a>
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-6">
      {{ $events->links() }}
    </div>
    @else
        <div class="bg-white border rounded p-6 text-gray-600">
            No upcoming events.
        </div>
    @endif
</div>
@endsection
