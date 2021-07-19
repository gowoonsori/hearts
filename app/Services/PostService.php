<?php


namespace App\Services;


use App\Exceptions\InternalServerException;
use App\Exceptions\NotFoundException;
use App\Models\Post;
use App\Models\User;
use App\Repositories\PostRepository;
use App\Repositories\PostTagRepository;
use App\Repositories\TagRepository;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class PostService
{
    private $postRepository;
    private $postTagRepository;


    public function __construct(PostRepository $postRepository, PostTagRepository $postTagRepository)
    {
        $this->postRepository = $postRepository;
        $this->postTagRepository = $postTagRepository;
    }

    /**
     * @param integer $postId
     * @return Builder|Builder[]|\Illuminate\Database\Eloquent\Collection|Model
     * @throws InternalServerException
     * @throws NotFoundException
     */
    public function getPostById(int $postId)
    {
        $post =  $this->postRepository->findByIdWithTags($postId);
        if(empty($post)){
            throw new NotFoundException('존재하지 않은 문구입니다.');
        }
        return $post;
    }

    /**
     * @param integer $userId
     * @return Collection|User[]|null
     * @throws InternalServerException
     */
    public function getPostsByUserId(int $userId){
        $post = $this->postRepository->findAll($userId);
        if(empty($post)) $post = null;
        return $post;
    }

    /**
     * @param integer $userId
     * @param integer $categoryId
     * @return array|Collection|null
     * @throws InternalServerException
     */
    public function getPostsByCategories(int $userId, int $categoryId){
        $posts = $this->postRepository->findMyPostsByCategories($userId,$categoryId);
        if(empty($posts)) $posts = null;
        return $posts;
    }


    /**
     * 문구와 Tag 연결 관계 맺어주는 함수
     * @param Post $post
     * @param Collection $tags
     */
    public function connectWithTags(Post $post, Collection $tags){
        $tagList = [];
        foreach($tags->all() as $tag){
            array_push($tagList,[
                'tag_id'=>$tag->id
            ]);
        }
        $this->postTagRepository->insert($post,$tagList);
    }


    /**
     * @param Post $post
     * @param User $user
     * @return Post
     */
    public function updateLike(Post $post,User $user): Post
    {
        $post->likes()->attach($user);
        $post->total_like += 1;
        $post->save();
        return $post;
    }

    /**
     * @param Post $post
     * @param User $user
     * @return bool
     */
    public function deleteLike(Post $post,User $user): bool
    {
        $post->likes()->detach($user);
        $post->total_like -= 1;
        $post->save();
        return true;
    }

    /**
     * @param Post $post
     * @param User $user
     * @return bool
     */
    public function isLikePost(User $user,Post $post): bool
    {
        $posts = $user->likes()->get();
        if(!empty($posts->all())) {
            $contain = $posts->contains($post);
            if($contain) return true;
        }
        return false;
    }


    /**
     * @param Post $post
     * @return bool
     * */
    public function updateShareCount(Post $post): bool
    {
        $post->share_cnt += 1;
        return $post->save();
    }
}
