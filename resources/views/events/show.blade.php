@extends('layouts.app')
@section('title', $event->title)
@section('content')
@php
  // 날짜는 어떤 형식이 와도 안전하게 파싱
  $date = \Carbon\Carbon::parse($event->event_date)->timezone(config('app.timezone'));
  $isPast = $date->isPast();

  // 남은 좌석: 모델 메서드 있으면 사용, 없으면 계산
  $remaining = method_exists($event, 'remainingSpots')
      ? $event->remainingSpots()
      : max(0, (int)$event->capacity - (int)($event->bookings_count ?? $event->bookings()->count()));

  // 주최자명: 관계가 null이어도 안전
  $org = optional($event->organiser);
  $orgName = $org->name ?? $org->email ?? 'Organizer';

  // 권한/소유자
  $user        = auth()->user();
  $isGuest     = !$user;
  $isAttendee  = $user && $user->role === 'attendee';
  $isOrganiser = $user && $user->role === 'organiser';
  $ownerId     = $event->organiser_id ?? $event->organizer_id ?? null;
  $ownsEvent   = $isOrganiser && ($ownerId === ($user->id ?? null));
@endphp

<div class="max-w-5xl mx-auto px-6 py-8 md:py-12">
  {{-- breadcrumb --}}
  <nav class="text-sm text-gray-500 mb-6">
    @if ($ownsEvent)
      <a href="{{ route('organiser.dashboard') }}" class="hover:text-gray-700">Dashboard</a>
      <span class="mx-2">/</span>
      <span class="text-gray-700">{{ \Illuminate\Support\Str::limit($event->title, 60) }}</span>
    @else
      <a href="{{ route('events.index') }}" class="hover:text-gray-700">Events</a>
      <span class="mx-2">/</span>
      <span class="text-gray-700">{{ \Illuminate\Support\Str::limit($event->title, 60) }}</span>
    @endif
  </nav>

  {{-- title & intro --}}
  <header class="mb-8">
    <h1 class="text-3xl md:text-4xl font-semibold tracking-tight text-gray-900">
      {{ $event->title }}
    </h1>

    {{-- 카테고리 뱃지 --}}
    <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-gray-600">
      <span>{{ $date->format('M d, Y g:i A') }}</span>
      <span class="text-gray-300">•</span>
      <span class="truncate">{{ $event->location }}</span>
      <span class="text-gray-300">•</span>

      {{-- 카테고리 뱃지 --}}
      @if(!empty($event->category))
        <a
          href="{{ route('events.index', ['category' => $event->category]) }}"
          class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium
                bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100">
          {{-- 아이콘(선택) --}}
          <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M12 3l10 6-10 6L2 9l10-6zm0 8.485l10-6V21l-10 6-10-6V5.485l10 6z"/>
          </svg>
          {{ $event->category }}
        </a>
      @else
        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs
                    bg-gray-100 text-gray-600 border border-gray-200">
          Uncategorized
        </span>
      @endif
    </div>

    @if ($event->description)
      <p class="mt-4 text-gray-600 leading-7 max-w-3xl">
        {{ $event->description }}
      </p>
    @endif
  </header>

  {{-- details card --}}
  <section class="bg-white border rounded-xl shadow-sm">
    <div class="px-6 py-5 border-b">
      <h2 class="text-lg font-semibold text-gray-900">Event Details</h2>
    </div>

    <div class="px-6 py-6 grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-6 text-gray-800">
      <div>
        <div class="text-xs uppercase tracking-wider text-gray-500">Date</div>
        <div class="mt-1 font-medium">{{ $date->format('M d, Y') }}</div>
      </div>
      <div>
        <div class="text-xs uppercase tracking-wider text-gray-500">Time</div>
        <div class="mt-1 font-medium">{{ $date->format('g:i A') }}</div>
      </div>
      <div>
        <div class="text-xs uppercase tracking-wider text-gray-500">Location</div>
        <div class="mt-1 font-medium">{{ $event->location }}</div>
      </div>
      <div>
        <div class="text-xs uppercase tracking-wider text-gray-500">Organizer</div>
        <div class="mt-1 font-medium">{{ $orgName }}</div>
      </div>
      <div>
        <div class="text-xs uppercase tracking-wider text-gray-500">Capacity</div>
        <div class="mt-1 font-medium">{{ (int) $event->capacity }}</div>
      </div>
      <div>
        <div class="text-xs uppercase tracking-wider text-gray-500">Remaining Spots</div>
        <div class="mt-1 font-medium {{ $remaining ? 'text-green-700' : 'text-red-700' }}">
          {{ $remaining }}
        </div>
      </div>
    </div>

    {{-- CTA --}}
    <div class="px-6 pb-6 mt-6">
      @if ($isGuest)
        <a href="{{ route('login') }}"
           class="inline-flex items-center justify-center rounded-lg px-5 py-3 text-sm font-medium
                  bg-indigo-600 text-white hover:bg-indigo-700 transition">
          Login to Book
        </a>
      @elseif ($isAttendee)
        @if ($alreadyBooked ?? false)
          <form method="POST" action="{{ route('bookings.destroy', $myBookingId ?? 0) }}">
            @csrf @method('DELETE')
            <button class="inline-flex items-center justify-center rounded-lg px-5 py-3 text-sm font-medium
                           bg-red-600 text-white hover:bg-red-700 transition">
              Cancel Booking
            </button>
          </form>
        @else
          <form method="POST" action="{{ route('bookings.store', $event) }}">
            @csrf
            <button class="inline-flex items-center justify-center rounded-lg px-5 py-3 text-sm font-medium
                           {{ $remaining < 1 ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-indigo-600 text-white hover:bg-indigo-700' }}"
                    {{ $remaining < 1 ? 'disabled' : '' }}>
              {{ $remaining < 1 ? 'Sold Out' : 'Book Now' }}
            </button>
          </form>
        @endif
      @elseif ($ownsEvent)
        <div class="flex flex-wrap gap-2">
          <a href="{{ route('events.edit', $event) }}"
             class="px-4 py-2 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">
            Edit Event
          </a>
          <a href="{{ route('bookings.export', $event) }}"
             class="px-4 py-2 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">
            Export CSV
          </a>
          <button type="button"
                  class="px-4 py-2 rounded border border-gray-300 text-gray-700 hover:bg-gray-50 js-open-attendees"
                  data-url="{{ route('events.attendees', ['event' => $event->id]) }}">
            View Attendees
          </button>
          @can('manage', $event)
          <form action="{{ route('events.destroy', ['event' => $event->id]) }}"
                method="POST"
                onsubmit="return confirm('Delete this event?');" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50 text-red-600">
              Delete
            </button>
          </form>
          @endcan
        </div>
      @endif
    </div>
  </section>
</div>
@endsection
