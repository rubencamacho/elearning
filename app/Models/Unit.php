<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Unit extends Model
{
    protected $fillable = [
        'course_id',
        'name',
        'slug',
        'content', 
        'sort',
        'published',
        'free',
    ];

    protected $casts = [
        'published' => 'boolean',
        'free' => 'boolean',
    ];

    public function course() : BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

}
