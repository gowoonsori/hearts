<?php


namespace App\oauth;

use Illuminate\Support\Arr;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class SaraminProvider extends AbstractProvider
{

    public const IDENTIFIER = 'SARAMIN';

    protected $scopes = [
        "openid",
        "profile",
        "email"
    ];

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(
            'https://sid.saramin.co.kr/oauth/authorize',
            $state
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://sid.saramin.co.kr/oauth/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(
            'https://ea-auth-apigateway.sri-kube.saraminhr.co.kr/api/user/oauth/user',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]);

        return json_decode($response->getBody()->getContents(), true);
    }


    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id' => $user['id'],
            'email' => $user['email'],
            'name' => $user['name'],
        ]);
    }
}
