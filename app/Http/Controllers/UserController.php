<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\UserRepository;

class UserController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    function get(Request $request, $userId){
        $user = $this->userRepository->findById($userId);
        if(empty($user)){
            return redirect('/fail');
        }
        return $user;
    }




}
