<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
            //
            'http://localhost:8000/user/category', //This is the url that I dont want Csrf for postman.
            'http://localhost:8000/user/post', //This is the url that I dont want Csrf for postman.
            'http://localhost:8000/user/post/1/like',
            'http://localhost:8000/user/post/1/like',
            'http://localhost:8000/post/share',
            'http://localhost:8000/user/post/1/like',
            'http://localhost:8000/search',
            'http://localhost:8000/user/category?categoryId=95',
            'http://localhost:8000/user/category?categoryId=96',
            'http://localhost:8000/user/post/77/like',
            'http://localhost:8000/post/77/share',
    ];
}
