<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function (){
    return 'hello';
});

Route::get('/fail', function (){
    throw new \App\Exceptions\NotFoundException("not found");
});

Route::get('/success',function(){
    return 'success';
});

//내 정보
Route::prefix('user/{userId}')->group(function(){
    //개인 정보 조회
    Route::get('/','UserController@get');

    //카테고리
    Route::prefix('category')->group(function(){
        Route::get('/', 'CategoryController@getCategories');
        Route::post('/','CategoryController@createCategory');
    });

    //문구
    Route::prefix('post')->group(function(){
        Route::post('/','PostController@createPost');
        Route::get('/{postId}','PostController@getPost');
    });
});

//Oauth Redirect
Route::get('/login/{provider}','SocialController@execute');

//Callback URL
Route::get('/login/oauth2/code/{provider}','SocialController@execute');
Route::get('/login/oauth2/code/naver','SocialController@show');
