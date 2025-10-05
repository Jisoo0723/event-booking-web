@props(['status'])

@php
    $authRoutes = [
        'login','register',
        'password.request','password.email','password.reset',
        'verification.notice','verification.verify'
    ];
    $shouldShow = in_array(optional(Route::current())->getName(), $authRoutes);
@endphp

@if ($status && $shouldShow)
    <div class="max-w-4xl mx-auto px-4 py-2 bg-blue-100 text-blue-800 border border-blue-300 rounded mb-4">
        {{ $status }}
    </div>
@endif
