<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

use App\Http\Controllers\Auth\PhoneVerificationController;
use App\Http\Controllers\{
    ProfileController,
    DashboardController,
    DeviceController,
    PlanController,
    UserController,      // use this instead of CustomerController
    VoucherController,
    BillingController,
    TicketController,
    TicketMessageController,
    ProvisioningController,
    SubscriptionController,
    WebhookController,
    SettingsController,
    UtilityController,
    TrialController,
    TwoFactorController,
    UserNotificationController,
    LeadController,
    EquipmentController,
    ExpenseController,
    SmsController,
    EmailController,
    CampaignController
};
use App\Http\Controllers\Admin\{
    SystemUserController,
    RolePermissionController
};
/*
|--------------------------------------------------------------------------
| Common middleware stacks
|--------------------------------------------------------------------------
*/
$secure     = ['auth','email_phone_verified','2fa'];
$userSecure = ['auth','role:user','email_phone_verified','2fa'];

/*
|--------------------------------------------------------------------------
| Admin: System Users
|--------------------------------------------------------------------------
*/

Route::middleware(['auth','role:admin'])
    ->prefix('system-users')
    ->name('systemusers.')
    ->group(function () {
        // Manage Roles UI
        Route::get('/roles', [RolePermissionController::class, 'index'])
            ->middleware('permission:roles.edit')
            ->name('roles.index');

        Route::put('/roles/sync', [RolePermissionController::class, 'sync'])
            ->middleware('permission:roles.edit')
            ->name('roles.sync');

        // System Users
        Route::get('/', [SystemUserController::class, 'index'])
            ->middleware('permission:users.view')
            ->name('index');

        Route::get('/create', [SystemUserController::class, 'create'])
            ->middleware('permission:users.create')
            ->name('create');

        Route::post('/', [SystemUserController::class, 'store'])
            ->middleware('permission:users.create')
            ->name('store');

        Route::get('/{user}', [SystemUserController::class, 'show'])
            ->middleware('permission:users.view')
            ->name('show');

        Route::get('/{user}/edit', [SystemUserController::class, 'edit'])
            ->middleware('permission:users.edit')
            ->name('edit');

        Route::put('/{user}', [SystemUserController::class, 'update'])
            ->middleware('permission:users.edit')
            ->name('update');

        Route::post('/{user}/password', [SystemUserController::class, 'changePassword'])
            ->middleware('permission:users.password')
            ->name('password');

        Route::post('/{user}/token', [SystemUserController::class, 'generateToken'])
            ->middleware('permission:users.token')
            ->name('token');
    });



/*
|--------------------------------------------------------------------------
| Emails module (scoped)
|--------------------------------------------------------------------------
*/
Route::middleware($secure)->prefix('emails')->name('emails.')->group(function () {
    Route::get('/',            [EmailController::class, 'index'])->middleware('permission:emails.view')->name('index');
    Route::get('/create',      [EmailController::class, 'create'])->middleware('permission:emails.create')->name('create');
    Route::post('/',           [EmailController::class, 'store'])->middleware('permission:emails.create')->name('store');
    Route::get('/{email}',     [EmailController::class, 'show'])->middleware('permission:emails.view')->name('show');
    Route::delete('/{email}',  [EmailController::class, 'destroy'])->middleware('permission:emails.delete')->name('destroy');
});

/*
|--------------------------------------------------------------------------
| Expenses
|--------------------------------------------------------------------------
*/
Route::middleware($secure)->group(function () {
    Route::resource('expenses', ExpenseController::class)->names('expenses')->middleware([
        'index'   => 'permission:expenses.view',
        'show'    => 'permission:expenses.view',
        'create'  => 'permission:expenses.create',
        'store'   => 'permission:expenses.create',
        'edit'    => 'permission:expenses.update',
        'update'  => 'permission:expenses.update',
        'destroy' => 'permission:expenses.delete',
    ]);
});

