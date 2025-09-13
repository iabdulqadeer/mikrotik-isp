<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{

    public function __construct()
    {
        // Page shell
        $this->middleware('permission:settings.general.view')->only(['index']);

        // Saves per tab
        $this->middleware('permission:settings.general.update')->only(['saveGeneral']);
        $this->middleware('permission:settings.branding.update')->only(['saveBranding']);
        $this->middleware('permission:settings.password.update')->only(['savePassword']);
        $this->middleware('permission:settings.payments.update')->only(['savePayments']);
        $this->middleware('permission:settings.pppoe.update')->only(['savePPPoE']);
        $this->middleware('permission:settings.hotspot.update')->only(['saveHotspot']);
        $this->middleware('permission:settings.sms.update')->only(['saveSMS']);
        $this->middleware('permission:settings.email.update')->only(['saveEmail']);
        $this->middleware('permission:settings.notifications.update')->only(['saveNotifications']);
    }

    public function index(Request $r)
    {
        $u   = $r->user();
        $tab = $r->query('tab', 'general');

        $settings = $u->settingMany([
            // Branding
            'branding.logo','branding.website','branding.desc',
            'branding.color_primary','branding.color_secondary',
            'branding.show_wifi_logo','branding.show_invoice_logo','branding.show_voucher_logo',
            'branding.business_name','branding.support_phone','branding.support_email',
            'branding.commission_rate',

            // Payments
            'pay.mtn.enabled','pay.mtn.key',
            'pay.airtel.enabled','pay.airtel.key',
            'pay.mpesa.enabled','pay.mpesa.key',
            'pay.tigo.enabled','pay.tigo.key',

            // PPPoE
            'pppoe.enabled','pppoe.ip','pppoe.dns','pppoe.secret',

            // Hotspot
            'hotspot.username_prefix','hotspot.template','hotspot.bg',
            'hotspot.prune_days','hotspot.redirect_url','hotspot.instructions',

            // SMS
            'sms.enabled','sms.provider','sms.key','sms.secret','sms.sender_id',
            'sms.tpl.voucher','sms.tpl.payment',

            // Email
            'mail.host','mail.port','mail.tls','mail.username','mail.password',
            'mail.from_email','mail.from_name',
            'mail.tpl.voucher','mail.tpl.payment',

            // Notifications
            'notify.email','notify.sms','notify.push',
            'notify.type.payment','notify.type.voucher',
            'notify.low.enabled','notify.low.threshold',
        ], [
            'branding.color_primary' => '#FFB001',
            'branding.color_secondary' => '#010160',
            'hotspot.template' => 'Simple',
            'hotspot.prune_days' => 7,
            'notify.low.threshold' => 1000,
        ]);


        if ($tab === 'logins') {
            $q      = trim((string) $r->get('q', ''));
            $from   = $r->get('from'); // Y-m-d
            $to     = $r->get('to');   // Y-m-d

            $logins = $r->user()->logins()->when($q, function ($query) use ($q) {
                    $query->where('ip', 'like', "%{$q}%")
                          ->orWhere('user_agent', 'like', "%{$q}%");
                })->when($from, function ($query) use ($from) {
                    $query->whereDate('logged_in_at', '>=', $from);
                })->when($to, function ($query) use ($to) {
                    $query->whereDate('logged_in_at', '<=', $to);
                })
                ->orderByDesc('logged_in_at')
                ->paginate(15);

            $logins  = $logins;
            $filters = ['q' => $q, 'from' => $from, 'to' => $to];

            return view('settings.index', compact('u','tab','settings','logins','filters'));
        }

        return view('settings.index', compact('u','tab','settings'));
    }

    /* ---------- GENERAL ---------- */
    public function saveGeneral(Request $r)
    {
        $u = $r->user();
        $data = $r->validate([
            'first_name'  => 'required|string|max:100',
            'last_name'   => 'required|string|max:100',
            'email'       => 'required|email|max:255',
            'phone'       => 'nullable|string|max:50',
            'whatsapp'    => 'nullable|string|max:50',
            'business_address'     => 'nullable|string|max:500',
            'country'     => 'nullable|string|max:5',
            'customer_care'=> 'nullable|string|max:50',
        ]);

        // Persist to users table (per requirements)
        $u->fill($data)->save();

        return to_route('settings.index', ['tab'=>'general'])->with('ok','General settings updated.');
    }

    /* ---------- BRANDING ---------- */
    public function saveBranding(Request $r)
    {
        $u = $r->user();
        $v = $r->validate([
            'website'        => 'nullable|url|max:255',
            'desc'           => 'nullable|string|max:1000',
            'color_primary'  => 'required|string|max:9',
            'color_secondary'=> 'required|string|max:9',
            'show_wifi_logo' => 'sometimes|boolean',
            'show_invoice_logo' => 'sometimes|boolean',
            'show_voucher_logo' => 'sometimes|boolean',
            'business_name'  => 'nullable|string|max:150',
            'support_phone'  => 'nullable|string|max:50',
            'support_email'  => 'nullable|email|max:255',
            'commission_rate'=> 'nullable|numeric|min:0|max:100',
            'logo'           => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $logoPath = $u->settingGet('branding.logo');

        if ($r->hasFile('logo')) {
            if ($logoPath) Storage::disk('public')->delete($logoPath);
            $logoPath = $r->file('logo')->store("users/{$u->id}/brand", 'public');
        }

        $u->settingPutMany([
            'branding.logo'           => $logoPath,
            'branding.website'        => $v['website'] ?? null,
            'branding.desc'           => $v['desc'] ?? null,
            'branding.color_primary'  => $v['color_primary'],
            'branding.color_secondary'=> $v['color_secondary'],
            'branding.show_wifi_logo' => (bool)($v['show_wifi_logo'] ?? false),
            'branding.show_invoice_logo' => (bool)($v['show_invoice_logo'] ?? false),
            'branding.show_voucher_logo' => (bool)($v['show_voucher_logo'] ?? false),
            'branding.business_name'  => $v['business_name'] ?? null,
            'branding.support_phone'  => $v['support_phone'] ?? null,
            'branding.support_email'  => $v['support_email'] ?? null,
            'branding.commission_rate'=> (float)($v['commission_rate'] ?? 0),
        ]);

        return to_route('settings.index', ['tab'=>'branding'])->with('ok','Branding saved.');
    }

    /* ---------- PASSWORD ---------- */
    public function savePassword(Request $r)
    {
        $u = $r->user();
        $v = $r->validate([
            'current_password' => 'required|string',
            'password' => ['required','confirmed', Password::min(6)],
        ]);

        if (! Hash::check($v['current_password'], $u->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $u->forceFill(['password' => Hash::make($v['password'])])->save();
        return to_route('settings.index', ['tab'=>'password'])->with('ok','Password updated.');
    }

    /* ---------- PAYMENTS ---------- */
    public function savePayments(Request $r)
    {
        $u = $r->user();
        $v = $r->validate([
            'mtn_enabled'   => 'sometimes|boolean', 'mtn_key'   => 'nullable|string|max:255',
            'airtel_enabled'=> 'sometimes|boolean', 'airtel_key'=> 'nullable|string|max:255',
            'mpesa_enabled' => 'sometimes|boolean', 'mpesa_key' => 'nullable|string|max:255',
            'tigo_enabled'  => 'sometimes|boolean', 'tigo_key'  => 'nullable|string|max:255',
        ]);
        $u->settingPutMany([
            'pay.mtn.enabled'    => (bool)($v['mtn_enabled'] ?? false),
            'pay.mtn.key'        => $v['mtn_key'] ?? null,
            'pay.airtel.enabled' => (bool)($v['airtel_enabled'] ?? false),
            'pay.airtel.key'     => $v['airtel_key'] ?? null,
            'pay.mpesa.enabled'  => (bool)($v['mpesa_enabled'] ?? false),
            'pay.mpesa.key'      => $v['mpesa_key'] ?? null,
            'pay.tigo.enabled'   => (bool)($v['tigo_enabled'] ?? false),
            'pay.tigo.key'       => $v['tigo_key'] ?? null,
        ]);
        return to_route('settings.index', ['tab'=>'payments'])->with('ok','Payment methods saved.');
    }

    /* ---------- PPPOE ---------- */
    public function savePPPoE(Request $r)
    {
        $u = $r->user();
        $v = $r->validate([
            'enabled' => 'sometimes|boolean',
            'ip'      => 'nullable|ip',
            'dns'     => 'nullable|string|max:50',
            'secret'  => 'nullable|string|max:255',
        ]);
        $u->settingPutMany([
            'pppoe.enabled' => (bool)($v['enabled'] ?? false),
            'pppoe.ip'      => $v['ip'] ?? null,
            'pppoe.dns'     => $v['dns'] ?? null,
            'pppoe.secret'  => $v['secret'] ?? null,
        ]);
        return to_route('settings.index', ['tab'=>'pppoe'])->with('ok','PPPoE saved.');
    }

    /* ---------- HOTSPOT ---------- */
    public function saveHotspot(Request $r)
    {
        $u = $r->user();
        $v = $r->validate([
            'username_prefix' => 'required|string|max:10',
            'template'        => 'required|in:Simple,Modern',
            'bg'              => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'prune_days'      => 'required|integer|min:1|max:365',
            'redirect_url'    => 'required|url|max:255',
            'instructions'    => 'nullable|string|max:2000',
        ]);

        $bg = $u->settingGet('hotspot.bg');
        if ($r->hasFile('bg')) {
            if ($bg) Storage::disk('public')->delete($bg);
            $bg = $r->file('bg')->store("users/{$u->id}/hotspot", 'public');
        }

        $u->settingPutMany([
            'hotspot.username_prefix' => $v['username_prefix'],
            'hotspot.template'        => $v['template'],
            'hotspot.bg'              => $bg,
            'hotspot.prune_days'      => (int)$v['prune_days'],
            'hotspot.redirect_url'    => $v['redirect_url'],
            'hotspot.instructions'    => $v['instructions'] ?? null,
        ]);

        return to_route('settings.index', ['tab'=>'hotspot'])->with('ok','Hotspot saved.');
    }

    /* ---------- SMS ---------- */
    public function saveSMS(Request $r)
    {
        $u = $r->user();
        $v = $r->validate([
            'enabled'   => 'sometimes|boolean',
            'provider'  => 'nullable|string|max:100',
            'key'       => 'nullable|string|max:255',
            'secret'    => 'nullable|string|max:255',
            'sender_id' => 'nullable|string|max:11',
            'tpl_voucher'=> 'nullable|string|max:1000',
            'tpl_payment'=> 'nullable|string|max:1000',
        ]);
        $u->settingPutMany([
            'sms.enabled'     => (bool)($v['enabled'] ?? false),
            'sms.provider'    => $v['provider'] ?? null,
            'sms.key'         => $v['key'] ?? null,
            'sms.secret'      => $v['secret'] ?? null,
            'sms.sender_id'   => $v['sender_id'] ?? null,
            'sms.tpl.voucher' => $v['tpl_voucher'] ?? null,
            'sms.tpl.payment' => $v['tpl_payment'] ?? null,
        ]);
        return to_route('settings.index', ['tab'=>'sms'])->with('ok','SMS saved.');
    }

    /* ---------- EMAIL ---------- */
    public function saveEmail(Request $r)
    {
        $u = $r->user();
        $v = $r->validate([
            'host'      => 'nullable|string|max:255',
            'port'      => 'nullable|integer',
            'tls'       => 'sometimes|boolean',
            'username'  => 'nullable|string|max:255',
            'password'  => 'nullable|string|max:255',
            'from_email'=> 'nullable|email|max:255',
            'from_name' => 'nullable|string|max:150',
            'tpl_voucher'=> 'nullable|string|max:2000',
            'tpl_payment'=> 'nullable|string|max:2000',
        ]);
        $u->settingPutMany([
            'mail.host'       => $v['host'] ?? null,
            'mail.port'       => (int)($v['port'] ?? 587),
            'mail.tls'        => (bool)($v['tls'] ?? true),
            'mail.username'   => $v['username'] ?? null,
            'mail.password'   => $v['password'] ?? null,
            'mail.from_email' => $v['from_email'] ?? null,
            'mail.from_name'  => $v['from_name'] ?? null,
            'mail.tpl.voucher'=> $v['tpl_voucher'] ?? null,
            'mail.tpl.payment'=> $v['tpl_payment'] ?? null,
        ]);
        return to_route('settings.index', ['tab'=>'email'])->with('ok','Email/SMTP saved.');
    }

    /* ---------- NOTIFICATIONS ---------- */
    public function saveNotifications(Request $r)
    {
        $u = $r->user();
        $v = $r->validate([
            'notify_email' => 'sometimes|boolean',
            'notify_sms'   => 'sometimes|boolean',
            'notify_push'  => 'sometimes|boolean',
            'notify_payment' => 'sometimes|boolean',
            'notify_voucher' => 'sometimes|boolean',
            'low_enabled'  => 'sometimes|boolean',
            'low_threshold'=> 'nullable|numeric|min:0',
        ]);
        $u->settingPutMany([
            'notify.email'  => (bool)($v['notify_email'] ?? false),
            'notify.sms'    => (bool)($v['notify_sms'] ?? false),
            'notify.push'   => (bool)($v['notify_push'] ?? false),
            'notify.type.payment' => (bool)($v['notify_payment'] ?? false),
            'notify.type.voucher' => (bool)($v['notify_voucher'] ?? false),
            'notify.low.enabled'  => (bool)($v['low_enabled'] ?? false),
            'notify.low.threshold'=> (float)($v['low_threshold'] ?? 0),
        ]);
        return to_route('settings.index', ['tab'=>'notifications'])->with('ok','Notifications saved.');
    }
}
