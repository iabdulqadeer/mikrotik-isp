<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Campaign;

class CampaignPolicy
{
    public function viewAny(User $u): bool { return $u->can('campaigns.manage'); }
    public function view(User $u, Campaign $c): bool { return $u->can('campaigns.manage'); }
    public function create(User $u): bool { return $u->can('campaigns.manage'); }
    public function update(User $u, Campaign $c): bool { return $u->can('campaigns.manage'); }
    public function delete(User $u, Campaign $c): bool { return $u->can('campaigns.manage'); }
}
