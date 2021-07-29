<?php


namespace App\Services;


use App\Exceptions\InternalServerException;
use App\Exceptions\NotFoundException;
use App\Models\Post;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;

class UserService
{
    private UserRepository $userRepository;
    private CategoryService $categoryService;

    public function __construct(UserRepository $userRepository,CategoryService $categoryService)
    {
        $this->userRepository = $userRepository;
        $this->categoryService = $categoryService;
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

        $userId = $this->userRepository->insert($user);
        $this->categoryService->attachWithUser(1,$userId);

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
     *
     * @param User $user
     * @return \Illuminate\Support\Collection
     */
    public function getLikePosts(User $user): \Illuminate\Support\Collection
    {
        return $this->userRepository->findLikesById($user->id);
    }

    /**
     * 좋아요한 정보 포함한 사용자 정보
     * 쿼리 2번 발생
     * @param User $user
     */
    public function getUserWithLikes(User $user)
    {
        return $this->userRepository->findByIdWithLikes($user->id);
    }
}
