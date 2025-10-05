@extends('layouts.app')
@section('title', $event->title)

@section('content')
<div class="max-w-3xl mx-auto p-6 space-y-4">

  <h1 class="text-2xl font-semibold">{{ $event->title }}</h1>
  <div class="text-gray-700">
    <div>{{ $event->event_date->format('Y-m-d H:i') }} · {{ $event->location }}</div>
    <div>
      Capacity: {{ $event->capacity }}
      / Booked: {{ $event->bookings()->count() }}
      / Remaining: {{ $event->remainingSpots() }}
    </div>
  </div>
  <p class="bg-white p-4 rounded border">{{ $event->description }}</p>

  <div class="flex gap-3">
    @auth
      @if(auth()->id() === $event->organizer_id)
        <a class="px-3 py-2 bg-gray-800 text-white rounded"
           href="{{ route('events.edit', $event) }}">Edit</a>

        <a class="px-3 py-2 bg-indigo-600 text-white rounded"
           href="{{ route('bookings.manage', $event) }}">Manage</a>

        <form method="POST" action="{{ route('events.destroy', $event) }}" class="inline"
              onsubmit="return confirm('Delete this event?');">
          @csrf @method('DELETE')
          <button class="px-3 py-2 bg-red-600 text-white rounded">Delete</button>
        </form>
      @else
    @php
      $canBook = !$event->isPast() && $event->remainingSpots() > 0;
    @endphp

    {{-- 이미 예약했다면 비활성화 버튼 + 취소 버튼 --}}
    @if($alreadyBooked)
      <button class="px-3 py-2 bg-gray-300 text-gray-600 rounded cursor-not-allowed" disabled>
        Already booked
      </button>

      {{-- 취소 버튼: 내 예약 id가 있을 때만 노출 --}}
      @if(!empty($myBookingId))
        <form method="POST" action="{{ route('bookings.destroy', $myBookingId) }}"
              onsubmit="return confirm('Cancel your booking?');" class="inline">
          @csrf @method('DELETE')
          <button class="px-3 py-2 bg-red-600 text-white rounded">Cancel</button>
        </form>
      @endif

    {{-- 예약 가능하면 Book Now --}}
    @elseif($canBook)
      <form method="POST" action="{{ route('bookings.store', $event) }}">
        @csrf
        <button class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          Book Now
        </button>
      </form>

    {{-- 지난 이벤트 or 정원 만석 --}}
    @else
      <span class="px-3 py-2 bg-gray-300 text-gray-700 rounded">
        {{ $event->isPast() ? 'Past Event' : 'Fully Booked' }}
      </span>
    @endif
    @endauth
  @endauth

  </div>

</div>
@endsection
