@extends('layouts.app')
@section('title','My Bookings')

@section('content')
<div class="max-w-6xl mx-auto p-6">
  <h1 class="text-2xl md:text-3xl font-semibold mb-6">My Bookings</h1>

  {{-- Summary cards --}}
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white border rounded p-4">
      <div class="text-sm text-gray-600">Total Bookings</div>
      <div class="text-2xl font-semibold">{{ $summary['total'] ?? 0 }}</div>
    </div>
    <div class="bg-white border rounded p-4">
      <div class="text-sm text-gray-600">Upcoming</div>
      <div class="text-2xl font-semibold">{{ $summary['upcoming'] ?? 0 }}</div>
    </div>
    <div class="bg-white border rounded p-4">
      <div class="text-sm text-gray-600">Attended</div>
      <div class="text-2xl font-semibold">{{ $summary['attended'] ?? 0 }}</div>
    </div>
  </div>


  {{-- UPCOMING --}}
  <section class="mb-10">
    <h2 class="text-xl font-semibold mb-3">Upcoming</h2>

    @if($upcoming->isEmpty())
      <div class="text-gray-600 bg-white border rounded p-4">No upcoming bookings.</div>
    @else
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($upcoming as $b)
          @php $e = $b->event; @endphp
          <div class="bg-white border rounded-xl p-4 shadow-sm flex flex-col gap-3">
            <div class="flex items-start justify-between">
              <span class="text-sm text-gray-500">
                {{ optional($e->event_date)->format('M d, Y · h:i A') }}
              </span>
              @if(!empty($e->category))
                <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                  {{ $e->category }}
                </span>
              @endif
            </div>

            {{-- 제목: 상세 페이지 링크 가능 --}}
            <a href="{{ route('events.show', $e) }}" class="font-semibold hover:underline">
              {{ $e->title }}
            </a>
            <div class="text-gray-600 text-sm">@ {{ $e->location }}</div>

            @if(optional($e->event_date)->gt(now()))
            <div class="mt-2 flex gap-2">
              <form method="POST" action="{{ route('bookings.destroy', $b->id) }}"
                    onsubmit="return confirm('Cancel this booking?');">
                @csrf @method('DELETE')
                <button class="px-3 py-2 rounded-lg border text-red-700 border-red-200 hover:bg-red-50">
                  Cancel
                </button>
              </form>

              <a href="{{ route('events.show', $e) }}"
                class="px-3 py-2 rounded-lg border text-blue-700 border-blue-200 hover:bg-blue-50">
                View Event Details
              </a>
            </div>
          @endif


          </div>
        @endforeach
      </div>
    @endif
  </section>

  {{-- PAST --}}
  <section>
    <h2 class="text-xl font-semibold mb-3">Past</h2>

    @if($past->isEmpty())
      <div class="text-gray-600 bg-white border rounded p-4">No past bookings.</div>
    @else
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($past as $b)
          @php $e = $b->event; @endphp
          <div class="bg-white border rounded-xl p-4 shadow-sm flex flex-col gap-2 opacity-80">
            <div class="flex items-start justify-between">
              <span class="text-sm text-gray-500">
                {{ optional($e->event_date)->format('M d, Y · h:i A') }}
              </span>
              @if(!empty($e->category))
                <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                  {{ $e->category }}
                </span>
              @endif
            </div>

            {{-- 제목만(링크/버튼 없음) --}}
            <div class="font-semibold text-gray-800">{{ $e->title }}</div>
            <div class="text-gray-600 text-sm">@ {{ $e->location }}</div>
            <span class="mt-1 inline-block text-xs px-2 py-1 rounded bg-gray-100 text-gray-600">
              Event passed
            </span>
          </div>
        @endforeach
      </div>
    @endif
  </section>
</div>
@endsection
