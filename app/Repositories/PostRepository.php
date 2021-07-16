<?php


namespace App\Repositories;


use App\Exceptions\InternalServerException;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class PostRepository
{
    protected $post;

    public function __construct()
    {
        $this->post = new Post;
    }

    /**
     * @param $socialData
     * @return User|Model
     * @throws InternalServerException
     */
    public function insert($postData,$userId){
        $nowDt = now();
        try {
            return $this->post->create([
                'content' => $postData['content'],
                'total_like' => 0,
                'share_cnt' => 0,
                'visit_cnt' => 0,
                'search' => $postData['search'],
                'user_id' => $userId,
                'category_id' => $postData['category_id'],
                'created_at' => $nowDt,
                'updated_at' => $nowDt,
            ]);
        } catch (QueryException $exception) {
            Log::error("문구 등록중 오류가 발생했습니다. \n".$exception);
            throw new InternalServerException("문구 등록중 오류가 발생했습니다.");
        }
    }


    /**
     * Obtain the user information by data table idx.
     * @param $id
     * @return User|bool|Collection|Model
     */
    public function findById($id){
        $post = $this->post->find($id);
        if(empty($post)){
            Log::info('whereId(): id '.$id.' user is not found');
            return false;
        }
        return $post;
    }
    /**
     * Obtain all user's information in random order
     *
     * @return User[]|bool|Collection
     */
    public function findAll(){
        $posts = $this->post->get();
        if(empty($posts->all())){
            Log::info('all(): user table is empty.');
            return false;
        }
        return $posts;
    }
}
