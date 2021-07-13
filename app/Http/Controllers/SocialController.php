<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

   public function execute(Request $request, $provider){
        if (! $request->has('code')){
            return $this->redirectToProvider($provider);
        }
        return $this->handleProviderCallback($provider);
   }

   public function show(Request $request){
        return "hello";
   }

   protected function redirectToProvider($provider){
        return Socialite::driver($provider)->redirect();
   }

   protected function handleProviderCallback($provider){
       $socialData = Socialite::driver($provider)->user();
        dd($socialData);
       // 필수 정보 조회 성공 여부 확인
       if (empty($socialData->token)) {
           Log::info('Loading' . $provider . ' access token is fail');
           return redirect('/');
       }

       if (empty($socialData->getEmail())) {
           Log::info('Loading' . $provider . ' user email is fail');
           return redirect('/');
       }

       dd($socialData);


       auth()->login($user);
       Log::info('Sign in: ' . auth()->user()->name);
       return redirect()->back();
   }
}
