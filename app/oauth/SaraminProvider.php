<?php


namespace App\Oauth;

use Illuminate\Support\Arr;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class SaraminProvider extends AbstractProvider
{

    public const IDENTIFIER = 'SARAMIN';
    private const OAUTH_URL = 'https://sid.saramin.co.kr';
    private const GATEWAY_URL = 'https://openapi.saramin.co.kr';

    protected $scopes = [
        "openid",
        "profile",
        "email",
        "resume",
    ];

    /**
     * {@inheritdoc}
     */
    protected $scopeSeparator = ' ';



    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase(
            self::OAUTH_URL . '/oauth/authorize',
            $state
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl(): string
    {
        return self::OAUTH_URL . '/oauth/token';
    }

    /**
     * {@inheritdoc}
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(
            self::GATEWAY_URL . '/api/user/oauth/user',
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]);
        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject($user): User
    {
        return (new User())->setRaw($user)->map([
            'id' => Arr::get($user, 'sub'),
            'name' => Arr::get($user, 'name'),
            'email' => Arr::get($user, 'email'),
        ]);
    }
}
