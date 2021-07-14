<?php

// src/Model/Repository/ProductHistoryRepository.php

namespace Application\Model\Repository;

// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Application\Model\Entity\ProductHistory;
use Application\Model\RepositoryInterface\ProductHistoryRepositoryInterface;

class ProductHistoryRepository extends Repository implements ProductHistoryRepositoryInterface
{

    /**
     * @var string
     */
    protected $tableName = "product_history";

    /**
     * @var ProductHistory
     */
    protected ProductHistory $prototype;

    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param ProductHistory $prototype
     */
    public function __construct(
            AdapterInterface $db,
            HydratorInterface $hydrator,
            ProductHistory $prototype
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;

        parent::__construct();
    }

}
