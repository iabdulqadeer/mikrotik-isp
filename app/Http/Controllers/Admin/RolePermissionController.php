<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionController extends Controller
{
    public function __construct()
    {
        // Admin-only screen to edit role permissions
        $this->middleware(['permission:roles.edit']);
    }

    public function index(Request $request)
    {
        $guard = config('permission.defaults.guard_name') ?? 'web';

        // Eager-load permissions to avoid N+1
        $roles = Role::query()
            ->where('guard_name', $guard)
            ->orderBy('name')
            ->with(['permissions:id,name']) // only what we need
            ->get();

        // All permissions (grouped by prefix before the first dot if present)
        $allPerms = Permission::query()
            ->where('guard_name', $guard)
            ->orderBy('name')
            ->get();

        $grouped = [];
        foreach ($allPerms as $p) {
            $parts = explode('.', $p->name, 2);
            $group = count($parts) > 1 ? $parts[0] : 'general';
            $grouped[$group][] = $p;
        }
        ksort($grouped);

        // Build assignment matrix role_id => [permission_id,...]
        $assigned = [];
        foreach ($roles as $r) {
            $assigned[$r->id] = $r->permissions->pluck('id')->all();
        }

        return view('system-users.roles.index', [
            'roles'      => $roles,
            'grouped'    => $grouped,
            'assigned'   => $assigned,
            'totalPerms' => $allPerms->count(),
        ]);
    }

public function sync(Request $request)
{
    $guard = config('permission.defaults.guard_name') ?? 'web';

    // Validate; allow blanks from hidden inputs
    $data = $request->validate([
        'permissions'     => ['array'],
        'permissions.*'   => ['array'],
        'permissions.*.*' => ['nullable','integer'],
    ]);

    $payload = $data['permissions'] ?? [];

    DB::transaction(function () use ($payload, $guard) {
        // ✅ Iterate ALL roles for the guard, not just submitted keys
        $roles = Role::query()
            ->where('guard_name', $guard)
            ->get();

        foreach ($roles as $role) {
            // Use submitted list for this role, or empty if none submitted
            $permIdsRaw = $payload[$role->id] ?? [];

            // Normalize: keep only numeric ints
            $permIds = collect($permIdsRaw)
                ->filter(fn($v) => is_numeric($v))
                ->map(fn($v) => (int) $v)
                ->unique()
                ->values()
                ->all();

            // Optional safeguard: don't allow stripping admin completely
            if (strcasecmp($role->name, 'admin') === 0 && empty($permIds)) {
                continue;
            }

            // Resolve only valid permissions on the same guard
            $perms = Permission::query()
                ->whereIn('id', $permIds)
                ->where('guard_name', $role->guard_name)
                ->get();

            $role->syncPermissions($perms);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    });

    return redirect()
        ->route('systemusers.roles.index')
        ->with('status', 'Roles updated successfully.');
}


}
