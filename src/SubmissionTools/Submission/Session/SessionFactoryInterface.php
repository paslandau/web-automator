<?php
/**
 * Created by PhpStorm.
 * User: Hirnhamster
 * Date: 07.10.2014
 * Time: 11:24
 */

namespace paslandau\WebAutomator\SubmissionTools\Submission\Session;


interface SessionFactoryInterface {
    /**
     * @return SessionInterface
     */
    public function createSession();
} 