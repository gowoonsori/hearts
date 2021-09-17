<?php

namespace App\Http\Middleware;

use App\Exceptions\UnAuthorizeException;
use App\JwtAuth;
use App\Models\User;
use App\utils\ExceptionMessage;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Token;
use function Sodium\crypto_pwhash_scryptsalsa208sha256_str;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     * @throws UnAuthorizeException
     */
    public function handle(Request $request, Closure $next): mixed
    {
        try {
            //get Token
            $rawToken = JwtAuth::getToken($request);

            //get user Id
            $token = new Token($rawToken);
            $payload = JWTAuth::decode($token);

            //Authorize
            $id = $payload['sub'];
            $user = Cache::remember(hash('sha256',$token), USER::CACHE_TIME, function() use($id) {
                return User::find($id);
            });
            auth()->login($user);

        } catch (Exception $e) {
            Log::error($e);
//            if ($e instanceof TokenInvalidException ||$e instanceof TokenExpiredException
//                    || $e instanceof TokenBlacklistedException) {
//                throw new UnAuthorizeException('유효하지 않은 토큰입니다.');
//            } else {
            return throw new UnAuthorizeException(ExceptionMessage::UNAUTHORIZE);
            //}
        }
        return $next($request);
    }
}
