<?php
/**
 *
 */
namespace mikk150\Visma\Config;

abstract class Base implements IConfig
{
    public function getScope()
    {
        return 'sales';
    }
    public function redirectUri()
    {
        throw new \Exception("Pls to have implement redirectUri");
    }
}
