<?php

namespace paslandau\WebAutomator\SubmissionTools\Submission\Session;


use GuzzleHttp\Cookie\CookieJar;

class GuzzleSessionFactory implements SessionFactoryInterface {
    private $cookieJarClass;

    function __construct($cookieJarClass = null)
    {
        if($cookieJarClass === null){
            $cookieJarClass = CookieJar::class;
        }
        $this->cookieJarClass = $cookieJarClass;
    }

    /**
     * @return SessionInterface
     */
    public function createSession()
    {
        $jar = new $this->cookieJarClass();
//        $f = IOUtil::GetUniqueFilename($this->curlSessionCookieDirectory, "cookies", null, ".txt");
//        IOUtil::writeFileContent($f, "");
        $config = [];
//        $config = [
//            'curl' => [
////                CURLOPT_COOKIEJAR => $f,
////                CURLOPT_COOKIEFILE => $f,
////                CURLOPT_AUTOREFERER => true,
////                CURLOPT_FOLLOWLOCATION => true
////                CURLOPT_SSL_VERIFYPEER => false,
////                CURLOPT_SSL_VERIFYHOST => false,
////                CURLOPT_CAINFO => null,
////                CURLOPT_CAPATH => null,
//            ],
//        ];
        $session = new GuzzleSession($jar, $config, false);
        return $session;
    }


}