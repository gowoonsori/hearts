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
            'http://localhost:8000/user/1/category', //This is the url that I dont want Csrf for postman.
            'http://localhost:8000/user/1/post', //This is the url that I dont want Csrf for postman.
            'http://localhost:8000/user/1/post/1/like',
            'http://localhost:8000/user/1/post/1/like',
            'http://localhost:8000/post/1/share'
    ];
}
