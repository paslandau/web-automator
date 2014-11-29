<?php

namespace paslandau\WebAutomator\SubmissionTools\Submission\Session;


class SimpleSession implements SessionInterface
{
    /**
     * @var boolean
     */
    private $loggedIn;

    /**
     * @param bool $loggedIn
     */
    function __construct($loggedIn = false)
    {
        $this->loggedIn = $loggedIn;
    }

    /**
     * @return boolean
     */
    public function isLoggedIn()
    {
        return $this->loggedIn;
    }

    /**
     *
     */
    public function setLoggedIn()
    {
        $this->loggedIn = true;
    }

    /**
     *
     */
    public function setLoggedOut()
    {
        $this->loggedIn = false;
    }

    /**
     * Returns an empty array cause no cookies are specified
     * @return mixed[]
     */
    public function getCookiesAsArray()
    {
        return [];
    }

    /**
     * Has no effect since cookies are not used
     * @param array $cookies
     */
    public function setCookiesFromArray(array $cookies)
    {
    }

    /**
     * Returns an empty json string "{}" cause no cookies are specified
     * @return string
     */
    public function getCookiesAsJson()
    {
        return "{}";
    }

    /**
     * Has no effect since cookies are not used
     * @param $jsonString
     */
    public function setCookiesFromJson($jsonString)
    {
    }
}