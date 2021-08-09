<?php

// src/Model/Entity/User.php

namespace Application\Model\Entity;

use Application\Model\Repository\UserRepository;
use Application\Model\Repository\UserDataRepository;
use Application\Model\Repository\BasketRepository;
use Application\Model\Traits\Searchable;

/**
 * Description of User
 *
 * @author alex
 */
class User extends Entity
{

    use Searchable;

    /**
     * @var UserRepository
     */
    public static UserRepository $repository;

    /**
     * @var UserDataRepository
     */
    public static UserDataRepository $userDataRepository;

    /**
     * @var BasketRepository
     */
    public static BasketRepository $basketRepository;

    /**
     * Get primary key name
     *
     * @return string|array
     */
    public function primaryKeyName()
    {
        return 'id';
    }
    
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $user_id;

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
     * @var int
     */
    protected $email_confirmed;

    /**
     * @var timestamp
     */
    protected $timestamp;

    /**
     * @var array
     */
    protected $user_data;

    /**
     * @var array
     */
    protected $basket_data;

    /**
     * Set user_data.
     *
     * @param array $userData
     * @return $this
     */
    public function setUserData(array $userData)
    {
        foreach ($userData as $ud) {
            $ud->setUserId($this->getId());
            //self::$userDataRepository->persist($ud, ['id' => $ud->getId()]);
            self::$userDataRepository->persist($ud, ['user_id' => $ud->getUserId(), 'fias_id' => $ud->getFiasId(), 'fias_level' => $ud->getFiasLevel()]);
        }
        return $this;
    }

    /**
     * Get user_data.
     *
     * @return array
     */
    public function getUserData()
    {
        $this->user_data = self::$userDataRepository->findAll(['where' => ['user_id' => $this->getId()], 'order' => 'timestamp DESC']);
        return $this->user_data;
    }

    /**
     * Delete user data
     *
     * @param array $params
     * @return $this
     */
    public function deleteUserData($params)
    {
        /** $this->user_data = */ self::$userDataRepository->remove($params);
        return $this;
    }

    public function getBasketData()
    {
        $this->basket_data = self::$basketRepository->findAll(['where' => ['user_id' => $this->getId()]]);
        return $this->basket_data;
    }

    /**
     * Init UserData
     *
     * @return $this
     */
    public function init()
    {
        $this->setName('');
        $this->setEmailConfirmed(0);
        return $this;
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
     * Set user_id.
     *
     * @param int $user_id
     * @return $this
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
        return $this;
    }

    /**
     * Get user_id.
     *
     * @return string
     */
    public function getUserId()
    {
        return $this->user_id;
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
     * Set $email_confirmed.
     *
     * @param bool $emailConfirmed
     * @return $this
     */
    public function setEmailConfirmed($emailConfirmed)
    {
        $this->email_confirmed = $emailConfirmed;
        return $this;
    }

    /**
     * Get $email_confirmed.
     *
     * @return bool
     */
    public function getEmailConfirmed()
    {
        return $this->email_confirmed;
    }

    /**
     * Set timestamp.
     *
     * @param timestamp $timestamp
     * @return $this
     */
//    public function setTimestamp($timestamp)
//    {
//        $this->timestamp = $timestamp;
//        return $this;
//    }

    /**
     * Get timestamp
     *
     * @return string
     */
//    public function getTimestamp()
//    {
//        return $this->timestamp;
//    }
}
