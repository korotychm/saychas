<?php

// src/Model/Repository/ReviewImageRepository.php

namespace Application\Model\Repository;

// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Json\Json;
//use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;
//use Laminas\Db\Sql\Sql;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Application\Model\Entity\ReviewImage;
use Application\Model\RepositoryInterface\ReviewImageRepositoryInterface;

class ReviewImageRepository extends Repository implements ReviewImageRepositoryInterface
{

    /**
     * @var string
     */
    protected $tableName = "review_image";

    /**
     * @var ReviewImage
     */
    protected ReviewImage $prototype;
    
    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param ReviewImage $prototype
     */
    public function __construct(
            AdapterInterface $db,
            HydratorInterface $hydrator,
            ReviewImage $prototype
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;
        
        parent::__construct();
    }

}
