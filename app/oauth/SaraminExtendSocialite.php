<?php

namespace App\oauth;

use \SocialiteProviders\Manager\SocialiteWasCalled;

class SaraminExtendSocialite
{
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite('saramin', \App\oauth\SaraminProvider::class);
    }
}
