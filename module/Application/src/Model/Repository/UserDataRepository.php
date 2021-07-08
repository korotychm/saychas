<?php

// src/Model/Repository/UserDataRepository.php

namespace Application\Model\Repository;

// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
//use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Application\Model\Entity\UserData;
use Application\Model\RepositoryInterface\RepositoryInterface;
//use Laminas\Db\Sql\Sql;

class UserDataRepository extends Repository implements RepositoryInterface
{

    /**
     * @var string
     */
    protected $tableName = "user_data";

    /**
     * @var UserData
     */
    protected UserData $prototype;

    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param UserData $prototype
     */
    public function __construct(
            AdapterInterface $db,
            HydratorInterface $hydrator,
            UserData $prototype
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;
    }
    /**
     * remove this function if not working on main server
     * 
     * @param type $entity
     * @param type $params
     * @param \Laminas\Hydrator\ClassMethodsHydrator $hydrator
     * @return type
     */
    public function persist($entity, $params, $hydrator = null)
    {
        if (null == $hydrator) {
            $hydrator = new \Laminas\Hydrator\ClassMethodsHydrator();

            $composite = new \Laminas\Hydrator\Filter\FilterComposite();
            $composite->addFilter(
                    'excludegettimestamp',
                    new \Laminas\Hydrator\Filter\MethodMatchFilter('getTimestamp'),
                    \Laminas\Hydrator\Filter\FilterComposite::CONDITION_AND
            );
//            $composite->addFilter(
//                    //'excludesettimestamp',
//                    'excludegettimestamp',
//                    new \Laminas\Hydrator\Filter\MethodMatchFilter('getTimestamp'),
//                    \Laminas\Hydrator\Filter\FilterComposite::CONDITION_AND
//            );

            $hydrator->addFilter('excludes', $composite, \Laminas\Hydrator\Filter\FilterComposite::CONDITION_AND);
        }

        return parent::persist($entity, $params, $hydrator);
    }
    

}
