@extends('layouts.app', ['title' => 'Lead: '.$lead->name])

@section('content')
  <div class="mb-4 flex items-center justify-between">
    <div>
      <h1 class="text-[18px] font-semibold">{{ $lead->name }}</h1>
      <p class="text-[12px] text-gray-500">{{ $lead->company ?? '—' }}</p>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('leads.edit',$lead) }}" class="px-3 h-10 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">Edit</a>
      <a href="{{ route('leads.index') }}" class="px-3 h-10 rounded-xl border bg-white hover:bg-gray-50">Back</a>
    </div>
  </div>

  <div class="grid md:grid-cols-3 gap-4">
    <div class="bg-white rounded-2xl border shadow-sm p-4 md:col-span-2">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
        <div><div class="text-gray-500">Email</div><div class="font-medium">{{ $lead->email ?? '—' }}</div></div>
        <div><div class="text-gray-500">Phone</div><div class="font-medium">{{ $lead->phone ?? '—' }}</div></div>
        <div><div class="text-gray-500">Status</div><div class="font-medium capitalize">{{ $lead->status }}</div></div>
        <div><div class="text-gray-500">Source</div><div class="font-medium">{{ $lead->source ?? '—' }}</div></div>
        <div class="md:col-span-2"><div class="text-gray-500">Address</div>
          <div class="font-medium">
            {{ collect([$lead->address,$lead->city,$lead->state,$lead->postal_code,$lead->country])->filter()->implode(', ') ?: '—' }}
          </div>
        </div>
        <div><div class="text-gray-500">Last Contact</div><div class="font-medium">{{ optional($lead->last_contact_at)->format('M d, Y H:i') ?? '—' }}</div></div>
        <div><div class="text-gray-500">Next Follow Up</div><div class="font-medium">{{ optional($lead->next_follow_up_at)->format('M d, Y H:i') ?? '—' }}</div></div>
        <div class="md:col-span-2">
          <div class="text-gray-500 mb-1">Notes</div>
          <div class="prose max-w-none">{{ $lead->notes ? nl2br(e($lead->notes)) : '—' }}</div>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-2xl border shadow-sm p-4">
      <div class="text-sm text-gray-500 mb-2">Meta</div>
      <div class="text-sm space-y-1">
        <div><span class="text-gray-500">Owner:</span> <span class="font-medium">{{ optional($lead->owner)->name ?? '—' }}</span></div>
        <div><span class="text-gray-500">Created:</span> <span class="font-medium">{{ $lead->created_at->format('M d, Y H:i') }}</span></div>
        <div><span class="text-gray-500">Updated:</span> <span class="font-medium">{{ $lead->updated_at->format('M d, Y H:i') }}</span></div>
      </div>
    </div>
  </div>
@endsection
