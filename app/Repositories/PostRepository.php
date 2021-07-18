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
     * @param array $postData
     * @param integer $userId
     * @return User|Model
     * @throws InternalServerException
     */
    public function insert(array $postData,int $userId){
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
     * 특정 id의 문구 조회
     * @param integer $id
     * @return null | Model
     */
    public function findById(int $id): ?Model
    {
        return $this->post->find($id);
    }

    /**
     * Category로 자신의 문구 조회
     * @param integer $userId
     * @param integer $categoryId
     * @return array | Collection
     */
    public function findMyPostsByCategories(int $userId,int $categoryId)
    {
        $posts = $this->post->where(['user_id'=>$userId, 'category_id' => $categoryId])->get();
        if(empty($posts->all())) return [];
        return $posts;
    }

    /**
     * Obtain all user's information in random order
     *
     * @return User[]|bool|Collection
     */
    public function findAll($userId){
        $posts = $this->post->where(['user_id'=>$userId])->get();
        if(empty($posts->all())){
            return [];
        }
        return $posts;
    }
}
