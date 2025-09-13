@extends('layouts.app', ['title' => 'Leads'])

@php
  $cols = [
    ['key'=>'name',  'label'=>'Name'],
    ['key'=>'email', 'label'=>'Email'],
    ['key'=>'phone', 'label'=>'Phone'],
    ['key'=>'address','label'=>'Address'],
    ['key'=>'status','label'=>'Status'],
  ];
  function sortUrl($k){
    $dir = request('dir')==='asc' ? 'desc':'asc';
    return request()->fullUrlWithQuery(['sort'=>$k,'dir'=>$dir]);
  }
@endphp

@section('content')
  <div class="mb-4 flex flex-col lg:flex-row lg:items-center gap-3">
    <div>
      <h1 class="text-[18px] font-semibold">Leads</h1>
      <p class="text-[12px] text-gray-500">Do you have potential clients who might be interested in your products or services?</p>
    </div>
    <div class="lg:ml-auto flex gap-2">
      <form method="GET" class="hidden md:block">
        <div class="relative">
          <input name="q" value="{{ $q }}" placeholder="Search"
                 class="h-10 w-72 rounded-xl border border-gray-200 bg-white pl-9 pr-3" />
          <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path d="M12.9 14.32a8 8 0 111.414-1.414l4.387 4.387-1.414 1.414-4.387-4.387z"/></svg>
        </div>
      </form>

      @can('leads.create')
        <a href="{{ route('leads.create') }}"
           class="inline-flex items-center gap-2 px-3 h-10 rounded-xl bg-orange-500 text-white hover:bg-orange-600">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          Create a new lead
        </a>
      @endcan

      @can('leads.export')
        <a href="{{ route('leads.export', request()->query()) }}"
           class="inline-flex items-center gap-2 px-3 h-10 rounded-xl border bg-white hover:bg-gray-50">
          Export CSV
        </a>
      @endcan
    </div>
  </div>

  <div class="bg-white rounded-2xl border shadow-sm">
    {{-- Wrap the table in a form only if user can bulk delete --}}
    @canany(['leads.bulk_delete','leads.delete'])
      <form method="POST" action="{{ route('leads.bulk-destroy') }}">
        @csrf @method('DELETE')
    @endcanany

      <div class="overflow-x-auto">
        <table class="min-w-full">
          <thead class="text-left text-sm text-gray-600">
            <tr class="border-b">
              @canany(['leads.bulk_delete','leads.delete'])
                <th class="px-4 py-3 w-10">
                  <input type="checkbox" class="rounded"
                         onclick="document.querySelectorAll('[data-row]').forEach(c=>c.checked=this.checked)">
                </th>
              @endcanany

              @foreach($cols as $c)
                <th class="px-4 py-3">
                  <a href="{{ sortUrl($c['key']) }}" class="inline-flex items-center gap-1">
                    {{ $c['label'] }}
                    @if(request('sort')===$c['key'])
                      <svg class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor">
                        @if(request('dir')==='asc')
                          <path d="M3 12l7-8 7 8H3z"/>
                        @else
                          <path d="M3 8l7 8 7-8H3z"/>
                        @endif
                      </svg>
                    @endif
                  </a>
                </th>
              @endforeach

              @canany(['leads.view','leads.edit','leads.delete'])
                <th class="px-4 py-3 text-right">Actions</th>
              @endcanany
            </tr>
          </thead>

          <tbody class="text-sm">
            @forelse($leads as $lead)
              <tr class="border-b last:border-0 hover:bg-gray-50">
                @canany(['leads.bulk_delete','leads.delete'])
                  <td class="px-4 py-3">
                    <input type="checkbox" name="ids[]" value="{{ $lead->id }}" data-row class="rounded">
                  </td>
                @endcanany

                <td class="px-4 py-3">
                  @can('leads.view')
                    <a href="{{ route('leads.show',$lead) }}" class="font-medium text-gray-900 hover:underline">{{ $lead->name }}</a>
                  @else
                    <span class="font-medium text-gray-900">{{ $lead->name }}</span>
                  @endcan
                  @if($lead->company)
                    <div class="text-xs text-gray-500">{{ $lead->company }}</div>
                  @endif
                </td>

                <td class="px-4 py-3">{{ $lead->email ?? '—' }}</td>
                <td class="px-4 py-3">{{ $lead->phone ?? '—' }}</td>
                <td class="px-4 py-3">{{ $lead->address ? Str::limit($lead->address, 28) : '—' }}</td>

                <td class="px-4 py-3">
                  <span class="inline-flex px-2 py-0.5 rounded-full text-xs
                    @class([
                      'bg-gray-100 text-gray-700'   => $lead->status==='new',
                      'bg-blue-100 text-blue-700'   => $lead->status==='contacted',
                      'bg-amber-100 text-amber-700' => $lead->status==='qualified',
                      'bg-green-100 text-green-700' => $lead->status==='won',
                      'bg-rose-100 text-rose-700'   => $lead->status==='lost',
                    ])">
                    {{ ucfirst($lead->status) }}
                  </span>
                </td>

                @canany(['leads.view','leads.edit','leads.delete'])
                  <td class="px-4 py-3">
                    <div class="flex justify-end gap-2">
                      @can('leads.view')
                        <a href="{{ route('leads.show',$lead) }}"
                           class="px-3 py-1.5 rounded-lg border bg-white hover:bg-gray-50">View</a>
                      @endcan
                      @can('leads.edit')
                        <a href="{{ route('leads.edit',$lead) }}"
                           class="px-2 py-1 text-indigo-600 rounded-lg border bg-white">Edit</a>
                      @endcan
                      @can('leads.delete')
                        <form method="POST" action="{{ route('leads.destroy',$lead) }}"
                              onsubmit="return confirm('Delete this lead?')">
                          @csrf @method('DELETE')
                          <button class="px-2 py-1 text-rose-600 rounded-lg border bg-white">Delete</button>
                        </form>
                      @endcan
                    </div>
                  </td>
                @endcanany
              </tr>
            @empty
              <tr>
                <td colspan="{{ 1 + count($cols) + 1 }}" class="px-4 py-12">
                  <div class="flex flex-col items-center justify-center text-center text-gray-500">
                    <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mb-3">
                      <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                      </svg>
                    </div>
                    <div class="font-medium">No leads</div>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if($leads->count())
        <div class="flex items-center justify-between px-4 py-3">
          <div class="flex items-center gap-2">
            @canany(['leads.bulk_delete','leads.delete'])
              <button class="px-3 h-9 rounded-lg border bg-white hover:bg-gray-50"
                      onclick="return confirm('Delete selected leads?')">
                Delete selected
              </button>
            @endcanany
          </div>
          {{ $leads->links() }}
        </div>
      @endif

    @canany(['leads.bulk_delete','leads.delete'])
      </form>
    @endcanany
  </div>
@endsection
