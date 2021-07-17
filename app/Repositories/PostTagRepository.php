<?php


namespace App\Repositories;


use App\Models\Post;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PostTagRepository
{
    /**
     * Post와 tags 관계 연결
     *@param Post $post
     *@param Collection $tags
     * @return void
     */
    public function insert(Post $post,Collection $tags){
        $tagList = [];
        foreach($tags->all() as $tag){
            array_push($tagList,[
                'tag_id'=>$tag->id
            ]);
        }
        $post->tags()->attach($tagList);
    }
}
