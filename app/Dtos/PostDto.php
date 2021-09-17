<?php


namespace App\Dtos;


use App\Exceptions\BadRequestException;
use App\Models\Post;
use Illuminate\Support\Facades\Log;

class PostDto
{
    private Post $post;

    /**
     * @throws BadRequestException
     */
    public function __construct($content, $search, $categoryId, $userId,$tags)
    {
        $this->validate($content,$search,$categoryId,$userId,$tags);
        $this->post = new Post;
        $this->post->content = $content;
        $this->post->search = $search;
        $this->post->category_id = $categoryId;
        $this->post->user_id = $userId;
        $this->post->total_like = 0;
        $this->post->share_cnt = 0;
        $this->post->tags = $tags;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    /**
     * @throws BadRequestException
     */
    private function validate($content, $search, $categoryId, $userId,$tags)
    {
        if(!isset($categoryId) || !isset($userId) ||  !isset($search) || !isset($content)) throw new BadRequestException();
        if(mb_strlen($content) > 200) throw new BadRequestException();
        if(!($search == 1 || $search == 0)) throw new BadRequestException();
        if(gettype($categoryId) != 'integer' || gettype($userId) != 'integer') throw new BadRequestException();
        if(!empty($tags)){
            if(count($tags) > 5) throw new BadRequestException();
            foreach ($tags as $tag) {
                Log::info(gettype($tag['tag']) .  gettype($tag['color']) );
                if(gettype($tag['tag']) != 'string' ) throw new BadRequestException();
                if(gettype($tag['color']) != 'integer' ) throw new BadRequestException();
            }
        }
    }
}
