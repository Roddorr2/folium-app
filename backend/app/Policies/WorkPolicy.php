<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\Work;

class WorkPolicy
{
    /**
     * Public catalog access - anyone can view catalog works.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Work $work): bool
    {
        return true;
    }

    /**
     * WEMI Catalog Management: Restricted to admin & cataloger (RF3.2).
     */
    public function create(User $user): bool
    {
        return $user->isCataloger();
    }

    public function update(User $user, Work $work): bool
    {
        return $user->isCataloger();
    }

    public function delete(User $user, Work $work): bool
    {
        return $user->isCataloger();
    }
}
