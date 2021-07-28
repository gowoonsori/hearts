<?php


namespace App\Repositories;


use App\Exceptions\InternalServerException;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PostRepository
{
    protected Post $post;

    public function __construct()
    {
        $this->post = new Post;
    }

    /**
     * @return User|Model
     * @throws InternalServerException
     */
    public function insert( $post){
        $nowDt = now();
        try {
            return $this->post->create([
                'content' => $post['content'],
                'total_like' => $post['total_like'],
                'share_cnt' => $post['share_cnt'],
                'search' => $post['search'],
                'user_id' => $post['user_id'],
                'category_id' => $post['category_id'],
                'tags' => $post['tags'],
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
     * @throws InternalServerException
     */
    public function findById(int $id): ?Model
    {
        try{
            return $this->post->find($id);
        }catch (QueryException $e){
            throw new InternalServerException("문구 조회중 오류가 발생했습니다.");
        }
    }

    /**
     * Category로 자신의 문구 조회
     * @param integer $userId
     * @param integer $categoryId
     * @return array | Collection
     * @throws InternalServerException
     */
    public function findMyPostsByCategories(int $userId,int $categoryId)
    {
        try{
            $posts = $this->post->where(['user_id'=>$userId, 'category_id' => $categoryId])->get();
            if(empty($posts->all())) return [];
            return $posts;
        }catch (QueryException $e){
            throw new InternalServerException("문구 조회중 오류가 발생했습니다.");
        }
    }

    /**
     * Obtain all user's information in random order
     *
     * @return Collection|User[]
     * @throws InternalServerException
     */
    public function findAll($userId){
        try{
            $posts = $this->post->where(['user_id'=>$userId])->get();
            if(empty($posts->all())){
                return [];
            }
            return $posts;
        }catch (QueryException $e){
            throw new InternalServerException("문구 조회중 오류가 발생했습니다.");
        }
    }

    /**
     * Obtain all user's information in random order
     *
     * @param $userId
     * @return \Illuminate\Support\Collection | null
     * @throws InternalServerException
     */
    public function findAllWithUserWithCategory($userId): ?\Illuminate\Support\Collection
    {
        try{
            $posts = DB::table('posts','p')->select(DB::raw('p.*, u.name as owner, c.title as category'))
                ->join('categories as c','p.category_id','=','c.id')
                ->join('users as u','u.id','=','p.user_id')
                ->where('p.user_id',$userId)
                ->get();
            if(empty($posts->all())){
                return null;
            }
            return $posts;
        }catch (QueryException $e){
            Log::error($e);
            throw new InternalServerException("문구 조회중 오류가 발생했습니다.");
        }
    }
}
