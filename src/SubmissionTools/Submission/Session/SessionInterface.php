<?php

namespace paslandau\WebAutomator\SubmissionTools\Submission\Session;


interface SessionInterface
{
    /**
     * @return boolean
     */
    public function isLoggedIn();

    /**
     * @return void
     */
    public function setLoggedIn();

    /**
     * @return void
     */
    public function setLoggedOut();

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
    public function getCookiesAsArray();

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
    public function setCookiesFromArray(array $cookies);

    /**
     * Gets all current cookies as json encoded string.
     * @return string
     */
    public function getCookiesAsJson();

    /**
     * Adds all cookies from the json encoded $jsonString
     * @param $jsonString
     */
    public function setCookiesFromJson($jsonString);
}