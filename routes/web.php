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
        Route::get('/', 'CategoryController@getCategories');    //내 카테고리 조회
        Route::post('/','CategoryController@createCategory');   //카테고리 생성
    });

    //문구
    Route::prefix('post')->group(function(){
        Route::post('/','PostController@createPost');       //문구 생성
        Route::get('/all','PostController@getPosts');       //나의 모든 문구 조회
        Route::get('/','PostController@getPost');   //문구id로 문구 조회
        Route::get('/category/{categoryId}','PostController@getPostsByCategory'); //특정 카테고리의 나의 문구들 조회
        Route::get('/like','LikeController@getLikePosts');    //내가 좋아요한 문구 조회

        //좋아요
        Route::prefix('/{postId}/like')->group(function(){
            Route::patch('/','LikeController@likePost');    //좋아요
            Route::delete('/','LikeController@unlikePost'); //좋아요 취소
        });
    });
});

//문구 공유 횟수 증가
Route::patch('/post/{postId}/share', 'PostController@updateShareCount');

//Oauth Redirect url
Route::get('/login/{provider}','SocialController@execute');

//Callback URL
Route::get('/login/oauth2/code/{provider}','SocialController@execute');

//검색
Route::get('/search','SearchController@search');
