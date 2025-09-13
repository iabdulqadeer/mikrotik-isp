{{-- resources/views/tickets/index.blade.php --}}
@extends('layouts.app', ['title' => 'Tickets'])

@section('content')

  <div class="mb-4 flex flex-col lg:flex-row lg:items-center gap-3">
    <div>
      <h1 class="text-[18px] font-semibold">Support Tickets</h1>
      <p class="text-[12px] text-gray-500">Track, filter, and manage support requests.</p>
    </div>

    <div class="lg:ml-auto flex items-center gap-2">
      <form method="GET" class="flex flex-wrap items-center gap-2">
        <input name="q" value="{{ $filters['q'] ?? '' }}"
               class="h-10 w-64 rounded-xl border border-gray-200 bg-white px-3"
               placeholder="Search number / subject…">

        <select name="status" class="h-10 rounded-xl border border-gray-200 bg-white px-3">
          <option value="">All status</option>
          @foreach($statuses as $s)
            <option value="{{ $s }}" @selected(($filters['status'] ?? '')===$s)>{{ ucfirst($s) }}</option>
          @endforeach
        </select>

        <select name="priority" class="h-10 rounded-xl border border-gray-200 bg-white px-3">
          <option value="">Any priority</option>
          @foreach($priorities as $p)
            <option value="{{ $p }}" @selected(($filters['priority'] ?? '')===$p)>{{ ucfirst($p) }}</option>
          @endforeach
        </select>

        @php $s = $filters['sort'] ?? 'created_at'; @endphp
        <select name="sort" class="h-10 rounded-xl border border-gray-200 bg-white px-3">
          <option value="created_at" @selected($s==='created_at')>Newest</option>
          <option value="number"     @selected($s==='number')>Ticket #</option>
          <option value="subject"    @selected($s==='subject')>Subject</option>
          <option value="priority"   @selected($s==='priority')>Priority</option>
          <option value="status"     @selected($s==='status')>Status</option>
        </select>

        @php $d = $filters['dir'] ?? 'desc'; @endphp
        <select name="dir" class="h-10 rounded-xl border border-gray-200 bg-white px-3">
          <option value="desc" @selected($d==='desc')>Desc</option>
          <option value="asc"  @selected($d==='asc')>Asc</option>
        </select>

        <button class="h-10 px-4 rounded-xl border bg-white hover:bg-gray-50">Filter</button>
        @if(request()->hasAny(['q','status','priority','sort','dir']))
          <a href="{{ route('tickets.index') }}" class="h-10 px-3 rounded-xl border hover:bg-gray-50">Reset</a>
        @endif
      </form>

      @can('tickets.create')
        <a href="{{ route('tickets.create') }}"
           class="h-10 p-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">Open Ticket</a>
      @endcan
    </div>
  </div>

  @if($tickets->count() === 0)
    <div class="bg-white rounded-2xl border shadow-sm p-10 grid place-items-center text-gray-500">
      <div class="text-center">
        <div class="mx-auto w-10 h-10 border-2 border-dashed border-gray-300 rounded-full"></div>
        <div class="mt-2 text-[13px]">No tickets found</div>
        @can('tickets.create')
          <div class="text-[12px]">Click “Open Ticket” to create one.</div>
        @endcan
      </div>
    </div>
  @else
    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full text-[14px]">
          <thead class="bg-gray-50 text-gray-600">
            <tr>
              <th class="px-4 py-3 text-left">#</th>
              <th class="px-4 py-3 text-left">Subject</th>
              <th class="px-4 py-3 text-left">Customer</th>
              <th class="px-4 py-3 text-left">Priority</th>
              <th class="px-4 py-3 text-left">Status</th>
              <th class="px-4 py-3 text-left">Opened</th>
              <th class="px-4 py-3"></th>
            </tr>
          </thead>
          <tbody class="divide-y">
            @foreach($tickets as $t)
              <tr class="hover:bg-gray-50/60">
                <td class="px-4 py-3 font-mono">{{ $t->number }}</td>
                <td class="px-4 py-3">
                  @can('tickets.view')
                    <a class="underline decoration-indigo-400 underline-offset-2" href="{{ route('tickets.show',$t) }}">
                      {{ $t->subject }}
                    </a>
                  @else
                    {{ $t->subject }}
                  @endcan
                  <div class="text-[12px] text-gray-500">by {{ optional($t->user)->name ?? 'User #'.$t->opened_by }}</div>
                </td>
                <td class="px-4 py-3">{{ optional($t->user)->name ?? '—' }}</td>
                <td class="px-4 py-3 capitalize">
                  <span class="px-2 py-0.5 text-[11px] rounded-full border
                    @switch($t->priority)
                      @case('urgent') bg-rose-50 text-rose-700 border-rose-200 @break
                      @case('high') bg-amber-50 text-amber-700 border-amber-200 @break
                      @case('normal') bg-emerald-50 text-emerald-700 border-emerald-200 @break
                      @default bg-gray-50 text-gray-700 border-gray-200
                    @endswitch
                  ">{{ ucfirst($t->priority) }}</span>
                </td>
                <td class="px-4 py-3 capitalize">
                  <span class="px-2 py-0.5 text-[11px] rounded-full border
                    @switch($t->status)
                      @case('open') bg-indigo-50 text-indigo-700 border-indigo-200 @break
                      @case('pending') bg-amber-50 text-amber-700 border-amber-200 @break
                      @case('resolved') bg-emerald-50 text-emerald-700 border-emerald-200 @break
                      @default bg-gray-50 text-gray-700 border-gray-200
                    @endswitch
                  ">{{ ucfirst($t->status) }}</span>
                </td>
                <td class="px-4 py-3">
                  <span class="text-[12px] text-gray-500">{{ $t->created_at?->diffForHumans() ?? '—' }}</span>
                </td>
                <td class="px-4 py-3">
                  <div class="flex justify-end gap-2">
                    @can('tickets.view')
                      <a href="{{ route('tickets.show',$t) }}" class="px-3 py-1.5 rounded-lg border bg-white hover:bg-gray-50">View</a>
                    @endcan
                    @can('tickets.update')
                      <a href="{{ route('tickets.edit',$t) }}" class="px-3 py-1.5 rounded-lg border bg-white hover:bg-gray-50">Edit</a>
                    @endcan
                    @can('tickets.delete')
                      <form method="POST" action="{{ route('tickets.destroy',$t) }}"
                            onsubmit="return confirm('Delete ticket {{ $t->number }}?');" class="inline">
                        @csrf @method('DELETE')
                        <button class="px-3 py-1.5 rounded-lg border bg-white text-rose-600 hover:bg-rose-50">Delete</button>
                      </form>
                    @endcan
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="px-4 py-3 border-t">
        {{ $tickets->withQueryString()->links() }}
      </div>
    </div>
  @endif
@endsection
