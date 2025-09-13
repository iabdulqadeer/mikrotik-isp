@extends('layouts.app', ['title' => 'Send SMS'])

@section('content')
  <div class="max-w-2xl mx-auto bg-white rounded-2xl border shadow-sm p-6">
    <h1 class="text-lg font-semibold mb-4">Send SMS</h1>
    <form method="POST" action="{{ route('sms.store') }}" class="space-y-4">
      @csrf

      <div>
        <label class="block text-sm mb-1">Recipient</label>
        <select name="user_id" class="w-full h-10 rounded-xl border px-3">
          <option value="">Select user</option>
          @foreach($users as $u)
            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->phone ?? 'No phone' }})</option>
          @endforeach
        </select>
        <div class="mt-2">
          <label class="inline-flex items-center gap-2 text-sm">
            <input type="checkbox" name="send_to_all" value="1" class="rounded border-gray-300">
            Send to all users with phone
          </label>
        </div>
      </div>

      <div>
        <label class="block text-sm mb-1">Message</label>
        <textarea name="message" rows="4" required
                  class="w-full rounded-xl border px-3 py-2">{{ old('message') }}</textarea>
        @error('message') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
      </div>

      <div class="flex justify-end gap-2">
        <a href="{{ route('sms.index') }}" class="h-10 px-4 rounded-xl border bg-white hover:bg-gray-50">Cancel</a>
        <button class="h-10 px-4 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">Send</button>
      </div>
    </form>
  </div>
@endsection
