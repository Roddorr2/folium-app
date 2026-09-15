<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Work extends Model
{
    protected $fillable = ['title', 'abstract', 'original_language', 'dewey', 'nature'];

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'author_work');
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'subject_work');
    }

    public function expressions(): HasMany
    {
        return $this->hasMany(Expression::class);
    }
}
