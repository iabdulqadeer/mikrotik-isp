<div class="mt-5"></div>
@if(session('ok'))
  <div class="mb-3 rounded-xl border bg-emerald-50 text-emerald-800 px-4 py-3">{{ session('ok') }}</div>
@endif
@if(session('err'))
  <div class="mb-3 rounded-xl border bg-rose-50 text-rose-700 px-4 py-3">{{ session('err') }}</div>
@endif

@if(session('error'))
  <div class="mb-3 rounded-xl border bg-rose-50 text-rose-700 px-4 py-3">{{ session('error') }}</div>
@endif

@if(session('status'))
  <div class="mb-3 rounded-xl border bg-emerald-50 text-emerald-800 px-4 py-3">{{ session('status') }}</div>
@endif

@if ($errors->any())
  <div class="mb-3 rounded-xl border bg-rose-50 text-rose-700 px-4 py-3">
    <div class="font-semibold mb-1">Please fix the following:</div>
    <ul class="list-disc ml-5">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif
