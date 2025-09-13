@php $c = $campaign ?? null; @endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-3">
  <div class="md:col-span-2">
    <label class="block text-sm font-medium mb-1">Name*</label>
    <input name="name" value="{{ old('name', $c->name ?? '') }}" class="h-10 w-full rounded-xl border border-gray-200 px-3" required>
    @error('name')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Type*</label>
    <select name="type" x-model="type" class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3" required>
      <option value="banner">Banner</option>
      <option value="image">Image</option>
    </select>
    @error('type')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
    <p class="text-[12px] text-gray-500 mt-1" x-show="type==='banner'">Shown at the top of the packages page.</p>
    <p class="text-[12px] text-gray-500 mt-1" x-show="type==='image'">Shown at the bottom of the packages page.</p>
  </div>

  <template x-if="type==='image'">
    <div>
      <label class="block text-sm font-medium mb-1">Image Size*</label>
      <select name="image_size" class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3">
        <option value="">Select an option</option>
        <option value="full" @selected(old('image_size',$c->image_size ?? '')==='full')>Full width</option>
        <option value="wide" @selected(old('image_size',$c->image_size ?? '')==='wide')>Wide banner</option>
        <option value="square" @selected(old('image_size',$c->image_size ?? '')==='square')>Square</option>
      </select>
      @error('image_size')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
      <p class="text-[12px] text-gray-500 mt-1">Pick the layout that fits your page section.</p>
    </div>
  </template>
</div>

<div x-show="type==='banner'">
  <label class="block text-sm font-medium mb-1">Banner Text*</label>
  <input name="banner_text" value="{{ old('banner_text', $c->banner_text ?? '') }}" class="h-10 w-full rounded-xl border border-gray-200 px-3">
  @error('banner_text')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
</div>

<div x-show="type==='image'" x-data="{
      dragOver: false,
      fileName: '',
      preview: '',
      handleFile(e) {
        const file = e.target.files[0];
        if (!file) return;
        this.fileName = file.name;
        this.preview = URL.createObjectURL(file);
      },
      dropFile(e) {
        e.preventDefault();
        this.dragOver = false;
        const file = e.dataTransfer.files[0];
        if (!file) return;
        this.$refs.input.files = e.dataTransfer.files;
        this.handleFile({ target: this.$refs.input });
      }
    }"
    @dragover.prevent="dragOver = true"
    @dragleave.prevent="dragOver = false"
    @drop="dropFile($event)"
>
  <label class="block text-sm font-medium mb-1">Image*</label>

  <div class="rounded-xl border-2 border-dashed p-6 bg-gray-50 text-center cursor-pointer transition"
       :class="dragOver ? 'border-indigo-400 bg-indigo-50' : 'border-gray-300'"
       @click="$refs.input.click()"
  >
    <input type="file" name="image" accept="image/*"
           class="hidden" x-ref="input"
           @change="handleFile($event)">

    <template x-if="!preview">
      <div class="text-sm text-gray-500">
        <span class="block">Drag & drop an image here</span>
        <span class="block text-xs text-gray-400">or click to select</span>
      </div>
    </template>

    <template x-if="preview">
      <div class="space-y-2">
        <img :src="preview" alt="Preview" class="mx-auto max-h-48 rounded-lg shadow-sm">
        <div class="text-xs text-gray-600" x-text="fileName"></div>
      </div>
    </template>
  </div>

  @if($c?->image_path && !$errors->has('image'))
    <div class="mt-2 text-xs text-gray-500">
      Current: <a href="{{ $c->imageUrl() }}" target="_blank" class="text-indigo-600 underline">view</a>
    </div>
  @endif

  @error('image')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
</div>


<div class="grid grid-cols-1 md:grid-cols-2 gap-3">
  <div>
    <label class="block text-sm font-medium mb-1">Start Date*</label>
    <input name="start_date" type="date" value="{{ old('start_date', optional($c?->start_date)->format('Y-m-d')) }}" class="h-10 w-full rounded-xl border border-gray-200 px-3" required>
    @error('start_date')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
  </div>
  <div>
    <label class="block text-sm font-medium mb-1">End Date</label>
    <input name="end_date" type="date" value="{{ old('end_date', optional($c?->end_date)->format('Y-m-d')) }}" class="h-10 w-full rounded-xl border border-gray-200 px-3">
    @error('end_date')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
  </div>
</div>

<div class="flex items-center justify-end gap-2 pt-2">
  <a href="{{ route('campaigns.index') }}" class="px-3 py-2 rounded-xl border border-gray-200 text-gray-700">Cancel</a>
  <button class="px-4 py-2 rounded-xl bg-orange-500 text-white hover:bg-orange-600">{{ $submitLabel }}</button>
</div>
