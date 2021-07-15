<?php


namespace App\Exceptions;


class BadRequestException extends \Exception
{
    protected $code = 400;
    protected $message = '잘못된 요청입니다.';
}
