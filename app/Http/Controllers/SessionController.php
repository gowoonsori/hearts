<?php


namespace App\Http\Controllers;


use Illuminate\Http\RedirectResponse;
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
     * @return RedirectResponse
     */
    public function destroy(): RedirectResponse
    {
        $username = auth()->user()->name;
        auth()->logout();

        Log::info('Sign out: ' . $username);
        return redirect()->back();
    }
}
