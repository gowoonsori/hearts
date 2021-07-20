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
        'visit_cnt',
        'search',
        'user_id',
        'category_id',
    ];

    protected $hidden = ['pivot'];

    public function toSearchableArray (): array {
        $array = $this->toArray();
        $tags = $this->tags->toArray();
        //$array['tags'] = json_encode($tags) ;
        $tagInfo = '';
        foreach ($tags as $tag){
            $tagInfo = $tagInfo . ' , ' . $tag['title'];
        }
        $array['tags'] = $tagInfo;
        return array(
            'id' => $array['id'],
            'content' => $array['content'],
            'total_like' => $array['total_like'],
            'share_cnt' => $array['share_cnt'],
            'visit_cnt' => $array['visit_cnt'],
            'user_id' => $array['user_id'],
            'category_id' => $array['category_id'],
            'tags' => $array['tags']
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

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class,'post_tag');
    }
}
