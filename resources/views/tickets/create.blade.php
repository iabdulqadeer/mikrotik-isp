{{-- resources/views/tickets/create.blade.php --}}
@extends('layouts.app', ['title' => 'Open Ticket'])

@section('content')
  <div class="mb-4">
    <h1 class="text-[18px] font-semibold">Open Ticket</h1>
    <p class="text-[12px] text-gray-500">Create a new support request.</p>
  </div>

  <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data"
        class="bg-white rounded-2xl border shadow-sm p-4 space-y-4 max-w-3xl">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <div>
        <label class="block text-sm font-medium mb-1">Customer (optional)</label>
        <select name="user_id" class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3">
          <option value="">— None —</option>
          @foreach($users as $u)
            <option value="{{ $u->id }}" @selected(old('user_id')==$u->id)>{{ $u->name }} ({{ $u->email }})</option>
          @endforeach
        </select>
        <x-input-error :messages="$errors->get('user_id')" class="mt-1"/>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Priority</label>
        <select name="priority" class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3" required>
          @foreach (['low','normal','high','urgent'] as $p)
            <option value="{{ $p }}" @selected(old('priority','normal')==$p)>{{ ucfirst($p) }}</option>
          @endforeach
        </select>
        <x-input-error :messages="$errors->get('priority')" class="mt-1"/>
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Subject</label>
      <input name="subject" value="{{ old('subject') }}" class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3" required>
      <x-input-error :messages="$errors->get('subject')" class="mt-1"/>
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Message</label>
      <textarea name="body" rows="6" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2" required>{{ old('body') }}</textarea>
      <x-input-error :messages="$errors->get('body')" class="mt-1"/>
    </div>

    {{-- Drag & drop file input --}}
    <div x-data="fileDrop()" class="border-2 border-dashed rounded-xl p-4"
         :class="dragover ? 'border-indigo-400 bg-indigo-50/40' : 'border-gray-300 bg-white'">
      <label class="block text-sm font-medium mb-2">Attachments (optional)</label>
      <div class="text-center">
        <input type="file" name="attachments[]" id="attachments" class="hidden" multiple
               accept=".jpg,.jpeg,.png,.webp,.gif,.pdf,.txt,.log,.doc,.docx,.xls,.xlsx,.zip,.rar,.7z">
        <button type="button" @click="$refs.input.click()"
                class="px-3 py-2 rounded-lg border bg-white hover:bg-gray-50">Choose files</button>
        <p class="mt-2 text-[12px] text-gray-500">or drag & drop up to 10 files (max 5MB each)</p>
        <input x-ref="input" type="file" class="hidden" multiple
               name="attachments[]" @change="files=$event.target.files"
               accept=".jpg,.jpeg,.png,.webp,.gif,.pdf,.txt,.log,.doc,.docx,.xls,.xlsx,.zip,.rar,.7z">
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

      {{-- native drop zone --}}
      <div class="sr-only" @dragover.prevent="dragover=true" @dragleave.prevent="dragover=false"
           @drop.prevent="handleDrop($event)"></div>
      <x-input-error :messages="$errors->get('attachments')" class="mt-2"/>
      <x-input-error :messages="$errors->get('attachments.*')" class="mt-1"/>
    </div>

    <div class="flex justify-end gap-2">
      <a href="{{ route('tickets.index') }}" class="h-10 px-4 rounded-xl border bg-white hover:bg-gray-50">Cancel</a>
      <button class="h-10 px-4 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">Open Ticket</button>
    </div>
  </form>

  {{-- Alpine helper --}}
  <script>
    function fileDrop(){
      return {
        files: null,
        dragover: false,
        handleDrop(e){
          this.dragover = false;
          const dt = e.dataTransfer;
          if (!dt?.files?.length) return;
          // merge dropped with existing selected
          const list = new DataTransfer();
          if (this.files) { for (const f of this.files) list.items.add(f); }
          for (const f of dt.files) list.items.add(f);
          this.files = list.files;
          // sync to the hidden input
          this.$refs.input.files = this.files;
        }
      }
    }
  </script>
@endsection
