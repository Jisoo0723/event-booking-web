@extends('layouts.app')
@section('title', 'My Bookings')

@section('content')
<div class="max-w-5xl mx-auto p-6">
  <h1 class="text-2xl font-semibold mb-4">My Bookings</h1>

  @if($bookings->isEmpty())
    <div class="bg-white border rounded p-6 text-gray-600">
      You have no bookings yet.
      <a href="{{ route('events.index') }}" class="text-indigo-600 underline">Browse events</a>
    </div>
  @else
    <div class="grid md:grid-cols-2 gap-4">
      @foreach($bookings as $booking)
        @php
          $event = $booking->event;
          $isPast = optional($event->event_date)->isPast();
        @endphp

        <div class="bg-white border rounded p-4 flex flex-col gap-2">
          <div class="flex items-start justify-between">
            <a href="{{ route('events.show', $event) }}" class="font-semibold text-lg hover:underline">
              {{ $event->title }}
            </a>
            <span class="text-xs px-2 py-1 rounded
                {{ $isPast ? 'bg-gray-200 text-gray-700' : 'bg-green-100 text-green-700' }}">
              {{ $isPast ? 'Past' : 'Upcoming' }}
            </span>
          </div>

          <div class="text-gray-700 text-sm">
            <div>{{ optional($event->event_date)->format('Y-m-d H:i') }} · {{ $event->location }}</div>
            <div>
              Capacity: {{ $event->capacity }}
              · Booked: {{ $event->bookings_count ?? $event->bookings()->count() }}
              · Remaining: {{ $event->remainingSpots() }}
            </div>
          </div>

          <div class="mt-2 text-gray-600 line-clamp-3">{{ $event->description }}</div>

          <div class="mt-3 flex justify-between items-center">
            <a class="text-indigo-600 hover:underline" href="{{ route('events.show', $event) }}">View details</a>

            {{-- 취소 버튼: 지난 이벤트면 비활성화 --}}
            @if(!$isPast)
              <form method="POST" action="{{ route('bookings.destroy', $booking) }}"
                    onsubmit="return confirm('Cancel this booking?');">
                @csrf @method('DELETE')
                <button class="px-3 py-1.5 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                  Cancel
                </button>
              </form>
            @else
              <span class="text-sm text-gray-500">Cannot cancel past event</span>
            @endif
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-6">
      {{ $bookings->links() }}
    </div>
  @endif
</div>
@endsection
