<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
       'title','post_id'
    ];

    protected $hidden = ['pivot','created_at','updated_at'];

    public function posts() : BelongsToMany
    {
        return $this->belongsToMany(Post::class,'post_tag');
    }
}
