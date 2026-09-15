<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class TransferPolicy
{
    /**
     * Initiate interlibrary transfer request.
     */
    public function initiate(User $user): bool
    {
        return $user->isAdmin() || $user->isNetworkLibrarian() || $user->isReader();
    }

    /**
     * Confirm receipt of item at destination branch.
     */
    public function receive(User $user, int|string $destinationBranchId): bool
    {
        if ($user->isAdmin() || $user->isNetworkLibrarian()) {
            return true;
        }

        if ($user->isLibrarian()) {
            return (int) $user->branch_id === (int) $destinationBranchId;
        }

        return false;
    }

    /**
     * Declare loss or transport incident (markAsLost).
     * Restricted to admin and network_librarian.
     */
    public function markAsLost(User $user): bool
    {
        return $user->isAdmin() || $user->isNetworkLibrarian();
    }
}