/*
|--------------------------------------------------------------------------
| Leads
|--------------------------------------------------------------------------
*/
Route::middleware($secure)->group(function () {
    // Extra actions
    Route::get('leads/export', [LeadController::class, 'export'])
        ->name('leads.export')
        ->middleware('permission:leads.export');

    Route::delete('leads/bulk-destroy', [LeadController::class, 'bulkDestroy'])
        ->name('leads.bulk-destroy')
        ->middleware('permission:leads.bulk_delete');

    // Resource routes with per-action permissions
    Route::resource('leads', LeadController::class)->names('leads')->middleware([
        'index'   => 'permission:leads.view',
        'show'    => 'permission:leads.view',
        'create'  => 'permission:leads.create',
        'store'   => 'permission:leads.create',
        'edit'    => 'permission:leads.edit',
        'update'  => 'permission:leads.edit',
        'destroy' => 'permission:leads.delete',
    ]);
});
/*
|--------------------------------------------------------------------------
| Campaigns
|--------------------------------------------------------------------------
*/
Route::middleware($secure)->group(function () {
    Route::get(   'campaigns',            [CampaignController::class, 'index'])->name('campaigns.index')->middleware('permission:campaigns.list');
    Route::get(   'campaigns/create',     [CampaignController::class, 'create'])->name('campaigns.create')->middleware('permission:campaigns.create');
    Route::post(  'campaigns',            [CampaignController::class, 'store'])->name('campaigns.store')->middleware('permission:campaigns.create');
    Route::get(   'campaigns/{campaign}', [CampaignController::class, 'show'])->name('campaigns.show')->middleware('permission:campaigns.view');
    Route::get(   'campaigns/{campaign}/edit',[CampaignController::class, 'edit'])->name('campaigns.edit')->middleware('permission:campaigns.update');
    Route::put(   'campaigns/{campaign}', [CampaignController::class, 'update'])->name('campaigns.update')->middleware('permission:campaigns.update');
    Route::delete('campaigns/{campaign}', [CampaignController::class, 'destroy'])->name('campaigns.destroy')->middleware('permission:campaigns.delete');
});
/*
|--------------------------------------------------------------------------
| Equipment
|--------------------------------------------------------------------------
*/
Route::middleware($secure)->group(function () {
    Route::resource('equipment', EquipmentController::class)->names('equipment')->middleware([
        'index'   => 'permission:equipment.view',
        'show'    => 'permission:equipment.view',
        'create'  => 'permission:equipment.create',
        'store'   => 'permission:equipment.create',
        'edit'    => 'permission:equipment.edit',
        'update'  => 'permission:equipment.edit',
        'destroy' => 'permission:equipment.delete',
    ]);
});

/*
|--------------------------------------------------------------------------
| SMS
|--------------------------------------------------------------------------
*/
Route::middleware($secure)->group(function () {
    Route::get(   '/sms',           [SmsController::class, 'index'])->name('sms.index')
        ->middleware('permission:sms.list');
    Route::get(   '/sms/create',    [SmsController::class, 'create'])->name('sms.create')
        ->middleware('permission:sms.create');
    Route::post(  '/sms',           [SmsController::class, 'store'])->name('sms.store')
        ->middleware('permission:sms.create');
    Route::get(   '/sms/{sms}',     [SmsController::class, 'show'])->name('sms.show')
        ->middleware('permission:sms.view');
    Route::delete('/sms/{sms}',     [SmsController::class, 'destroy'])->name('sms.destroy')
        ->middleware('permission:sms.delete');
});

// Twilio status webhook (public)
Route::post('/webhooks/twilio/status', [\App\Http\Controllers\Webhooks\TwilioWebhookController::class,'status'])
    ->name('webhooks.twilio.status');

/*
|--------------------------------------------------------------------------
| Email verification (Laravel default)
|--------------------------------------------------------------------------
*/
Route::get('/email/verify', fn () => view('auth.verify-email'))
    ->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return auth()->user()->hasVerifiedPhone()
        ? redirect()->route('dashboard')
        : redirect()->route('phone.verify.notice');
})->middleware(['auth','signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status','verification-link-sent');
})->middleware(['auth','throttle:6,1'])->name('verification.send');

/*
|--------------------------------------------------------------------------
| Phone verification
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/phone/verify',        [PhoneVerificationController::class,'notice'])->name('phone.verify.notice');
    Route::post('/phone/verify/send',  [PhoneVerificationController::class,'send'])->middleware('throttle:6,1')->name('phone.verify.send');
    Route::post('/phone/verify',       [PhoneVerificationController::class,'verify'])->middleware('throttle:6,1')->name('phone.verify.check');
});

/*
|--------------------------------------------------------------------------
| 2FA Security
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/security/2fa',                        [TwoFactorController::class, 'settings'])->name('2fa.settings');
    Route::post('/security/2fa/confirm',               [TwoFactorController::class, 'confirm'])->name('2fa.confirm');
    Route::post('/security/2fa/disable',               [TwoFactorController::class, 'disable'])->name('2fa.disable');
    Route::post('/security/2fa/recovery/regenerate',   [TwoFactorController::class, 'regenerateRecovery'])->name('2fa.recovery.regen');
    Route::get('/security/2fa/recovery/download',      [TwoFactorController::class, 'downloadRecovery'])->name('2fa.recovery.download');
    Route::get('/security/2fa/challenge',              [TwoFactorController::class, 'challengeView'])->name('2fa.challenge');
    Route::post('/security/2fa/challenge',             [TwoFactorController::class, 'challengeVerify'])->name('2fa.challenge.verify');
});

/*
|--------------------------------------------------------------------------
| Utilities
|--------------------------------------------------------------------------
*/
Route::middleware($secure)->group(function () {
    Route::post('/clear-cache', [UtilityController::class, 'clearCache'])
        ->name('utilities.clear-cache');
});

