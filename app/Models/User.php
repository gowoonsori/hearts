<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory,Notifiable;

    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'social_id',
        'name',
        'email',
        'access_token',
    ];

    protected $hidden = ['access_token'];

    public function post(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function categories() : HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function likes() : BelongsToMany
    {
        return $this->belongsToMany(Like::class);
    }
}
