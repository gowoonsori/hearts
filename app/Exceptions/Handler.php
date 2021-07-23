<?php

namespace App\Exceptions;

use App\utils\ApiUtils;
use Facade\FlareClient\Api;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
        });
    }

    //Exception Handling
    public function render($request,Throwable $e){
        //default 404 error
        $error = $e->getMessage() ? $e: '찾을 수 없습니다.';
        $statusCode = 404;

       if($e instanceof BadRequestException){
            $statusCode = 400;
        }else if($e instanceof UnauthorizeException){
           $statusCode = 401;
       }else if($e instanceof ForbiddenException){
           $statusCode = 403;
       }else if($e instanceof InternalServerException){
           $statusCode = 500;
       }

        return ApiUtils::error($error,$statusCode);
    }
}
