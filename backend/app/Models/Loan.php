<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    protected $fillable = [
        'item_id',
        'user_id',
        'checkout_branch_id',
        'return_branch_id',
        'due_date',
        'returned_at'
    ];

    protected $casts = [
        'due_date' => 'date',
        'returned_at' => 'datetime',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function checkoutBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'checkout_branch_id');
    }

    public function returnBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'return_branch_id');
    }
}
