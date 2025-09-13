@extends('layouts.app', ['title' => 'Expenses'])

@section('content')
  <div class="mb-4 flex flex-col lg:flex-row lg:items-center gap-3">
    <div>
      <h1 class="text-[18px] font-semibold">Expenses</h1>
      <p class="text-[12px] text-gray-500">Track and manage your expenses.</p>
    </div>

    <div class="lg:ml-auto flex items-center gap-2">
      <form method="GET" class="flex items-center gap-2">
        <input name="q" value="{{ $filters['q'] ?? '' }}"
               class="h-10 w-64 rounded-xl border border-gray-200 bg-white px-3"
               placeholder="Search type / method / notes…">

        <select name="sort" class="h-10 rounded-xl border border-gray-200 bg-white px-3">
          @php $s = $filters['sort'] ?? 'spent_at'; @endphp
          <option value="spent_at" @selected($s==='spent_at')>Date</option>
          <option value="type" @selected($s==='type')>Type</option>
          <option value="amount" @selected($s==='amount')>Amount</option>
          <option value="payment_method" @selected($s==='payment_method')>Method</option>
          <option value="created_at" @selected($s==='created_at')>Created</option>
        </select>

        <select name="dir" class="h-10 rounded-xl border border-gray-200 bg-white px-3">
          @php $d = $filters['dir'] ?? 'desc'; @endphp
          <option value="desc" @selected($d==='desc')>Desc</option>
          <option value="asc"  @selected($d==='asc')>Asc</option>
        </select>

        <button class="h-10 px-4 rounded-xl border bg-white hover:bg-gray-50">Filter</button>
        @if(request()->hasAny(['q','sort','dir']))
          <a href="{{ route('expenses.index') }}" class="h-10 px-3 rounded-xl border hover:bg-gray-50">Reset</a>
        @endif
      </form>

      @can('expenses.create')
        <a href="{{ route('expenses.create') }}"
           class="h-10 p-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">Create Expense</a>
      @endcan
    </div>
  </div>

  {{-- Summary cards --}}
  <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
    <div class="rounded-2xl border shadow-sm p-4 bg-white">
      <div class="font-medium">Yearly Expenses</div>
      <div class="text-2xl font-bold mt-1">{{ number_format($yearly, 2) }}</div>
      <div class="text-[12px] text-gray-700">Total expenses this year</div>
    </div>

    <div class="rounded-2xl border shadow-sm p-4 bg-white">
      <div class="font-medium">Monthly Expenses</div>
      <div class="text-2xl font-bold mt-1">{{ number_format($monthly, 2) }}</div>
      <div class="text-[12px] text-gray-700">Total expenses this month</div>
    </div>

    <div class="rounded-2xl border shadow-sm p-4 bg-white">
      <div class="font-medium">Weekly Expenses</div>
      <div class="text-2xl font-bold mt-1">{{ number_format($weekly, 2) }}</div>
      <div class="text-[12px] text-gray-700">Total expenses this week</div>
    </div>
  </div>

  @if($expenses->count() === 0)
    <div class="bg-white rounded-2xl border shadow-sm p-10 grid place-items-center text-gray-500">
      <div class="text-center">
        <div class="mx-auto w-10 h-10 border-2 border-dashed border-gray-300 rounded-full"></div>
        <div class="mt-2 text-[13px]">No expenses</div>
        @can('expenses.create')
          <div class="text-[12px]">Click “Create Expense” to add one.</div>
        @endcan
      </div>
    </div>
  @else
    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full text-[14px]">
          <thead class="bg-gray-50 text-gray-600">
            <tr>
              <th class="px-4 py-3 text-left">Date</th>
              <th class="px-4 py-3 text-left">Type</th>
              <th class="px-4 py-3 text-left">Amount</th>
              <th class="px-4 py-3 text-left">Method</th>
              @canany(['expenses.view','expenses.update','expenses.delete'])
                <th class="px-4 py-3"></th>
              @endcanany
            </tr>
          </thead>
          <tbody class="divide-y">
            @foreach($expenses as $e)
              <tr class="hover:bg-gray-50/60">
                <td class="px-4 py-3">{{ $e->spent_at?->format('M d, Y H:i') }}</td>
                <td class="px-4 py-3">{{ $e->type }}</td>
                <td class="px-4 py-3">{{ number_format($e->amount, 2) }}</td>
                <td class="px-4 py-3">{{ $e->payment_method }}</td>
                @canany(['expenses.view','expenses.update','expenses.delete'])
                  <td class="px-4 py-3">
                    <div class="flex justify-end gap-2">
                      @can('expenses.view')
                        <a href="{{ route('expenses.show',$e) }}"
                           class="px-3 py-1.5 rounded-lg border bg-white hover:bg-gray-50">View</a>
                      @endcan

                      @can('expenses.update')
                        <a href="{{ route('expenses.edit',$e) }}"
                           class="px-3 py-1.5 rounded-lg border bg-white hover:bg-gray-50">Edit</a>
                      @endcan

                      @can('expenses.delete')
                        <form method="POST" action="{{ route('expenses.destroy',$e) }}"
                              onsubmit="return confirm('Delete this expense?');">
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
        {{ $expenses->withQueryString()->links() }}
      </div>
    </div>
  @endif
@endsection
