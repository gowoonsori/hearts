<?php


namespace App\Services;


use App\Exceptions\InternalServerException;
use App\Exceptions\NotFoundException;
use App\Models\Post;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    private UserRepository $userRepository;

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
    public function getUser(int $userId): User
    {
        $user = $this->userRepository->findById($userId);
        if(empty($user)){
            throw new NotFoundException('존재하지 않은 사용자입니다.');
        }
        return $user;
    }


    /**
     * 유저 정보  Email 로 조회
     * 쿼리 1번 발생
     * @param string $email
     * @return User | bool
     */
    public function getUserByEmail(string $email): User|bool
    {
        return $this->userRepository->findByEmail($email);
    }


    /**
     * 유저 생성
     * 쿼리 1번 발생
     * @param $socialData
     * @return User
     * @throws InternalServerException
     */
    public function createUser($socialData): User
    {
        $user = new User;
        $user->name = $socialData->getName();
        $user->email = $socialData->getEmail();
        $user->social_id = $socialData->getId();
        $user->access_token = $socialData->token;

        $result = $this->userRepository->insert($user);
        if(!$result){
            throw new InternalServerException("사용자 등록중 오류가 발생했습니다.");
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
        return $user->likes()->get();
    }
}
