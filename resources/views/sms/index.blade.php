{{-- resources/views/sms/index.blade.php --}}
@extends('layouts.app', ['title' => 'Sms'])

@section('content')
  <div class="mb-4 flex items-center justify-between">
    <h1 class="text-[18px] font-semibold">Sms</h1>
    @can('sms.create')
      <a href="{{ route('sms.create') }}"
         class="inline-flex items-center gap-2 rounded-xl bg-orange-500 text-white px-4 py-2 hover:bg-orange-600">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10z"/>
        </svg>
        <span>Send sms</span>
      </a>
    @endcan
  </div>

  <div class="bg-white rounded-2xl border shadow-sm">
    <div class="flex items-center justify-between p-4 border-b">
      <form class="w-full max-w-md">
        <div class="relative">
          <input type="text" name="search" value="{{ request('search') }}" placeholder="Search"
                 class="h-10 w-full rounded-xl border border-gray-200 bg-white pl-9 pr-3">
          <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"
               viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="11" cy="11" r="8"/>
            <path d="m21 21-4.3-4.3"/>
          </svg>
        </div>
      </form>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
          <tr>
            <th class="px-4 py-3 text-left">User</th>
            <th class="px-4 py-3 text-left">Phone</th>
            <th class="px-4 py-3 text-left">Message</th>
            <th class="px-4 py-3 text-left">Delivered</th>
            <th class="px-4 py-3 text-left">Sent</th>
            <th class="px-4 py-3 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          @forelse($items as $it)
            <tr>
              <td class="px-4 py-3">
                @if($it->user)
                  <div class="font-medium">{{ $it->user->name }}</div>
                  <div class="text-xs text-gray-500">{{ $it->user->email }}</div>
                @else
                  <span class="text-gray-500">—</span>
                @endif
              </td>
              <td class="px-4 py-3">{{ $it->phone }}</td>
              <td class="px-4 py-3">{{ $it->short }}</td>
              <td class="px-4 py-3">
                @if($it->delivered_at)
                  <span class="inline-flex items-center gap-1 text-green-600">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M20 6 9 17l-5-5"/>
                    </svg>
                    {{ $it->delivered_at->format('M d, H:i') }}
                  </span>
                @elseif($it->status === 'failed')
                  <span class="text-red-600">Failed</span>
                @else
                  <span class="text-gray-500">—</span>
                @endif
              </td>
              <td class="px-4 py-3">
                @if($it->sent_at)
                  {{ $it->sent_at->format('M d, H:i') }}
                @else
                  —
                @endif
              </td>
              <td class="px-4 py-3 text-right">
                <div class="flex justify-end gap-2">
                  @can('sms.view')
                    <a href="{{ route('sms.show', $it) }}"
                       class="px-3 py-1.5 rounded-lg border bg-white hover:bg-gray-50">View</a>
                  @endcan

                  @can('sms.delete')
                    <form method="POST" action="{{ route('sms.destroy', $it) }}"
                          onsubmit="return confirm('Delete this SMS?');">
                      @csrf @method('DELETE')
                      <button
                        class="px-3 py-1.5 rounded-lg border bg-white text-rose-600 hover:bg-rose-50">
                        Delete
                      </button>
                    </form>
                  @endcan
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-4 py-14 text-center text-gray-500">
                <div class="mx-auto w-14 h-14 rounded-full border border-gray-200 flex items-center justify-center mb-3">
                  <svg class="w-6 h-6 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M8 9h8m-8 4h5"/>
                  </svg>
                </div>
                <div class="font-medium">No sms</div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($items->hasPages())
      <div class="p-4">{{ $items->links() }}</div>
    @endif
  </div>
@endsection
