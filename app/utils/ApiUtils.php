<?php

namespace App\utils;

use Illuminate\Http\JsonResponse;

class ApiUtils
{
    private static $successCode = 200;

    /**
     * @param $response Object | array | string | integer | bool
     * @return JsonResponse
     */
    public static function success($response) : JsonResponse
    {
        return response()->json(ApiResult::of(true,$response), self::$successCode,
            ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param $e string | \Throwable
     * @param $statusCode integer
     * @return JsonResponse
     */
    public static function error($e, int $statusCode) : JsonResponse
    {
        $error = ApiError::of($e,$statusCode);
        return response()->json(ApiResult::of(false,$error), $statusCode,
            ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }
}
