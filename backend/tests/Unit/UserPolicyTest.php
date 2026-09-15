<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Policies\UserPolicy;
use PHPUnit\Framework\Attributes\Test;

class UserPolicyTest extends TestCase
{
    private UserPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new UserPolicy();
    }

    private function createUserWithRole(int $id, string $roleName): User
    {
        $role = new Role();
        $role->id = match ($roleName) {
            'admin' => 1,
            'cataloger' => 2,
            'librarian' => 3,
            'network_librarian' => 4,
            default => 5,
        };
        $role->name = $roleName;

        $user = new User();
        $user->id = $id;
        $user->name = "User {$id}";
        $user->email = "user{$id}@folium.test";
        $user->role_id = $role->id;
        $user->setRelation('role', $role);

        return $user;
    }

    #[Test]
    public function it_prevents_admin_from_deleting_themself(): void
    {
        $admin = $this->createUserWithRole(1, 'admin');

        $canDeleteSelf = $this->policy->delete($admin, $admin);

        $this->assertFalse($canDeleteSelf, 'Security Violation: Admin should never be allowed to delete their own active session user.');
    }

    #[Test]
    public function it_allows_admin_to_delete_another_user(): void
    {
        $admin = $this->createUserWithRole(1, 'admin');
        $targetUser = $this->createUserWithRole(2, 'cataloger');

        $canDeleteOther = $this->policy->delete($admin, $targetUser);

        $this->assertTrue($canDeleteOther, 'Admin must be authorized to delete other users in the system.');
    }

    #[Test]
    public function it_denies_non_admin_roles_from_deleting_users(): void
    {
        $cataloger = $this->createUserWithRole(2, 'cataloger');
        $librarian = $this->createUserWithRole(3, 'librarian');
        $reader = $this->createUserWithRole(5, 'reader');
        $targetUser = $this->createUserWithRole(4, 'network_librarian');

        $this->assertFalse($this->policy->delete($cataloger, $targetUser), 'Cataloger must not delete users.');
        $this->assertFalse($this->policy->delete($librarian, $targetUser), 'Librarian must not delete users.');
        $this->assertFalse($this->policy->delete($reader, $targetUser), 'Reader must not delete users.');
    }

    #[Test]
    public function it_restricts_view_any_and_create_exclusively_to_admin(): void
    {
        $admin = $this->createUserWithRole(1, 'admin');
        $cataloger = $this->createUserWithRole(2, 'cataloger');
        $librarian = $this->createUserWithRole(3, 'librarian');

        $this->assertTrue($this->policy->viewAny($admin));
        $this->assertTrue($this->policy->create($admin));

        $this->assertFalse($this->policy->viewAny($cataloger));
        $this->assertFalse($this->policy->create($cataloger));

        $this->assertFalse($this->policy->viewAny($librarian));
        $this->assertFalse($this->policy->create($librarian));
    }

    #[Test]
    public function it_allows_users_to_view_and_update_their_own_profile_or_by_admin(): void
    {
        $admin = $this->createUserWithRole(1, 'admin');
        $userA = $this->createUserWithRole(2, 'cataloger');
        $userB = $this->createUserWithRole(3, 'librarian');

        // Admin can view and update any user
        $this->assertTrue($this->policy->view($admin, $userA));
        $this->assertTrue($this->policy->update($admin, $userA));

        // User can view and update themselves
        $this->assertTrue($this->policy->view($userA, $userA));
        $this->assertTrue($this->policy->update($userA, $userA));

        // User cannot view or update another user
        $this->assertFalse($this->policy->view($userA, $userB));
        $this->assertFalse($this->policy->update($userA, $userB));
    }
}
