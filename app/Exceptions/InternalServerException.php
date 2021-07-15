<?php


namespace App\Exceptions;


class InternalServerException extends \Exception
{
    protected $code = 500;
    protected $message = "서버에 오류가 발생하였습니다.";
}
