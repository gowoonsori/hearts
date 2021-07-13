<?php

namespace App\Http\Controllers;

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
        $user = Socialite::driver($provider)->user();

        dd($user);
        return $user;
   }
}
