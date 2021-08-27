<?php

// src/Model/Entity/Country.php

namespace Application\Model\Entity;

use Application\Model\Traits\Searchable;
use Application\Model\Repository\CountryRepository;
/**
 * Country
 *
 * @ORM\Table(name="brand")
 * @ORM\Entity
 */
class Country extends Entity
{

    /**
     * Behavior
     */
    use Searchable;

    /**
     * @var CountryRepository
     */
    public static CountryRepository $repository;
    
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id.
     *
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Brand
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set code.
     *
     * @param string code
     *
     * @return Brand
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

}
