<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Requests\EventRequest;

class EventController extends Controller
{
    // 공개 목록
    public function index()
    {
        $q = request('q');

        $events = \App\Models\Event::with('organizer')
            ->upcoming()
            ->when($q, fn($qq) =>
                $qq->where(function($w) use ($q){
                    $w->where('title','like',"%$q%")
                    ->orWhere('location','like',"%$q%");
                })
            )
            ->orderBy('event_date')
            ->paginate(9)
            ->withQueryString();

        return view('events.index', compact('events','q'));
    }

    // 이벤트 상세 보기
    public function show($id)
    {
        $event = Event::with('organizer')
            ->withCount('bookings') // 예약 수 미리 로드
            ->findOrFail($id);

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

    // (Organiser 전용) 대시보드
    public function organiserDashboard()
    {
        $events = \App\Models\Event::where('organizer_id', auth()->id())
            ->orderBy('event_date', 'asc')
            ->paginate(10);

        return view('events.organiser-dashboard', compact('events'));
    }

    // (Organiser 전용) 생성 폼 - routes에서 can:isOrganiser로 보호됨
    public function create()
    {
        return view('events.create', ['event' => new Event()]);
    }

    // (Organiser 전용) 저장
    public function store(EventRequest $request)
    {
        $data = $request->validated();
        $data['organizer_id'] = auth()->id();

        $event = Event::create($data);

        return redirect()
            ->route('events.show', $event)
            ->with('success', 'Event created.');
    }

    // (Organiser 전용) 수정 폼
    public function edit(Event $event)
    {
        abort_unless($event->organizer_id === auth()->id(), 403);

        return view('events.edit', compact('event'));
    }

    // (Organiser 전용) 업데이트
    public function update(EventRequest $request, Event $event)
    {
        abort_unless($event->organizer_id === auth()->id(), 403);

        $event->update($request->validated());

        return redirect()
            ->route('events.show', $event)
            ->with('success', 'Event updated.');
    }

    // (Organiser 전용) 삭제
    public function destroy(Event $event)
    {
        abort_unless($event->organizer_id === auth()->id(), 403);

        $event->delete();

        return redirect()
            ->route('events.index')
            ->with('success', 'Event deleted.');
    }
}
