<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'title',
    ];

    protected $hidden = [
        'pivot'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class,'user_category');
    }

    public function posts() : HasMany
    {
        return $this->hasMany(Post::class);
    }
}
