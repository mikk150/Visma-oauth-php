<?php
namespace mikk150\Visma\Config;

class Live extends Base
{
    public function authEndpoint()
    {
        return 'https://auth.vismaonline.com/eaccountingapi/oauth/authorize';
    }
    public function tokenEndpoint()
    {
        return 'https://auth.vismaonline.com/eaccountingapi/oauth/token';
    }
    public function getScope()
    {
        return 'sales';
    }
}
