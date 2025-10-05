{{-- resources/views/events/organiser-dashboard.blade.php --}}
@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="max-w-5xl mx-auto p-6">
  <h1 class="text-2xl font-bold mb-4">Dashboard</h1>

  <div class="mb-4">
    <a href="{{ route('events.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded">
      + Create New Event
    </a>
  </div>

  @if($events->count())
    <table class="w-full bg-white border rounded">
      <thead>
        <tr class="bg-gray-100 text-left">
          <th class="p-3">Event</th>
          <th class="p-3">Date</th>
          <th class="p-3">Capacity</th>
          <th class="p-3">Booked</th>
          <th class="p-3">Remaining</th>
          <th class="p-3">Manage</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($events as $event)
          <tr class="border-t">
            <td class="p-3">
              {{-- ✅ $event 대신 ID를 명시적으로 전달 (404 방지) --}}
              <a href="{{ route('events.show', ['event' => $event->id]) }}" class="text-indigo-600 underline">
                {{ $event->title }}
              </a>
            </td>
            <td class="p-3">{{ $event->event_date->format('Y-m-d H:i') }}</td>
            <td class="p-3">{{ $event->capacity }}</td>
            <td class="p-3">{{ $event->bookings_count }}</td>
            <td class="p-3">
              {{ max(0, (int)$event->capacity - (int)($event->bookings_count ?? 0)) }}
            </td>
            <td class="p-3">
              {{-- Edit --}}
              <a href="{{ route('events.edit', ['event' => $event->id]) }}" class="text-blue-600 underline">Edit</a>

              {{-- CSV --}}
              <a href="{{ route('bookings.export', ['event' => $event->id]) }}" class="text-indigo-600 hover:underline ml-2">CSV</a>

              {{-- ✅ 참가자 보기(모달 오픈) --}}
              <button type="button"
                      class="text-indigo-600 hover:underline ml-2"
                      onclick="openAttendeesModal({{ $event->id }})">
                Attendees
              </button>

              {{-- ✅ Delete --}}
              <form action="{{ route('events.destroy', ['event' => $event->id]) }}"
                    method="POST"
                    class="inline"
                    onsubmit="return confirm('Delete this event?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:underline ml-2">
                  Delete
                </button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <div class="mt-4">{{ $events->links() }}</div>
  @else
    <div class="bg-white border rounded p-4 text-gray-600">
      You haven’t created any events yet.
    </div>
  @endif
</div>
@endsection

{{-- ✅ 모달 오버레이 --}}
@push('modals')
<div id="attendees-modal"
     class="fixed inset-0 z-[60] hidden"
     role="dialog" aria-modal="true" aria-labelledby="attendees-modal-title" tabindex="-1">

  <!-- 오버레이 -->
  <div class="absolute inset-0 bg-black/40" onclick="closeAttendeesModal()"></div>

  <!-- 모달 컨테이너 -->
  <div class="absolute inset-0 flex items-start justify-center p-4">
    <div class="w-full max-w-3xl bg-white rounded shadow-lg mt-10">
      <div class="flex items-center justify-between border-b px-4 py-3">
        <h2 id="attendees-modal-title" class="font-semibold">Attendees</h2>
        <button type="button" class="text-gray-500 hover:text-gray-700"
                onclick="closeAttendeesModal()" aria-label="Close">&times;</button>
      </div>

      <div id="attendees-modal-body" class="p-4">
        Loading...
      </div>

      <div class="flex justify-end border-t px-4 py-3">
        <button type="button" class="px-4 py-2 bg-gray-200 rounded"
                onclick="closeAttendeesModal()">Close</button>
      </div>
    </div>
  </div>
</div>
@endpush

<script>
  function closeAttendeesModal() {
    const modal = document.getElementById('attendees-modal');
    modal.classList.add('hidden');
    document.removeEventListener('keydown', escCloser);
  }

  function escCloser(e) {
    if (e.key === 'Escape') closeAttendeesModal();
  }

  function openAttendeesModal(eventId) {
    const modal = document.getElementById('attendees-modal');
    const body  = document.getElementById('attendees-modal-body');
    body.innerHTML = 'Loading...';
    modal.classList.remove('hidden');

    fetch(`{{ url('/events') }}/${eventId}/attendees`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(async (res) => {
      if (!res.ok) {
        const txt = await res.text().catch(()=> '');
        console.error(`❌ HTTP ${res.status} ${res.statusText}`, txt);
        throw new Error(`HTTP ${res.status} ${res.statusText}`);
      }
      return res.text();
    })
    .then(html => {
      body.innerHTML = html;
    })
    .catch(err => {
      console.error(err);
      body.innerHTML = '<div class="text-red-600">Failed to load attendees.</div>';
    });

    document.addEventListener('keydown', escCloser);
  }
</script>
