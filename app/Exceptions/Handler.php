<?php

namespace App\Exceptions;

use App\utils\ApiUtils;
use App\utils\ExceptionMessage;
use Facade\FlareClient\Api;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
        if($e instanceof NotFoundHttpException) $e = new NotFoundException();
        else if(! ($e instanceof BadRequestException || $e instanceof UnAuthorizeException
                || $e instanceof ForbiddenException || $e instanceof InternalServerException
                || $e instanceof NotFoundException) || $e instanceof NotFoundHttpException){
            $e = new InternalServerException();
        }
        Log::error($e);

        $statusCode = $e->getCode();
        return ApiUtils::error($e,$statusCode);
    }
}
