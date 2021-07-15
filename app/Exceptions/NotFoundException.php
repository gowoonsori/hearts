<?php


namespace App\Exceptions;


class NotFoundException extends \Exception
{
    protected $code = 404;
    protected $message = '찾을 수 없습니다.';
}
