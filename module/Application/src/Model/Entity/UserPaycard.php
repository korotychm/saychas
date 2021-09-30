<?php

// src/Model/Entity/UserPaycard.php

namespace Application\Model\Entity;

use Application\Model\Repository\UserPaycardRepository;
use Application\Model\Traits\Searchable;

/**
 * UserPaycard
 *
 * @ORM\Table(name="user_paycard")
 * @ORM\Entity
 */
class UserPaycard extends Entity
{

     /**
     * Behavior
     */
    use Searchable;

    /**
     * @var UserPaycardRepository
     */

    public static UserPaycardRepository $repository;
   /**
     * @var string
     */
    protected $user_id;

    /**
     * @var string
     */
    protected $card_id;

    /**
     * @var string
     */
    protected $pan;

    /**
     * @var string
     */
    protected $timestamp;
    
    /**
     * @var int
     */
    protected $time;

    /**
     * Get primary key
     *
     * @return string|array
     */
    public function primaryKey()
    {
        return 'card_id';
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
     * Set user_id.
     *
     * @param string $userId
     * @return $this
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;
        return $this;
    }

    /**
     * Set card_id.
     *
     * @param string $cardId
     *
     * @return UserPaycard
     */
    public function setCardId($cardId)
    {
        $this->card_id = $cardId;

        return $this;
    }

    /**
     * Get card_id.
     *
     * @return string
     */
    public function getCardId()
    {
        return $this->card_id;
    }

    /**
     * Set $pan.
     *
     * @param string $pan
     *
     * @return UserPaycard
     */
    public function setPan($pan)
    {
        $this->pan = $pan;

        return $this;
    }

    /**
     * Get pan.
     *
     * @return string
     */
    public function getPan()
    {
        return $this->pan;
    }

    /**
     * Set timestamp.
     *
     * @param string $timestamp
     *
     * @return UserPaycard
     */
    public function setTimestamp($timestamp)
    {
        $this->image = $timestamp;

        return $this;
    }

    /**
     * Get timestamp.
     *
     * @return string
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Set time.
     *
     * @param string $time
     *
     * @return UserPaycard
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time.
     *
     * @return string
     */
    public function getTime()
    {
        return $this->time;
    }
}
