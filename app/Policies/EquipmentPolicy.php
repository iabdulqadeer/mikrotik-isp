<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Equipment;

class EquipmentPolicy
{
    public function viewAny(User $u): bool { return $u->can('equipment.view'); }
    public function view(User $u, Equipment $e): bool { return $u->can('equipment.view'); }
    public function create(User $u): bool { return $u->can('equipment.create'); }
    public function update(User $u, Equipment $e): bool { return $u->can('equipment.update'); }
    public function delete(User $u, Equipment $e): bool { return $u->can('equipment.delete'); }
    public function restore(User $u, Equipment $e): bool { return $u->can('equipment.delete'); }
    public function forceDelete(User $u, Equipment $e): bool { return false; }
}
