@extends('layouts.app', ['title' => 'Create New Voucher'])

@section('content')

  <div class="mb-4">
    <h1 class="text-[18px] font-semibold">Create Voucher</h1>
    <p class="text-[12px] text-gray-500">Generate one or multiple vouchers with duration, price, and validity.</p>
  </div>

  <div class="bg-white rounded-2xl border shadow-sm p-4">
    <form method="POST" action="{{ route('vouchers.store') }}">
      @include('vouchers._form', ['submitLabel' => 'Create'])
    </form>
  </div>
@endsection
