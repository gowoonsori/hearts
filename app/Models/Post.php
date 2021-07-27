<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Scout\Searchable;

class Post extends Model
{
    use HasFactory,Searchable;

    public $timestamps = false;

    protected $fillable = [
        'content',
        'total_like',
        'share_cnt',
        'search',
        'user_id',
        'category_id',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array'
    ];

    protected $hidden = ['pivot'];

    public function toSearchableArray (): array {
        $array = $this->toArray();
        return array(
            'id' => $array['id'],
            'content' => $array['content'],
            'tags' => json_encode( $array['tags'], JSON_UNESCAPED_UNICODE)
        );
    }

    public function shouldBeSearchable()
    {
        return $this->search;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function likes(): BelongsToMany
    {
        return $this->belongsToMany( User::class,'likes');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class,'category_id');
    }
}