/*
|--------------------------------------------------------------------------
| Home / Dashboard
|--------------------------------------------------------------------------
*/
Route::redirect('/', '/login')->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(array_merge($secure, ['permission:dashboard.view']))
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Trial
|--------------------------------------------------------------------------
*/
Route::middleware($secure)->group(function () {
    Route::post('/trial/start', [TrialController::class, 'start'])->name('trial.start');
});

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/
Route::middleware(array_merge($secure, ['permission:profile.manage']))->group(function () {
    Route::get('/profile',   [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Provisioning (public script endpoint)
|--------------------------------------------------------------------------
*/
Route::get('provision.rsc', [ProvisioningController::class, 'rsc'])->name('provision.rsc');

/*
|--------------------------------------------------------------------------
| Devices
|--------------------------------------------------------------------------
*/

Route::middleware($secure)->group(function () {
    // Resourceful routes with per-action permission middleware
    Route::resource('devices', DeviceController::class)->names('devices')->middleware([
        'index'   => 'permission:devices.view',
        'show'    => 'permission:devices.view',
        'create'  => 'permission:devices.create',
        'store'   => 'permission:devices.create',
        'edit'    => 'permission:devices.edit',
        'update'  => 'permission:devices.edit',
        'destroy' => 'permission:devices.delete',
    ]);

    // Extra custom endpoints
    Route::post('devices/{device}/test', [DeviceController::class, 'test'])
        ->name('devices.test')
        ->middleware('permission:devices.test');

    Route::get('devices/{device}/provision-link', [DeviceController::class, 'provisionLink'])
        ->name('devices.provision-link')
        ->middleware('permission:devices.provision');
});

/*
|--------------------------------------------------------------------------
| Plans (explicit per-method permissions)
|--------------------------------------------------------------------------
*/
Route::middleware($secure)->group(function () {
    Route::get(   'plans',            [PlanController::class, 'index'])->name('plans.index')->middleware('permission:plans.view');
    Route::get(   'plans/create',     [PlanController::class, 'create'])->name('plans.create')->middleware('permission:plans.create');
    Route::post(  'plans',            [PlanController::class, 'store'])->name('plans.store')->middleware('permission:plans.create');
    Route::get(   'plans/{plan}',     [PlanController::class, 'show'])->name('plans.show')->middleware('permission:plans.view');
    Route::get(   'plans/{plan}/edit',[PlanController::class, 'edit'])->name('plans.edit')->middleware('permission:plans.update');
    Route::put(   'plans/{plan}',     [PlanController::class, 'update'])->name('plans.update')->middleware('permission:plans.update');
    Route::delete('plans/{plan}',     [PlanController::class, 'destroy'])->name('plans.destroy')->middleware('permission:plans.delete');
});

/*
|--------------------------------------------------------------------------
| Users (explicit per-method permissions)
|--------------------------------------------------------------------------
*/
Route::prefix('users')->name('users.')->middleware($secure)->group(function () {
    Route::get('',                [UserController::class,'index'])->name('index')->middleware('permission:users.list');
    Route::get('create',          [UserController::class,'create'])->name('create')->middleware('permission:users.create');
    Route::post('',               [UserController::class,'store'])->name('store')->middleware('permission:users.create');

    // static first (prevents collision with {user})
    Route::get('active',          [UserController::class,'activeIndex'])->name('active')->middleware('permission:users.active');

    // dynamic with constraint
    Route::get('{user}',          [UserController::class,'show'])->whereNumber('user')->name('show')->middleware('permission:users.view');
    Route::get('{user}/edit',     [UserController::class,'edit'])->whereNumber('user')->name('edit')->middleware('permission:users.update');
    Route::put('{user}',          [UserController::class,'update'])->whereNumber('user')->name('update')->middleware('permission:users.update');
    Route::delete('{user}',       [UserController::class,'destroy'])->whereNumber('user')->name('destroy')->middleware('permission:users.delete');

    Route::post('{user}/impersonate', [UserController::class,'impersonate'])->whereNumber('user')->name('impersonate')->middleware('permission:users.impersonate');
    Route::patch('{user}/toggle-active', [UserController::class,'toggleActive'])->whereNumber('user')->name('toggle-active')->middleware('permission:users.update');

    // no collision with {user}, so placing here is fine
    Route::post('impersonate/leave', [UserController::class,'leaveImpersonation'])->name('impersonate.leave');
});


/*
|--------------------------------------------------------------------------
| Vouchers (custom + explicit per-method permissions)
|--------------------------------------------------------------------------
*/
Route::middleware($secure)
  ->prefix('vouchers')
  ->name('vouchers.')
  ->group(function () {

    // Bulk accepts ANY of these actions (export | revoke | delete)
    Route::post('bulk',  [VoucherController::class, 'bulk'])
      ->name('bulk')
      ->middleware('permission:vouchers.export|vouchers.revoke|vouchers.delete');

    // Print sheet
    Route::post('print', [VoucherController::class, 'printSheet'])
      ->name('print')
      ->middleware('permission:vouchers.print');

    // CRUD/LIST/VIEW
    Route::get(   '/',               [VoucherController::class, 'index'])
      ->name('index')->middleware('permission:vouchers.list');

    Route::get(   '/create',         [VoucherController::class, 'create'])
      ->name('create')->middleware('permission:vouchers.create');

    Route::post(  '/',               [VoucherController::class, 'store'])
      ->name('store')->middleware('permission:vouchers.create');

    Route::get(   '/{voucher}',      [VoucherController::class, 'show'])
      ->name('show')->middleware('permission:vouchers.view');

    Route::get(   '/{voucher}/edit', [VoucherController::class, 'edit'])
      ->name('edit')->middleware('permission:vouchers.update');

    Route::put(   '/{voucher}',      [VoucherController::class, 'update'])
      ->name('update')->middleware('permission:vouchers.update');

    Route::delete('/{voucher}',      [VoucherController::class, 'destroy'])
      ->name('destroy')->middleware('permission:vouchers.delete');
});

/*
|--------------------------------------------------------------------------
| Invoices (explicit subset + permissions)
|--------------------------------------------------------------------------
*/
Route::middleware($secure)->group(function () {
    Route::get(   'invoices',           [BillingController::class, 'index'])->name('invoices.index')->middleware('permission:invoices.list');
    Route::get(   'invoices/{invoice}', [BillingController::class, 'show'])->name('invoices.show')->middleware('permission:invoices.view');
    Route::post(  'invoices',           [BillingController::class, 'store'])->name('invoices.store')->middleware('permission:invoices.create');
    Route::put(   'invoices/{invoice}', [BillingController::class, 'update'])->name('invoices.update')->middleware('permission:invoices.update');
});

/*
|--------------------------------------------------------------------------
| Tickets (explicit per-method permissions)
|--------------------------------------------------------------------------
*/
Route::middleware($secure)->group(function () {
    // Tickets CRUD
    Route::get(   'tickets',              [TicketController::class, 'index'])->name('tickets.index')->middleware('permission:tickets.list');
    Route::get(   'tickets/create',       [TicketController::class, 'create'])->name('tickets.create')->middleware('permission:tickets.create');
    Route::post(  'tickets',              [TicketController::class, 'store'])->name('tickets.store')->middleware('permission:tickets.create');
    Route::get(   'tickets/{ticket}',     [TicketController::class, 'show'])->name('tickets.show')->middleware('permission:tickets.view');
    Route::get(   'tickets/{ticket}/edit',[TicketController::class, 'edit'])->name('tickets.edit')->middleware('permission:tickets.update');
    Route::put(   'tickets/{ticket}',     [TicketController::class, 'update'])->name('tickets.update')->middleware('permission:tickets.update');
    Route::delete('tickets/{ticket}',     [TicketController::class, 'destroy'])->name('tickets.destroy')->middleware('permission:tickets.delete');

    // Ticket Messages
    Route::post(  'tickets/{ticket}/messages',           [TicketMessageController::class, 'store'])->name('tickets.messages.store')->middleware('permission:tickets.update');
    Route::delete('tickets/{ticket}/messages/{message}', [TicketMessageController::class, 'destroy'])->name('tickets.messages.destroy')->middleware('permission:tickets.update');
});

/*
|--------------------------------------------------------------------------
| Settings
|--------------------------------------------------------------------------
*/

Route::middleware($secure)
    ->prefix('settings')
    ->name('settings.')
    ->group(function () {
        // Page shell (shows tabs; content gated in Blade)
        Route::get('/', [SettingsController::class,'index'])
            ->middleware('permission:settings.general.view')
            ->name('index');

        // Saves per tab
        Route::post('/general',       [SettingsController::class,'saveGeneral'])
            ->middleware('permission:settings.general.update')->name('general.save');

        Route::post('/branding',      [SettingsController::class,'saveBranding'])
            ->middleware('permission:settings.branding.update')->name('branding.save');

        Route::post('/password',      [SettingsController::class,'savePassword'])
            ->middleware('permission:settings.password.update')->name('password.save');

        Route::post('/payments',      [SettingsController::class,'savePayments'])
            ->middleware('permission:settings.payments.update')->name('payments.save');

        Route::post('/pppoe',         [SettingsController::class,'savePPPoE'])
            ->middleware('permission:settings.pppoe.update')->name('pppoe.save');

        Route::post('/hotspot',       [SettingsController::class,'saveHotspot'])
            ->middleware('permission:settings.hotspot.update')->name('hotspot.save');

        Route::post('/sms',           [SettingsController::class,'saveSMS'])
            ->middleware('permission:settings.sms.update')->name('sms.save');

        Route::post('/email',         [SettingsController::class,'saveEmail'])
            ->middleware('permission:settings.email.update')->name('email.save');

        Route::post('/notifications', [SettingsController::class,'saveNotifications'])
            ->middleware('permission:settings.notifications.update')->name('notifications.save');
    });


/*
|--------------------------------------------------------------------------
| Subscriptions & billing
|--------------------------------------------------------------------------
*/
Route::middleware($secure)->group(function () {
    // Subscriptions
    Route::get('/subscription', [SubscriptionController::class,'index'])
        ->name('subscriptions.index')
        ->middleware('permission:subscriptions.view');

    Route::post('/subscription/checkout/{planPrice}', [SubscriptionController::class,'checkout'])
        ->name('subscriptions.checkout')
        ->middleware('permission:subscriptions.subscribe');

    Route::post('/subscription/swap/{planPrice}', [SubscriptionController::class,'swap'])
        ->name('subscriptions.swap')
        ->middleware('permission:subscriptions.swap');

    Route::post('/subscription/cancel', [SubscriptionController::class,'cancel'])
        ->name('subscriptions.cancel')
        ->middleware('permission:subscriptions.cancel');

    Route::post('/subscription/resume', [SubscriptionController::class,'resume'])
        ->name('subscriptions.resume')
        ->middleware('permission:subscriptions.resume');

    Route::get('/subscription/success', [SubscriptionController::class,'success'])
        ->name('subscriptions.success')
        ->middleware('permission:subscriptions.view');

    // Billing
    Route::get('/billing', [BillingController::class,'portal'])
        ->name('billing.portal')
        ->middleware('permission:subscriptions.billing_portal');

    Route::get('/billing/invoices', [BillingController::class,'invoices'])
        ->name('billing.invoices')
        ->middleware('permission:subscriptions.view_invoices');

    Route::get('/billing/invoices/{id}', [BillingController::class,'download'])
        ->name('billing.invoices.download')
        ->middleware('permission:subscriptions.view_invoices');
});

/*
|--------------------------------------------------------------------------
| User Notifications (role:user)
|--------------------------------------------------------------------------
*/
Route::middleware($userSecure)
    ->prefix('notifications')
    ->name('notifications.')
    ->group(function () {
        Route::get('/', [UserNotificationController::class, 'index'])
            ->middleware('permission:notifications.view')
            ->name('index');

        Route::get('{notification}', [UserNotificationController::class, 'show'])
            ->middleware('permission:notifications.view')
            ->name('show'); // marks read then redirects

        Route::post('{notification}/read', [UserNotificationController::class, 'markRead'])
            ->middleware('permission:notifications.mark_read')
            ->name('markRead');

        Route::post('read-all', [UserNotificationController::class, 'markAllRead'])
            ->middleware('permission:notifications.mark_all')
            ->name('markAllRead');
    });

/*
|--------------------------------------------------------------------------
| Webhooks
|--------------------------------------------------------------------------
*/
Route::post('/stripe/webhook', [WebhookController::class, 'handleWebhook'])->name('stripe.webhook');

/*
|--------------------------------------------------------------------------
| Auth scaffolding
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Fallback (404)
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    abort(404);
});

