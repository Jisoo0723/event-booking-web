@extends('layouts.app')
@section('title', 'Manage Bookings: ' . $event->title)

@section('content')
<div class="max-w-5xl mx-auto p-6 space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">
            Manage Bookings — {{ $event->title }}
        </h1>
        <a href="{{ route('events.show', $event) }}" class="text-blue-700 hover:underline">Back to event</a>
    </div>

    @include('partials.flash')

    <div class="bg-white rounded border p-4">
        <div class="grid md:grid-cols-4 gap-4 text-sm">
            <div><span class="text-gray-500">Date/Time:</span> {{ $event->event_date->format('Y-m-d H:i') }}</div>
            <div><span class="text-gray-500">Location:</span> {{ $event->location }}</div>
            <div><span class="text-gray-500">Capacity:</span> {{ $event->capacity }}</div>
            <div>
                <span class="text-gray-500">Booked / Remaining:</span>
                {{ $event->bookings()->count() }} / {{ $event->remainingSpots() }}
            </div>
        </div>
    </div>

    <div class="bg-white rounded border">
        <div class="p-4 border-b font-medium">Attendees</div>

        @forelse($bookings as $b)
            <div class="p-4 flex items-center justify-between border-b last:border-none">
                <div>
                    <div class="font-medium">{{ $b->user->name ?? 'Unknown User' }}</div>
                    <div class="text-sm text-gray-600">
                        {{ $b->user->email ?? '' }} · Booked at {{ $b->created_at->format('Y-m-d H:i') }}
                    </div>
                </div>

                {{-- Organiser가 참가자 예약을 취소(관리 목적) --}}
                <form method="POST" action="{{ route('bookings.destroy', $b) }}"
                      onsubmit="return confirm('Remove this attendee from the event?');">
                    @csrf
                    @method('DELETE')
                    <button class="px-3 py-2 bg-red-600 text-white rounded">Remove</button>
                </form>
            </div>
        @empty
            <div class="p-6 text-gray-600">No attendees yet.</div>
        @endforelse

        <div class="p-4">
            {{ $bookings->links() }}
        </div>
    </div>
</div>
@endsection
