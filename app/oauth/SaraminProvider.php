<?php


namespace App\oauth;

use Illuminate\Support\Arr;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class SaraminProvider extends AbstractProvider
{

    public const IDENTIFIER = 'SARAMIN';


//    protected $scopes = [
//        "openid",
//        "profile",
//        "email"
//    ];

    /**
     * {@inheritdoc}
     */
    protected function formatScopes(array $scopes, $scopeSeparator): string
    {
        return implode(' ', $scopes);
    }

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase(
            'https://sid.saramin.co.kr/oauth/authorize',
            $state
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl(): string
    {
        return 'https://sid.saramin.co.kr/oauth/token';
    }

    /**
     * {@inheritdoc}
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(
            'https://openapi.saramin.co.kr/api/user/oauth/user',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ],
                //'verify' => false,
            ]);
        dd($response->getBody()->getContents());
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject($user)
    {
        dd($user);
        return (new User)->setRaw($user)->map([
            'name' => Arr::get($user, 'response.name'),
            'email' => Arr::get($user, 'response.email'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code): array
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }

}
