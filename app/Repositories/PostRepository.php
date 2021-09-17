<?php


namespace App\Repositories;


use App\Exceptions\InternalServerException;
use App\Models\Post;
use App\Models\User;
use App\utils\ExceptionMessage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
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
     * 문구 생성
     * @param $post
     * @return object
     * @throws InternalServerException
     */
    public function insert($post) : object
    {
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
            throw new InternalServerException(ExceptionMessage::INTERNAL_POST_INSERT);
        }
    }


    /**
     * 특정 id의 문구 조회
     * @param integer $id
     * @return null | object
     * @throws InternalServerException
     */
    public function findById(int $id):  ?object
    {
        try{
            return $this->post->find($id);
        }catch (QueryException $e){
            throw new InternalServerException(ExceptionMessage::INTERNAL_POST_GET);
        }
    }

    /**
     * CategoryFactory 로 자신의 문구 조회
     * @param integer $userId
     * @param integer $categoryId
     * @return \Illuminate\Support\Collection
     * @throws InternalServerException
     */
    public function findByCategoryAndUserId(int $userId,int $categoryId): \Illuminate\Support\Collection
    {
        try{
            return DB::table('posts','p')->select(DB::raw('p.*, u.name as owner, c.title as category'))
                ->join('categories as c','p.category_id','=','c.id')
                ->join('users as u','u.id','=','p.user_id')
                ->where(['u.id'=>$userId, 'c.id' => $categoryId])
                ->get()
                ->transform(function ($item){
                    $item->tags = json_decode($item->tags);
                    return $item;
                });
        }catch (QueryException $e){
            throw new InternalServerException(ExceptionMessage::INTERNAL_POST_GET);
        }
    }

    /**
     * CategoryFactory 로 자신의 문구 조회
     * @param integer $userId
     * @param integer $categoryId
     * @return \Illuminate\Support\Collection
     * @throws InternalServerException
     */
    public function findIdByCategoryAndUserId(int $userId,int $categoryId): \Illuminate\Support\Collection
    {
        try{
            return DB::table('posts')->select('id')
                ->where(['user_id'=>$userId, 'category_id' => $categoryId])
                ->get();
        }catch (QueryException $e){
            throw new InternalServerException(ExceptionMessage::INTERNAL_POST_GET);
        }
    }

    /**
     * CategoryFactory 로 자신의 문구 조회
     * @param integer $userId
     * @param integer $categoryId
     * @param int $lastId
     * @param int $size
     * @return \Illuminate\Support\Collection
     * @throws InternalServerException
     */
    public function findByCategoryAndUserIdAndLastIdAndLimit(int $userId,int $categoryId,int $lastId,int  $size): \Illuminate\Support\Collection
    {
        try{
            return DB::table('posts','p')->select(DB::raw('p.*, u.name as owner, c.title as category'))
                ->join('categories as c','p.category_id','=','c.id')
                ->join('users as u','u.id','=','p.user_id')
                ->where('u.id',$userId)
                ->where('c.id', $categoryId)
                ->where('p.id' ,'<', $lastId)
                ->orderBy('p.id','desc')
                ->limit($size)
                ->get()
                ->transform(function ($item){
                    $item->tags = json_decode($item->tags);
                    return $item;
                });
        }catch (QueryException $e){
            throw new InternalServerException(ExceptionMessage::INTERNAL_POST_GET);
        }
    }

    /**
     * 자신의 문구중 lastId 다음 limit 개수만큼 조회
     * @param integer $userId
     * @param int $lastId
     * @param int $size
     * @return \Illuminate\Support\Collection
     * @throws InternalServerException
     */
    public function findAllByLastIdAndLimitWithUserWithCategory(int $userId,int $lastId,int $size): \Illuminate\Support\Collection
    {
        try{
            return DB::table('posts','p')->select(DB::raw('p.*, u.name as owner, c.title as category'))
                ->join('categories as c','p.category_id','=','c.id')
                ->join('users as u','u.id','=','p.user_id')
                ->where('u.id', '=' ,$userId)
                ->where('p.id' ,'<', $lastId)
                ->orderBy('p.id','desc')
                ->limit($size)
                ->get()
                ->transform(function ($item){
                    $item->tags = json_decode($item->tags);
                    return $item;
                });
        }catch (QueryException $e){
            throw new InternalServerException(ExceptionMessage::INTERNAL_POST_GET);
        }
    }

    /**
     * 모든 문구 조회
     * @param $userId
     * @return Collection | array
     * @throws InternalServerException
     */
    public function findAll($userId) : Collection | array
    {
        try{
            $posts = $this->post->where(['user_id'=>$userId])->get();
            if(empty($posts->all())){
                return [];
            }
            return $posts;
        }catch (QueryException $e){
            throw new InternalServerException(ExceptionMessage::INTERNAL_POST_GET);
        }
    }

    /**
     * 카테고리정보와 작성자 이름 포함한 문구 정보 id로 한개 조회
     * @param $postId
     * @return object|null
     * @throws InternalServerException
     */
    public function findByIdWithUserWithCategory($postId):  ?object
    {
        try{
            $posts = DB::table('posts','p')->select(DB::raw('p.*, u.name as owner, c.title as category'))
                ->join('categories as c','p.category_id','=','c.id')
                ->join('users as u','u.id','=','p.user_id')
                ->where('p.id',$postId)
                ->first();
            if(empty($posts)) return null;
            $posts->tags = json_decode($posts->tags);
            return $posts;
        }catch (QueryException $e){
            throw new InternalServerException(ExceptionMessage::INTERNAL_POST_GET);
        }
    }

    /**
     * 카테고리정보와 작성자 이름 포함한 문구 정보 모두 조회
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
                ->get()
                ->transform(function ($item){
                    $item->tags = json_decode($item->tags);
                    return $item;
                });
            if(empty($posts->all())){
                return null;
            }
            return $posts;
        }catch (QueryException $e){
            throw new InternalServerException(ExceptionMessage::INTERNAL_POST_GET);
        }
    }

    /**
     * postId들 카테고리 벌크 수정 메서드
     * @param array $postIds
     * @param int $categoryId
     * @return int
     * @throws InternalServerException
     */
    public function updateCategoryByPostIds(array $postIds,int $categoryId): int
    {
        try{
            return DB::table('posts')->whereIn('id', $postIds)->update(['category_id' => $categoryId]);
        }catch (QueryException $e){
            throw new InternalServerException(ExceptionMessage::INTERNAL_POST_UPDATE);
        }
    }
}
