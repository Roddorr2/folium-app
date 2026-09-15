<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array with complete identity and presentation contracts.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'short_name' => $this->resolveShortName((string) $this->name),
            'initials' => $this->resolveInitials((string) $this->name),
            'email' => $this->email,
            'dni' => $this->dni,
            'role_id' => $this->role_id,
            'role' => new RoleResource($this->role),
            'branch_id' => $this->branch_id,
            'branch' => $this->branch ? [
                'id' => $this->branch->id,
                'name' => $this->branch->name,
                'city' => $this->branch->city,
                'address' => $this->branch->address,
            ] : null,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }

    private function resolveShortName(string $name): string
    {
        $segments = preg_split('/\s+/', trim($name));
        if ($segments && count($segments) > 1) {
            return $segments[0] . ' ' . strtoupper(substr($segments[1], 0, 1)) . '.';
        }
        return $segments[0] ?? '';
    }

    private function resolveInitials(string $name): string
    {
        $segments = preg_split('/\s+/', trim($name));
        if ($segments && count($segments) >= 2) {
            return strtoupper(substr($segments[0], 0, 1) . substr($segments[1], 0, 1));
        }
        return strtoupper(substr($segments[0] ?? 'U', 0, 1));
    }
}
