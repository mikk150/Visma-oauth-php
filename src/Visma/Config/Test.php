<?php
namespace mikk150\Visma\Config;

class Test extends Base
{
    public function authEndpoint()
    {
        return 'https://auth-sandbox.test.vismaonline.com/eaccountingapi/oauth/authorize';
    }
    public function tokenEndpoint()
    {
        return 'https://auth-sandbox.test.vismaonline.com/eaccountingapi/oauth/token';
    }
    public function getScope()
    {
        return 'sales';
    }
}
