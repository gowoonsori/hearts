<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory,Notifiable;

    public const CACHE_TIME = 300; //60s * 5 => 5 min

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
    ];

    protected $hidden = ['social_id','id','pivot'];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function categories() : BelongsToMany
    {
        return $this->belongsToMany(Category::class,'user_category');
    }

    public function likes() : BelongsToMany
    {
        return $this->belongsToMany(Post::class,'likes');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
