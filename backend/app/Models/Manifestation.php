<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Manifestation extends Model
{
    protected $fillable = ['expression_id', 'isbn', 'publisher', 'publication_year', 'format'];

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
