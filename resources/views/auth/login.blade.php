{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.app')
@section('title','Log in')

@section('content')
<div class="max-w-md mx-auto mt-8 bg-white shadow-md rounded p-6">
  <h1 class="text-2xl font-bold mb-4">Log in</h1>

  @include('partials.flash')

  <form method="POST" action="{{ route('login') }}" class="space-y-4">
    @csrf

    <div>
      <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
      <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
             class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
      @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
      <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
      <input id="password" name="password" type="password" required
             class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
      @error('password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center justify-between">
      <label class="inline-flex items-center">
        <input id="remember" type="checkbox" name="remember"
               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
        <span class="ms-2 text-sm text-gray-600">Remember me</span>
      </label>

      @if (Route::has('password.request'))
        <a href="{{ route('password.request') }}" class="text-sm text-blue-700 hover:underline">
          Forgot password?
        </a>
      @endif
    </div>

    <button type="submit" class="w-full bg-blue-600 text-white rounded p-2">
      Log in
    </button>
  </form>

  <div class="mt-4 text-sm">
    New here?
    <a href="{{ route('register') }}" class="text-blue-700 hover:underline ml-1">Create an account</a>
  </div>
</div>
@endsection
