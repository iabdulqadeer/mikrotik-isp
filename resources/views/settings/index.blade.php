{{-- resources/views/settings/index.blade.php --}}
@extends('layouts.app', ['title' => 'Settings'])

@php
  $active = $tab ?? 'general';
  function tabUrl($t){ return route('settings.index', ['tab' => $t]); }
  $logo = $settings['branding.logo'] ? asset('storage/'.$settings['branding.logo']) : null;
  $bg   = $settings['hotspot.bg'] ? asset('storage/'.$settings['hotspot.bg']) : null;

  $tabs = [
    'general' => ['label' => 'General', 'icon' => 'M4 6h16M4 12h16M4 18h7'],
    'branding'=> ['label' => 'Branding','icon' => 'M12 3v18m9-9H3'],
    'payments'=> ['label' => 'Payments','icon' => 'M4 7h16v10H4zM4 11h16'],
    'pppoe'   => ['label' => 'PPPoE',   'icon' => 'M3 12h18M3 6h18M3 18h18'],
    'hotspot' => ['label' => 'Hotspot', 'icon' => 'M12 20v-6m0-4V4m0 6h8M12 10H4'],
    'sms'     => ['label' => 'SMS',     'icon' => 'M21 12a9 9 0 1 1-6.219-8.56'],
    'email'   => ['label' => 'Email',   'icon' => 'm3 7 9 6 9-6M5 19h14'],
    'security' => ['label' => '2FA', 'icon' => 'M12 2l7 4v6c0 5-3.8 9.4-7 10-3.2-.6-7-5-7-10V6l7-4zm0 6a1 1 0 0 0-.894.553l-3 6a1 1 0 1 0 1.788.894L12 10.618l2.106 4.829a1 1 0 1 0 1.788-.894l-3-6A1 1 0 0 0 12 8z'],
    'password'=> ['label' => 'Password','icon' => 'M12 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0 0v7'],
    'logins' => ['label' => 'Activity', 'icon' => 'M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.16V11a6 6 0 1 0-12 0v3.16c0 .54-.21 1.06-.6 1.44L4 17h5'],
    'notifications' => ['label' => 'Notifications','icon' => 'M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.16V11a6 6 0 1 0-12 0v3.16c0 .54-.21 1.06-.6 1.44L4 17h5'],
    // NEW: 2FA Security tab (shield-check icon)
  ];
@endphp

