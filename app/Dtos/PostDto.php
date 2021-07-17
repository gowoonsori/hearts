<?php


namespace App\Dtos;


use App\Exceptions\BadRequestException;
use App\Models\Post;

class PostDto
{
    private $post;

    /**
     * @throws BadRequestException
     */
    public function __construct($content, $search, $categoryId, $userId)
    {
        $this->post = new Post;
        $this->validate($content,$search,$categoryId,$userId);
        $this->post->content = $content;
        $this->post->search = $search;
        $this->post->category_id = $categoryId;
        $this->post->user_id = $userId;
        $this->post->total_like = 0;
        $this->post->share_cnt = 0;
        $this->post->visit_cnt = 0;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    /**
     * @throws BadRequestException
     */
    private function validate($content, $search, $categoryId, $userId)
    {
        if(preg_match_all("/[-(){}[]/",$content) != 0) throw new BadRequestException('잘못된 요청입니다.');
        if(gettype($search) != 'boolean') throw new BadRequestException('잘못된 요청입니다.');
        if(gettype($categoryId) != 'integer' && gettype($userId) != 'integer') throw new BadRequestException('잘못된 요청입니다.');
    }
}
