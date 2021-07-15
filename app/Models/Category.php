<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'title',
        'user_id'
    ];

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function posts() : HasMany
    {
        return $this->hasMany(Post::class);
    }
}
