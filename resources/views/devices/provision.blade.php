@extends('layouts.app', ['title' => 'Provision '.$device->name])

@section('content')
  <div class="bg-white rounded-2xl border shadow-sm p-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-[18px] font-semibold">Provision “{{ $device->name }}”</h1>
        <p class="text-[12px] text-gray-500">Run this in WinBox terminal to fetch & import the router script.</p>
      </div>
      <a href="{{ route('devices.show',$device) }}" class="px-3 py-2 rounded-xl border bg-white hover:bg-gray-50">Back</a>
    </div>

    <div class="mt-4">
      <label class="text-[12px] text-gray-500">Command</label>
      <pre id="provCmd" class="mt-1 p-3 bg-gray-50 border rounded-xl overflow-x-auto text-[12px]">
/tool fetch mode=https url="{{ $url }}" dst-path=provision.rsc check-certificate=no; delay 2s; /import file-name=provision.rsc;
      </pre>
      <div class="mt-2 flex gap-2">
        <button class="px-4 py-2 rounded-xl border bg-white hover:bg-gray-50" onclick="copyProv()">Copy</button>
        <span id="copyOk" class="text-[12px] text-emerald-600 hidden">Copied ✓</span>
      </div>
    </div>
  </div>

  <script>
    function copyProv(){
      const el = document.getElementById('provCmd');
      const txt = el.innerText.trim();
      navigator.clipboard.writeText(txt).then(()=>{
        document.getElementById('copyOk').classList.remove('hidden');
        setTimeout(()=>document.getElementById('copyOk').classList.add('hidden'), 1500);
      });
    }
  </script>
@endsection
