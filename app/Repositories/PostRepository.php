<?php


namespace App\Repositories;


use App\Exceptions\BadRequestException;
use App\Exceptions\InternalServerException;
use App\Models\Post;
use App\Models\User;
use http\Exception\BadQueryStringException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
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
     * 특정 id의 문구 조회
     * @param integer $id
     * @return Builder|Builder[]|Collection|Model|null
     * @throws InternalServerException
     */
    public function findByIdWithTags(int $id)
    {
        try{
           return $this->post->with('tags')->find($id);
        }catch (QueryException $e){
            throw new InternalServerException("문구 조회중 오류가 발생했습니다.");
        }
    }

    /**
     * 특정 id의 문구 조회 raw query로 post와 tag한번에 조회
     * @param integer $id
     * @return \Illuminate\Support\Collection
     * @throws InternalServerException
     */
    public function findByIdWithTagsRaw(int $id): \Illuminate\Support\Collection
    {
        try{
            return DB::table('posts')
                ->select('posts.*', 'tags.id AS tag_id','tags.title AS tag_title')
                ->join('post_tag','posts.id','=','post_tag.post_id')
                ->join('tags','post_tag.tag_id','=','tags.id')
                ->where(['posts.id' => $id])->get();
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
            $posts = $this->post->with('tags')->where(['user_id'=>$userId, 'category_id' => $categoryId])->get();
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
            $posts = $this->post->with('tags')->where(['user_id'=>$userId])->get();
            if(empty($posts->all())){
                return [];
            }
            return $posts;
        }catch (QueryException $e){
            throw new InternalServerException("문구 조회중 오류가 발생했습니다.");
        }
    }
}
