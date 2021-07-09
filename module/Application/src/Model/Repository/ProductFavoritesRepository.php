<?php

// src/Model/Repository/ProductFavoritesRepository.php

namespace Application\Model\Repository;

// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Application\Model\Entity\ProductFavorites;
use Application\Model\RepositoryInterface\ProductFavoritesRepositoryInterface;

class ProductFavoritesRepository extends Repository implements ProductFavoritesRepositoryInterface
{

    /**
     * @var string
     */
    protected $tableName = "product_favorites";

    /**
     * @var ProductFavorites
     */
    protected ProductFavorites $prototype;

    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param ProductFavorites $prototype
     */
    public function __construct(
            AdapterInterface $db,
            HydratorInterface $hydrator,
            ProductFavorites $prototype
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;

        parent::__construct();
    }

}
