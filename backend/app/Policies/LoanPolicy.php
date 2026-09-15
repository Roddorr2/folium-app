<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\Loan;

class LoanPolicy
{
    /**
     * Issue loan (checkout): Requires admin or librarian role.
     * IDOR Prevention: Enforces branch scoping so local librarians can only operate on items from their assigned branch.
     */
    public function issueLoan(User $user, int|string $targetBranchId): bool
    {
        if ($user->isAdmin() || $user->isNetworkLibrarian()) {
            return true;
        }

        if ($user->isLibrarian()) {
            return (int) $user->branch_id === (int) $targetBranchId;
        }

        return false;
    }

    /**
     * Process return: Requires admin or librarian role.
     * Enforces receiving branch scoping for local librarians.
     */
    public function returnLoan(User $user, int|string $receivingBranchId): bool
    {
        if ($user->isAdmin() || $user->isNetworkLibrarian()) {
            return true;
        }

        if ($user->isLibrarian()) {
            return (int) $user->branch_id === (int) $receivingBranchId;
        }

        return false;
    }
}
