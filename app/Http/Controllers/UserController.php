<?php


namespace App\Http\Controllers;

use App\Exceptions\UnauthorizeException;
use App\Services\UserService;
use App\utils\ApiUtils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws UnauthorizeException
     */
    function get(Request $request) : JsonResponse
    {
        $user = Auth::user();
        if(empty($user)) throw new UnauthorizeException('인증되지 않은 사용자입니다.');

        return ApiUtils::success($user);
    }




}
