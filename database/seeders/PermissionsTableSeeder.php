<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsTableSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cache to avoid stale permission maps
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guard = config('auth.defaults.guard', 'web');

        /*
        |--------------------------------------------------------------------------
        | Permissions extracted from routes/web.php
        |--------------------------------------------------------------------------
        | ONLY permissions that appear inside ->middleware('permission:...') are included.
        | (Combined pipes like A|B|C are split into separate permission names.)
        |--------------------------------------------------------------------------
        */

        $permissions = [

            // ==== Admin: Roles/Permissions UI ====
            'roles.edit',

            // ==== Admin: System Users (note: routes use "users.*" permissions) ====
            'users.view',
            'users.create',
            'users.edit',
            'users.password',
            'users.token',
            'users.active',

            // ==== Emails ====
            'emails.view',
            'emails.create',
            'emails.delete',

            // ==== Expenses ====
            'expenses.view',
            'expenses.create',
            'expenses.update',
            'expenses.delete',

            // ==== Leads ====
            'leads.export',
            'leads.bulk_delete',
            'leads.view',
            'leads.create',
            'leads.edit',
            'leads.delete',

            // ==== Campaigns ====
            'campaigns.list',
            'campaigns.create',
            'campaigns.view',
            'campaigns.update',
            'campaigns.delete',

            // ==== Equipment ====
            'equipment.view',
            'equipment.create',
            'equipment.edit',
            'equipment.delete',

            // ==== SMS ====
            'sms.list',
            'sms.create',
            'sms.view',
            'sms.delete',

            // ==== Dashboard ====
            'dashboard.view',

            // ==== Profile ====
            'profile.manage',

            // ==== Devices (resource + extras) ====
            'devices.view',
            'devices.create',
            'devices.edit',
            'devices.delete',
            'devices.test',
            'devices.provision',

            // ==== Plans ====
            'plans.view',
            'plans.create',
            'plans.update',
            'plans.delete',

            // ==== Users (regular user management area) ====
            'users.list',
            'users.update',
            'users.delete',
            'users.impersonate',

            // ==== Vouchers ====
            'vouchers.export',
            'vouchers.revoke',
            'vouchers.delete', // also part of export|revoke|delete pipe
            'vouchers.print',
            'vouchers.list',
            'vouchers.create',
            'vouchers.view',
            'vouchers.update',

            // ==== Invoices ====
            'invoices.list',
            'invoices.view',
            'invoices.create',
            'invoices.update',

            // ==== Tickets (+ messages secured by tickets.update) ====
            'tickets.list',
            'tickets.create',
            'tickets.view',
            'tickets.update',
            'tickets.delete',

            // ==== Settings (granular) ====
            'settings.general.view',
            'settings.general.update',
            'settings.branding.update',
            'settings.password.update',
            'settings.payments.update',
            'settings.pppoe.update',
            'settings.hotspot.update',
            'settings.sms.update',
            'settings.email.update',
            'settings.notifications.update',

            // ==== Subscriptions & Billing ====
            'subscriptions.view',
            'subscriptions.subscribe',
            'subscriptions.swap',
            'subscriptions.cancel',
            'subscriptions.resume',
            'subscriptions.billing_portal',
            'subscriptions.view_invoices',

            // ==== User Notifications (role:user area) ====
            'notifications.view',
            'notifications.mark_read',
            'notifications.mark_all',
        ];

        // Create all permissions if missing
        foreach (array_unique($permissions) as $name) {
            Permission::firstOrCreate([
                'name'       => $name,
                'guard_name' => $guard,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Roles -> permission bundles
        |--------------------------------------------------------------------------
        | Tailored to the permissions actually present in web.php.
        | Adjust as needed for your org.
        |--------------------------------------------------------------------------
        */

        $roles = [

            // Full access
            'admin' => $permissions,

            // Ops: daily operations, devices, vouchers, tickets, read-only finance & plans
            'operator' => [
                'dashboard.view',

                // Devices
                'devices.view','devices.create','devices.edit','devices.delete','devices.test','devices.provision',

                // Vouchers
                'vouchers.list','vouchers.view','vouchers.create','vouchers.update','vouchers.delete','vouchers.export','vouchers.revoke','vouchers.print',

                // Tickets
                'tickets.list','tickets.view','tickets.create','tickets.update',

                // Users (limited)
                'users.list','users.view',

                // Plans & Invoices (read)
                'plans.view',
                'invoices.list','invoices.view',

                // Leads (view/export)
                'leads.view','leads.export',

                // Settings page (view only)
                'settings.general.view',
            ],

            // Support: tickets first, some vouchers, read-most
            'support' => [
                'dashboard.view',

                // Tickets
                'tickets.list','tickets.view','tickets.create','tickets.update',

                // Vouchers (list/print/view)
                'vouchers.list','vouchers.view','vouchers.print',

                // Users (read)
                'users.list','users.view',

                // Plans & Invoices (read)
                'plans.view',
                'invoices.list','invoices.view',

                // Settings (view)
                'settings.general.view',
            ],

            // Accounting: invoices + billing portal + plans (read), minimal others
            'accountant' => [
                'dashboard.view',

                // Invoices
                'invoices.list','invoices.view','invoices.create','invoices.update',

                // Billing/Subscriptions (view + invoices + portal)
                'subscriptions.view',
                'subscriptions.billing_portal',
                'subscriptions.view_invoices',

                // Plans (read)
                'plans.view',

                // Settings (view)
                'settings.general.view',
            ],

            // Read-only across major modules
            'viewer' => [
                'dashboard.view',

                'users.list','users.view',
                'plans.view',
                'vouchers.list','vouchers.view','vouchers.print',
                'invoices.list','invoices.view',
                'tickets.list','tickets.view',
                'leads.view',
                'emails.view',
                'expenses.view',
                'campaigns.view',
                'equipment.view',
                'sms.view',
                'settings.general.view',
                'notifications.view',
            ],

            // End user (portal-style)
            'user' => [
                'dashboard.view',

                // Users (read-only self-lists if applicable)
                'users.list','users.view',

                // Tickets (read-only)
                'tickets.list','tickets.view',

                // Finance
                'plans.view',
                'invoices.list','invoices.view',
                'vouchers.list','vouchers.view','vouchers.print',

                // Notifications
                'notifications.view','notifications.mark_read','notifications.mark_all',
            ],
        ];

        foreach ($roles as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => $guard]);
            $role->syncPermissions($perms);
        }

        // Re-cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
