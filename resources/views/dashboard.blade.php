{{-- resources/views/dashboard.blade.php --}}
@php
  $u = auth()->user();
  $C = $currency ?? ($u?->display_currency ?? config('currency.default.code','USD'));
@endphp

@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')
  {{-- Greeting (timezone-aware) --}}
  <div class="mb-6">
    <div class="text-[15px] font-medium">
      {{ $greeting }}, {{ $u->name ?? 'there' }}! 👋
    </div>
    <div class="text-[13px] text-gray-500">
      {{ \Illuminate\Support\Carbon::now($tz)->format('D, M j · g:i A') }} ({{ $tz }})
    </div>
  </div>
  
  {{-- ====== Your existing Trial/Subscription notices stay as-is ====== --}}
  @php $u = auth()->user(); @endphp
  @if ($u->isOnTrial())
    <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3">
      <div class="flex items-center justify-between">
        <div>
          <div class="font-semibold text-amber-900">Free Trial Active</div>
          <div class="text-sm text-amber-800">
            Ends on <strong>{{ $u->trial_ends_at->timezone($tz)->format('M d, Y H:i') }}</strong>
            ({{ max($u->trialDaysLeft(), 0) }} day{{ $u->trialDaysLeft() === 1 ? '' : 's' }} left).
          </div>
        </div>
        <a href="{{ route('subscriptions.index') }}"
           class="inline-flex px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-sm hover:bg-indigo-700">
          Upgrade now
        </a>
      </div>
    </div>
  @elseif (!$u->hasActiveSubscription() && $u->hasExpiredTrial())
    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3">
      <div class="flex items-center justify-between">
        <div>
          <div class="font-semibold text-red-900">Access Restricted</div>
          <div class="text-sm text-red-800">
            Your free trial has ended. Please upgrade to continue using premium features.
          </div>
        </div>
        <a href="{{ route('subscriptions.index') }}"
           class="inline-flex px-3 py-1.5 rounded-lg bg-red-600 text-white text-sm hover:bg-red-700">
          Choose a plan
        </a>
      </div>
    </div>
  @elseif ($u->canStartTrial())
    <div class="rounded-xl flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border border-sky-200 bg-sky-50 px-4 py-3 mb-3">
      <div>
        <div class="font-semibold text-sky-900">Start your 3-day free trial</div>
        <div class="text-sm text-sky-800">Try all premium features for 3 days. No card required to start.</div>
      </div>
      <form method="POST" action="{{ route('trial.start') }}">
        @csrf
        <button type="submit"
          class="inline-flex px-3 py-1.5 rounded-lg bg-amber-500 text-white text-sm hover:bg-amber-600 w-full sm:w-auto justify-center">
          Start Free Trial
        </button>
      </form>
    </div>
  @endif

  {{-- ====== Stat cards ====== --}}
  <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white rounded-2xl border shadow-sm p-4 md:p-5">
      <div class="text-[12px] text-gray-600">Amount this month</div>
      <div class="mt-1 flex items-center justify-between">
        <div class="text-[26px] leading-7 font-semibold">{{ $C }} {{ number_format($stats['amount_this_month'] ?? 0,2) }}</div>
        <div class="w-10 h-10 rounded-xl grid place-items-center text-white bg-gradient-to-br from-orange-500 to-orange-600">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 1v22M5 6h9a3 3 0 0 1 0 6H8a3 3 0 0 0 0 6h11"/></svg>
        </div>
      </div>
      <div class="mt-1 text-[12px] text-gray-500">Total earned this month</div>
    </div>

    <div class="bg-white rounded-2xl border shadow-sm p-4 md:p-5">
      <div class="text-[12px] text-gray-600">SMS Balance</div>
      <div class="mt-1 flex items-center justify-between">
        <div class="text-[26px] leading-7 font-semibold">{{ $C }} {{ number_format($stats['sms_balance'] ?? 0,2) }}</div>
        <div class="w-10 h-10 rounded-xl grid place-items-center text-indigo-600 bg-indigo-50">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 15a4 4 0 0 1-4 4H7l-4 4V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/></svg>
        </div>
      </div>
      <div class="mt-1 text-[12px] text-gray-500">Your SMS balance</div>
    </div>

    <div class="bg-white rounded-2xl border shadow-sm p-4 md:p-5">
      <div class="text-[12px] text-gray-600">Total Clients</div>
      <div class="mt-1 flex items-center justify-between">
        <div class="text-[26px] leading-7 font-semibold">{{ number_format($stats['total_clients'] ?? 0) }}</div>
        <div class="w-10 h-10 rounded-xl grid place-items-center text-violet-600 bg-violet-50">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
        </div>
      </div>
      <div class="mt-1 text-[12px] text-gray-500">Number of clients ({{ $stats['subscribed_clients'] ?? 0 }} subscribed)</div>
    </div>

    <div class="bg-white rounded-2xl border shadow-sm p-4 md:p-5">
      <div class="text-[12px] text-gray-600">Commission Earned</div>
      <div class="mt-1 flex items-center justify-between">
        <div class="text-[26px] leading-7 font-semibold">{{ $C }} {{ number_format($stats['commission'] ?? 0,2) }}</div>
        <div class="w-10 h-10 rounded-xl grid place-items-center text-amber-600 bg-amber-50">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 3v18h18"/><rect x="7" y="12" width="3" height="6"/><rect x="12" y="9" width="3" height="9"/><rect x="17" y="5" width="3" height="13"/></svg>
        </div>
      </div>
      <div class="mt-1 text-[12px] text-gray-500">{{ (int)($commissionRate*100) }}% commission this month</div>
    </div>
  </div>

  {{-- Meta row --}}
  <div class="mt-4 flex items-center gap-3 text-[12px] text-gray-500">
    <div class="ml-auto">
      Account Expiry
      <span class="font-medium">
        {{ ($stats['expires_at'] ?? null) ? $stats['expires_at']->format('M d, Y') : '—' }}
      </span>
    </div>
    <button class="px-3 py-1.5 rounded-lg border bg-white hover:bg-gray-50">Filters</button>
  </div>

  {{-- Row: Revenue/Expenses + Active Users --}}
  <div class="mt-4 grid lg:grid-cols-2 gap-4">
    <x-card-chart title="Payments" filter="This year" canvasId="revExpChart"></x-card-chart>
    <x-card-chart title="Active Users" filter="This week" canvasId="activeUsersChart"></x-card-chart>
  </div>

  {{-- Row: Retention + Data Usage --}}
  <div class="mt-4 grid lg:grid-cols-2 gap-4">
    <x-card-chart title="Customer retention rate (6 months)" filter="This year" canvasId="retentionChart"></x-card-chart>
    <x-card-chart title="Data Usage" filter="This week" canvasId="dataUsageChart"></x-card-chart>
  </div>

  {{-- Row: Package Utilization + Revenue Forecast --}}
  <div class="mt-4 grid lg:grid-cols-2 gap-4">
    <x-card-chart title="Package Utilization" filter="" canvasId="pkgDonutChart"></x-card-chart>
    <x-card-chart title="Revenue Forecast (3 months)" filter="" canvasId="forecastChart"></x-card-chart>
  </div>

  {{-- Row: Sent SMS + Registrations --}}
  <div class="mt-4 grid lg:grid-cols-2 gap-4">
    <x-card-chart title="Sent SMS" filter="This week" canvasId="smsWeekChart"></x-card-chart>
    <x-card-chart title="User Registrations" filter="This week" canvasId="regsWeekChart"></x-card-chart>
  </div>

  {{-- Most Active Users + Package Performance --}}
  <div class="mt-4 grid lg:grid-cols-2 gap-4">
    <div class="bg-white rounded-2xl border shadow-sm p-4">
      <div class="mb-3 font-medium">Most Active Users</div>
      @if(($topUsers ?? collect())->isEmpty())
        <div class="text-gray-500 text-sm">No active users yet.</div>
      @else
        <ul class="space-y-2">
          @foreach($topUsers as $row)
            <li class="flex items-center justify-between text-sm">
              <span class="truncate">{{ $row->username ?? 'Unknown' }}</span>
              <span class="text-gray-500">{{ $row->sessions }} sessions</span>
            </li>
          @endforeach
        </ul>
      @endif
    </div>

    <div class="bg-white rounded-2xl border shadow-sm p-4">
      <div class="mb-3 font-medium">Package Performance Comparison</div>
      <div class="overflow-x-auto">
        <table class="w-full text-[13px]">
          <thead>
            <tr class="text-left text-gray-500">
              <th class="py-2">Package Name</th>
              <th class="py-2">Price</th>
              <th class="py-2">Active Users</th>
              <th class="py-2">Monthly Revenue</th>
              <th class="py-2">Avg. Data Usage</th>
              <th class="py-2">ARPU</th>
            </tr>
          </thead>
          <tbody>
            @forelse(($pkgPerf ?? []) as $r)
              <tr class="border-t">
                <td class="py-2">{{ $r['name'] }}</td>
                <td class="py-2">{{ $C }} {{ number_format($r['price'],2) }}</td>
                <td class="py-2">{{ $r['active_users'] }}</td>
                <td class="py-2">{{ $C }} {{ number_format($r['monthly_revenue'],2) }}</td>
                <td class="py-2">{{ number_format($r['avg_data'],2) }} MB</td>
                <td class="py-2">{{ $C }} {{ number_format($r['arpu'],2) }}</td>
              </tr>
            @empty
              <tr><td colspan="6" class="py-4 text-gray-500">No data available.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- ===== Charts JS ===== --}}
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
  <script>
    const fmt = new Intl.NumberFormat(undefined, {maximumFractionDigits:2});

    const rev = @json($charts['revenue_expenses']['revenue'] ?? []);
    const exp = @json($charts['revenue_expenses']['expenses'] ?? []);
    new Chart(document.getElementById('revExpChart'), {
      type: 'line',
      data: { labels: @json($charts['revenue_expenses']['labels'] ?? []),
        datasets: [
          { label:'Revenue', data: rev,   tension:.35, fill:false },
          { label:'Expenses',data: exp,   tension:.35, fill:false }
        ]
      },
      options: { responsive:true, plugins:{ legend:{ display:true }}, scales:{ y:{ beginAtZero:true } } }
    });

    new Chart(document.getElementById('activeUsersChart'), {
      type: 'line',
      data: { labels: @json($charts['active_users_week']['labels'] ?? []),
        datasets: [{ label:'Active', data:@json($charts['active_users_week']['active'] ?? []), tension:.35, fill:false }]
      },
      options:{ scales:{ y:{ beginAtZero:true, ticks:{ precision:0 } } } }
    });

    new Chart(document.getElementById('retentionChart'), {
      type: 'line',
      data: {
        labels: @json($charts['retention']['labels'] ?? []),
        datasets: [
          { label:'New',       data:@json($charts['retention']['new'] ?? []), tension:.35, fill:false },
          { label:'Returning', data:@json($charts['retention']['returning'] ?? []), tension:.35, fill:false },
          { label:'Churn',     data:@json($charts['retention']['churn'] ?? []), tension:.35, fill:false }
        ]
      },
      options:{ scales:{ y:{ beginAtZero:true } } }
    });

    new Chart(document.getElementById('dataUsageChart'), {
      type: 'line',
      data: {
        labels: @json($charts['data_usage']['labels'] ?? []),
        datasets: [
          { label:'Download (MB)', data:@json($charts['data_usage']['download'] ?? []), tension:.35, fill:false },
          { label:'Upload (MB)',   data:@json($charts['data_usage']['upload'] ?? []),   tension:.35, fill:false }
        ]
      },
      options:{ scales:{ y:{ beginAtZero:true } } }
    });

    new Chart(document.getElementById('pkgDonutChart'), {
      type: 'doughnut',
      data: { labels:@json($charts['packages']['labels'] ?? []),
        datasets:[{ data:@json($charts['packages']['values'] ?? []) }]
      },
      options:{ cutout:'65%' }
    });

    new Chart(document.getElementById('forecastChart'), {
      type: 'line',
      data: {
        labels: @json($charts['forecast']['labels'] ?? []),
        datasets: [
          { label:'Historical Revenue', data:@json($charts['forecast']['hist'] ?? []), tension:.35, fill:false },
          { label:'Forecast', data:@json($charts['forecast']['pred'] ?? []), tension:.35, fill:false },
          { label:'Upper Confidence', data:@json($charts['forecast']['high'] ?? []), tension:.35, borderDash:[6,6], fill:false },
          { label:'Lower Confidence', data:@json($charts['forecast']['low'] ?? []),  tension:.35, borderDash:[6,6], fill:false },
        ]
      },
      options:{ scales:{ y:{ beginAtZero:true } } }
    });

    new Chart(document.getElementById('smsWeekChart'), {
      type: 'bar',
      data: { labels:@json($charts['sms_week']['labels'] ?? []),
        datasets:[{ label:'Messages', data:@json($charts['sms_week']['values'] ?? []) }]
      },
      options:{ scales:{ y:{ beginAtZero:true, ticks:{ precision:0 } } } }
    });

    new Chart(document.getElementById('regsWeekChart'), {
      type: 'bar',
      data: { labels:@json($charts['registrations']['labels'] ?? []),
        datasets:[{ label:'Registrations', data:@json($charts['registrations']['values'] ?? []) }]
      },
      options:{ scales:{ y:{ beginAtZero:true, ticks:{ precision:0 } } } }
    });
  </script>

  {{-- ====== Mobile Money Integration (unchanged) ====== --}}
  <div class="mt-4 bg-white rounded-2xl border shadow-sm">
    <div class="px-4 py-3 border-b font-medium">Mobile Money Integration Status</div>
    <div class="p-4 grid md:grid-cols-2 lg:grid-cols-3 gap-3">
      @php
        $rows = [
          ['MTN MoMo','Uganda, Rwanda, Ghana','Ready','bg-yellow-100 text-yellow-700 border-yellow-200'],
          ['Airtel Money','Kenya, Uganda','Ready','bg-yellow-100 text-yellow-700 border-yellow-200'],
          ['M-Pesa','Kenya, Tanzania','Ready','bg-yellow-100 text-yellow-700 border-yellow-200'],
          ['Tigo Pesa','Tanzania','Ready','bg-yellow-100 text-yellow-700 border-yellow-200'],
          ['Telebirr','Ethiopia','Testing','bg-amber-50 text-amber-700 border-amber-200'],
          ['NilePay','South Sudan','Pending','bg-gray-50 text-gray-600 border-gray-200'],
        ];
      @endphp
      @foreach($rows as [$name,$desc,$label,$badge])
        <div class="border rounded-2xl p-4 flex items-start gap-3">
          <div class="w-9 h-9 rounded-xl bg-gray-100 grid place-items-center font-semibold text-[11px]">
            {{ strtoupper(substr($name,0,3)) }}
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
              <div class="font-medium truncate">{{ $name }}</div>
              <span class="px-2 py-0.5 text-[11px] rounded-full border {{ $badge }}">{{ $label }}</span>
            </div>
            <div class="text-[12px] text-gray-500 truncate">{{ $desc }}</div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
@endsection

{{-- ===== Small Blade component for chart cards (drop this in resources/views/components/card-chart.blade.php) ===== --}}
@once
  @push('components')
@endonce
