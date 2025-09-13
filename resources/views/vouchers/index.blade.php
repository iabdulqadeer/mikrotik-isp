{{-- resources/views/vouchers/index.blade.php --}}
@extends('layouts.app', ['title' => 'Vouchers'])

@section('content')

  {{-- Header / Filters / Actions --}}
  <div class="mb-4 flex flex-col lg:flex-row lg:items-center gap-3">
    <div>
      <h1 class="text-[18px] font-semibold">Vouchers</h1>
      <p class="text-[12px] text-gray-500">Manage access codes you sell or issue to users.</p>
    </div>

    <div class="lg:ml-auto flex items-center gap-2">
      <form method="GET" class="flex items-center gap-2">
        {{-- search --}}
        <input name="q" value="{{ $filters['q'] ?? '' }}"
               class="h-10 w-64 rounded-xl border border-gray-200 bg-white px-3"
               placeholder="Search code / plan / profile…">

        {{-- status --}}
        @php $status = $filters['status'] ?? ''; @endphp
        <select name="status" class="h-10 rounded-xl border border-gray-200 bg-white px-3">
          <option value="">Any status</option>
          @foreach(['active','used','expired','revoked'] as $s)
            <option value="{{ $s }}" @selected($status===$s)>{{ ucfirst($s) }}</option>
          @endforeach
        </select>

        {{-- device --}}
        <select name="device_id" class="h-10 rounded-xl border border-gray-200 bg-white px-3">
          <option value="">All devices</option>
          @foreach($devices as $d)
            <option value="{{ $d->id }}" @selected(($filters['device_id'] ?? '') == (string)$d->id)>{{ $d->name }}</option>
          @endforeach
        </select>

        <button class="h-10 px-4 rounded-xl border bg-white hover:bg-gray-50">Filter</button>

        @if(request()->hasAny(['q','status','device_id']))
          <a href="{{ route('vouchers.index') }}" class="h-10 px-3 rounded-xl border hover:bg-gray-50">Reset</a>
        @endif
      </form>

      @can('vouchers.create')
        <a href="{{ route('vouchers.create') }}"
           class="h-10 p-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">Add Voucher</a>
      @endcan
    </div>
  </div>

  @if($vouchers->count() === 0)
    {{-- Empty state (aligned with Plans) --}}
    <div class="bg-white rounded-2xl border shadow-sm p-10 grid place-items-center text-gray-500">
      <div class="text-center">
        <div class="mx-auto w-10 h-10 border-2 border-dashed border-gray-300 rounded-full"></div>
        <div class="mt-2 text-[13px]">No vouchers yet</div>
        <div class="text-[12px]">Click “Add Voucher” to create one.</div>
      </div>
    </div>
  @else
    @php
      $canExport = auth()->user()?->can('vouchers.export');
      $canRevoke = auth()->user()?->can('vouchers.revoke');
      $canDelete = auth()->user()?->can('vouchers.delete');
      $canPrint  = auth()->user()?->can('vouchers.print');
      $canBulk   = $canExport || $canRevoke || $canDelete || $canPrint;
    @endphp

    {{-- Bulk actions (only if user has any bulk permission) --}}
    @if($canBulk)
      <form method="POST" action="{{ route('vouchers.bulk') }}" class="mb-3">
        @csrf
        <div class="flex flex-col sm:flex-row sm:items-center gap-2">
          <div class="flex items-center gap-2">
            <select name="action" class="h-10 rounded-xl border border-gray-200 bg-white px-3 text-sm">
              @if($canExport)<option value="export">Export CSV</option>@endif
              @if($canRevoke)<option value="revoke">Revoke</option>@endif
              @if($canDelete)<option value="delete">Delete</option>@endif
            </select>
            <button class="h-10 px-4 rounded-xl border bg-white hover:bg-gray-50 text-sm">Run</button>
          </div>

          {{-- Print Sheet --}}
          @if($canPrint)
            <button formaction="{{ route('vouchers.print') }}" formmethod="POST"
                    class="h-10 px-4 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 text-sm">
              @csrf
              Print Sheet
            </button>
          @endif
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl border shadow-sm overflow-hidden mt-3">
          <div class="overflow-x-auto">
            <table class="min-w-full text-[14px]">
              <thead class="bg-gray-50 text-gray-600">
              <tr>
                {{-- bulk select column --}}
                <th class="px-4 py-3">
                  <input type="checkbox"
                         onclick="document.querySelectorAll('.rowchk').forEach(cb=>cb.checked=this.checked)">
                </th>
                <th class="px-4 py-3 text-left">Code</th>
                <th class="px-4 py-3 text-left">Plan</th>
                <th class="px-4 py-3 text-left">Profile</th>
                <th class="px-4 py-3 text-left">Duration</th>
                <th class="px-4 py-3 text-left">Price</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-left">Device</th>
                <th class="px-4 py-3 text-left">Created</th>
                <th class="px-4 py-3"></th>
              </tr>
              </thead>
              <tbody class="divide-y">
              @foreach($vouchers as $v)
                <tr class="hover:bg-gray-50/60">
                  <td class="px-4 py-3">
                    <input class="rowchk" type="checkbox" name="ids[]" value="{{ $v->id }}">
                  </td>
                  <td class="px-4 py-3 font-mono">
                    @can('vouchers.view')
                      <a class="underline decoration-indigo-400 underline-offset-2" href="{{ route('vouchers.show',$v) }}">
                        {{ $v->code }}
                      </a>
                    @else
                      {{ $v->code }}
                    @endcan
                  </td>
                  <td class="px-4 py-3">{{ $v->plan }}</td>
                  <td class="px-4 py-3">{{ $v->profile }}</td>
                  <td class="px-4 py-3">{{ (int)$v->duration_minutes }} min</td>
                  <td class="px-4 py-3">₨ {{ number_format((float)$v->price, 2) }}</td>
                  <td class="px-4 py-3">
                    @php
                      $badge = match($v->status){
                        'active'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'used'    => 'bg-amber-50 text-amber-700 border-amber-200',
                        'expired' => 'bg-gray-50 text-gray-600 border-gray-200',
                        'revoked' => 'bg-rose-50 text-rose-600 border-rose-200',
                        default   => 'bg-gray-50 text-gray-600 border-gray-200',
                      };
                    @endphp
                    <span class="px-2 py-0.5 text-[11px] rounded-full border {{ $badge }}">
                      {{ ucfirst($v->status) }}
                    </span>
                  </td>
                  <td class="px-4 py-3">{{ $v->device?->name ?? '—' }}</td>
                  <td class="px-4 py-3">
                    <span class="text-[12px] text-gray-500">{{ $v->created_at?->diffForHumans() ?? '—' }}</span>
                  </td>
                  <td class="px-4 py-3">
                    <div class="flex justify-end gap-2">
                      @can('vouchers.view')
                        <a href="{{ route('vouchers.show',$v) }}" class="px-3 py-1.5 rounded-lg border bg-white hover:bg-gray-50">View</a>
                      @endcan
                      @can('vouchers.update')
                        <a href="{{ route('vouchers.edit',$v) }}" class="px-3 py-1.5 rounded-lg border bg-white hover:bg-gray-50">Edit</a>
                      @endcan
                      @can('vouchers.delete')
                        <form method="POST" action="{{ route('vouchers.destroy',$v) }}"
                              onsubmit="return confirm('Delete voucher “{{ $v->code }}”?');">
                          @csrf @method('DELETE')
                          <button class="px-3 py-1.5 rounded-lg border bg-white text-rose-600 hover:bg-rose-50">
                            Delete
                          </button>
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
            {{ $vouchers->withQueryString()->links() }}
          </div>
        </div>
      </form>
    @else
      {{-- No bulk rights: show READ-ONLY table (no checkbox column, still honor per-row view/edit/delete) --}}
      <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full text-[14px]">
            <thead class="bg-gray-50 text-gray-600">
            <tr>
              <th class="px-4 py-3 text-left">Code</th>
              <th class="px-4 py-3 text-left">Plan</th>
              <th class="px-4 py-3 text-left">Profile</th>
              <th class="px-4 py-3 text-left">Duration</th>
              <th class="px-4 py-3 text-left">Price</th>
              <th class="px-4 py-3 text-left">Status</th>
              <th class="px-4 py-3 text-left">Device</th>
              <th class="px-4 py-3 text-left">Created</th>
              <th class="px-4 py-3"></th>
            </tr>
            </thead>
            <tbody class="divide-y">
            @foreach($vouchers as $v)
              <tr class="hover:bg-gray-50/60">
                <td class="px-4 py-3 font-mono">
                  @can('vouchers.view')
                    <a class="underline decoration-indigo-400 underline-offset-2" href="{{ route('vouchers.show',$v) }}">
                      {{ $v->code }}
                    </a>
                  @else
                    {{ $v->code }}
                  @endcan
                </td>
                <td class="px-4 py-3">{{ $v->plan }}</td>
                <td class="px-4 py-3">{{ $v->profile }}</td>
                <td class="px-4 py-3">{{ (int)$v->duration_minutes }} min</td>
                <td class="px-4 py-3">₨ {{ number_format((float)$v->price, 2) }}</td>
                <td class="px-4 py-3">
                  @php
                    $badge = match($v->status){
                      'active'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                      'used'    => 'bg-amber-50 text-amber-700 border-amber-200',
                      'expired' => 'bg-gray-50 text-gray-600 border-gray-200',
                      'revoked' => 'bg-rose-50 text-rose-600 border-rose-200',
                      default   => 'bg-gray-50 text-gray-600 border-gray-200',
                    };
                  @endphp
                  <span class="px-2 py-0.5 text-[11px] rounded-full border {{ $badge }}">
                    {{ ucfirst($v->status) }}
                  </span>
                </td>
                <td class="px-4 py-3">{{ $v->device?->name ?? '—' }}</td>
                <td class="px-4 py-3">
                  <span class="text-[12px] text-gray-500">{{ $v->created_at?->diffForHumans() ?? '—' }}</span>
                </td>
                <td class="px-4 py-3">
                  <div class="flex justify-end gap-2">
                    @can('vouchers.view')
                      <a href="{{ route('vouchers.show',$v) }}" class="px-3 py-1.5 rounded-lg border bg-white hover:bg-gray-50">View</a>
                    @endcan
                    @can('vouchers.update')
                      <a href="{{ route('vouchers.edit',$v) }}" class="px-3 py-1.5 rounded-lg border bg-white hover:bg-gray-50">Edit</a>
                    @endcan
                    @can('vouchers.delete')
                      <form method="POST" action="{{ route('vouchers.destroy',$v) }}"
                            onsubmit="return confirm('Delete voucher “{{ $v->code }}”?');">
                        @csrf @method('DELETE')
                        <button class="px-3 py-1.5 rounded-lg border bg-white text-rose-600 hover:bg-rose-50">
                          Delete
                        </button>
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
          {{ $vouchers->withQueryString()->links() }}
        </div>
      </div>
    @endif
  @endif
@endsection
