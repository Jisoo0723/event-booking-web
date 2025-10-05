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

    // 이벤트 상세 보기
    public function show(Event $event)
    {
        $event->load('organiser')->loadCount('bookings');

        // 과거 이벤트 접근 제한
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

        return view('events.show', compact(
            'event',
            'isAttendee',
            'isOrganiser',
            'alreadyBooked',
            'myBookingId'
        ));
    }

    // Organiser 대시보드
    public function organiserDashboard()
    {
        $events = Event::where('organizer_id', auth()->id())
            ->withCount('bookings')
            ->orderBy('event_date', 'asc')
            ->paginate(10);

        return view('events.organiser-dashboard', compact('events'));
    }

    // Organiser - 이벤트 생성 폼
    public function create()
    {
        return view('events.create', ['event' => new Event()]);
    }

    // 저장
    public function store(EventRequest $request)
    {
        $data = $request->validated();
        $data['organizer_id'] = auth()->id(); 

        $event = Event::create($data);

        return redirect()
            ->route('events.show', $event)
            ->with('success', 'Event created.');
    }

    // 수정 폼
    public function edit(Event $event)
    {
        $this->authorize('update', $event);
        return view('events.edit', compact('event'));
    }

    // 업데이트
    public function update(EventRequest $request, Event $event)
    {
        $this->authorize('update', $event);

        $event->update($request->validated());

        return redirect()
            ->route('events.show', $event)
            ->with('success', 'Event updated.');
    }

    // 삭제
    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);
        $event->delete();

        return redirect()
            ->route('events.index')
            ->with('success', 'Event deleted.');
    }
}
