<?php

namespace App\Http\Controllers;

use App\Exceptions\BadRequestException;
use App\Exceptions\InternalServerException;
use App\Exceptions\UnAuthorizeException;
use App\JwtAuth;
use App\Oauth\SaraminProvider;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Social login Handler
     * @param Request $request
     * @param string $provider
     * @throws BadRequestException
     * @throws InternalServerException
     * @throws UnAuthorizeException
     */
    public function execute(Request $request, string $provider)
    {
        //인증서버로 redirect
        if (! $request->has('code')){
            return $this->redirectToProvider($provider);
        }
        //token 을 가지고 있다면 token 서버로 redirect
        return $this->handleProviderCallback($provider);
    }

    /**
     * 요청 oauth 서버 프로바이더에 맞게 리다이렉트
     * @param string $provider
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws BadRequestException
     */
    protected function redirectToProvider(string $provider): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        return match ($provider) {
            SaraminProvider::IDENTIFIER => Socialite::driver($provider)
                ->redirect(),
            default => throw new BadRequestException("잘못된 요청입니다."),
        };
    }

    /**
     * 사용자 정보 얻어온 후, 자동 회원가입과 로그인 수행후 front home page 로 리다이렉트
     * @param string $provider
     * @return RedirectResponse
     * @throws InternalServerException
     * @throws UnAuthorizeException
     */
    protected function handleProviderCallback(string $provider): RedirectResponse
    {
        $socialData = Socialite::driver($provider)->user();

        // 사용자 정보 조회 성공 여부 확인
        if (empty($socialData->token)) {
            Log::info('Loading' . $provider . ' access token is fail');
            throw new UnAuthorizeException();
        }
        if (empty($socialData->getEmail())) {
            Log::info('Loading' . $provider . ' user email is fail');
            throw new UnAuthorizeException();
        }
        if (empty($socialData->getName())) {
            Log::info('Loading' . $provider . ' user name is fail');
            throw new UnAuthorizeException();
        }

        //사용자 등록 여부 확인
        $socialId = $socialData->getId();
        $user = $this->userService->getUserBySocialId($socialId);

        //사용자 추가
        if(empty($user)){
            $user = $this->userService->createUser($socialData);
            Log::info('Sign Up: ' . $socialData->getEmail());
        }

        //JWT 발급
        $token = JwtAuth::createToken($user);
        Log::info("JWT 발급 : User Email => " . $user->email . ", token => " . $token);

        //쿠키에 토큰 삽입
        $cookie = Cookie::create(JwtAuth::HEADER, $token,time() + 7200);
        return redirect()->away('http://localhost/')->withCookie($cookie);
    }
}
