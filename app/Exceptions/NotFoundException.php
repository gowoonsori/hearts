<?php


namespace App\Exceptions;


use App\utils\ExceptionMessage;

class NotFoundException extends \Exception
{
    protected $code = 404;
    protected $message = ExceptionMessage::NOTFOUND;
}
