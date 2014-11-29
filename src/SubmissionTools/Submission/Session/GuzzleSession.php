<?php

namespace paslandau\WebAutomator\SubmissionTools\Submission\Session;


use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;

class GuzzleSession extends SimpleSession
{
    /**
     * @var mixed[]
     */
    private $requestOptions;

    /**
     * @var CookieJar
     */
    private $cookieJar;

    function __construct(CookieJar $cookieJar, array $requestOptions = null, $loggedIn = false)
    {
        $this->cookieJar = $cookieJar;
        $this->requestOptions = $requestOptions;
        parent::__construct($loggedIn);
    }

    public function getGuzzleOptions()
    {
        $options = ['cookies' => $this->cookieJar, 'config' => $this->requestOptions];
        return $options;
    }

    /**
     *   Returns cookies as associative array:
     *   $cookie = [
     *  'Name'     => null,
     *  'Value'    => null,
     *  'Domain'   => null,
     *  'Path'     => '/',
     *  'Max-Age'  => null,
     *  'Expires'  => null,
     *  'Secure'   => false,
     *  'Discard'  => false,
     *  'HttpOnly' => false
     *  ];
     * @return mixed[]
     */
    public function getCookiesAsArray()
    {
        $cookies = $this->cookieJar->toArray();
        return $cookies;
    }

    /**
     * @param array $cookies - expectes the following keys per element:
     * $cookie = [
     *  'Name'     => null,
     *  'Value'    => null,
     *  'Domain'   => null,
     *  'Path'     => '/',
     *  'Max-Age'  => null,
     *  'Expires'  => null,
     *  'Secure'   => false,
     *  'Discard'  => false,
     *  'HttpOnly' => false
     *  ];
     */
    public function setCookiesFromArray(array $cookies)
    {
        foreach ($cookies as $cookie) {
            $setCookie = new SetCookie($cookie);
            $this->cookieJar->setCookie($setCookie);
        }
    }

    /**
     * Gets all current cookies as json encoded string.
     * @return string
     */
    public function getCookiesAsJson()
    {
        $cookies = $this->getCookiesAsArray();
        $json = json_encode($cookies);
        return $json;
    }

    /**
     * Adds all cookies from the json encoded $jsonString
     * @param $jsonString
     */
    public function setCookiesFromJson($jsonString)
    {
        $cookies = json_decode($jsonString, true);
        if ($cookies === false && json_last_error() !== JSON_ERROR_NONE) {
            throw new \UnexpectedValueException("Could not set cookies due to invalid json string. Error: " . json_last_error_msg());
        }
        if (!is_array($cookies)) {
            $cookies = [];
        }
        $this->setCookiesFromArray($cookies);
    }
}