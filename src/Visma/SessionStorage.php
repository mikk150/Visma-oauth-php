<?php
namespace mikk150\Visma;

/**
 *
 */
class SessionStorage implements IStorage
{
    public function __construct()
    {
        session_start();
        if ($this->getState() && !$this->getAccessToken()) {
            $this->setState(null);
        }
    }
    public function setAccessToken($value)
    {
        $_SESSION['accessToken'] = $value;
    }
    public function getAccessToken()
    {
        if (isset($_SESSION['accessToken'])) {
            return $_SESSION['accessToken'];
        }
    }
    public function setRefreshToken($value)
    {
        $_SESSION['refreshToken'] = $value;
    }
    public function getRefreshToken()
    {
        if (isset($_SESSION['refreshToken'])) {
            return $_SESSION['refreshToken'];
        }
    }
    public function getState()
    {
        if (isset($_SESSION['accessToken'])) {
            return $_SESSION['accessToken'];
        }
    }
    public function setState($value)
    {
        $_SESSION['accessToken'] = $value;
    }
    public function setTimeout($value)
    {
        $_SESSION['timeout'] = $value + time();
    }
    public function getTimeout()
    {
        if (isset($_SESSION['timeout'])) {
            return $_SESSION['timeout'];
        }
        return 0;
    }
}
