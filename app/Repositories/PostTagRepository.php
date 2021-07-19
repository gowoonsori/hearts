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
     *@param array $tags
     * @return void
     */
    public function insert(Post $post,array $tags){
        $post->tags()->attach($tags);
    }
}
