<?php


namespace App\Exceptions;


use App\utils\ExceptionMessage;

class BadRequestException extends \Exception
{
    protected $code = 400;
    protected $message = ExceptionMessage::BADREQUEST;
}
