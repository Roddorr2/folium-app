<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    protected $fillable = ['name', 'city', 'address', 'phone'];

    /**
     * Relationship alias so legacy calls ($branch->items, Branch::withCount('items')) 
     * map directly to physical items currently located at this branch.
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'current_branch_id');
    }

    public function currentItems(): HasMany
    {
        return $this->hasMany(Item::class, 'current_branch_id');
    }

    public function homeItems(): HasMany
    {
        return $this->hasMany(Item::class, 'home_branch_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function checkoutLoans(): HasMany
    {
        return $this->hasMany(Loan::class, 'checkout_branch_id');
    }

    public function returnLoans(): HasMany
    {
        return $this->hasMany(Loan::class, 'return_branch_id');
    }
}
