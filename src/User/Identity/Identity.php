<?php
namespace paslandau\WebAutomator\User\Identity;

use paslandau\DataFiltering\Util\ReflectionUtil;

class Identity implements IdentityInterface
{
    /**
     *
     * @var string
     */
    private $salutation;
    /**
     *
     * @var string
     */
    private $firstname;
    /**
     *
     * @var string
     */
    private $lastname;
    /**
     *
     * @var string
     */
    private $sex;
    /**
     *
     * @var string
     */
    private $street;
    /**
     *
     * @var string
     */
    private $no;
    /**
     *
     * @var string
     */
    private $city;
    /**
     *
     * @var string
     */
    private $zip;
    /**
     *
     * @var string
     */
    private $country;
    /**
     *
     * @var string
     */
    private $phone;
    /**
     *
     * @var string
     */
    private $mobile;
    /**
     *
     * @var string
     */
    private $identity_username;
    /**
     *
     * @var string
     */
    private $identity_password;
    /**
     *
     * @var string
     */
    private $identity_email;
    /**
     *
     * @var string
     */
    private $job;
    /**
     *
     * @var string
     */
    private $state;
    /**
     *
     * @var string
     */
    private $fax;

    /**
     * @var \DateTime
     */
    private $birthday;

    /**
     * @var string
     */
    private $email = "";

    /**
     * Returns "$firstname $lastname"
     * @return string
     */
    public function getFullName(){
        $nameParts = array();
        $nameParts[] = $this->firstname;
        $nameParts[] = $this->lastname;
        foreach($nameParts as $key => $p){
            if($p === null || $p === ""){
                unset($nameParts[$key]);
            }
        }
        return implode(" ",$nameParts);
    }

    public function toArray(){
        $arr = ReflectionUtil::ObjectToArray($this);
        if($arr["birthday"] !== null){
            $b = $arr["birthday"];
            /** @var \DateTime $b */
            $arr["birthday"] = $b->format("d.m.Y");
        }
        return $arr;
    }

    public function fillFromArray($arr, $strict = true){
        if(array_key_exists("birthday", $arr)){
            $arr["birthday"] = \DateTime::createFromFormat("d.m.Y", $arr["birthday"]);
        }
        ReflectionUtil::FillObjectFromArray($this, $arr, $strict);
    }

    /**
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param \DateTime $birthday
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * @param string $fax
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getIdentityEmail()
    {
        return $this->identity_email;
    }

    /**
     * @param string $identity_email
     */
    public function setIdentityEmail($identity_email)
    {
        $this->identity_email = $identity_email;
    }

    /**
     * @return string
     */
    public function getIdentityPassword()
    {
        return $this->identity_password;
    }

    /**
     * @param string $identity_password
     */
    public function setIdentityPassword($identity_password)
    {
        $this->identity_password = $identity_password;
    }

    /**
     * @return string
     */
    public function getIdentityUsername()
    {
        return $this->identity_username;
    }

    /**
     * @param string $identity_username
     */
    public function setIdentityUsername($identity_username)
    {
        $this->identity_username = $identity_username;
    }

    /**
     * @return string
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * @param string $job
     */
    public function setJob($job)
    {
        $this->job = $job;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param string $mobile
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * @return string
     */
    public function getNo()
    {
        return $this->no;
    }

    /**
     * @param string $no
     */
    public function setNo($no)
    {
        $this->no = $no;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getSalutation()
    {
        return $this->salutation;
    }

    /**
     * @param string $salutation
     */
    public function setSalutation($salutation)
    {
        $this->salutation = $salutation;
    }

    /**
     * @return string
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * @param string $sex
     */
    public function setSex($sex)
    {
        $this->sex = $sex;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param string $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param string $zip
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

}