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
        if(preg_match_all("/[-(){}[]/",$content) != 0) throw new BadRequestException('잘못된 요청입니다.');
        if(!($search == 1 || $search == 0)) throw new BadRequestException('잘못된 요청입니다.');
        if(gettype($categoryId) != 'integer' || gettype($userId) != 'integer') throw new BadRequestException('잘못된 요청입니다.');
        if(!empty($tags)){
            foreach ($tags as $tag) {
                Log::info(gettype($tag['tag']) .  gettype($tag['color']) );
                if(gettype($tag['tag']) != 'string' ) throw new BadRequestException('잘못된 요청입니다.');
                if(gettype($tag['color']) != 'integer' ) throw new BadRequestException('잘못된 요청입니다.');
            }
        }
    }
}
