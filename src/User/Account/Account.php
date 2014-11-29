<?php

namespace paslandau\WebAutomator\User\Account;


use paslandau\WebAutomator\SubmissionTools\Submission\Session\SessionInterface;
use paslandau\WebAutomator\User\Credentials\Credentials;
use paslandau\WebAutomator\User\Credentials\CredentialsInterface;
use paslandau\WebAutomator\User\Identity\Identity;
use paslandau\WebAutomator\User\Identity\IdentityInterface;

class Account implements AccountInterface
{
    /**
     * @var CredentialsInterface
     */
    protected $credentials;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var IdentityInterface
     */
    protected $identity;

    /**
     * @var string
     */
    private $proxy;

    /**
     * @var string
     */
    private $userAgent;

    /**
     * @param CredentialsInterface $credentials
     * @param IdentityInterface $identity
     * @param SessionInterface $session [optional]. Default: null.
     */
    function __construct(CredentialsInterface $credentials, IdentityInterface $identity, SessionInterface $session = null)
    {
        $this->credentials = $credentials;
        $this->identity = $identity;
        $this->session = $session;
    }

    public static function fromCsvArray($arr, SessionInterface $session)
    {
        $credentials = new Credentials($arr["credentials_username"], $arr["credentials_password"]);
        $identitiy = new Identity();
        $identitiy->fillFromArray($arr, false);
        $cookies = $arr["session_cookies"];
        if ($cookies !== null && trim($cookies) !== "") {
            $session->setCookiesFromJson($cookies);
        }
        $class = $arr["class"];
        $reflClass = new \ReflectionClass($class);
        $acc = $reflClass->newInstanceWithoutConstructor();
        $acc->setCredentials($credentials);
        $acc->setIdentity($identitiy);
        $acc->setSession($session);
//        $acc = new $class($credentials,$identitiy,$session);
        $acc->proxy = $arr["proxy"];
        $acc->userAgent = $arr["userAgent"];
        return $acc;
    }

    /**
     * @return CredentialsInterface
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * @param CredentialsInterface $credentials
     */
    public function setCredentials($credentials)
    {
        $this->credentials = $credentials;
    }

    /**
     * @return IdentityInterface
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * @param IdentityInterface $identity
     */
    public function setIdentity($identity)
    {
        $this->identity = $identity;
    }

    /**
     * @return SessionInterface
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param SessionInterface $session
     */
    public function setSession($session)
    {
        $this->session = $session;
    }

    /**
     * @return string
     */
    public function getProxy()
    {
        return $this->proxy;
    }

    /**
     * @param string $proxy
     */
    public function setProxy($proxy = null)
    {
        $this->proxy = $proxy;
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @param string $userAgent
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
    }

    public function toCsvArray()
    {
        $credentialsArr = [
            "credentials_username" => $this->credentials->getUsername(),
            "credentials_password" => $this->credentials->getPassword(),
        ];
        $identityArr = $this->identity->toArray();
        $sessionArr = [
            "session_cookies" => $this->session === null ? "" : $this->session->getCookiesAsJson()
        ];
        $accArr = [
            "class" => get_class($this),
            "userAgent" => $this->getUserAgent(),
            "proxy" => $this->getProxy()
        ];
        return array_merge($accArr, $credentialsArr, $identityArr, $sessionArr);
    }
}