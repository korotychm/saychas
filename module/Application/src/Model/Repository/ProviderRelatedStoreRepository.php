<?php

// src/Model/Repository/ProviderRelatedStoreRepository.php

namespace Application\Model\Repository;

// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Application\Model\Entity\Store;
//use Application\Model\Entity\ProviderRelatedStore;
use Application\Model\RepositoryInterface\ProviderRelatedStoreRepositoryInterface;

class ProviderRelatedStoreRepository extends Repository implements ProviderRelatedStoreRepositoryInterface
{

    /**
     * @var string
     */
    protected $tableName = "store";

    /**
     * @var Store
     */
    protected Store $prototype;

//    protected ProviderRelatedStore $prototype;

    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param Store $prototype
     */
    public function __construct(
            AdapterInterface $db,
            HydratorInterface $hydrator,
            Store $prototype
            //ProviderRelatedStore $prototype
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;
    }

    /**
     * Adds given store into it's repository
     *
     * @param json $content
     */
    public function replace($content)
    {
        return ['result' => false, 'description' => '', 'statusCode' => 405];
    }

}
