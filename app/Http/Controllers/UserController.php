<?php


namespace App\Http\Controllers;

use App\Exceptions\NotFoundException;
use App\utils\ApiUtils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;

class UserController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     * @param integer $userId
     * @return JsonResponse
     * @throws NotFoundException
     */
    function get(Request $request, int $userId) : JsonResponse
    {
        $user = $this->userRepository->findById($userId);
        if(empty($user)){
            throw new NotFoundException('존재하지 않은 사용자입니다.');
        }
        return ApiUtils::success($user);
    }




}
