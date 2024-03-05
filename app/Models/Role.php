<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    CONST ADMIN = 1;
    CONST TEACHER = 2;
    CONST STUDENT = 3;

    protected $fillable = [
        'name',
        'description'
    ];

    public function users() : HasMany
    {
        return $this->hasMany(User::class);
    }
}
