<?php


namespace App\Services;


use App\Exceptions\InternalServerException;
use App\Exceptions\NotFoundException;
use App\Models\Post;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Log;

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
     * @param integer $userId
     * @return object | null
     * @throws NotFoundException|InternalServerException
     */
    public function getUser(int $userId): ?object
    {
        $user = $this->userRepository->findById($userId);
        if(empty($user)){
            throw new NotFoundException('존재하지 않은 사용자입니다.');
        }
        return $user;
    }


    /**
     * 유저 정보  Email 로 조회
     * @param string $email
     * @return User | bool
     * @throws InternalServerException
     */
    public function getUserByEmail(string $email): User|bool
    {
        return $this->userRepository->findByEmail($email);
    }

    /**
     * 유저 정보  socialId 로 조회
     * @param int $socialId
     * @return User | bool
     * @throws InternalServerException
     */
    public function getUserBySocialId(int $socialId): User|bool
    {
        return $this->userRepository->findBySocialId($socialId);
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

        //사용자 생성
        $userId = $this->userRepository->insert($user);
        $user->id = $userId;

        //기본 카테고리 생성
        $this->categoryService->attachWithUser(1,$userId);

        return $user;
    }


    /**
     * 문구 생성
     * @param User $user
     * @param Post $post
     * @return void
     * */
    public function createPost(User $user, Post $post){
        $user->posts()->save($post);
    }

    /**
     * 좋아요한 문구 모두 조회
     * @param User $user
     * @return \Illuminate\Support\Collection
     * @throws InternalServerException
     */
    public function getLikePosts(User $user): \Illuminate\Support\Collection
    {
        return $this->userRepository->findLikesById($user->id);
    }

    /**
     * 좋아요한 문구 페이지네이션 조회
     * @param User $user
     * @param int $lastId
     * @param int $limit
     * @return \Illuminate\Support\Collection
     * @throws InternalServerException
     */
    public function getLikePostsCursorPaging(User $user, int $lastId, int $limit): \Illuminate\Support\Collection
    {
        return $this->userRepository->findLikesByIdAndLastIdAndLimit($user->id,$lastId,$limit);
    }

    /**
     * 좋아요한 정보 포함한 사용자 정보
     * @param User $user
     * @return object | null
     * @throws InternalServerException
     */
    public function getUserWithLikes(User $user): ?object
    {
        return $this->userRepository->findByIdWithLikes($user->id);
    }
}
