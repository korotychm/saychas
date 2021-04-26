<?php

// src/Model/Entity/Provider.php

namespace Application\Model\Entity;

use Application\Model\RepositoryInterface\StoreRepositoryInterface;

class Provider extends Entity
{

    /**
     * @var StoreRepository
     */
    public static StoreRepositoryInterface $storeRepository;
    
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string | null
     */
    protected $title;

    /**
     * @var string | null
     */
    protected $description;

    /**
     * @var string | null
     */
    protected $icon;
    
    /**
     * 
     * @return Store[]
     * @throws \Exception
     */
    public function getStores()
    {
        if (!( self::$storeRepository instanceof StoreRepositoryInterface )) {
            throw new \Exception('StoreRepositoryInterface expected; other type given');
        }
        return self::$storeRepository->findAll(['where' => ['provider_id=?' => $this->getId()] ]);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string | null $title
     * @return $this
     */
    public function setTitle($title = null)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string | null $description
     * @return $this
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string | null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string | null $icon
     * @return $this
     */
    public function setIcon($icon = null)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return string | null
     */
    public function getIcon()
    {
        return $this->icon;
    }

}
