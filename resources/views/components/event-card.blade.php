@props(['event'])

<a href="{{ route('events.show', $event) }}"
   class="group block rounded-xl shadow-sm ring-1 ring-black/5 bg-white hover:shadow-md transition overflow-hidden">
  <div class="relative aspect-[16/10] w-full bg-gray-200">
    @if(!empty($event->image))
      <img src="{{ asset('storage/'.$event->image) }}" alt="{{ $event->title }}"
           class="h-full w-full object-cover">
    @endif
    @if(!empty($event->category))
      <span class="absolute top-3 right-3 rounded-full bg-white/90 px-2.5 py-1 text-[11px] font-semibold text-gray-800 shadow">
        {{ $event->category }}
      </span>
    @endif
  </div>

  <div class="p-4">
    <div class="text-xs text-gray-500">
      {{ $event->event_date->format('M d, g:i A') }}
    </div>

    <h3 class="mt-1 text-[15px] font-semibold text-gray-900 group-hover:underline">
      {{ $event->title }}
    </h3>

    <div class="mt-1 flex items-center gap-1.5 text-sm text-gray-600">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M19.5 10.5c0 7.5-7.5 10.5-7.5 10.5S4.5 18 4.5 10.5a7.5 7.5 0 1115 0z"/>
      </svg>
      <span class="truncate">{{ $event->location }}</span>
    </div>
  </div>
</a>
