<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Requests\EventRequest;

class EventController extends Controller
{
    // 공개 목록
    public function index()
    {
        $events = Event::with('organiser')
            ->upcoming()
            ->withCount('bookings')        // 예약 수
            ->orderByDesc('bookings_count')// 예약 많은 순
            ->orderBy('event_date','asc')  // 동률이면 날짜 가까운 순
            ->paginate(8);

        // 홈에서도 events.index 뷰 재사용한다면:
        $categories = ['All','Art','Business','Fashion','Film','Food & Drink','Music','Sports','Tech'];
        return view('events.index', [
            'events'     => $events,
            'q'          => null,
            'category'   => null,
            'categories' => $categories,
        ]);
    }

    public function indexPopular()
    {
        $events = \App\Models\Event::with('organiser')
            ->upcoming()
            ->withCount('bookings')
            ->orderByDesc('bookings_count')
            ->orderBy('event_date','asc')
            ->paginate(8);

        // 카테고리 제외 (홈에는 필요 없음)
        return view('events.index', [
            'events'     => $events,
            'q'          => null,
            'category'   => null,
        ]);
    }

    // 이벤트 상세 보기 (모델 바인딩 + 정책/규칙 반영)
    public function show(Event $event)
    {
        // 필요 데이터 eager-load
        $event->load('organiser')->loadCount('bookings');

        // 과거 이벤트는 organiser만 접근 (Policy와 동일 로직 유지)
        if ($event->isPast() && !(auth()->check() && auth()->user()->role === 'organiser')) {
            return redirect()->route('events.index')->with('error','This event has passed.');
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

    // (Organiser 전용) 대시보드
    public function organiserDashboard()
    {
        $events = Event::where('organizer_id', auth()->id())
            ->withCount('bookings')
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
        $data['organiser_id'] = auth()->id(); // 모델의 브리지(mutator)로 organizer_id에 저장

        $event = Event::create($data);

        return redirect()
            ->route('events.show', $event)
            ->with('success', 'Event created.');
    }

    // (Organiser 전용) 수정 폼
    public function edit(Event $event)
    {
        // 정책 사용
        $this->authorize('update', $event);

        return view('events.edit', compact('event'));
    }

    // (Organiser 전용) 업데이트
    public function update(EventRequest $request, Event $event)
    {
        // 정책 사용
        $this->authorize('update', $event);

        $event->update($request->validated());

        return redirect()
            ->route('events.show', $event)
            ->with('success', 'Event updated.');
    }

    // (Organiser 전용) 삭제
    public function destroy(Event $event)
    {
        // 정책 사용
        $this->authorize('delete', $event);

        $event->delete();

        return redirect()
            ->route('events.index')
            ->with('success', 'Event deleted.');
    }
}
