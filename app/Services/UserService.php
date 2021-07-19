<?php


namespace App\Services;


use App\Exceptions\NotFoundException;
use App\Models\Post;
use App\Models\User;
use App\Repositories\UserRepository;

class UserService
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
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
     * @param User $user
     * @param Post $post
     * @return void
     * */
    public function createPost(User $user, Post $post){
        $user->posts()->save($post);
    }
}
