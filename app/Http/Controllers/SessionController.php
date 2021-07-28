<?php


namespace App\Http\Controllers;


use App\utils\ApiUtils;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class SessionController extends Controller
{
    /**
     * SessionsController constructor.
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'destroy']);
    }

    /**
     * Sign out and destroy user's session data
     *
     * @return JsonResponse
     */
    public function destroy(): JsonResponse
    {
        $username = auth()->user()->name;
        auth()->logout();

        Log::info('Sign out: ' . $username);
        return ApiUtils::success(true);
    }
}
