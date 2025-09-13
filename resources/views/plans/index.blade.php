@extends('layouts.app', ['title' => 'Plans'])

@section('content')

  @php
    use App\Models\Campaign;

    $banner  = Campaign::running()->banner()->latest()->first();
    $imageAd = Campaign::running()->image()->latest()->first();
  @endphp

  {{-- ===== Top Banner Ad ===== --}}
  @if($banner)
    @php $banner->incrementViews(); @endphp
    <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-amber-900">
      {{ $banner->banner_text }}
    </div>
  @endif

  {{-- ===== Page Header + Filters ===== --}}
  <div class="mb-4 flex flex-col lg:flex-row lg:items-center gap-3">
    <div>
      <h1 class="text-[18px] font-semibold">Plans</h1>
      <p class="text-[12px] text-gray-500">Manage bandwidth/price plans and their billing cycles.</p>
    </div>

    <div class="lg:ml-auto flex items-center gap-2">
      <form method="GET" class="flex items-center gap-2">
        <input name="q" value="{{ $filters['q'] ?? '' }}"
               class="h-10 w-64 rounded-xl border border-gray-200 bg-white px-3"
               placeholder="Search name / price…">

        <select name="billing_cycle" class="h-10 rounded-xl border border-gray-200 bg-white px-3">
          <option value="">All cycles</option>
          @foreach($cycles as $c)
            <option value="{{ $c }}" @selected(($filters['cycle']??'')===$c)>{{ ucfirst($c) }}</option>
          @endforeach
        </select>

        <select name="active" class="h-10 rounded-xl border border-gray-200 bg-white px-3">
          <option value="">Any status</option>
          <option value="1" @selected(($filters['active']??'')==='1')>Active</option>
          <option value="0" @selected(($filters['active']??'')==='0')>Inactive</option>
        </select>

        <select name="sort" class="h-10 rounded-xl border border-gray-200 bg-white px-3">
          @php $s = $filters['sort'] ?? 'created_at'; @endphp
          <option value="created_at" @selected($s==='created_at')>Newest</option>
          <option value="name" @selected($s==='name')>Name</option>
          <option value="price" @selected($s==='price')>Price</option>
        </select>

        <select name="dir" class="h-10 rounded-xl border border-gray-200 bg-white px-3">
          @php $d = $filters['dir'] ?? 'desc'; @endphp
          <option value="desc" @selected($d==='desc')>Desc</option>
          <option value="asc"  @selected($d==='asc')>Asc</option>
        </select>

        <button class="h-10 px-4 rounded-xl border bg-white hover:bg-gray-50">Filter</button>
        @if(request()->hasAny(['q','billing_cycle','active','sort','dir']))
          <a href="{{ route('plans.index') }}" class="h-10 px-3 rounded-xl border hover:bg-gray-50">Reset</a>
        @endif
      </form>

      @can('plans.create')
        <a href="{{ route('plans.create') }}"
           class="h-10 p-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">Add Plan</a>
      @endcan
    </div>
  </div>

  {{-- ===== Plans Table / Empty State ===== --}}
  @if($plans->count() === 0)
    <div class="bg-white rounded-2xl border shadow-sm p-10 grid place-items-center text-gray-500">
      <div class="text-center">
        <div class="mx-auto w-10 h-10 border-2 border-dashed border-gray-300 rounded-full"></div>
        <div class="mt-2 text-[13px]">No plans yet</div>
        @can('plans.create')
          <div class="text-[12px]">Click “Add Plan” to create one.</div>
        @endcan
      </div>
    </div>
  @else
    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full text-[14px]">
          <thead class="bg-gray-50 text-gray-600">
            <tr>
              <th class="px-4 py-3 text-left">Name</th>
              <th class="px-4 py-3 text-left">Cycle</th>
              <th class="px-4 py-3 text-left">Speed</th>
              <th class="px-4 py-3 text-left">Price</th>
              <th class="px-4 py-3 text-left">Active</th>
              <th class="px-4 py-3 text-left">Created</th>
              @canany(['plans.view','plans.update','plans.delete'])
                <th class="px-4 py-3"></th>
              @endcanany
            </tr>
          </thead>
          <tbody class="divide-y">
            @foreach($plans as $p)
              <tr class="hover:bg-gray-50/60">
                <td class="px-4 py-3 font-medium">
                  @can('plans.view')
                    <a class="underline decoration-indigo-400 underline-offset-2" href="{{ route('plans.show',$p) }}">
                      {{ $p->name }}
                    </a>
                  @else
                    {{ $p->name }}
                  @endcan
                </td>
                <td class="px-4 py-3 capitalize">{{ $p->billing_cycle }}</td>
                <td class="px-4 py-3">{{ $p->speed_label }}</td>
                <td class="px-4 py-3">₨ {{ number_format((float)$p->price, 2) }}</td>
                <td class="px-4 py-3">
                  <span class="px-2 py-0.5 text-[11px] rounded-full border {{ $p->active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-gray-50 text-gray-600 border-gray-200' }}">
                    {{ $p->active ? 'Yes' : 'No' }}
                  </span>
                </td>
                <td class="px-4 py-3">
                  <span class="text-[12px] text-gray-500">{{ $p->created_at?->diffForHumans() ?? '—' }}</span>
                </td>
                @canany(['plans.view','plans.update','plans.delete'])
                  <td class="px-4 py-3">
                    <div class="flex justify-end gap-2">
                      @can('plans.view')
                        <a href="{{ route('plans.show',$p) }}" class="px-3 py-1.5 rounded-lg border bg-white hover:bg-gray-50">View</a>
                      @endcan
                      @can('plans.update')
                        <a href="{{ route('plans.edit',$p) }}" class="px-3 py-1.5 rounded-lg border bg-white hover:bg-gray-50">Edit</a>
                      @endcan
                      @can('plans.delete')
                        <form method="POST" action="{{ route('plans.destroy',$p) }}"
                              onsubmit="return confirm('Delete plan “{{ $p->name }}”?');">
                          @csrf @method('DELETE')
                          <button class="px-3 py-1.5 rounded-lg border bg-white text-rose-600 hover:bg-rose-50">
                            Delete
                          </button>
                        </form>
                      @endcan
                    </div>
                  </td>
                @endcanany
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="px-4 py-3 border-t">
        {{ $plans->withQueryString()->links() }}
      </div>
    </div>
  @endif

  {{-- ===== Bottom Image Ad ===== --}}
  @if($imageAd)
    @php $imageAd->incrementViews(); @endphp
    <div class="mt-6 flex justify-center">
      <img
        src="{{ $imageAd->imageUrl() }}"
        alt="Ad"
        class="@if($imageAd->image_size==='full') w-full @elseif($imageAd->image_size==='wide') max-w-3xl @else w-64 @endif rounded-xl border"
        loading="lazy">
    </div>
  @endif

@endsection
