<?php
namespace mikk150\Visma\Config;

interface IConfig
{
    public function authEndpoint();
    public function tokenEndpoint();
    public function redirectUri();
    public function getScope();
}
