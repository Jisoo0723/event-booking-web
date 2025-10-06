<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BookingController extends Controller
{
    use AuthorizesRequests; 
    // 내 예약 목록
    public function index()
    {
        $userId = auth()->id();

        // 브리즈번 기준 현재시각 (문자열 비교로 고정)
        $now = now(config('app.timezone'))->toDateTimeString();

        $withEvent = ['event' => function ($q) {
            $q->select('id','title','event_date','location','capacity','category','image');
        }];

        // Upcoming: 지금 이후
        $upcoming = Booking::with($withEvent)
            ->where('user_id', $userId)
            ->whereHas('event', fn($q) => $q->where('event_date', '>', $now))
            ->orderBy(
                Event::select('event_date')->whereColumn('events.id','bookings.event_id')
            )
            ->get();

        // Past(= Attended): 지금 이전/같음
        $past = Booking::with($withEvent)
            ->where('user_id', $userId)
            ->whereHas('event', fn($q) => $q->where('event_date', '<=', $now))
            ->orderByDesc(
                Event::select('event_date')->whereColumn('events.id','bookings.event_id')
            )
            ->get();

        $summary = [
            'total'     => Booking::where('user_id', $userId)->count(),
            'upcoming'  => $upcoming->count(),
            'attended'  => $past->count(),
        ];

        return view('bookings.index', compact('upcoming','past','summary'));
    }


    // 예약 생성
    public function store(Event $event, Request $request)
    {
        $user = auth()->user();

        return DB::transaction(function () use ($event, $user) {
            // 과거/종료 이벤트 방지 (타임존 일관)
            if (optional($event->event_date)->lte(now(config('app.timezone')))) {
                return back()->withErrors('This event has already passed.');
            }

            // 본인 이벤트 예약 금지(네 기존 가드 유지)
            if ($event->organiser_id === $user->id) {
                abort(403);
            }

            // 현재 예약 수 재계산(트랜잭션 + 행 잠금)
            // MySQL/Postgres에서 유효, SQLite에서는 무시돼도 안전
            $booked = $event->bookings()->lockForUpdate()->count();
            if ($booked >= (int) $event->capacity) {
                return back()->withErrors('Fully booked.');
            }

            // 중복 예약 방지: 유니크 제약을 신뢰하고 예외 처리
            try {
                $event->bookings()->create(['user_id' => $user->id]);
            } catch (\Throwable $e) {
                // 유니크 인덱스( user_id, event_id ) 위반 등
                return back()->withErrors('You already booked this event.');
            }

            return back()->with('success', 'Booking confirmed.');
        });
    }

    // 예약 취소
    public function destroy(Booking $booking)
    {
        abort_unless($booking->user_id === auth()->id(), 403);

        // 과거 이벤트는 취소 불가
        if (optional($booking->event?->event_date)->lte(now())) {
            return back()->withErrors('Past events cannot be cancelled.');
        }

        $booking->delete();

        return redirect()->route('bookings.index')
                        ->with('success', 'Booking cancelled.');
    }


    // organiser 관리 페이지
    public function manage(\App\Models\Event $event)
    {
        // 본인 소유 이벤트만 접근
        abort_unless(auth()->id() === $event->organiser_id, 403);

        $bookings = $event->bookings()->with('user')->latest()->paginate(20);

        return view('bookings.manage', compact('event', 'bookings'));
    }

    public function export(Event $event)
    {
        // Policy 사용 (organiser 본인만)
        $this->authorize('manage', $event);

        // 예약 목록 (없어도 오류 X)
        $rows = $event->bookings()
            ->with('user:id,name,email')
            ->orderBy('created_at')
            ->get()
            ->map(fn($b) => [
                'Name'      => $b->user->name ?? "User#{$b->user_id}",
                'Email'     => $b->user->email ?? '',
                'Booked At' => $b->created_at
                    ->timezone(config('app.timezone'))
                    ->format('Y-m-d H:i'),
            ]);

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');

            // BOM 추가 (엑셀에서 깨짐 방지)
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // 헤더
            fputcsv($out, ['Name', 'Email', 'Booked At']);

            // 데이터 (없어도 루프는 그냥 지나감)
            foreach ($rows as $r) {
                fputcsv($out, $r);
            }

            fclose($out);
        }, 'attendees.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }


    public function attendeesPartial(Event $event)
    {
        // 로그인 및 권한 확인
        if (!auth()->check()) {
            return response('<div class="p-4 text-red-600">Not logged in.</div>', 403);
        }

        if (auth()->user()->role !== 'organiser') {
            return response('<div class="p-4 text-red-600">Not organiser.</div>', 403);
        }

        // 🔧 organiser_id 또는 organizer_id 확인
        if (($event->organiser_id ?? $event->organizer_id) !== auth()->id()) {
            return response('<div class="p-4 text-red-600">You are not the owner of this event.</div>', 403);
        }

        // 예약자 목록 가져오기
        $bookings = $event->bookings()->with('user:id,name,email')->orderByDesc('created_at')->get();

        if ($bookings->isEmpty()) {
            return '<div class="p-4 text-gray-600">No attendees yet.</div>';
        }

        // partial 뷰로 반환
        return view('partials.attendees_table', [
            'bookings' => $bookings,
            'event'    => $event,
        ]);
    }


}
