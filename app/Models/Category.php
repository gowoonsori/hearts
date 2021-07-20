<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Category extends Model
{
    use HasFactory,Searchable;

    public $timestamps = false;

    protected $fillable = [
        'title',
    ];

    protected $hidden = [
        'pivot',
        'created_at',
        'updated_at'
    ];

    public  function  toSearchableArray (): array {
        return  $this ->withoutRelations()->toArray ();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class,'user_category');
    }

    public function posts() : HasMany
    {
        return $this->hasMany(Post::class);
    }
}
