<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/* 좋아요 뱃지 로드 */
Route::get('/api/hearts/badge.svg', [
    'as' => 'hearts.show',
    'uses' => 'HeartsController@show'
]);

Route::get('/session',function (Request $request){
    \Illuminate\Support\Facades\Log::info($request->ips());
    \Illuminate\Support\Facades\Log::info($request->userAgent());
    \Illuminate\Support\Facades\Log::info($request->ip());
    return $request->ip();
});
