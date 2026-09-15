<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Item extends Model
{
    protected $fillable = [
        'manifestation_id', 
        'home_branch_id', 
        'current_branch_id', 
        'barcode', 
        'shelf_location', 
        'status'
    ];

    /**
     * Appended virtual attributes to maintain 100% backward compatibility with API consumers.
     */
    protected $appends = ['branch_id', 'branch_name'];

    public function homeBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'home_branch_id');
    }

    public function currentBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'current_branch_id');
    }

    /**
     * Relationship alias to guarantee legacy 'branch' eager loading continues working.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'current_branch_id');
    }

    public function getBranchIdAttribute(): int
    {
        return (int) ($this->attributes['current_branch_id'] ?? $this->attributes['home_branch_id'] ?? 1);
    }

    public function getBranchNameAttribute(): ?string
    {
        return $this->currentBranch?->name ?? $this->homeBranch?->name ?? null;
    }

    public function manifestation(): BelongsTo
    {
        return $this->belongsTo(Manifestation::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function activeLoan(): HasOne
    {
        return $this->hasOne(Loan::class)->whereNull('returned_at');
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(Transfer::class);
    }
}
