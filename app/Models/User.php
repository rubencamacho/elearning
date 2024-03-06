<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role_id',
        'avatar',
        'name',
        'email',
        'password',
        'active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function role() : BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function plans() : BelongsToMany
    {
        return $this->belongsToMany(Plan::class)->withPivot('active');
    }

    public function courses() : BelongsToMany
    {
        return $this->belongsToMany(Course::class)->withPivot('completed');
    }

    public function scopeStudents(Builder $builder): Builder
    {
        return $builder->where('role_id', Role::STUDENT);
    }

    public function scopeTeachers(Builder $builder): Builder
    {
        return $builder->where('role_id', Role::TEACHER);
    }

    public function scopeAdmin(Builder $builder): Builder
    {
        return $builder->where('role_id', Role::ADMIN);
    }

    public function scopeActive(Builder $builder): Builder
    {
        return $builder->where('active', true);
    }
}
