{{-- resources/views/equipment/index.blade.php --}}
@extends('layouts.app', ['title' => 'Equipment'])

@section('content')
  <div class="mb-4 flex flex-col lg:flex-row lg:items-center gap-3">
    <div>
      <h1 class="text-[18px] font-semibold">Equipment</h1>
      <p class="text-[12px] text-gray-500">All equipment including routers, switches, servers that you have rented to customers.</p>
    </div>

    <div class="lg:ml-auto flex gap-2">
      <form method="get" class="flex items-center gap-2">
        <input type="text" name="q" value="{{ $q }}" placeholder="Search"
               class="h-10 w-56 rounded-xl border border-gray-200 bg-white px-3" />
        <select name="type" class="h-10 rounded-xl border border-gray-200 bg-white px-3">
          <option value="">All Types</option>
          @foreach($types as $t)
            <option value="{{ $t }}" @selected($type===$t)>{{ $t }}</option>
          @endforeach
        </select>
        <input type="hidden" name="sort" value="{{ $sort }}">
        <input type="hidden" name="dir" value="{{ $dir }}">
        <button class="h-10 px-3 rounded-xl border bg-white hover:bg-gray-50">Filter</button>
      </form>

      @can('equipment.create')
        <a href="{{ route('equipment.create') }}"
           class="inline-flex items-center gap-2 h-10 px-3 rounded-xl bg-orange-500 text-white hover:bg-orange-600">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14"/></svg>
          Add Equipment
        </a>
      @endcan
    </div>
  </div>

  <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
          <tr>
            @php
              $dirFlip = $dir==='asc'?'desc':'asc';
              function sortUrl($col,$dirFlip){ return request()->fullUrlWithQuery(['sort'=>$col,'dir'=>$dirFlip]); }
            @endphp
            <th class="px-4 py-3 text-left"><a href="{{ sortUrl('user',$dirFlip) }}" class="flex items-center gap-1">User</a></th>
            <th class="px-4 py-3 text-left"><a href="{{ sortUrl('type',$dirFlip) }}">Type</a></th>
            <th class="px-4 py-3 text-left"><a href="{{ sortUrl('name',$dirFlip) }}">Equipment Name</a></th>
            <th class="px-4 py-3 text-left"><a href="{{ sortUrl('price',$dirFlip) }}">Equipment Price</a></th>
            <th class="px-4 py-3 text-left"><a href="{{ sortUrl('paid_amount',$dirFlip) }}">Paid Amount</a></th>
            @canany(['equipment.view','equipment.edit','equipment.delete'])
              <th class="px-4 py-3 text-right">Actions</th>
            @endcanany
          </tr>
        </thead>
        <tbody class="divide-y">
        @forelse($items as $e)
          <tr class="hover:bg-gray-50">
            <td class="px-4 py-3">
              @if($e->user)
                <div class="font-medium">{{ $e->user->name }}</div>
                <div class="text-xs text-gray-500">{{ $e->user->email }}</div>
              @else
                <span class="text-gray-400">—</span>
              @endif
            </td>
            <td class="px-4 py-3">{{ $e->type }}</td>
            <td class="px-4 py-3">
              <div class="font-medium">{{ $e->name }}</div>
              @if($e->serial_number)
                <div class="text-xs text-gray-500">S/N: {{ $e->serial_number }}</div>
              @endif
            </td>
            <td class="px-4 py-3">{{ $e->currency }} {{ number_format($e->price,2) }}</td>
            <td class="px-4 py-3">
              @if(!is_null($e->paid_amount))
                {{ $e->currency }} {{ number_format($e->paid_amount,2) }}
                @if($e->outstanding>0)
                  <span class="ml-1 text-xs text-amber-700 bg-amber-100 rounded px-1.5 py-0.5">Due: {{ number_format($e->outstanding,2) }}</span>
                @endif
              @else
                <span class="text-gray-400">—</span>
              @endif
            </td>

            @canany(['equipment.view','equipment.edit','equipment.delete'])
              <td class="px-4 py-3">
                <div class="flex justify-end gap-2">
                  @can('equipment.view')
                    <a href="{{ route('equipment.show',$e) }}"
                       class="px-3 py-1.5 rounded-lg border bg-white hover:bg-gray-50">View</a>
                  @endcan

                  @can('equipment.edit')
                    <a href="{{ route('equipment.edit',$e) }}"
                       class="px-2 py-1 rounded-lg border hover:bg-gray-50">Edit</a>
                  @endcan

                  @can('equipment.delete')
                    <form method="POST" action="{{ route('equipment.destroy',$e) }}"
                          onsubmit="return confirm('Delete this equipment?')">
                      @csrf @method('DELETE')
                      <button class="px-2 py-1 rounded-lg border text-red-600 hover:bg-red-50">Delete</button>
                    </form>
                  @endcan
                </div>
              </td>
            @endcanany
          </tr>
        @empty
          <tr>
            <td colspan="6" class="px-4 py-16 text-center text-gray-500">
              <div class="flex flex-col items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-300" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 7h18M5 7l.4 11.2a2 2 0 0 0 2 1.8h9.2a2 2 0 0 0 2-1.8L21 7M8 7V5a4 4 0 0 1 8 0v2"/></svg>
                <div class="font-medium">No equipment</div>
                <div class="text-sm">You have not added any equipment yet.</div>

                @can('equipment.create')
                  <a href="{{ route('equipment.create') }}"
                     class="mt-2 inline-flex items-center gap-2 rounded-xl bg-orange-500 text-white px-3 py-2 hover:bg-orange-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14"/></svg>
                    Add Equipment
                  </a>
                @endcan
              </div>
            </td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>

    <div class="p-4">{{ $items->links() }}</div>
  </div>
@endsection
