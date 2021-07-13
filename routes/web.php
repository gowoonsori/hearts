<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/login/{provider}','SocialController@execute');

Route::get('/login/oauth2/code/{provider}','SocialController@execute');
Route::get('/login/oauth2/code/naver','SocialController@show');
