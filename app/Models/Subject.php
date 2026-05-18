<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
    ];

    public function classSections(): HasMany
    {
        return $this->hasMany(ClassSection::class);
    }
}
