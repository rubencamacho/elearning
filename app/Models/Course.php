<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'slug',
        'image',
        'published',
        'featured',
    ];

    public function teacher() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function units() : HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function students() : BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('completed');
    }
}
