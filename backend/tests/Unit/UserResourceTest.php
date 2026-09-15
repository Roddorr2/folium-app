<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;

class UserResourceTest extends TestCase
{
    #[Test]
    public function it_serializes_user_with_computed_initials_and_short_name(): void
    {
        $role = new Role();
        $role->id = 1;
        $role->name = 'admin';
        $role->display_name = 'Director General';

        $branch = new Branch();
        $branch->id = 5;
        $branch->name = 'Sede Central';
        $branch->city = 'Santiago';
        $branch->address = 'Av. Libertador 1000';

        $user = new User();
        $user->id = 42;
        $user->name = 'Elena Vasquez Gomez';
        $user->email = 'elena.vasquez@folium.test';
        $user->dni = '12345678-9';
        $user->role_id = 1;
        $user->branch_id = 5;
        $user->setRelation('role', $role);
        $user->setRelation('branch', $branch);

        $resource = new UserResource($user);
        $serialized = $resource->toArray(Request::create('/api/v1/users'));

        $this->assertSame(42, $serialized['id']);
        $this->assertSame('Elena Vasquez Gomez', $serialized['name']);
        $this->assertSame('Elena V.', $serialized['short_name']);
        $this->assertSame('EV', $serialized['initials']);
        $this->assertSame('elena.vasquez@folium.test', $serialized['email']);
        $this->assertSame('12345678-9', $serialized['dni']);
        $this->assertSame(1, $serialized['role_id']);
        $this->assertIsObject($serialized['role']);
        $this->assertSame('admin', $serialized['role']->resource->name);

        $this->assertIsArray($serialized['branch']);
        $this->assertSame(5, $serialized['branch']['id']);
        $this->assertSame('Sede Central', $serialized['branch']['name']);

        // Sensitive fields should never be exposed in API Resource
        $this->assertArrayNotHasKey('password', $serialized);
        $this->assertArrayNotHasKey('remember_token', $serialized);
    }

    #[Test]
    public function it_computes_single_name_initials_and_short_name_correctly(): void
    {
        $user = new User();
        $user->id = 99;
        $user->name = 'Admin';
        $user->email = 'admin@folium.test';

        $resource = new UserResource($user);
        $serialized = $resource->toArray(Request::create('/api/v1/users'));

        $this->assertSame('Admin', $serialized['short_name']);
        $this->assertSame('A', $serialized['initials']);
        $this->assertNull($serialized['branch']);
    }
}
