<?php
namespace mikk150\Visma;

/**
 *
 */
interface IStorage
{
    public function setAccessToken($value);
    public function getAccessToken();
    public function setRefreshToken($value);
    public function getRefreshToken();
    public function setState($value);
    public function getState();
    public function setTimeout($value);
    public function getTimeout();
}
