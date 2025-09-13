@extends('layouts.app', ['title' => 'Emails'])

@section('content')
  <div class="mb-4 flex items-center justify-between">
    <h1 class="text-[18px] font-semibold">Emails</h1>

    @can('emails.create')
      <a href="{{ route('emails.create') }}"
         class="inline-flex items-center gap-2 rounded-xl bg-amber-600 text-white px-4 py-2 hover:bg-amber-700">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Send Email
      </a>
    @endcan
  </div>

  <div class="bg-white rounded-2xl border shadow-sm">
    <div class="p-3 flex items-center gap-2">
      <form method="GET" class="flex-1">
        <div class="relative">
          <input type="text" name="q" value="{{ $q }}" placeholder="Search"
                 class="w-full h-10 rounded-xl border border-gray-200 pl-9 pr-3">
          <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"
               xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
        </div>
      </form>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
          <tr>
            <th class="px-4 py-3 text-left">Subject</th>
            <th class="px-4 py-3 text-left">Email</th>
            <th class="px-4 py-3 text-left">Message</th>
            <th class="px-4 py-3 text-left">Status</th>
            <th class="px-4 py-3 text-left">Sent At</th>
            @canany(['emails.view','emails.delete'])
              <th class="px-4 py-3"></th>
            @endcanany
          </tr>
        </thead>
        <tbody class="divide-y">
          @forelse($emails as $e)
            <tr>
              <td class="px-4 py-3 font-medium">{{ Str::limit($e->subject, 60) }}</td>
              <td class="px-4 py-3">{{ $e->to_email }}</td>
              <td class="px-4 py-3 text-gray-600">{{ Str::limit(strip_tags($e->message), 70) }}</td>
              <td class="px-4 py-3">
                <span @class([
                  'px-2 py-0.5 rounded-full text-xs',
                  'bg-gray-100 text-gray-700' => $e->status==='draft',
                  'bg-amber-100 text-amber-800' => $e->status==='queued',
                  'bg-green-100 text-green-700' => $e->status==='sent',
                  'bg-rose-100 text-rose-700' => $e->status==='failed',
                ])>{{ ucfirst($e->status) }}</span>
              </td>
              <td class="px-4 py-3">{{ optional($e->sent_at)->format('M d, Y H:i') }}</td>
              @canany(['emails.view','emails.delete'])
                <td class="px-4 py-3 text-right">
                  @can('emails.view')
                    <a href="{{ route('emails.show',$e) }}" class="text-indigo-600 hover:underline">View</a>
                  @endcan
                  @can('emails.delete')
                    <form action="{{ route('emails.destroy',$e) }}" method="POST" class="inline-block ml-2"
                          onsubmit="return confirm('Delete this email?')">
                      @csrf @method('DELETE')
                      <button class="text-rose-600 hover:underline">Delete</button>
                    </form>
                  @endcan
                </td>
              @endcanany
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                <div class="mx-auto w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mb-2">
                  <svg class="w-6 h-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                       viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                  </svg>
                </div>
                <div class="mb-4">No emails yet</div>
                @can('emails.create')
                  <a href="{{ route('emails.create') }}"
                     class="inline-flex items-center gap-2 rounded-xl bg-amber-600 text-white px-4 py-2 hover:bg-amber-700">
                    <span>+ Send Email</span>
                  </a>
                @endcan
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="px-4 py-3">{{ $emails->links() }}</div>
  </div>
@endsection
