@csrf

<div class="space-y-4">
  <div>
    <label class="block text-sm font-medium">Title</label>
    <input type="text" name="title" value="{{ old('title', $event->title) }}"
           class="mt-1 w-full border rounded p-2" required>
    @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  <div>
    <label class="block text-sm font-medium">Description</label>
    <textarea name="description" rows="4" class="mt-1 w-full border rounded p-2">{{ old('description', $event->description) }}</textarea>
    @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  <div class="grid md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium">Date & Time</label>
      <input type="datetime-local" name="event_date"
             value="{{ old('event_date', optional($event->event_date)->format('Y-m-d\TH:i')) }}"
             class="mt-1 w-full border rounded p-2" required>
      @error('event_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
      <label class="block text-sm font-medium">Capacity</label>
      <input type="number" name="capacity" min="1"
             value="{{ old('capacity', $event->capacity) }}"
             class="mt-1 w-full border rounded p-2" required>
      @error('capacity') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
  </div>

  <div>
    <label for="category" class="block text-sm font-medium">Category</label>
    <select name="category" id="category" class="mt-1 w-full border rounded p-2">
      <option value="">-- Select a category --</option>
      @foreach ($categories as $category)
        <option value="{{ $category }}"
          {{ old('category', $event->category ?? '') === $category ? 'selected' : '' }}>
          {{ $category }}
        </option>
      @endforeach
    </select>
    @error('category')
      <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
    @enderror
  </div>

  <div>
    <label class="block text-sm font-medium">Location</label>
    <input type="text" name="location" value="{{ old('location', $event->location) }}"
           class="mt-1 w-full border rounded p-2" required>
    @error('location') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>
</div>
