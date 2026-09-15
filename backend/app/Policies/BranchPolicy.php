<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\Branch;

class BranchPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Branch $branch): bool
    {
        return true;
    }

    /**
     * Branch Management: Restricted exclusively to admin.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Branch $branch): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Branch $branch): bool
    {
        return $user->isAdmin();
    }
}
