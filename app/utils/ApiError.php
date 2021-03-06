<?php


namespace App\utils;

use Throwable;

class ApiError
{
    /**
     * @param $e  string | Throwble
     * @param $status integer
     * @return array
     */
    public static function of($e, int $status) : array
    {
        /*Throwable 형태이면 getMessage 로 message 를 얻고
        String 형태이면 message 로 간주*/
        if($e instanceof Throwable){
            $message = $e->getMessage();
        }else $message = $e;

        return [
            "status" => $status,
            "message" => $message,
        ];
    }

}
