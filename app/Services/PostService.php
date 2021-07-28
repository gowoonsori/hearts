<?php


namespace App\Services;


use App\Exceptions\InternalServerException;
use App\Exceptions\NotFoundException;
use App\Models\Post;
use App\Models\User;
use App\Repositories\PostRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class PostService
{
    private PostRepository $postRepository;


    public function __construct(PostRepository $postRepository,)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * 문구 생성 메서드
     * @param $post
     * @return User|Model
     * @throws InternalServerException
     */
    public function createPost($post): Model|User
    {
        return $this->postRepository->insert($post);
    }



    /**
     * 문구 id로 문구 조회
     * @param integer $postId
     * @return Model
     * @throws InternalServerException
     * @throws NotFoundException
     */
    public function getPostById(int $postId): Model
    {
        $post =  $this->postRepository->findById($postId);
        if(empty($post)){
            throw new NotFoundException('존재하지 않은 문구입니다.');
        }
        return $post;
    }

    /**
     * 문구내용(사용자 이름과 카테고리) 조회 메서드
     * @param $postId
     * @throws InternalServerException
     */
    public function getPostFullInfo($postId)
    {
        return $this->postRepository->findByIdWithUserWithCategory($postId);
    }

    /**
     * 유저 id로 모든 문구 조회
     * @param integer $userId
     * @return Collection|null
     * @throws InternalServerException
     */
    public function getPostsByUserId(int $userId): ?Collection
    {
        return $this->postRepository->findAllWithUserWithCategory($userId);
    }

    /**
     * 사용자의 특정 카테고리의 모든 문구 조회
     * @param integer $userId
     * @param integer $categoryId
     * @return array|Collection|null
     * @throws InternalServerException
     */
    public function getPostsByCategories(int $userId, int $categoryId): array|Collection|null
    {
        $posts = $this->postRepository->findMyPostsByCategories($userId,$categoryId);
        if(empty($posts)) $posts = null;
        return $posts;
    }

    /**
     * 문구 좋아요 메서드
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
     * 문구 좋아요 취소 메서드
     * @param $post
     * @param $user
     * @return Post
     */
    public function deleteLike( $user,$post): Post
    {
        $post->likes()->detach($user);
        $post->total_like -= 1;
        $post->save();
        return $post;
    }

    /**
     * 특정 문구를 좋아요 상태인지 판별하는 메서드
     * @param $user
     * @param $post
     * @return bool
     */
    public function isLikePost($user,$post): bool
    {
        $posts = $user->likes()->get();
        if(!empty($posts->all())) {
            $contain = $posts->contains($post);
            if($contain) return true;
        }
        return false;
    }


    /**
     * 문구의 공유수 증가시키는 메서드
     * @param Post $post
     * @return bool
     * */
    public function updateShareCount(Post $post): bool
    {
        $post->share_cnt += 1;
        return $post->save();
    }

    /**
     * 문구 내용 수정 메서드
     * @param $post
     * @param $postDto
     * @return mixed
     */
    public function updatePost($post,$postDto): mixed
    {
        $post->content = $postDto->content;
        $post->search = $postDto->search;
        $post->category_id = $postDto->category_id;
        $post->tags = $postDto->tags;

        return $post->save();
    }


    /**
     * 문구 삭제 메서드
     * @param $post
     * @return void
     * */
    public function deletePost($post){
        $post->delete();
    }
}
