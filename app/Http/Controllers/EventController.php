<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Requests\EventRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class EventController extends Controller
{
    use AuthorizesRequests;

    public function indexPopular()
    {
        $events = Event::with('organiser')
            ->upcoming()
            ->withCount('bookings')
            ->orderByDesc('bookings_count')
            ->orderBy('event_date', 'asc')
            ->paginate(8);

        return view('home', compact('events'));
    }

    public function index()
    {
        $events = Event::with('organiser')
            ->upcoming()
            ->withCount('bookings')
            ->orderBy('event_date', 'asc')
            ->paginate(8);

        $categories = ['All','Art','Business','Fashion','Film','Food & Drink','Music','Sports','Tech'];

        return view('events.index', [
            'events'     => $events,
            'q'          => request('q'),
            'category'   => request('category'),
            'categories' => $categories,
        ]);
    }

    // Event detail view
    public function show(Event $event)
    {
        $event->load('organiser')->loadCount('bookings');

        // Block access if event already passed
        if ($event->isPast() && !(auth()->check() && auth()->user()->role === 'organiser')) {
            return redirect()->route('events.index')->with('error', 'This event has passed.');
        }

        $user = auth()->user();
        $isAttendee  = $user && $user->role === 'attendee';
        $isOrganiser = $user && $user->role === 'organiser';

        $alreadyBooked = false;
        $myBookingId   = null;

        if ($user) {
            $myBooking = $event->bookings()
                ->where('user_id', $user->id)
                ->first(['id']);

            $alreadyBooked = !is_null($myBooking);
            $myBookingId   = $myBooking?->id;
        }

        $isGuest  = !$user;
        $ownerId  = $event->organiser_id ?? $event->organizer_id ?? null;
        $ownsEvent = $isOrganiser && ($ownerId === ($user->id ?? null));

        return view('events.show', [
            'event' => $event,
            'isGuest' => $isGuest,
            'isAttendee' => $isAttendee,
            'isOrganiser' => $isOrganiser,
            'alreadyBooked' => $alreadyBooked,
            'myBookingId' => $myBookingId,
            'ownerId' => $ownerId,
            'ownsEvent' => $ownsEvent,
            'date' => $event->event_date ? $event->event_date->timezone(config('app.timezone')) : null,
            'orgName' => $event->organiser->name ?? $event->organiser?->email ?? 'Organizer',
            'remaining' => method_exists($event, 'remainingSpots')
                ? $event->remainingSpots()
                : max(0, (int)$event->capacity - (int)($event->bookings_count ?? $event->bookings()->count())),
        ]);
    }

    // Organiser dashboard
    public function organiserDashboard()
    {
        $events = Event::where('organizer_id', auth()->id())
            ->withCount('bookings')
            ->orderBy('event_date', 'asc')
            ->paginate(10);

        return view('events.organiser-dashboard', compact('events'));
    }

    // Organiser create form
    public function create()
    {
        return view('events.create', ['event' => new Event()]);
    }

    // Store
    public function store(EventRequest $request)
    {
        $data = $request->validated();
        $data['organizer_id'] = auth()->id(); 

        $event = Event::create($data);

        return redirect()
            ->route('events.show', $event)
            ->with('success', 'Event created.');
    }

    // Edit form
    public function edit(Event $event)
    {
        $this->authorize('update', $event);
        return view('events.edit', compact('event'));
    }

    // Update
    public function update(EventRequest $request, Event $event)
    {
        $this->authorize('update', $event);

        $event->update($request->validated());

        return redirect()
            ->route('events.show', $event)
            ->with('success', 'Event updated.');
    }

    // Delete
    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);
        $event->delete();

        return redirect()
            ->route('events.index')
            ->with('success', 'Event deleted.');
    }
}

