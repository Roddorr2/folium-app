<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Language extends Model
{
    protected $fillable = [
        'code',
        'name',
        'native_name',
        'script',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function expressions(): HasMany
    {
        return $this->hasMany(Expression::class);
    }
}
