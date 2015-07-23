<?php
namespace mikk150\Visma;

use OAuth2;

/**
 *
 */

class Client
{
    private $_oauth2;
    private $_storage;
    private $_config;

    public function __construct($secret, $id, IStorage $tokenStorage = null, IConfig $config = null, \OAuth2\Client $oauth2 = null)
    {

        // Token Storage
        if (!$tokenStorage) {
            $tokenStorage = new SessionStorage;
        }

        $this->_storage = $tokenStorage;

        // Oauth
        if (!$oauth2) {
            $oauth2 = new \OAuth2\Client($id, $secret);
        }

        $this->_oauth2 = $oauth2;

        //Config
        if (!$config) {
            $config = new DefaultConfig;
        }

        $this->_config = $config;

        $this->init();
    }

    public function init()
    {
        if (!$this->_storage->getState() && !isset($_GET['code'])) {
            $this->getAuthCode();
        } else if (!$this->_storage->getState() && isset($_GET['code'])) {
            $this->getAuthToken($_GET['code']);
        }
        $this->_oauth2->setAccessToken($this->_storage->getAccessToken());
    }

    public function getAuthToken($code)
    {
        $params = array('code' => $code, 'redirect_uri' => $this->_config->redirectUri());
        $at = $this->_oauth2->getAccessToken($this->_config->tokenEndpoint(), 'authorization_code', $params);
        $this->_storage->setState(1);
        $this->_storage->setAccessToken($at['result']['access_token']);
    }
    public function getAuthCode()
    {
        $params = array(
            'scope' => $this->_config->getScope(),
            'state' => rand(10000, 99999),
        );
        $auth_url = $this->_oauth2->getAuthenticationUrl($this->_config->authEndpoint(), $this->_config->redirectUri(), $params);
        header('Location: ' . $auth_url);
        die('Redirect');
    }
    public function getClient()
    {
        return $this->_oauth2;
    }
}

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
}
interface IConfig
{
    public function authEndpoint();
    public function tokenEndpoint();
    public function redirectUri();
    public function getScope();
}

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
}
class DefaultConfig implements IConfig
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
    public function redirectUri()
    {
        return 'http://localhost/newtime/Visma/';
    }
}
class TestConfig implements IConfig
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
    public function redirectUri()
    {
        return 'http://localhost/newtime/Visma/';
    }
}
class FBConfig implements IConfig
{
    public function authEndpoint()
    {
        return 'https://graph.facebook.com/oauth/authorize';
    }
    public function tokenEndpoint()
    {
        return 'https://graph.facebook.com/oauth/access_token';
    }
    public function getScope()
    {
        return '';
    }
    public function redirectUri()
    {
        return 'http://localhost/newtime/Visma/';
    }
}
class GoogConfig implements IConfig
{
    public function authEndpoint()
    {
        return 'https://accounts.google.com/o/oauth2/auth';
    }
    public function tokenEndpoint()
    {
        return 'https://accounts.google.com/o/oauth2/token';
    }
    public function getScope()
    {
        return 'https://www.googleapis.com/auth/drive.readonly';
    }
    public function redirectUri()
    {
        return 'http://localhost/newtime/Visma/';
    }
}
