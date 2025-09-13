{{-- resources/views/tickets/show.blade.php --}}
@extends('layouts.app', ['title' => 'Ticket '.$ticket->number])

@section('content')

  <div class="mb-4 flex flex-col lg:flex-row lg:items-center gap-3">
    <div>
      <h1 class="text-[18px] font-semibold">{{ $ticket->subject }}</h1>
      <p class="text-[12px] text-gray-500">
        <span class="font-mono">{{ $ticket->number }}</span> •
        Priority: <span class="capitalize">{{ $ticket->priority }}</span> •
        Status: <span class="capitalize">{{ $ticket->status }}</span>
      </p>
    </div>

    <div class="lg:ml-auto text-[12px] text-gray-500">
      Customer: {{ optional($ticket->customer)->name ?? '—' }} •
      Opened: {{ $ticket->created_at->format('Y-m-d H:i') }} by {{ optional($ticket->user)->name ?? 'User #'.$ticket->opened_by }}
    </div>
  </div>

  {{-- conversation --}}
  <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
    <div class="border-b px-4 py-3 font-medium">Conversation</div>

   <div class="divide-y">
  @forelse($ticket->messages as $m)
    <div class="px-4 py-3">
      <div class="flex items-center justify-between">
        <div class="text-[13px] text-gray-600">
          <span class="font-semibold">{{ $m->user->name }}</span>
          <span class="text-gray-400">• {{ $m->created_at->diffForHumans() }}</span>
        </div>
        ...
      </div>

      {{-- message body --}}
      <div class="mt-2 whitespace-pre-wrap text-[14px]">{{ $m->body }}</div>

      {{-- attachments (add this block here) --}}
      @foreach(($m->attachments ?? []) as $path)
        <div class="mt-2">
          <a href="{{ asset('storage/'.$path) }}" target="_blank"
             class="inline-flex items-center gap-1 text-[12px] text-indigo-700 underline decoration-indigo-300 underline-offset-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828L18 9.828M8 7h8m-8 0a4 4 0 015.657-5.657l2.121 2.121A4 4 0 0116 7" />
            </svg>
            {{ basename($path) }}
          </a>
        </div>
      @endforeach
    </div>
  @empty
    <div class="px-4 py-6 text-center text-gray-500 text-[13px]">No messages yet.</div>
  @endforelse
</div>


   {{-- in resources/views/tickets/show.blade.php, replace reply form --}}
@can('tickets.update')
  <div class="border-t px-4 py-3">
    <form method="POST" action="{{ route('tickets.messages.store', $ticket) }}" enctype="multipart/form-data"
          class="space-y-3" x-data="fileDrop()">
      @csrf
      <textarea name="body" rows="4" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2"
                placeholder="Write a reply..." required>{{ old('body') }}</textarea>

      <div class="border-2 border-dashed rounded-xl p-4"
           :class="dragover ? 'border-indigo-400 bg-indigo-50/40' : 'border-gray-300 bg-white'">
        <label class="block text-sm font-medium mb-2">Attachments</label>
        <div class="text-center">
          <input x-ref="input" type="file" class="hidden" multiple
                 name="attachments[]" @change="files=$event.target.files"
                 accept=".jpg,.jpeg,.png,.webp,.gif,.pdf,.txt,.log,.doc,.docx,.xls,.xlsx,.zip,.rar,.7z">
          <button type="button" @click="$refs.input.click()"
                  class="px-3 py-2 rounded-lg border bg-white hover:bg-gray-50">Choose files</button>
          <p class="mt-2 text-[12px] text-gray-500">or drag & drop up to 10 files (max 5MB each)</p>
        </div>
        <div class="mt-3 space-y-1" x-show="files && files.length">
          <template x-for="f in files" :key="f.name+f.size">
            <div class="text-[13px] text-gray-700 flex items-center gap-2">
              <span class="inline-block w-2 h-2 rounded-full bg-indigo-500"></span>
              <span x-text="f.name"></span>
              <span class="text-gray-400" x-text="`(${(f.size/1024/1024).toFixed(2)} MB)`"></span>
            </div>
          </template>
        </div>

        <div class="sr-only" @dragover.prevent="dragover=true" @dragleave.prevent="dragover=false"
             @drop.prevent="handleDrop($event)"></div>
        <x-input-error :messages="$errors->get('attachments')" class="mt-2"/>
        <x-input-error :messages="$errors->get('attachments.*')" class="mt-1"/>
      </div>

      <div class="flex justify-end">
        <button class="h-10 px-4 rounded-xl bg-gray-800 text-white hover:bg-gray-900">Send</button>
      </div>
    </form>
  </div>
@endcan

{{-- Alpine helper (once on page is fine; if already included in create, you can omit here) --}}
<script>
  function fileDrop(){
    return {
      files: null,
      dragover: false,
      handleDrop(e){
        this.dragover = false;
        const dt = e.dataTransfer;
        if (!dt?.files?.length) return;
        const list = new DataTransfer();
        if (this.files) { for (const f of this.files) list.items.add(f); }
        for (const f of dt.files) list.items.add(f);
        this.files = list.files;
        this.$refs.input.files = this.files;
      }
    }
  }
</script>

  </div>

  {{-- quick meta update --}}
  @can('tickets.update')
    <div class="mt-4">
      <form method="POST" action="{{ route('tickets.update', $ticket) }}" class="bg-white rounded-2xl border shadow-sm p-4 grid grid-cols-1 md:grid-cols-4 gap-3">
        @csrf @method('PUT')
        <div>
          <label class="block text-sm font-medium mb-1">Status</label>
          <select name="status" class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3">
            @foreach(['open','pending','resolved','closed'] as $s)
              <option value="{{ $s }}" @selected($ticket->status===$s)>{{ ucfirst($s) }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Priority</label>
          <select name="priority" class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3">
            @foreach(['low','normal','high','urgent'] as $p)
              <option value="{{ $p }}" @selected($ticket->priority===$p)>{{ ucfirst($p) }}</option>
            @endforeach
          </select>
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium mb-1">Subject</label>
          <input name="subject" value="{{ old('subject', $ticket->subject) }}" class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3">
        </div>
        <input type="hidden" name="customer_id" value="{{ $ticket->customer_id }}">
        <div class="md:col-span-4 flex justify-end">
          <button class="h-10 px-4 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">Update</button>
        </div>
      </form>
    </div>
  @endcan
@endsection
