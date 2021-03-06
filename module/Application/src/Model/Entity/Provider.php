<?php

// src/Model/Entity/Provider.php

namespace Application\Model\Entity;

use Application\Model\RepositoryInterface\StoreRepositoryInterface;
use Application\Model\Repository\ProviderRepository;
use Application\Model\Traits\Searchable;

class Provider extends Entity
{

    use Searchable;
    
    /**
     * @var ProviderRepository
     */
    public static ProviderRepository $repository;
    
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
    protected $title = '';
    
     /**
     * @var string
     */
    protected $url;
    
    /**
     * Set friendly URL.
     *
     * @param string $url
     *
     * @return Brand
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get friendly URL.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @var string | null
     */
    protected $description = '';

    /**
     * @var string | null
     */
    protected $image = '';

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
        return self::$storeRepository->findAll(['where' => ['provider_id=?' => $this->getId()]]);
    }
    
    public function storesToArray()
    {
        $stores = $this->getStores();
        return $stores->toArray();
    }
    
    public function getStoreArray()
    {
        $stores = $this->getStores();
        $result = [];
        foreach($stores as $store) {
            $result[] = $store;
        }
        return $result;
    }
    
    /**
     * @param array $list
     * @return Store []
     * @throws \Exception
     */
    public function recieveStoresInList(array $list)
    {
        if (!( self::$storeRepository instanceof StoreRepositoryInterface )) {
            throw new \Exception('StoreRepositoryInterface expected; other type given');
        }
        return self::$storeRepository->findAll(['where' => ['provider_id=?' => $this->getId()], 'sequence' => $list])->current();
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
    public function setImage($image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return string | null
     */
    public function getImage()
    {
        return $this->image;
    }

}
