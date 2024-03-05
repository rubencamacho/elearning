<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'price',
        'description',
        'sort',
        'active',
        'featured',
    ];

    protected $casts = [
        'active' => 'boolean',
        'featured' => 'boolean',
    ];

    public function users() : BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('active');
    }
}
