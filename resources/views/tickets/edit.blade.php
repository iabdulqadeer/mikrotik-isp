{{-- resources/views/tickets/edit.blade.php --}}
@extends('layouts.app', ['title' => 'Edit Ticket'])

@section('content')

  <div class="mb-4">
    <h1 class="text-[18px] font-semibold">Edit Ticket</h1>
    <p class="text-[12px] text-gray-500">Update the ticket details.</p>
  </div>

  <form method="POST" action="{{ route('tickets.update', $ticket) }}"
        class="bg-white rounded-2xl border shadow-sm p-4 space-y-4 max-w-3xl">
    @csrf @method('PUT')

    <div>
      <label class="block text-sm font-medium mb-1">Customer (optional)</label>
      <select name="user_id" class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3">
        <option value="">— None —</option>
        @foreach($usres as $u)
          <option value="{{ $u->id }}" @selected(old('user_id',$ticket->user_id)==$u->id)>{{ $u->name }}</option>
        @endforeach
      </select>
      <x-input-error :messages="$errors->get('user_id')" class="mt-1"/>
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Subject</label>
      <input name="subject" value="{{ old('subject',$ticket->subject) }}" class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3" required>
      <x-input-error :messages="$errors->get('subject')" class="mt-1"/>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <div>
        <label class="block text-sm font-medium mb-1">Priority</label>
        <select name="priority" class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3" required>
          @foreach (['low','normal','high','urgent'] as $p)
            <option value="{{ $p }}" @selected(old('priority',$ticket->priority)==$p)>{{ ucfirst($p) }}</option>
          @endforeach
        </select>
        <x-input-error :messages="$errors->get('priority')" class="mt-1"/>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Status</label>
        <select name="status" class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3" required>
          @foreach (['open','pending','resolved','closed'] as $s)
            <option value="{{ $s }}" @selected(old('status',$ticket->status)==$s)>{{ ucfirst($s) }}</option>
          @endforeach
        </select>
        <x-input-error :messages="$errors->get('status')" class="mt-1"/>
      </div>
    </div>

    <div class="flex justify-end gap-2">
      <a href="{{ route('tickets.show',$ticket) }}" class="h-10 px-4 rounded-xl border bg-white hover:bg-gray-50">Cancel</a>
      <button class="h-10 px-4 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">Save</button>
    </div>
  </form>
@endsection
