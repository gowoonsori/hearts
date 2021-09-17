<?php

namespace App\utils;

use Illuminate\Http\JsonResponse;

class ApiUtils
{

    /**
     * @param $response Object | integer | bool | array | string
     * @param int $code
     * @return JsonResponse
     */
    public static function success(object|int|bool|array|string $response, int $code = 200) : JsonResponse
    {
        return response()->json(ApiResult::of(true,$response), $code,
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
