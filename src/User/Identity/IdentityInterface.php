<?php
namespace paslandau\WebAutomator\User\Identity;

interface IdentityInterface
{
    /**
     * Returns "$firstname $lastname"
     * @return string
     */
    public function getFullName();

    public function toArray();

    public function fillFromArray($arr, $strict = true);

    /**
     * @return \DateTime
     */
    public function getBirthday();

    /**
     * @param \DateTime $birthday
     */
    public function setBirthday($birthday);

    /**
     * @return string
     */
    public function getCity();

    /**
     * @param string $city
     */
    public function setCity($city);

    /**
     * @return string
     */
    public function getCountry();

    /**
     * @param string $country
     */
    public function setCountry($country);

    /**
     * @return string
     */
    public function getFax();

    /**
     * @param string $fax
     */
    public function setFax($fax);

    /**
     * @return string
     */
    public function getFirstname();

    /**
     * @param string $firstname
     */
    public function setFirstname($firstname);

    /**
     * @return string
     */
    public function getIdentityEmail();

    /**
     * @param string $identity_email
     */
    public function setIdentityEmail($identity_email);

    /**
     * @return string
     */
    public function getIdentityPassword();

    /**
     * @param string $identity_password
     */
    public function setIdentityPassword($identity_password);

    /**
     * @return string
     */
    public function getIdentityUsername();

    /**
     * @param string $identity_username
     */
    public function setIdentityUsername($identity_username);

    /**
     * @return string
     */
    public function getJob();

    /**
     * @param string $job
     */
    public function setJob($job);

    /**
     * @return string
     */
    public function getLastname();

    /**
     * @param string $lastname
     */
    public function setLastname($lastname);

    /**
     * @return string
     */
    public function getMobile();

    /**
     * @param string $mobile
     */
    public function setMobile($mobile);

    /**
     * @return string
     */
    public function getNo();

    /**
     * @param string $no
     */
    public function setNo($no);

    /**
     * @return string
     */
    public function getPhone();

    /**
     * @param string $phone
     */
    public function setPhone($phone);

    /**
     * @return string
     */
    public function getSalutation();

    /**
     * @param string $salutation
     */
    public function setSalutation($salutation);

    /**
     * @return string
     */
    public function getSex();

    /**
     * @param string $sex
     */
    public function setSex($sex);

    /**
     * @return string
     */
    public function getState();

    /**
     * @param string $state
     */
    public function setState($state);

    /**
     * @return string
     */
    public function getStreet();

    /**
     * @param string $street
     */
    public function setStreet($street);

    /**
     * @return string
     */
    public function getZip();

    /**
     * @param string $zip
     */
    public function setZip($zip);

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @param string $email
     */
    public function setEmail($email);
}