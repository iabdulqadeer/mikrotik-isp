@extends('layouts.app', ['title' => 'Subscriptions'])
@section('content')

  <div class="max-w-6xl mx-auto">

    {{-- Header --}}
    <div class="mb-4 flex flex-col lg:flex-row lg:items-center gap-3">
      <div class="min-w-0">
        <h1 class="text-[18px] font-semibold">Subscriptions</h1>
        <p class="text-[12px] text-gray-500">Manage your subscription, switch plans, and control billing.</p>
      </div>
      <div class="lg:ml-auto flex items-center gap-2">
        @can('subscriptions.view_invoices')
          <a href="{{ route('billing.invoices') }}" class="h-10 px-3 rounded-xl border bg-white hover:bg-gray-50">Invoices</a>
        @endcan
        @can('subscriptions.billing_portal')
          <a href="{{ route('billing.portal') }}" class="h-10 px-3 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">Manage Billing</a>
        @endcan
      </div>
    </div>

    {{-- Top summary strip --}}
    @php
      $st = $status ?? null;
      $badge = match($st){
        'active'   => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'trialing' => 'bg-sky-50 text-sky-700 border-sky-200',
        'past_due' => 'bg-amber-50 text-amber-700 border-amber-200',
        'canceled' => 'bg-gray-50 text-gray-600 border-gray-200',
        'incomplete','incomplete_expired','unpaid' => 'bg-rose-50 text-rose-600 border-rose-200',
        default    => 'bg-gray-50 text-gray-600 border-gray-200',
      };
    @endphp

    <div class="mb-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
      <div class="bg-white rounded-2xl border shadow-sm p-4">
        <div class="text-[12px] text-gray-500">Status</div>
        <div class="mt-1">
          @if($sub)
            <span class="px-2 py-0.5 text-[11px] rounded-full border {{ $badge }}">
              {{ ucfirst(str_replace('_',' ', $st)) }}
            </span>
            @if($sub->onGracePeriod())
              <div class="text-[12px] text-amber-600 mt-1">
                Ends {{ optional($sub->ends_at)->format('Y-m-d H:i') }} ({{ optional($sub->ends_at)->diffForHumans() }})
              </div>
            @endif
          @else
            <span class="px-2 py-0.5 text-[11px] rounded-full border bg-gray-50 text-gray-600 border-gray-200">No subscription</span>
          @endif
        </div>
      </div>

      <div class="bg-white rounded-2xl border shadow-sm p-4">
        <div class="text-[12px] text-gray-500">Customer</div>
        <div class="mt-1 font-medium">{{ $user->name }}</div>
        <div class="text-[12px] text-gray-500">{{ $user->email }}</div>
      </div>

      <div class="bg-white rounded-2xl border shadow-sm p-4">
        <div class="text-[12px] text-gray-500">Current Period</div>
        <div class="mt-1 text-[13px]">
          @if($sub && $sub->asStripeSubscription()?->current_period_start)
            {{ \Carbon\Carbon::createFromTimestamp($sub->asStripeSubscription()->current_period_start)->format('Y-m-d') }}
            <span class="text-gray-400">→</span>
            {{ \Carbon\Carbon::createFromTimestamp($sub->asStripeSubscription()->current_period_end)->format('Y-m-d') }}
          @else
            —
          @endif
        </div>
      </div>

      <div class="bg-white rounded-2xl border shadow-sm p-4">
        <div class="text-[12px] text-gray-500">Actions</div>
        <div class="mt-1 flex flex-wrap gap-2">
          @if($sub && $sub->onGracePeriod())
            @can('subscriptions.resume')
              <form method="POST" action="{{ route('subscriptions.resume') }}">@csrf
                <button class="h-9 px-3 rounded-lg border bg-white hover:bg-gray-50 text-[13px]">Resume</button>
              </form>
            @endcan
          @elseif($sub && $sub->active())
            @can('subscriptions.cancel')
              <form method="POST" action="{{ route('subscriptions.cancel') }}">@csrf
                <button class="h-9 px-3 rounded-lg border bg-white hover:bg-gray-50 text-[13px]">Cancel</button>
              </form>
            @endcan
          @else
            @can('subscriptions.subscribe')
              <a href="#plans" class="h-9 px-3 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-[13px]">Choose a Plan</a>
            @endcan
          @endif
        </div>
      </div>
    </div>

    {{-- Plan chooser --}}
    <div id="plans" class="bg-white rounded-2xl border shadow-sm p-4">
      <div class="flex items-center justify-between">
        <h2 class="text-[14px] font-semibold">Available Plans</h2>
        <div class="text-[12px] text-gray-500">Pick the plan that fits your speed & billing needs.</div>
      </div>

      <div class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($plans as $plan)
          <div class="rounded-2xl border p-4 flex flex-col">
            <div class="flex items-start justify-between">
              <div class="font-medium">{{ $plan->name }}</div>
              <span class="text-[11px] px-2 py-0.5 rounded-full border
                  {{ $plan->active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-gray-50 text-gray-600 border-gray-200' }}">
                {{ $plan->active ? 'Active' : 'Inactive' }}
              </span>
            </div>

            <div class="mt-1 text-[12px] text-gray-500">
              {{ $plan->speed_down_kbps }}↓ / {{ $plan->speed_up_kbps }}↑ kbps
            </div>

            <div class="mt-2">
              <span class="text-2xl font-semibold">{{ number_format($plan->price,2) }}</span>
              <span class="text-sm text-gray-600">/ {{ $plan->billing_cycle }}</span>
            </div>

            <div class="mt-4">
              @if($sub && $sub->active())
                @can('subscriptions.swap')
                  <form method="POST" action="{{ route('subscriptions.swap', $plan) }}">@csrf
                    <button class="w-full h-10 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">Switch</button>
                  </form>
                @endcan
              @else
                @can('subscriptions.subscribe')
                  <form method="POST" action="{{ route('subscriptions.checkout', $plan) }}">@csrf
                    <button class="w-full h-10 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">Subscribe</button>
                  </form>
                @endcan
              @endif
            </div>

            @if(!empty($plan->stripe_price_id))
              <div class="mt-3 text-[11px] text-gray-500">
                Stripe Price: <span class="font-mono">{{ $plan->stripe_price_id }}</span>
              </div>
            @endif
          </div>
        @empty
          <div class="text-[13px] text-gray-500">No plans available yet.</div>
        @endforelse
      </div>
    </div>

    {{-- Footer actions --}}
    <div class="mt-4 flex flex-wrap gap-2">
      @can('subscriptions.view_invoices')
        <a href="{{ route('billing.invoices') }}" class="px-3 py-2 rounded-xl border bg-white hover:bg-gray-50">View invoices</a>
      @endcan
      @can('subscriptions.billing_portal')
        <a href="{{ route('billing.portal') }}" class="px-3 py-2 rounded-xl bg-white border hover:bg-gray-50">Billing portal</a>
      @endcan
    </div>

  </div>
@endsection
