<?php


namespace App\Http\Controllers;

use App\Exceptions\NotFoundException;
use App\Services\UserSerivce;
use App\Services\UserService;
use App\utils\ApiUtils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param Request $request
     * @param integer $userId
     * @return JsonResponse
     * @throws NotFoundException
     */
    function get(Request $request, int $userId) : JsonResponse
    {
        return ApiUtils::success($this->userService->getInfo($userId));
    }




}
