<?php
namespace paslandau\WebAutomator\User\Account;

use paslandau\WebAutomator\SubmissionTools\Submission\Session\SessionInterface;
use paslandau\WebAutomator\User\Credentials\CredentialsInterface;
use paslandau\WebAutomator\User\Identity\IdentityInterface;

interface AccountInterface
{
    /**
     * @return CredentialsInterface
     */
    public function getCredentials();

    /**
     * @param CredentialsInterface $credentials
     */
    public function setCredentials($credentials);

    /**
     * @return IdentityInterface
     */
    public function getIdentity();

    /**
     * @param IdentityInterface $identity
     */
    public function setIdentity($identity);

    /**
     * @return SessionInterface
     */
    public function getSession();

    /**
     * @param SessionInterface $session
     */
    public function setSession($session);

    /**
     * @return string
     */
    public function getProxy();

    /**
     * @param string $proxy
     */
    public function setProxy($proxy = null);

    /**
     * @return string
     */
    public function getUserAgent();

    /**
     * @param string $userAgent
     */
    public function setUserAgent($userAgent);
}