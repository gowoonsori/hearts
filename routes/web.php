<?php

use Illuminate\Support\Facades\Route;

//임시 url
Route::get('/',function(){
    return 'home';
})->name('home');

Route::get('/success',function(){
    return 'success';
});


//사이트의 모든 기능은 세션으로 사용자 구분
Route::group(['prefix'=> 'user', 'middleware'=>'auth'],function(){
    //개인 정보 조회
    Route::get('/','UserController@get');

    //카테고리
    Route::prefix('category')->group(function(){
        Route::get('/', 'CategoryController@getCategories');    //내 카테고리 조회
        Route::post('/','CategoryController@createCategory');   //카테고리 생성
        Route::patch('/','CategoryController@updateCategory');   //카테고리 수정
        Route::delete('/','CategoryController@deleteCategory');   //카테고리 삭제
    });

    //문구
    Route::prefix('post')->group(function(){
        Route::get('/','PostController@getPost');   //문구id로 문구 조회
        Route::post('/','PostController@createPost');       //문구 생성
        Route::patch('/','PostController@updatePost');       //문구 수정
        Route::delete('/','PostController@deletePost');       //문구 삭제
        Route::get('/all','PostController@getPosts');       //나의 모든 문구 조회
        Route::get('/category/{categoryId}','PostController@getPostsByCategory'); //특정 카테고리의 나의 문구들 조회
        Route::get('/like','LikeController@getLikePosts');    //내가 좋아요한 문구 조회

        //좋아요
        Route::prefix('/{postId}/like')->group(function(){
            Route::patch('/','LikeController@likePost');    //좋아요
            Route::delete('/','LikeController@unlikePost'); //좋아요 취소
        });

        //문구 공유 횟수 증가
        Route::patch('/{postId}/share', 'PostController@updateShareCount');
    });
});


/*
 * 사용자 세션이 없어도 접근할 수 있는 url
 * */

//로그인
//Oauth Redirect url
Route::get('/login/{provider}','SocialController@execute')->name('login');
//Oauth Callback URL
Route::get('/login/oauth2/code/{provider}','SocialController@execute');

//로그아웃
Route::post('/logout','SessionController@destroy')->name('logout');


//검색
Route::get('/search','SearchController@search');        //통합 검색
Route::get('/search/tag','SearchController@tagSearch');  //태그로 검색
Route::get('/search/post','SearchController@contentSearch'); //문구내용으로 검색
