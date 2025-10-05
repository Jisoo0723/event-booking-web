<div class="mb-3">
  <h3 class="text-lg font-semibold">
    Attendees — {{ $event->title }}
    <span class="text-gray-500 text-sm">({{ $bookings->count() }} shown)</span>
  </h3>
  <p class="text-gray-500 text-sm">{{ $event->event_date->format('Y-m-d H:i') }} · {{ $event->location }}</p>
</div>

@if($bookings->count())
  <div class="overflow-x-auto">
    <table class="min-w-full border text-sm">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-3 py-2 text-left">#</th>
          <th class="px-3 py-2 text-left">Name</th>
          <th class="px-3 py-2 text-left">Email</th>
          <th class="px-3 py-2 text-left">Booked At</th>
        </tr>
      </thead>
      <tbody>
        @foreach($bookings as $i => $b)
          <tr class="border-t">
            <td class="px-3 py-2">{{ $i + 1 }}</td>
            <td class="px-3 py-2">{{ $b->user->name ?? 'User #'.$b->user_id }}</td>
            <td class="px-3 py-2">{{ $b->user->email ?? '—' }}</td>
            <td class="px-3 py-2">{{ $b->created_at->format('Y-m-d H:i') }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@else
  <div class="bg-white border rounded p-6 text-gray-600">
    No attendees yet.
  </div>
@endif
