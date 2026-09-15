<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'branch_id',
        'dni',
    ];

    protected $appends = ['role_name'];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function getRoleNameAttribute(): string
    {
        return $this->role?->name ?? 'reader';
    }

    /**
     * RBAC Role Checks
     */
    public function isAdmin(): bool
    {
        return $this->role?->name === 'admin';
    }

    public function isCataloger(): bool
    {
        return $this->isAdmin() || $this->role?->name === 'cataloger';
    }

    public function isLibrarian(): bool
    {
        return $this->isAdmin() || $this->role?->name === 'librarian';
    }

    public function isNetworkLibrarian(): bool
    {
        return $this->isAdmin() || $this->role?->name === 'network_librarian';
    }

    public function isReader(): bool
    {
        return $this->role?->name === 'reader';
    }

    public function hasAnyRole(array|string $roles): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $rolesArray = is_array($roles) ? $roles : [$roles];
        return in_array($this->role?->name, $rolesArray, true);
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
