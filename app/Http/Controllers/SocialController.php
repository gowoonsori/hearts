<?php

namespace App\Http\Controllers;

use App\Exceptions\BadRequestException;
use App\Exceptions\InternalServerException;
use App\Exceptions\UnauthorizeException;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Handle social login process.
     *
     * @param Request $request
     * @param string $provider
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
   public function execute(Request $request, string $provider){
       //인증서버로 redirect
        if (! $request->has('code')){
            return $this->redirectToProvider($provider);
        }

        //token을 가지고 있다면 token서버로 redirect
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
       switch ($provider){
           case 'saramin':
               return Socialite::driver($provider)
                   ->redirect();
           default:
               throw new BadRequestException("잘못된 요청입니다.");
       }
   }

    /**
     * Obtain the user information from the Social Login Provider.
     *
     * @param string $provider
     * @return Application|Redirector|RedirectResponse
     * @throws UnauthorizeException
     * @throws InternalServerException
     */
   protected function handleProviderCallback(string $provider){
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
       $user = $this->userRepository->findByEmail($userMail);

       //사용자 추가
       if( empty($user)){
           $user = $this->userRepository->insert($socialData);
           Log::info('Sign Up: ' . $socialData->getEmail());
       }

       auth()->login($user);
       Log::info('Sign in: ' . auth()->user()->name);
       return redirect('/success');
   }
}
