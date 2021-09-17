<?php


namespace App\Exceptions;


use App\utils\ExceptionMessage;

class InternalServerException extends \Exception
{
    protected $code = 500;
    protected $message = ExceptionMessage::INTERNAL;
}
