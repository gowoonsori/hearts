<?php


namespace App\Http\Controllers;

use App\Exceptions\InternalServerException;
use App\Exceptions\UnAuthorizeException;
use App\JwtAuth;
use App\Services\UserService;
use App\utils\ApiUtils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Token;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * /user
     * 사용자 정보 조회
     * @return JsonResponse
     * @throws UnAuthorizeException|InternalServerException
     */
    function get() : JsonResponse
    {
        $user = Auth::user();
        if(empty($user)) throw new UnAuthorizeException();
        //좋아요 정보 포함
        $res = $this->userService->getUserWithLikes($user);
        return ApiUtils::success($res);
    }

    /**
     * Sign out and destroy user's session data
     *
     * @param Request $request
     * @return JsonResponse
     * @throws UnAuthorizeException
     */
    public function destroy(Request $request): JsonResponse
    {
        $token = new Token(JwtAuth::getToken($request));
        JwtAuth::setToken($token);
        JwtAuth::invalidate($token);
        return ApiUtils::success(true);
    }
}
