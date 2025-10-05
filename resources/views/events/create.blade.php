@extends('layouts.app')
@section('title','Create Event')

@section('content')
<div class="max-w-2xl mx-auto p-6 bg-white rounded border mt-8 md:mt-10">
  <h1 class="text-xl font-semibold mb-4">Create Event</h1>

  @include('partials.flash')

  <form method="POST" action="{{ route('events.store') }}">
    @include('events._form', ['event' => $event])
    <div class="mt-6 flex justify-end gap-2">
      <a href="{{ route('events.index') }}" class="px-4 py-2 rounded border">Cancel</a>
      <button class="px-4 py-2 rounded bg-blue-600 text-white">Save</button>
    </div>
  </form>
</div>
@endsection
