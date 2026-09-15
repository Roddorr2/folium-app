<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array with self-descriptive presentation metadata.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (is_null($this->resource)) {
            return [
                'id' => 0,
                'key' => 'reader',
                'name' => 'reader',
                'display_name' => 'Lector Registrado',
                'short_label' => 'Lector',
                'badge_variant' => 'stone',
                'description' => null,
            ];
        }

        $key = (string) ($this->name ?? 'reader');

        return [
            'id' => $this->id ?? 0,
            'key' => $key,
            'name' => $key,
            'display_name' => $this->display_name ?? ucfirst($key),
            'short_label' => $this->short_label ?? $this->resolveShortLabel($key),
            'badge_variant' => $this->badge_variant ?? $this->resolveBadgeVariant($key),
            'description' => $this->description ?? null,
        ];
    }

    private function resolveShortLabel(string $key): string
    {
        return match ($key) {
            'admin' => 'Admin',
            'cataloger' => 'Catalogador',
            'librarian' => 'Bibliotecario',
            'network_librarian' => 'Bib. de Red',
            'curator' => 'Curador',
            'archivist' => 'Archivero',
            default => ucfirst($key),
        };
    }

    private function resolveBadgeVariant(string $key): string
    {
        return match ($key) {
            'admin' => 'forest',
            'cataloger' => 'emerald',
            'librarian' => 'copper',
            'network_librarian' => 'crimson',
            default => 'stone',
        };
    }
}
