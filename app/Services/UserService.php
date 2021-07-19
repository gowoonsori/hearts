<?php


namespace App\Services;


use App\Exceptions\NotFoundException;
use App\Models\User;
use App\Repositories\UserRepository;

class UserSerivce
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param integer $userId
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
}
