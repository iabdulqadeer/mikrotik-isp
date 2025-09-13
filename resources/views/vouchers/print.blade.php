{{-- resources/views/vouchers/print.blade.php --}}
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Voucher Sheet</title>
  <style>
    *{font-family: ui-sans-serif,system-ui,Segoe UI,Roboto,Helvetica,Arial}
    .grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}
    .card{border:1px solid #ddd;border-radius:8px;padding:12px}
    .code{font-family: ui-monospace,Consolas,Monaco,monospace;font-size:18px;letter-spacing:2px}
    @media print {.no-print{display:none}}
  </style>
</head>
<body>
  <div class="no-print" style="margin:10px 0">
    <button onclick="window.print()">Print</button>
  </div>
  <div class="grid">
    @foreach($vouchers as $v)
      <div class="card">
        <div class="code">{{ $v->code }}</div>
        <div>Plan: {{ $v->plan ?: '—' }}</div>
        <div>Duration: {{ $v->duration_minutes }} min</div>
        @if($v->valid_until)<div>Valid till: {{ $v->valid_until->format('Y-m-d H:i') }}</div>@endif
        @if($v->price>0)<div>Price: {{ number_format($v->price,2) }}</div>@endif
        <div style="font-size:12px;color:#666;margin-top:6px;">Powered by {{ config('app.name') }}</div>
      </div>
    @endforeach
  </div>
</body>
</html>
