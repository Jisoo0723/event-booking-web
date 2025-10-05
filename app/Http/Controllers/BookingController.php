<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Event;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    // 내 예약 목록
    public function index()
    {
        $bookings = Booking::with(['event' => function ($q) {
                $q->withCount('bookings');   //  이벤트의 예약 수 같이 가져오기
            }])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    // 예약 생성
    public function store(Event $event, Request $request)
    {
        // 본인 이벤트 예약 금지
        if ($event->organizer_id === auth()->id()) {
            abort(403);
        }


        // 지난 이벤트 차단
        if ($event->isPast()) {
            return back()->with('error', 'This event is already in the past.');
        }

        // 중복 예약 차단
        $exists = Booking::where('user_id', auth()->id())
            ->where('event_id', $event->id)
            ->exists();
        if ($exists) {
            return back()->with('error', 'You have already booked this event.');
        }

        // 만석 차단
        if ($event->remainingSpots() <= 0) {
            return back()->with('error', 'This event is fully booked.');
        }

        // 예약 생성
        Booking::create([
            'user_id'  => auth()->id(),
            'event_id' => $event->id,
        ]);

        return back()->with('success', 'Booking confirmed.');
    }

    // 예약 취소
    public function destroy(Booking $booking)
    {
        abort_unless($booking->user_id === auth()->id(), 403);

        $booking->delete();

        return redirect()->route('bookings.index')
                          ->with('success', 'Booking cancelled.');
    }

    // organiser 관리 페이지
    public function manage(\App\Models\Event $event)
    {
        // 본인 소유 이벤트만 접근
        abort_unless(auth()->id() === $event->organizer_id, 403);

        $bookings = $event->bookings()->with('user')->latest()->paginate(20);

        return view('bookings.manage', compact('event', 'bookings'));
    }

    public function export(\App\Models\Event $event)
    {
        abort_unless($event->organizer_id === auth()->id(), 403);

        $rows = $event->bookings()
            ->with('event','user:id,name,email')
            ->orderBy('created_at')
            ->get()
            ->map(fn($b)=>[
                'event'      => $event->title,
                'user_name'  => $b->user->name ?? '',
                'user_email' => $b->user->email ?? '',
                'booked_at'  => $b->created_at->toDateTimeString(),
            ]);

        $csv = "event,user_name,user_email,booked_at\n".
            $rows->map(fn($r)=>implode(',', array_map(fn($v)=>'"'.str_replace('"','""',$v).'"',$r)))->implode("\n");

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="bookings_'.$event->id.'.csv"',
        ]);
    }

    public function attendeesPartial(Event $event)
    {
        abort_unless($event->organizer_id === auth()->id(), 403);

        $bookings = $event->bookings()
            ->with('user')
            ->latest()
            ->take($event->capacity)
            ->get();

        return view('bookings.partials.attendees_table', compact('event','bookings'));
    }

}
