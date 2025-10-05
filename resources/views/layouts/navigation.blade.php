<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 sticky top-0 z-[50]">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between h-16 items-center">

      {{-- 왼쪽 영역: 로고 + 메뉴 --}}
      <div class="flex items-center gap-8">
        <a href="{{ route('home') }}" class="flex items-center gap-2">
          <svg class="h-5 w-5 text-indigo-600" viewBox="0 0 24 24" fill="currentColor">
            <path d="M7 2a1 1 0 0 1 1 1v1h8V3a1 1 0 1 1 2 0v1h1a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1V3a1 1 0 1 1 2 0v1Z"/>
          </svg>
          <span class="font-extrabold tracking-wide">BOOKiFY</span>
        </a>

        @php
          $isHome   = request()->routeIs('home');
          $isEvents = request()->routeIs('events.index') || request()->routeIs('events.show');
          $isCreate = request()->routeIs('events.create');
          $isDash   = request()->routeIs('organiser.dashboard');
          $isBooks  = request()->routeIs('bookings.*');

          $linkBase = 'inline-flex items-center h-10 px-3 leading-10 text-sm';
          $active   = 'text-indigo-600 font-semibold';
          $normal   = 'text-gray-900';
        @endphp

        <div class="hidden sm:flex items-center gap-8">
          <a href="{{ route('events.index') }}"
             class="{{ $linkBase }} {{ (!$isHome && $isEvents) ? $active : $normal }}">
            Events
          </a>

          @auth
            @if(auth()->user()->role === 'attendee')
              <a href="{{ route('bookings.index') }}"
                 class="{{ $linkBase }} {{ (!$isHome && $isBooks) ? $active : $normal }}">
                My Bookings
              </a>
            @endif

            @if(auth()->user()->role === 'organiser')
              <a href="{{ route('events.create') }}"
                 class="{{ $linkBase }} {{ (!$isHome && $isCreate) ? $active : $normal }}">
                Create Event
              </a>
              <a href="{{ route('organiser.dashboard') }}"
                 class="{{ $linkBase }} {{ (!$isHome && $isDash) ? $active : $normal }}">
                Dashboard
              </a>
            @endif
          @endauth
        </div>
      </div>

      {{-- 오른쪽 영역: 사용자 정보 + 로그아웃 --}}
      <div class="hidden sm:flex items-center gap-6">
        @auth
          <div class="flex items-center gap-2">
            <span class="inline-flex h-6 w-6 rounded-full bg-gradient-to-tr from-indigo-500 via-sky-400 to-amber-300"></span>
            <span class="inline-flex items-center h-10 leading-10 text-sm text-gray-900 whitespace-nowrap">
              {{ Auth::user()->name }}
              @if(Auth::user()->role === 'organiser') / Organizer @endif
            </span>
          </div>
          <form method="POST" action="{{ route('logout') }}" class="h-10 flex items-center">
            @csrf
            <button type="submit" class="inline-flex items-center h-10 px-3 leading-10 text-sm text-gray-600 hover:text-gray-900">
              Log Out
            </button>
          </form>
        @else
          <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:underline">Log in</a>
          <a href="{{ route('register') }}" class="text-sm bg-[#2973EB] text-white px-3 py-1.5 rounded hover:bg-[#2567d4]">Sign Up</a>
        @endauth
      </div>
    </div>
  </div>
</nav>