@section('content')

  {{-- Page header --}}
  <div class="mb-4">
    <h1 class="text-[18px] font-semibold">Settings</h1>
    <p class="text-[12px] text-gray-500">Configure your hotspot billing system and account preferences.</p>
  </div>

  {{-- Card wrapper --}}
  <div class="bg-white rounded-2xl border shadow-sm">
    {{-- Sticky tabs --}}
  {{-- ====== Tabs ====== --}}
  <div class="sticky top-[56px] lg:top-[56px] z-30 bg-white/85 backdrop-blur rounded-t-2xl border-b">
    <div class="px-3 py-2">
      <div class="flex gap-1 overflow-x-auto no-scrollbar">
        @foreach($tabs as $key => $meta)
          @can("settings.$key.view")
            <a href="{{ tabUrl($key) }}"
               class="group inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm border
                      {{ $active === $key
                          ? 'bg-indigo-50 text-indigo-700 border-indigo-200'
                          : 'hover:bg-gray-50 border-transparent text-gray-700' }}">
              <svg class="w-4 h-4 {{ $active === $key ? 'text-indigo-700' : 'text-gray-500 group-hover:text-gray-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="{{ $meta['icon'] }}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
              </svg>
              {{ $meta['label'] }}
              @if($active === $key)
                <span class="ml-1 inline-block h-1 w-1 rounded-full bg-indigo-600"></span>
              @endif
            </a>
          @endcan
        @endforeach
      </div>
    </div>
  </div>


    {{-- Body --}}
    <div class="p-4">
      {{-- ===== GENERAL ===== --}}
      @if($active==='general')
        @can('settings.general.update')
        <form method="POST" action="{{ route('settings.general.save') }}" class="space-y-6">
          @csrf

          <section class=" p-4">
            <header class="mb-4">
              <h2 class="text-sm font-semibold">Profile</h2>
              <p class="text-xs text-gray-500">Basic details used across invoices, receipts, and communications.</p>
            </header>

            <div class="grid md:grid-cols-2 gap-4">
              <x-input label="First Name" name="first_name" :value="$u->first_name" required/>
              <x-input label="Last Name" name="last_name" :value="$u->last_name" required/>
              <x-input type="email" label="Email Address" name="email" :value="$u->email" required/>
              <x-input label="Phone Number" name="phone" :value="$u->phone" placeholder="+92 3xx xxxxxx"/>
              <x-input label="WhatsApp Number" name="whatsapp" :value="$u->whatsapp"/>
              <x-input label="Country" name="country" :value="$u->country" placeholder="PK"/>
              <div class="md:col-span-2">
                <x-input label="Business Address" name="business_address" :value="$u->business_address" placeholder="Street, City, State / Province"/>
              </div>
              <x-input label="Customer Care Contact" name="customer_care" :value="$u->customer_care"/>
              <div class="md:col-span-2">
                <label class="block text-sm mb-1">Account Created</label>
                <div class="h-10 rounded-xl border border-gray-200 bg-gray-50 px-3 flex items-center text-sm text-gray-600">
                  {{ $u->created_at?->toDayDateTimeString() }}
                </div>
              </div>
            </div>
          </section>

          <div class="flex items-center justify-end gap-2">
            <a href="{{ tabUrl('general') }}" class="h-10 px-4 rounded-xl border bg-white hover:bg-gray-50 text-sm">Reset</a>
            <button class="h-10 px-4 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 text-sm">Save Changes</button>
          </div>
        </form>
        @endcan
      @endif

      {{-- ===== BRANDING ===== --}}
      @if($active==='branding')
        @can('settings.branding.update')
        <form method="POST" action="{{ route('settings.branding.save') }}" enctype="multipart/form-data" class="space-y-6">
          @csrf

          <section class=" p-4 space-y-6">
            <header>
              <h2 class="text-sm font-semibold">Brand assets</h2>
              <p class="text-xs text-gray-500">Upload your logo and set primary/secondary brand colors.</p>
            </header>

            <div class="grid md:grid-cols-[1fr,auto] gap-4 items-center">
              <div>
                <label class="block text-sm mb-1">Logo Upload</label>
                <input type="file" name="logo" class="block w-full text-sm">
                <p class="text-[11px] text-gray-500 mt-1">PNG/JPG up to 2MB. Transparent background recommended.</p>
              </div>
              @if($logo)
                <img src="{{ $logo }}" class="h-10 w-auto rounded-md border" alt="Brand logo preview">
              @endif
            </div>

            <div class="grid md:grid-cols-2 gap-4">
              <x-input label="Website URL" name="website" :value="$settings['branding.website']" placeholder="https://example.com"/>
              <div></div>
              <div class="md:col-span-2">
                <x-textarea label="Brand Description" name="desc" rows="4">{{ $settings['branding.desc'] }}</x-textarea>
              </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
              <x-input label="Primary Color" name="color_primary" :value="$settings['branding.color_primary'] ?? '#FFB001'"/>
              <x-input label="Secondary Color" name="color_secondary" :value="$settings['branding.color_secondary'] ?? '#010160'"/>
            </div>
          </section>

          <section class=" p-4 space-y-6">
            <header>
              <h2 class="text-sm font-semibold">Usage preferences</h2>
              <p class="text-xs text-gray-500">Control where your brand appears.</p>
            </header>
            <div class="grid md:grid-cols-3 gap-4">
              <x-switch label="Show logo on Wi-Fi login pages" name="show_wifi_logo" :checked="$settings['branding.show_wifi_logo']"/>
              <x-switch label="Show logo on invoices / receipts" name="show_invoice_logo" :checked="$settings['branding.show_invoice_logo']"/>
              <x-switch label="Show logo on voucher printouts" name="show_voucher_logo" :checked="$settings['branding.show_voucher_logo']"/>
            </div>
          </section>

          <section class=" p-4 space-y-4">
            <header>
              <h2 class="text-sm font-semibold">Business info</h2>
              <p class="text-xs text-gray-500">Shown to customers on receipts and emails.</p>
            </header>
            <div class="grid md:grid-cols-3 gap-4">
              <x-input label="Business Name" name="business_name" :value="$settings['branding.business_name']"/>
              <x-input label="Support Phone" name="support_phone" :value="$settings['branding.support_phone']"/>
              <x-input label="Support Email" type="email" name="support_email" :value="$settings['branding.support_email']"/>
            </div>
            <div class="grid md:grid-cols-3 gap-4">
              <x-input label="Commission Rate (%)" name="commission_rate" type="number" step="0.01" :value="$settings['branding.commission_rate']" />
            </div>
            <div class="rounded-lg border bg-neutral-50 p-3 text-sm">
              A commission of <b>{{ number_format($settings['branding.commission_rate'] ?? 5, 2) }}%</b> will be deducted automatically.
            </div>
          </section>

          <div class="flex items-center justify-end gap-2">
            <a href="{{ tabUrl('branding') }}" class="h-10 px-4 rounded-xl border bg-white hover:bg-gray-50 text-sm">Reset</a>
            <button class="h-10 px-4 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 text-sm">Save Changes</button>
          </div>
        </form>
        @endcan
      @endif

      {{-- ===== PASSWORD ===== --}}
      @if($active==='password')
        @can('settings.password.update')
        <form method="POST" action="{{ route('settings.password.save') }}" class="space-y-6 max-w-2xl">
          @csrf

          <section class=" p-4 space-y-4">
            <header>
              <h2 class="text-sm font-semibold">Change password</h2>
              <p class="text-xs text-gray-500">We’ll ask for your current password to confirm it’s you.</p>
            </header>

            <x-input label="Current Password*" name="current_password" type="password" required/>
            <div class="grid md:grid-cols-2 gap-4">
              <x-input label="New Password*" name="password" type="password" required/>
              <x-input label="Confirm New Password*" name="password_confirmation" type="password" required/>
            </div>

            <div class="rounded-lg border p-3 bg-blue-50 text-blue-900 text-xs">
              <b>Security tips:</b> Use at least 8 characters, mix letters/numbers/symbols, and avoid reusing passwords.
            </div>
          </section>

          <div class="flex items-center justify-end gap-2">
            <a href="{{ tabUrl('password') }}" class="h-10 px-4 rounded-xl border bg-white hover:bg-gray-50 text-sm">Reset</a>
            <button class="h-10 px-4 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 text-sm">Save Changes</button>
          </div>
        </form>
        @endcan
      @endif

      {{-- ===== PAYMENTS ===== --}}
      @if($active==='payments')

        @can('settings.payments.update')

        <form method="POST" action="{{ route('settings.payments.save') }}" class="space-y-6">
          @csrf

          <section class=" p-4 space-y-4">
            <header>
              <h2 class="text-sm font-semibold">Mobile money providers</h2>
              <p class="text-xs text-gray-500">Enable providers you accept and add your API credentials.</p>
            </header>

            <div class="space-y-4">
              @include('settings.partials.payment-card', ['title'=>'MTN Mobile Money','name'=>'mtn','enabled'=>$settings['pay.mtn.enabled'],'key'=>$settings['pay.mtn.key']])
              @include('settings.partials.payment-card', ['title'=>'Airtel Money','name'=>'airtel','enabled'=>$settings['pay.airtel.enabled'],'key'=>$settings['pay.airtel.key']])
              @include('settings.partials.payment-card', ['title'=>'M-Pesa','name'=>'mpesa','enabled'=>$settings['pay.mpesa.enabled'],'key'=>$settings['pay.mpesa.key']])
              @include('settings.partials.payment-card', ['title'=>'Tigo Pesa','name'=>'tigo','enabled'=>$settings['pay.tigo.enabled'],'key'=>$settings['pay.tigo.key']])
            </div>
          </section>

          <div class="flex items-center justify-end gap-2">
            <a href="{{ tabUrl('payments') }}" class="h-10 px-4 rounded-xl border bg-white hover:bg-gray-50 text-sm">Reset</a>
            <button class="h-10 px-4 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 text-sm">Save Changes</button>
          </div>
        </form>

        @endcan

      @endif

      {{-- ===== PPPOE ===== --}}
      @if($active==='pppoe')

        @can('settings.pppoe.update')

        <form method="POST" action="{{ route('settings.pppoe.save') }}" class="space-y-6 max-w-4xl">
          @csrf
          <section class=" p-4 space-y-4">
            <header>
              <h2 class="text-sm font-semibold">PPPoE server</h2>
              <p class="text-xs text-gray-500">Server connectivity and authentication settings.</p>
            </header>

            <x-switch label="Enable PPPoE Server" name="enabled" :checked="$settings['pppoe.enabled']"/>
            <div class="grid md:grid-cols-2 gap-4">
              <x-input label="Server IP Address" name="ip" :value="$settings['pppoe.ip']" placeholder="192.168.88.1"/>
              <x-input label="DNS Server" name="dns" :value="$settings['pppoe.dns']" placeholder="8.8.8.8"/>
            </div>
            <x-input label="Shared Secret" name="secret" :value="$settings['pppoe.secret']" placeholder="********"/>
          </section>

          <div class="flex items-center justify-end gap-2">
            <a href="{{ tabUrl('pppoe') }}" class="h-10 px-4 rounded-xl border bg-white hover:bg-gray-50 text-sm">Reset</a>
            <button class="h-10 px-4 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 text-sm">Save Changes</button>
          </div>
        </form>

        @endcan 

      @endif

      {{-- ===== HOTSPOT ===== --}}
      @if($active==='hotspot')

        @can('settings.hotspot.update')

        <form method="POST" action="{{ route('settings.hotspot.save') }}" enctype="multipart/form-data" class="space-y-6">
          @csrf

          <section class=" p-4 space-y-4">
            <header>
              <h2 class="text-sm font-semibold">Templates & UI</h2>
              <p class="text-xs text-gray-500">Choose the login template and background.</p>
            </header>

            <div class="grid md:grid-cols-2 gap-4">
              <x-input label="Username Prefix*" name="username_prefix" :value="$settings['hotspot.username_prefix']" required/>
              <div>
                <label class="block text-sm mb-1">Hotspot Template*</label>
                <select name="template" class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3">
                  @foreach(['Simple','Modern'] as $opt)
                    <option value="{{ $opt }}" @selected(($settings['hotspot.template'] ?? 'Simple') === $opt)>{{ $opt }}</option>
                  @endforeach
                </select>
              </div>
              <div class="md:col-span-2">
                <label class="block text-sm mb-1">Template Background</label>
                <input type="file" name="bg" class="block w-full text-sm">
                @if($bg)<img src="{{ $bg }}" class="h-24 mt-2 rounded-xl border">@endif
                <p class="text-[11px] text-gray-500 mt-1">Recommended 1600×900 or larger.</p>
              </div>
            </div>
          </section>

          <section class=" p-4 space-y-4">
            <header>
              <h2 class="text-sm font-semibold">Behavior</h2>
              <p class="text-xs text-gray-500">Retention and post-login flow.</p>
            </header>

            <div class="grid md:grid-cols-2 gap-4">
              <x-select label="Prune Inactive Users After" name="prune_days" :options="[1,3,7,14,30]" :value="$settings['hotspot.prune_days'] ?? 7"/>
              <x-input label="Redirect URL*" name="redirect_url" :value="$settings['hotspot.redirect_url']" required placeholder="https://your-site.com/thanks"/>
            </div>

            <x-textarea
              label="Purchase Instructions"
              name="instructions"
              rows="4"
              :value="$settings['hotspot.instructions']"
            />
          </section>

          <div class="flex items-center justify-end gap-2">
            <a href="{{ tabUrl('hotspot') }}" class="h-10 px-4 rounded-xl border bg-white hover:bg-gray-50 text-sm">Reset</a>
            <button class="h-10 px-4 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 text-sm">Save Changes</button>
          </div>
        </form>

        @endcan

      @endif

      {{-- ===== SMS ===== --}}
      @if($active==='sms')

        @can('settings.sms.update')

        <form method="POST" action="{{ route('settings.sms.save') }}" class="space-y-6">
          @csrf

          <section class=" p-4 space-y-4">
            <header>
              <h2 class="text-sm font-semibold">SMS provider</h2>
              <p class="text-xs text-gray-500">Enable/disable and configure API credentials.</p>
            </header>

            <x-switch label="Enable SMS Service" name="enabled" :checked="$settings['sms.enabled']"/>
            <div class="grid md:grid-cols-2 gap-4">
              <x-input label="SMS Provider" name="provider" :value="$settings['sms.provider']" placeholder="Twilio, Vonage, etc."/>
              <div></div>
              <x-input label="API Key" name="key" :value="$settings['sms.key']"/>
              <x-input label="API Secret" name="secret" :value="$settings['sms.secret']"/>
              <x-input label="Sender ID" name="sender_id" :value="$settings['sms.sender_id']" placeholder="max 11 chars"/>
            </div>
          </section>

          <section class=" p-4 space-y-4">
            <header>
              <h2 class="text-sm font-semibold">Templates</h2>
              <p class="text-xs text-gray-500">Use variables like <code class="font-mono">:name</code>, <code class="font-mono">:amount</code>, <code class="font-mono">:plan</code>.</p>
            </header>
            <div class="grid md:grid-cols-2 gap-4">
              <x-textarea label="Voucher SMS Template" name="tpl_voucher" rows="4">{{ $settings['sms.tpl.voucher'] }}</x-textarea>
              <x-textarea label="Payment Confirmation Template" name="tpl_payment" rows="4">{{ $settings['sms.tpl.payment'] }}</x-textarea>
            </div>
          </section>

          <div class="flex items-center justify-end gap-2">
            <a href="{{ tabUrl('sms') }}" class="h-10 px-4 rounded-xl border bg-white hover:bg-gray-50 text-sm">Reset</a>
            <button class="h-10 px-4 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 text-sm">Save Changes</button>
          </div>
        </form>

        @endcan

      @endif

      {{-- ===== EMAIL ===== --}}
      @if($active==='email')

        @can('settings.email.update') 

        <form method="POST" action="{{ route('settings.email.save') }}" class="space-y-6">
          @csrf

          <section class=" p-4 space-y-4">
            <header>
              <h2 class="text-sm font-semibold">SMTP credentials</h2>
              <p class="text-xs text-gray-500">We’ll use these when sending receipts and notifications.</p>
            </header>

            <div class="grid md:grid-cols-2 gap-4">
              <x-input label="SMTP Host*" name="host" :value="$settings['mail.host']" placeholder="smtp.mailserver.com"/>
              <x-input label="SMTP Port*" name="port" type="number" :value="$settings['mail.port'] ?? 587"/>
            </div>
            <x-switch label="Use TLS/SSL Encryption" name="tls" :checked="$settings['mail.tls'] ?? true"/>
            <div class="grid md:grid-cols-2 gap-4">
              <x-input label="SMTP Username*" name="username" :value="$settings['mail.username']"/>
              <x-input label="SMTP Password*" name="password" :value="$settings['mail.password']"/>
            </div>
            <div class="grid md:grid-cols-2 gap-4">
              <x-input label="From Email*" name="from_email" :value="$settings['mail.from_email']"/>
              <x-input label="From Name" name="from_name" :value="$settings['mail.from_name']"/>
            </div>
          </section>

          <section class=" p-4 space-y-4">
            <header>
              <h2 class="text-sm font-semibold">Email templates</h2>
              <p class="text-xs text-gray-500">You can use variables like <code class="font-mono">:name</code>, <code class="font-mono">:amount</code>, <code class="font-mono">:plan</code>.</p>
            </header>
            <div class="grid md:grid-cols-2 gap-4">
              <x-textarea label="Voucher Email Template" name="tpl_voucher" rows="5">{{ $settings['mail.tpl.voucher'] }}</x-textarea>
              <x-textarea label="Payment Confirmation Email Template" name="tpl_payment" rows="5">{{ $settings['mail.tpl.payment'] }}</x-textarea>
            </div>
          </section>

          <div class="flex items-center justify-end gap-2">
            <a href="{{ tabUrl('email') }}" class="h-10 px-4 rounded-xl border bg-white hover:bg-gray-50 text-sm">Reset</a>
            <button class="h-10 px-4 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 text-sm">Save Changes</button>
          </div>
        </form>

        @endcan

      @endif

      {{-- ===== NOTIFICATIONS ===== --}}
      @if($active==='notifications')

        @can('settings.notifications.update')

        <form method="POST" action="{{ route('settings.notifications.save') }}" class="space-y-6 max-w-4xl">
          @csrf

          {{-- Notification Methods --}}
          <section class="space-y-4">
            <header class="px-1">
              <h2 class="text-[16px] font-semibold text-slate-900">Notification Methods</h2>
              <p class="text-xs text-slate-500">Configure how you want to receive notifications and alerts</p>
            </header>

            <x-toggle-row
              title="Email Notifications"
              subtitle="Receive notifications via email"
              name="notify_email"
              :checked="$settings['notify.email']"
              :icon="'M4 4h16M4 20h16M4 4l8 8 8-8'"
              iconColor="text-indigo-700"
            />

            <x-toggle-row
              title="SMS Notifications"
              subtitle="Receive notifications via SMS"
              name="notify_sms"
              :checked="$settings['notify.sms']"
              :icon="'M21 15a4 4 0 0 1-4 4H7l-4 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v8z'"
              iconColor="text-orange-600"
            />

            <x-toggle-row
              title="Push Notifications"
              subtitle="Receive browser push notifications"
              name="notify_push"
              :checked="$settings['notify.push']"
              :icon="'M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.16V11a6 6 0 1 0-12 0v3.16c0 .54-.21 1.06-.6 1.44L4 17h5'"
              iconColor="text-purple-600"
            />
          </section>

          {{-- Notification Types --}}
          <section class="space-y-4">
            <header class="px-1">
              <h2 class="text:[16px] font-semibold text-slate-900">Notification Types</h2>
              <p class="text-xs text-slate-500">Fine-tune which events create alerts</p>
            </header>

            <x-toggle-row
              title="Payment Notifications"
              subtitle="Get notified when payments are received"
              name="notify_payment"
              :checked="$settings['notify.type.payment']"
              :icon="'M3 7h18v10H3z M3 11h18'"
              iconColor="text-indigo-700"
            />

            <x-toggle-row
              title="Voucher Purchase Notifications"
              subtitle="Get notified when users purchase vouchers"
              name="notify_voucher"
              :checked="$settings['notify.type.voucher']"
              :icon="'M4 4h16v12H4z M4 8h16'"
              iconColor="text-orange-600"
            />

            {{-- Low balance with threshold input --}}
            <x-toggle-row
              title="Low Balance Alerts"
              subtitle="Get notified when your balance is low"
              name="low_enabled"
              :checked="$settings['notify.low.enabled']"
              :icon="'M12 9v4m0 4h.01M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20z'"
              iconColor="text-rose-600"
            >
              <div class="pl-1">
                <x-input
                  label="Low Balance Threshold (ETB)"
                  name="low_threshold"
                  type="number"
                  step="0.01"
                  :value="$settings['notify.low.threshold'] ?? 1000"
                  class="max-w-[220px]"
                />
                <p class="text-[11px] text-slate-500 mt-1">You'll be notified when your balance falls below this amount</p>
              </div>
            </x-toggle-row>
          </section>

          <div class="flex items-center justify-end gap-2">
            <a href="{{ tabUrl('notifications') }}" class="h-10 px-4 rounded-xl border bg-white hover:bg-gray-50 text-sm">Reset</a>
            <button class="h-10 px-4 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 text-sm">Save Changes</button>
          </div>
        </form>

        @endcan

      @endif

      {{-- ===== 2FA SECURITY (NEW) ===== --}}
      @if($active==='security')

        @can('settings.security.update')

        @php
          /** @var \App\Models\User $authUser */
          $authUser = $u ?? auth()->user();

          $google2fa = new \PragmaRX\Google2FA\Google2FA();

          // Only prepare pending secret if not enabled
          $pendingSecret = $authUser->two_factor_enabled
              ? null
              : (session()->get('2fa_pending_secret') ?? $google2fa->generateSecretKey(32));

          if ($pendingSecret) {
              session()->put('2fa_pending_secret', $pendingSecret);
              $otpauth = $google2fa->getQRCodeUrl(config('app.name','Laravel'), $authUser->email, $pendingSecret);

              // Build SVG QR using BaconQrCode (no GD/Imagick needed)
              $renderer = new \BaconQrCode\Renderer\ImageRenderer(
                  new \BaconQrCode\Renderer\RendererStyle\RendererStyle(300),
                  new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
              );
              $writer = new \BaconQrCode\Writer($renderer);
              $svg = $writer->writeString($otpauth);
              $qrDataUri = 'data:image/svg+xml;base64,' . base64_encode($svg);
          } else {
              $qrDataUri = null;
          }
          $recoveryCodes = $authUser->two_factor_recovery_codes ?? [];
        @endphp

        <div class="max-w-3xl mx-auto">
          <div class="mb-4">
            <h2 class="text-sm font-semibold">Two-Factor Authentication (2FA)</h2>
            <p class="text-xs text-gray-500">Add an extra layer of security with Google Authenticator.</p>
          </div>

          @if(! $authUser->two_factor_enabled)
            <div class="rounded-2xl border bg-white p-4">
              <h3 class="text-sm font-semibold mb-2">Enable 2FA</h3>
              <ol class="text-sm text-gray-600 list-decimal ml-5 space-y-1 mb-4">
                <li>Install <b>Google Authenticator</b> (or Authy/1Password) on your phone.</li>
                <li>Scan the QR code below or enter the secret manually.</li>
                <li>Enter the 6-digit code to confirm.</li>
              </ol>

              @if($qrDataUri)
                <div class="flex flex-col md:flex-row gap-4 items-start">
                  <img class="w-48 h-48 rounded-xl border" src="{{ $qrDataUri }}" alt="2FA QR Code">
                  <div class="text-sm">
                    <div class="mb-2">
                      <span class="font-medium">Secret:</span>
                      <code class="text-xs bg-gray-100 rounded px-2 py-1">{{ $pendingSecret }}</code>
                    </div>

                    <form method="POST" action="{{ route('2fa.confirm') }}" class="flex items-center gap-2">
                      @csrf
                      <input name="code" maxlength="6" inputmode="numeric" autocomplete="one-time-code"
                             class="h-10 w-40 rounded-xl border border-gray-200 bg-white px-3"
                             placeholder="123456" />
                      <button class="h-10 px-4 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">Confirm</button>
                    </form>
                    @error('code')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                  </div>
                </div>
              @else
                <p class="text-sm text-gray-500">Generating setup… refresh if the QR is missing.</p>
              @endif
            </div>
          @else
            {{-- inside: @else (when $authUser->two_factor_enabled) --}}
            <div class="rounded-2xl border bg-white p-4 space-y-6">
              <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                <div>
                  <h3 class="text-sm font-semibold">2FA is enabled</h3>
                  <p class="text-xs text-gray-500">Confirmed at {{ $authUser->two_factor_confirmed_at?->format('Y-m-d H:i') }}</p>
                </div>

                {{-- Disable requires verification --}}
                <form method="POST" action="{{ route('2fa.disable') }}" class="flex flex-col sm:flex-row gap-2 sm:items-center">
                  @csrf
                  <input name="code" maxlength="32" autocomplete="one-time-code"
                         class="h-10 w-56 rounded-xl border border-gray-200 bg-white px-3 text-sm"
                         placeholder="Enter 6-digit or recovery code" />
                  @error('code')<p class="text-xs text-red-600 sm:ml-2">{{ $message }}</p>@enderror

                  <button class="h-10 px-4 rounded-xl border hover:bg-gray-50">Disable</button>
                </form>
              </div>

              <div class="border-t pt-4">
                <h4 class="text-sm font-semibold mb-2">Recovery Codes</h4>
                <p class="text-xs text-gray-500 mb-3">Use a recovery code if you lose access to your authenticator. Each code can be used once.</p>

                @if(!empty($recoveryCodes))
                  <div id="recoveryCodes" class="grid md:grid-cols-2 gap-2 text-xs">
                    @foreach($recoveryCodes as $code)
                      <div class="rounded-xl border px-3 py-2 bg-gray-50 font-mono tracking-wider">{{ $code }}</div>
                    @endforeach
                  </div>

                  <div class="mt-3 flex flex-wrap gap-2">
                    <a href="{{ route('2fa.recovery.download') }}"
                       class="h-10 inline-flex items-center px-4 rounded-xl border bg-white hover:bg-gray-50 text-sm">
                      Download .txt
                    </a>

                    <button type="button" id="copyRecovery"
                            class="h-10 inline-flex items-center px-4 rounded-xl border bg-white hover:bg-gray-50 text-sm">
                      Copy to clipboard
                    </button>
                  </div>
                @else
                  <p class="text-sm text-gray-500">No codes generated yet.</p>
                @endif

                <form method="POST" action="{{ route('2fa.recovery.regen') }}" class="mt-3">
                  @csrf
                  <button class="h-10 px-4 rounded-xl bg-white border hover:bg-gray-50">Regenerate Recovery Codes</button>
                </form>
              </div>
            </div>

            {{-- Add this tiny script at the bottom of the security tab (still inside the @if($active==='security') block) --}}
            <script>
              (function () {
                const btn = document.getElementById('copyRecovery');
                if (!btn) return;

                btn.addEventListener('click', () => {
                  const container = document.getElementById('recoveryCodes');
                  if (!container) return;

                  const codes = Array.from(container.querySelectorAll('div'))
                    .map(el => el.textContent.trim())
                    .filter(Boolean)
                    .join('\n');

                  navigator.clipboard.writeText(codes).then(() => {
                    btn.textContent = 'Copied!';
                    setTimeout(() => (btn.textContent = 'Copy to clipboard'), 1500);
                  }).catch(() => {
                    btn.textContent = 'Copy failed';
                    setTimeout(() => (btn.textContent = 'Copy to clipboard'), 1500);
                  });
                });
              })();
            </script>

          @endif
        </div>


        @endcan

      @endif


      {{-- ===== LOGIN ACTIVITY ===== --}}
