<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;

class ExpensePolicy
{
    public function viewAny(User $user): bool { return $user->can('expenses.view'); }
    public function view(User $user, Expense $expense): bool { return $user->can('expenses.view') && $expense->user_id === $user->id; }
    public function create(User $user): bool { return $user->can('expenses.create'); }
    public function update(User $user, Expense $expense): bool { return $user->can('expenses.update') && $expense->user_id === $user->id; }
    public function delete(User $user, Expense $expense): bool { return $user->can('expenses.delete') && $expense->user_id === $user->id; }
}
