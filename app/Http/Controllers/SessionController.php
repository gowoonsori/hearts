<?php


namespace App\Http\Controllers;


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
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy()
    {
        $username = auth()->user()->name;
        auth()->logout();

        Log::info('Sign out: ' . $username);
        return redirect()->back();
    }
}
