<?php

// src/Model/Entity/User.php

namespace Application\Model\Entity;

use Application\Model\Repository\UserDataRepository;
/**
 * Description of User
 *
 * @author alex
 */
class User extends Entity
{

    /**
     * @var UserDataRepository
     */
    public static UserDataRepository $userDataRepository;
    
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $phone;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var text
     */
    protected $address;

    /**
     * @var text
     */
    protected $geodata;
    
    /**
     * @var timestamp
     */
    protected $timestamp;
    
    protected $user_data;
    
    public function setUserData(array $userData)
    {
        foreach($userData as $ud) {
            $ud->setUserId($this->getId());
            self::$userDataRepository->persist($ud, []);
        }
        return $this;
    }
    
    public function getUserData()
    {
        $this->user_data = self::$userDataRepository->findAll(['id'=>$this->getId()]);
        return $this->user_data;
    }
    
    public function init()
    {
        $this->setName('');
        $this->setPhone(0);
        $this->setEmail('');
        $this->setGeodata('');
        //$this->setTimestamp(mktime(1));
        $this->setAddress('');        
    }

    /**
     * Set id.
     * 
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
    /**
     * Get id.
     *
     * @return type
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set phone.
     *
     * @param int $phone
     * @return $this
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * Get phone
     *
     * @return int
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set email.
     *
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set address.
     *
     * @param string $address
     * @return $this
     */
    public function setAddress(string $address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set geodata.
     *
     * @param string $geodata
     * @return $this
     */
    public function setGeodata(string $geodata)
    {
        $this->geodata = $geodata;
        return $this;
    }

    /**
     * Get geodata.
     *
     * @return string
     */
    public function getGeodata()
    {
        return $this->geodata;
    }
    
    /**
     * Set timestamp.
     * 
     * @param timestamp $timestamp
     * @return $this
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
        return $this;
    }
    
    /**
     * Get timestamp
     * 
     * @return string
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

}
