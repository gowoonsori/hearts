<?php

namespace App\Http\Controllers;

use App\Exceptions\BadRequestException;
use App\Exceptions\InternalServerException;
use App\Exceptions\UnauthorizeException;
use App\Services\UserService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
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
     *
     * @param Request $request
     * @param string $provider
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Redirector|RedirectResponse|Application
     * @throws BadRequestException
     * @throws InternalServerException
     * @throws UnauthorizeException
     */
   public function execute(Request $request, string $provider): \Symfony\Component\HttpFoundation\RedirectResponse|Redirector|RedirectResponse|Application
   {
       //인증서버로 redirect
        if (! $request->has('code')){
            return $this->redirectToProvider($provider);
        }

        //token 을 가지고 있다면 token 서버로 redirect
       return $this->handleProviderCallback($provider);
   }

    /**
     * Redirect the user to the Social Login Provider's authentication page.
     *
     * @param string $provider
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws BadRequestException
     */
   protected function redirectToProvider(string $provider): \Symfony\Component\HttpFoundation\RedirectResponse
   {
       return match ($provider) {
           'saramin' => Socialite::driver($provider)
               ->redirect(),
           default => throw new BadRequestException("잘못된 요청입니다."),
       };
   }

    /**
     * Obtain the user information from the Social Login Provider.
     *
     * @param string $provider
     * @return Application|Redirector|RedirectResponse
     * @throws UnauthorizeException
     * @throws InternalServerException
     */
   protected function handleProviderCallback(string $provider): Redirector|RedirectResponse|Application
   {
       $socialData = Socialite::driver($provider)->user();
       // 사용자 정보 조회 성공 여부 확인
       if (empty($socialData->token)) {
           Log::info('Loading' . $provider . ' access token is fail');
           throw new UnauthorizeException('인증되지 않은 사용자입니다.');
       }
       if (empty($socialData->getEmail())) {
           Log::info('Loading' . $provider . ' user email is fail');
           throw new UnauthorizeException('인증되지 않은 사용자입니다.');
       }
       if (empty($socialData->getName())) {
           Log::info('Loading' . $provider . ' user name is fail');
           throw new UnauthorizeException('인증되지 않은 사용자입니다.');
       }

       //사용자 등록 여부 확인
       $userMail = $socialData->getEmail();
       $user = $this->userService->getUserByEmail($userMail);

       //사용자 추가
       if(empty($user)){
           $user = $this->userService->createUser($socialData);
           Log::info('Sign Up: ' . $socialData->getEmail());
       }

       Auth::login($user);
       Log::info('Sign in: ' . auth()->user()->name);
       return redirect('/success');
   }
}
