<?php


namespace App\utils;

class ApiResult
{
    /**
     * @param $success bool
     * @param $response Object | array | string | integer | bool
     * @return array
     */
    public static function of(bool $success,$response): array
    {
        return [
            "success" => $success,
            "response" => $response
        ];
    }
}
