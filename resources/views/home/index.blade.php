@extends('layouts.app')
@section('title','Upcoming Events')

@section('content')
<div class="max-w-5xl mx-auto p-6">
  <h1 class="text-xl font-semibold mb-4">Upcoming Events</h1>

  <form method="GET" action="{{ route('home') }}" class="mb-4 flex gap-2">
    <input name="q"
           value="{{ $q ?? '' }}"
           placeholder="Search events or location"
           class="border rounded px-3 py-2 w-full md:w-80">
    <button type="submit" class="px-3 py-2 bg-blue-600 text-white rounded">
      Search
    </button>
  </form>

  @include('partials.flash')

  @if($events->isEmpty())
    <p>여기에 “미래 이벤트 목록”이 나올 자리입니다.</p>
  @else
    <div class="grid md:grid-cols-2 gap-4">
      @foreach($events as $event)
        <div class="bg-white p-4 rounded border">
          <a class="text-lg font-medium hover:underline"
             href="{{ route('events.show', $event) }}">{{ $event->title }}</a>
          <div class="text-sm text-gray-600">
            {{ $event->event_date->format('Y-m-d H:i') }} · {{ $event->location }}
          </div>
          <div class="text-sm mt-1">
            Organiser: {{ $event->organiser->name ?? 'N/A' }}
          </div>
          <div class="text-sm mt-1">
            Remaining: {{ $event->remainingSpots() }}
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-6">
      {{ $events->links() }}
    </div>
  @endif
</div>
@endsection
