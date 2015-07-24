<?php
namespace mikk150\Visma;

use \OAuth2;

/**
 *
 */

class Client
{
    private $_oauth2;
    private $_storage;
    private $_config;

    private $_id;
    private $_secret;

    public function __construct($secret, $id, IStorage $tokenStorage = null, Config\IConfig $config = null, \OAuth2\Client $oauth2 = null)
    {

        // Token Storage
        if (!$tokenStorage) {
            $tokenStorage = new SessionStorage;
        }

        $this->_storage = $tokenStorage;

        // Oauth
        if (!$oauth2) {
            $oauth2 = new \OAuth2\Client($id, $secret, OAuth2\Client::AUTH_TYPE_AUTHORIZATION_BASIC);
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
        if (!$this->_storage->getRefreshToken() && !$this->_storage->getAccessToken()) {
            //Get the token by logging in
            if (!$this->_storage->getState() && !isset($_GET['code']) && !isset($_GET['error'])) {
                $this->getAuthCode();
            } else if (!$this->_storage->getState() && isset($_GET['code'])) {
                $this->getAuthToken($_GET['code']);
            } else if (isset($_GET['error'])) {
                $this->handleError($_GET['error']);
            }
        } else if ($this->tokenExpired()) {
            $this->refreshAuthToken($this->_storage->getRefreshToken());
            //Get the token by refresh
        } else {

        }
        $this->_oauth2->setAccessTokenType(OAuth2\Client::ACCESS_TOKEN_BEARER);
        $this->_oauth2->setAccessToken($this->_storage->getAccessToken());
    }

    public function handleError($error)
    {
        echo 'error';
        die();
    }

    public function tokenExpired()
    {
        return $this->_storage->getTimeout() > time();
    }

    public function getAuthToken($code)
    {
        $params = array(
            'code' => $code,
            'redirect_uri' => $this->_config->redirectUri(),
        );

        $at = $this->_oauth2->getAccessToken($this->_config->tokenEndpoint(), 'authorization_code', $params);
        $this->_storage->setAccessToken($at['result']['access_token']);
        $this->_storage->setRefreshToken($at['result']['refresh_token']);
        $this->_storage->setTimeout($at['result']['expires_in']);
    }
    public function refreshAuthToken($refreshToken)
    {
        $params = array(
            'refresh_token' => $refreshToken,
        );

        $at = $this->_oauth2->getAccessToken($this->_config->tokenEndpoint(), 'refresh_token', $params);
        $this->_storage->setAccessToken($at['result']['access_token']);
        $this->_storage->setRefreshToken($at['result']['refresh_token']);
        $this->_storage->setTimeout($at['result']['expires_in']);
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
    public function setTimeout($value);
    public function getTimeout();
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
