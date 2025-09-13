{{-- resources/views/layouts/partials/theme-toggle.blade.php --}}
<div class="inline-flex items-center gap-3">
  <div
    class="flex items-center gap-4 rounded-2xl border border-gray-200 bg-white px-3 py-2 shadow-sm dark:border-gray-800 dark:bg-gray-900"
    id="theme-toggle"
  >
    {{-- Light --}}
    <button type="button" data-theme-choice="light"
      class="group rounded-xl p-2 data-[active=true]:bg-gray-100 dark:data-[active=true]:bg-gray-800"
      title="Light">
      <svg viewBox="0 0 24 24" class="h-6 w-6">
        <path d="M12 4V2M12 22v-2M4 12H2M22 12h-2M5.64 5.64 4.22 4.22M19.78 19.78l-1.42-1.42M5.64 18.36 4.22 19.78M19.78 4.22l-1.42 1.42" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/>
        <circle cx="12" cy="12" r="4" fill="currentColor"/>
      </svg>
    </button>

    {{-- Dark --}}
    <button type="button" data-theme-choice="dark"
      class="group rounded-xl p-2 data-[active=true]:bg-gray-100 dark:data-[active=true]:bg-gray-800"
      title="Dark">
      <svg viewBox="0 0 24 24" class="h-6 w-6">
        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z" fill="currentColor"/>
      </svg>
    </button>

    {{-- System --}}
    <button type="button" data-theme-choice="system"
      class="group rounded-xl p-2 data-[active=true]:bg-gray-100 dark:data-[active=true]:bg-gray-800"
      title="System">
      <svg viewBox="0 0 24 24" class="h-6 w-6">
        <rect x="3" y="4" width="18" height="14" rx="2" ry="2" fill="none" stroke="currentColor" stroke-width="1.5"/>
        <rect x="10" y="19" width="4" height="1.5" rx="0.75" fill="currentColor"/>
      </svg>
    </button>
  </div>
</div>
