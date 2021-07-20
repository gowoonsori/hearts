<?php


namespace App\Services;


use App\Exceptions\NotFoundException;
use App\Models\Post;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * 유저 정보  id로 조회
     * 쿼리 1번 발생
     * @param integer $userId
     * @return User
     * @throws NotFoundException
     */
    public function getInfo(int $userId): User
    {
        $user = $this->userRepository->findById($userId);
        if(empty($user)){
            throw new NotFoundException('존재하지 않은 사용자입니다.');
        }
        return $user;
    }

    /**
     * 문구 생성
     * 쿼리 1번 발생
     * @param User $user
     * @param Post $post
     * @return void
     * */
    public function createPost(User $user, Post $post){
        $user->posts()->save($post);
    }

    /**
     * 좋아요한 문구들 조회
     * 쿼리 2번 발생
     * @param User $user
     * @return Collection
     * */
    public function getLikePosts(User $user): Collection
    {
        return $user->likes()->with('tags')->get();
    }
}
