<?php


namespace App\Exceptions;


class ForbiddenException extends \Exception
{
    protected $code = 403;
    protected $message = '접근 권한이 존재하지 않습니다.';
}
