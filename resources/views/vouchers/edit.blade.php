{{-- resources/views/vouchers/edit.blade.php --}}
@extends('layouts.app', ['title' => 'Edit Voucher'])

@section('content')

  <div class="mb-4">
    <h1 class="text-[18px] font-semibold">Edit Voucher</h1>
    <p class="text-[12px] text-gray-500">Update voucher details, validity, and status.</p>
  </div>

  <div class="bg-white rounded-2xl border shadow-sm p-4">
    <form method="POST" action="{{ route('vouchers.update',$voucher) }}">
      @csrf
      @method('PUT')

      {{-- Reuse the same form partial, but pass the voucher model and a custom button label --}}
      @include('vouchers._form', ['submitLabel' => 'Save changes', 'voucher' => $voucher])
    </form>
  </div>
@endsection
