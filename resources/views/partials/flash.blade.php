@php
  // 레이아웃에서 forceSuccess/forceError로 넘기면 우선 사용
  $successMsg = $forceSuccess ?? session('success');
  $errorMsg   = $forceError   ?? session('error');
@endphp

@if ($successMsg)
  <div role="alert"
       class="js-flash max-w-4xl mx-auto px-4 py-2 bg-green-100 text-green-800 border border-green-300 rounded mb-4 transition duration-300"
       data-timeout="3000" aria-live="polite">
    {{ $successMsg }}
  </div>
@endif

@if ($errorMsg)
  <div role="alert"
       class="js-flash max-w-4xl mx-auto px-4 py-2 bg-red-100 text-red-800 border border-red-300 rounded mb-4 transition duration-300"
       data-timeout="3000" aria-live="assertive">
    {{ $errorMsg }}
  </div>
@endif

@if (session('warning'))
  <div role="alert"
       class="js-flash max-w-4xl mx-auto px-4 py-2 bg-yellow-100 text-yellow-800 border border-yellow-300 rounded mb-4 transition duration-300"
       data-timeout="3000" aria-live="polite">
    {{ session('warning') }}
  </div>
@endif

@if (session('info'))
  <div role="alert"
       class="js-flash max-w-4xl mx-auto px-4 py-2 bg-blue-100 text-blue-800 border border-blue-300 rounded mb-4 transition duration-300"
       data-timeout="3000" aria-live="polite">
    {{ session('info') }}
  </div>
@endif

@if ($errors->any())
  <div role="alert"
       class="js-flash max-w-4xl mx-auto px-4 py-3 bg-red-100 text-red-800 border border-red-300 rounded mb-4 transition duration-300"
       data-timeout="5000" aria-live="assertive">
    <ul class="list-disc pl-5 space-y-1">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif
