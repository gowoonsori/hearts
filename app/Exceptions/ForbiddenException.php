<?php


namespace App\Exceptions;


use App\utils\ExceptionMessage;

class ForbiddenException extends \Exception
{
    protected $code = 403;
    protected $message = ExceptionMessage::FORBIDDEN;
}
