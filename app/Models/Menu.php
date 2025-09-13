<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Role;

class Menu extends Model
{
    protected $fillable = [
        'label','icon','route_name','url','permission','parent_id','sort_order','is_active'
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'menu_role');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('sort_order');
    }

    public static function treeForUser($user)
{
    $isAdmin = $user?->hasRole('admin');

    // Always get ALL top-level active items + active children (ordered)
    $items = static::query()
        ->whereNull('parent_id')
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->with([
            'children' => fn($q) => $q->where('is_active', true)->orderBy('sort_order'),
        ])
        ->get();

    // === Admin: show everything, ignore role/permission completely ===
    if ($isAdmin) {
        foreach ($items as $item) {
            // Attach children as a *relation* so Blade always sees it
            $item->setRelation('visible_children', $item->children);
        }
        return $items->values();
    }

    // === Non-admins: apply role + permission filters ===
    // Lazy-load roles only when we need them
    $items->loadMissing(['roles:id,name', 'children.roles:id,name']);

    $userRoleNames = ($user?->getRoleNames() ?? collect())->values();

    $allowed = function ($menu) use ($user, $userRoleNames) {
        $okRole = $menu->roles->isEmpty()
            || $userRoleNames->intersect($menu->roles->pluck('name'))->isNotEmpty();

        $okPerm = !$menu->permission || ($user && $user->can($menu->permission));

        return $okRole && $okPerm;
    };

    $visible = $items->filter($allowed)->values();

    foreach ($visible as $item) {
        $item->setRelation(
            'visible_children',
            $item->children->filter($allowed)->values()
        );
    }

    return $visible;
}

}