@if($active==='logins')

    @can('settings.logins.update')

      <div class="mb-4 flex flex-col lg:flex-row lg:items-center gap-3">
        <div>
          <h1 class="text-[18px] font-semibold">Login Activity</h1>
          <p class="text-[12px] text-gray-500">Recent sign-ins to your account (IP & device).</p>
        </div>

        <div class="lg:ml-auto flex items-center gap-2">
          <form method="GET" class="flex flex-col sm:flex-row sm:items-center gap-2">
            <input type="hidden" name="tab" value="logins" />
            <input name="q" value="{{ $filters['q'] ?? '' }}"
                   class="h-10 w-64 rounded-xl border border-gray-200 bg-white px-3"
                   placeholder="Search IP / device…">

            <input type="date" name="from" value="{{ $filters['from'] ?? '' }}"
                   class="h-10 rounded-xl border border-gray-200 bg-white px-3" />
            <input type="date" name="to" value="{{ $filters['to'] ?? '' }}"
                   class="h-10 rounded-xl border border-gray-200 bg-white px-3" />

            <button class="h-10 px-4 rounded-xl border bg-white hover:bg-gray-50">Filter</button>

            @if(request()->hasAny(['q','from','to']))
              <a href="{{ route('settings.index', ['tab'=>'logins']) }}" class="h-10 px-3 rounded-xl border hover:bg-gray-50">Reset</a>
            @endif
          </form>
        </div>
      </div>

      @if(($logins->count() ?? 0) === 0)
        <div class="bg-white rounded-2xl border shadow-sm p-10 grid place-items-center text-gray-500">
          <div class="text-center">
            <div class="mx-auto w-10 h-10 border-2 border-dashed border-gray-300 rounded-full"></div>
            <div class="mt-2 text-[13px]">No login activity yet</div>
            <div class="text-[12px]">Your sign-ins will appear here.</div>
          </div>
        </div>
      @else
        <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full text-[14px]">
              <thead class="bg-gray-50 text-gray-600">
                <tr>
                  <th class="px-4 py-3 text-left">When</th>
                  <th class="px-4 py-3 text-left">IP</th>
                  <th class="px-4 py-3 text-left">Device / Browser</th>
                </tr>
              </thead>
              <tbody class="divide-y">
              @foreach($logins as $l)
                @php
                  $ua = (string)($l->user_agent ?? '');
                  // quick, readable UA parsing (very light; no package)
                  $isMobile = str_contains(strtolower($ua), 'mobile') || str_contains(strtolower($ua), 'iphone') || str_contains(strtolower($ua), 'android');
                  $deviceBadge = $isMobile
                    ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                    : 'bg-indigo-50 text-indigo-700 border-indigo-200';
                @endphp
                <tr class="hover:bg-gray-50/60">
                  <td class="px-4 py-3">
                    <div class="text-[13px]">{{ $l->logged_in_at?->diffForHumans() ?? '—' }}</div>
                    <div class="text-[12px] text-gray-500">{{ $l->logged_in_at?->format('Y-m-d H:i') }}</div>
                  </td>
                  <td class="px-4 py-3 font-mono">{{ $l->ip ?? '—' }}</td>
                  <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                      <span class="px-2 py-0.5 text-[11px] rounded-full border {{ $deviceBadge }}">
                        {{ $isMobile ? 'Mobile' : 'Desktop' }}
                      </span>
                      <span class="text-[12px] text-gray-600 truncate max-w-[520px]" title="{{ $ua }}">{{ $ua ?: '—' }}</span>
                    </div>
                  </td>
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>

          <div class="px-4 py-3 border-t">
            {{ $logins->withQueryString()->links() }}
          </div>
        </div>
      @endif

   @endcan

@endif


    </div>
  </div>
@endsection
