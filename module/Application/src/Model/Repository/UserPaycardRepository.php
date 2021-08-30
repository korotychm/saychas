<?php

// src/Model/Repository/UserPaycardRepository.php

namespace Application\Model\Repository;

// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
//use Laminas\Json\Json;
//use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;
//use Laminas\Db\Sql\Sql;
//use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Application\Model\Entity\UserPaycard;
use Application\Model\Repository\UserPaycardRepository;
//use Application\Model\RepositoryInterface\UserPaycardRepositoryInterface;

class UserPaycardRepository extends Repository //  implements UserPaycardRepositoryInterface
{

    /**
     * @var string
     */
    protected $tableName = "user_paycard";

    /**
     * @var UserPaycard
     */
    protected UserPaycard $prototype;
    
    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param UserPaycard $prototype
     */
    public function __construct(
            AdapterInterface $db,
            HydratorInterface $hydrator,
            UserPaycard $prototype
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;
        
        parent::__construct();
    }

}
