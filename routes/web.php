<?php

use Illuminate\Support\Facades\Route;

/*
 * 웹이 아닌 badge와 관련된 서비스는 /api로 시작
 * */
Route::prefix('api')->group(function(){

    /* 좋아요 뱃지 로드 */
    Route::get('/api/hearts/badge.svg',[
        'as' => 'hearts.getBadge',
        'uses' => 'HeartsController@show'
    ]);
});
