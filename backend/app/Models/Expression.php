<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Expression extends Model
{
    protected $fillable = ['work_id', 'language_id', 'type', 'revision_year', 'description', 'translation_date'];

    public function work(): BelongsTo
    {
        return $this->belongsTo(Work::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function manifestations(): HasMany
    {
        return $this->hasMany(Manifestation::class);
    }
}
