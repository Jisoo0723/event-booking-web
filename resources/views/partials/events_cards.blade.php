@if($events->count())
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 items-stretch">
    @foreach($events as $event)
      <div class="relative h-full rounded-xl border bg-white shadow-sm p-4 flex flex-col">
        {{-- 카테고리 배지 (있을 때만) --}}
        @if(!empty($event->category))
          <span class="absolute top-3 right-3 text-xs px-2 py-1 rounded-full border font-medium">
            {{ $event->category }}
          </span>
        @endif

        <div>
          <div class="text-xs text-gray-500">{{ $event->event_date->format('M d, g:i A') }}</div>

          <a href="{{ route('events.show', $event) }}"
             class="mt-1 block text-[15px] font-semibold hover:underline">
            {{ $event->title }}
          </a>

          <div class="mt-1 text-sm text-gray-600 truncate">{{ $event->location }}</div>

          @if($event->description)
            <p class="mt-2 text-sm text-gray-700 line-clamp-3 min-h-[60px]">
              {{ $event->description }}
            </p>
          @else
            <div class="min-h-[60px]"></div>
          @endif
        </div>

        <div class="mt-auto pt-4 text-sm text-gray-600">
          Capacity: {{ $event->capacity }}
          · Booked: {{ $event->bookings_count ?? $event->bookings()->count() }}
          · Remaining: {{ method_exists($event,'remainingSpots') ? $event->remainingSpots() : max(0,(int)$event->capacity - (int)($event->bookings_count ?? $event->bookings()->count())) }}
        </div>

        <a href="{{ route('events.show', $event) }}"
           class="mt-3 inline-flex w-full items-center justify-center rounded-lg border px-4 py-2.5 text-sm font-medium hover:bg-gray-50">
          View Details
        </a>
      </div>
    @endforeach
  </div>

  <div class="mt-8 flex flex-col items-center space-y-2">
    <div class="flex justify-center">
      {{ $events->onEachSide(1)->links() }}
    </div>
  </div>
@else
  <div class="bg-white border rounded p-6 text-gray-600 text-center">No events found.</div>
@endif
