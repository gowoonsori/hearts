<?php


namespace App\Exceptions;


class UnauthorizeException extends \Exception
{
    protected $code = 401;
    protected $message = '인증되지 않은 사용자입니다.';
}
