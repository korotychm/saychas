<?php

// src/Model/Repository/HandbookRelatedProductRepository.php

namespace Application\Model\Repository;

// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Application\Model\Entity\HandbookRelatedProduct;
//use Application\Model\Entity\HandbookRelatedProduct;
use Application\Model\RepositoryInterface\HandbookRelatedProductRepositoryInterface;

class HandbookRelatedProductRepository extends Repository implements HandbookRelatedProductRepositoryInterface
{

    /**
     * @var string
     */
    protected $tableName = "product";

    /**
     * @var HandbookRelatedProduct
     */
    protected HandbookRelatedProduct $prototype;

    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param HandbookRelatedProduct $prototype
     */
    public function __construct(
            AdapterInterface $db,
            HydratorInterface $hydrator,
            HandbookRelatedProduct $prototype
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;
    }

    /**
     * Adds given handbookRelatedProduct into it's repository
     *
     * @param json $content
     */
    public function replace($content)
    {
        return ['result' => false, 'description' => '', 'statusCode' => 405];
    }

}
