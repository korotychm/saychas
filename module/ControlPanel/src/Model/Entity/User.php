<?php

// ControlPanel/src/Model/Entity/User.php

namespace ControlPanel\Model\Entity;

/**
 * Description of User
 *
 * @author alex
 */
class User
{

    /** @var string */
//    protected $user_id;
    
    /** @var string */
    protected string $provider_id;

    /** @var string */
    protected string $login;
    
    protected bool $offer;

    /** @var string */
    protected string $password;

    /** @var bool  */
    protected bool $access_is_allowed;

    /** @var ?string */
    protected ?string $roles;
    
//    public function setUserId($userId)
//    {
//        $this->user_id = $userId;
//        return $this;
//    }
//    
//    public function getUserId()
//    {
//        return $this->user_id;
//    }

    /**
     * Get password
     *
     * @return string
     */
    public function getProviderId()
    {
        return $this->provider_id;
    }

    /**
     * Set password
     *
     * @param string $providerId
     * @return $this
     */
    public function setProviderId($providerId)
    {
        $this->provider_id = $providerId;
        return $this;
    }

    /**
     * Get login
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }
    
    public function setOffer($offer)
    {
        $this->offer = $offer;
        return $this;
    }
    
    public function getOffer()
    {
        return $this->offer;
    }

    /**
     * Set string
     *
     * @param string $login
     * @return $this
     */
    public function setLogin($login)
    {
        $this->login = $login;
        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Get access_is_allowed
     *
     * @return bool
     */
    public function getAccessIsAllowed()
    {
        return $this->access_is_allowed;
    }

    /**
     * Set access_is_allowed
     *
     * @param bool $accessIsAllowed
     * @return $this
     */
    public function setAccessIsAllowed($accessIsAllowed)
    {
        $this->access_is_allowed = $accessIsAllowed;
        return $this;
    }
    
    /**
     * Get roles
     * 
     * @return string
     */
    public function getRoles()
    {
        return $this->roles;
    }
    
    /**
     * Set roles
     * 
     * @param string $roles
     * @return $this
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
        return $this;
    }
    
    public function rolesToArray()
    {
        return explode(',', $this->roles);
    }
    
}
