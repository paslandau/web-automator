<?php
namespace paslandau\WebAutomator\User\Credentials;


interface CredentialsInterface {
    /**
     * @return string
     */
    public function getUsername();

    /**
     * @return string
     */
    public function getPassword();
} 