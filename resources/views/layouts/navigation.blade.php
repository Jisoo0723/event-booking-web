<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between h-16">
      <!-- Left: Logo + Main links -->
      <div class="flex items-center gap-8">
        <!-- Logo -->
        <a href="{{ route('home') }}" class="flex items-center gap-2">
          <svg class="h-5 w-5 text-indigo-600" viewBox="0 0 24 24" fill="currentColor"><path d="M7 2a1 1 0 0 1 1 1v1h8V3a1 1 0 1 1 2 0v1h1a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1V3a1 1 0 1 1 2 0v1Zm12 6H5v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V8Z"/></svg>
          <span class="font-extrabold tracking-wide">BOOKiFY</span>
        </a>

        <!-- Main links -->
        <div class="hidden sm:flex items-center gap-8">
          <a href="{{ route('events.index') }}"
             class="text-sm {{ request()->routeIs('home') || request()->routeIs('events.index') ? 'text-indigo-600 font-semibold' : 'text-gray-700' }}">
            Events
          </a>

          @auth
          @if(auth()->user()->role === 'attendee')
            <a href="{{ route('bookings.index') }}" class="text-sm px-3 py-2 hover:underline">
              My Bookings
            </a>
          @endif
          @endauth


          @auth
            @can('isOrganiser')
              <a href="{{ route('events.create') }}"
                class="text-sm {{ request()->routeIs('events.create') ? 'text-indigo-600 font-semibold' : 'text-gray-700' }}">
                Create Event
              </a>
              <a href="{{ route('organiser.dashboard') }}"
                class="text-sm {{ request()->routeIs('organiser.dashboard') ? 'text-indigo-600 font-semibold' : 'text-gray-700' }}">
                Dashboard
              </a>
            @elsecan('isAttendee')
              <a href="{{ route('bookings.index') }}"
                class="text-sm {{ request()->routeIs('bookings.index') ? 'text-indigo-600 font-semibold' : 'text-gray-700' }}">
                My Bookings
              </a>
            @endcan
          @endauth

        </div>
      </div>

      <!-- Right: Auth -->
      <div class="hidden sm:flex items-center gap-6">
        @auth
          <div class="flex items-center gap-2">
            <span class="inline-flex h-6 w-6 rounded-full bg-gradient-to-tr from-indigo-500 via-sky-400 to-amber-300"></span>
            <span class="text-sm text-gray-900">
              {{ Auth::user()->name }}
              @if(Auth::user()->role === 'organiser') / Organizer @endif
            </span>
          </div>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="text-sm text-gray-600 hover:text-gray-900">Log Out</button>
          </form>
        @else
          @if (Route::has('login'))
            <a href="{{ route('login') }}" class="text-sm text-gray-900">Log in</a>
          @endif
          @if (Route::has('register'))
            <a href="{{ route('register') }}" class="text-sm text-gray-900">Sign Up</a>
          @endif
        @endauth
      </div>

      <!-- Mobile hamburger -->
      <div class="-me-2 flex items-center sm:hidden">
        <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:bg-gray-100">
          <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
  </div>

  <!-- Mobile menu -->
  <div :class="{'block': open, 'hidden': ! open}" class="sm:hidden hidden border-t border-gray-100">
    <div class="px-4 py-3 space-y-2">
      <a href="{{ route('home') }}" class="block text-gray-700">Events</a>

      @auth
        @if(Auth::user()->role === 'organiser')
          <a href="{{ route('events.create') }}" class="block text-gray-700">Create Event</a>
          <a href="{{ route('dashboard') }}" class="block text-gray-700">Dashboard</a>
        @else
          <a href="{{ route('bookings.index') }}" class="block text-gray-700">My Bookings</a>
        @endif

        <div class="pt-3 border-t border-gray-200">
          <div class="text-sm text-gray-900">{{ Auth::user()->name }} @if(Auth::user()->role==='organiser') / Organizer @endif</div>
          <div class="text-xs text-gray-500">{{ Auth::user()->email }}</div>
          <form method="POST" action="{{ route('logout') }}" class="mt-2">
            @csrf
            <button class="text-left w-full text-gray-700">Log Out</button>
          </form>
        </div>
      @else
        @if (Route::has('login'))
          <a href="{{ route('login') }}" class="block text-gray-700">Log in</a>
        @endif
        @if (Route::has('register'))
          <a href="{{ route('register') }}" class="block text-gray-700">Sign Up</a>
        @endif
      @endauth
    </div>
  </div>
</nav>
