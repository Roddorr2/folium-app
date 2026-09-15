<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Role;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;

class RoleResourceTest extends TestCase
{
    #[Test]
    public function it_serializes_role_with_presentation_metadata_and_badge_variants(): void
    {
        $roleCases = [
            'admin' => ['short_label' => 'Admin', 'badge_variant' => 'forest'],
            'cataloger' => ['short_label' => 'Catalogador', 'badge_variant' => 'emerald'],
            'librarian' => ['short_label' => 'Bibliotecario', 'badge_variant' => 'copper'],
            'network_librarian' => ['short_label' => 'Bib. de Red', 'badge_variant' => 'crimson'],
        ];

        foreach ($roleCases as $roleName => $expected) {
            $role = new Role();
            $role->id = 10;
            $role->name = $roleName;
            $role->display_name = 'Custom ' . ucfirst($roleName);
            $role->description = 'Role description';

            $resource = new RoleResource($role);
            $serialized = $resource->toArray(Request::create('/api/v1/roles'));

            $this->assertSame(10, $serialized['id']);
            $this->assertSame($roleName, $serialized['key']);
            $this->assertSame($roleName, $serialized['name']);
            $this->assertSame('Custom ' . ucfirst($roleName), $serialized['display_name']);
            $this->assertSame($expected['short_label'], $serialized['short_label']);
            $this->assertSame($expected['badge_variant'], $serialized['badge_variant']);
            $this->assertSame('Role description', $serialized['description']);
        }
    }

    #[Test]
    public function it_handles_null_resource_gracefully_with_reader_defaults(): void
    {
        $resource = new RoleResource(null);
        $serialized = $resource->toArray(Request::create('/api/v1/roles'));

        $this->assertSame(0, $serialized['id']);
        $this->assertSame('reader', $serialized['key']);
        $this->assertSame('Lector Registrado', $serialized['display_name']);
        $this->assertSame('Lector', $serialized['short_label']);
        $this->assertSame('stone', $serialized['badge_variant']);
    }
}
