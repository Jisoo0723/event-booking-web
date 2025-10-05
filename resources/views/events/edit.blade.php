@extends('layouts.app')
@section('title','Edit: '.$event->title)

@section('content')
<div class="max-w-2xl mx-auto p-6 bg-white rounded border">
  <h1 class="text-xl font-semibold mb-4">Edit Event</h1>

  @include('partials.flash')

  <form method="POST" action="{{ route('events.update', $event) }}">
    @method('PUT')
    @include('events._form', ['event' => $event])
    <div class="mt-6 flex justify-end gap-2">
      <a href="{{ route('events.show', $event) }}" class="px-4 py-2 rounded border">Back</a>
      <button class="px-4 py-2 rounded bg-blue-600 text-white">Update</button>
    </div>
  </form>
</div>
@endsection
