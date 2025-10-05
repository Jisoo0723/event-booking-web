<?php

namespace App\Http\Controllers;

use App\Models\Event;

class HomeController extends Controller
{
    public function index()
    {
        $q = request('q');

        $events = Event::with('organizer')
            ->upcoming() // 지난 이벤트 숨기기 (모델에 scopeUpcoming 이미 있음)
            ->when($q, fn($qq) =>
                $qq->where(function ($w) use ($q) {
                    $w->where('title', 'like', "%{$q}%")
                    ->orWhere('location', 'like', "%{$q}%");
                })
            )
            ->orderBy('event_date')
            ->paginate(8)              // 원하는 개수로
            ->withQueryString();       // 검색어 유지한 채 페이징

        return view('home.index', compact('events', 'q'));
    }

}
