<?php

namespace App\Oauth;

use \SocialiteProviders\Manager\SocialiteWasCalled;

class SaraminExtendSocialite
{
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite('saramin', \App\Oauth\SaraminProvider::class);
    }
}
