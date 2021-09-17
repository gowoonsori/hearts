<?php


namespace App;


use App\Exceptions\UnAuthorizeException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;

class JwtAuth extends Facade
{
    public const HEADER = 'auth_token';

    /**
     * Bearer Token 에서 Token 값만 분리
     * @param Request $request
     * @return string
     * @throws UnAuthorizeException
     */
    public static function getToken(Request $request): string
    {
        $rawToken = $request->cookie(self::HEADER);
        $token = explode(" ", $rawToken);
        if (count($token) != 2 || $token[0] != 'Bearer') throw new UnAuthorizeException();

        return $token[1];
    }

    /**
     * Bearer Token 생성
     * @param $user
     * @return string
     * @throws UnAuthorizeException
     */
    public static function createToken($user): string
    {
        $token = auth()->claims(['iss' => 'hearts'])->login($user);
        if(!$token){
            throw new UnAuthorizeException();
        }
        return 'Bearer ' . $token;
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'tymon.jwt.auth';
    }
}
