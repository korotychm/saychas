<?php
// src/Model/Repository/BrandRepository.php

namespace Application\Model\Repository;

// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Json\Json;
use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Application\Model\Entity\User;
use Application\Model\Entity\Post;
use Application\Model\RepositoryInterface\RepositoryInterface;

class UserRepository extends Repository implements RepositoryInterface
{
    /**
     * @var string
     */
    protected $tableName="user";

    /**
     * @var User
     */
    protected User $prototype;
    
    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param User $prototype
     */
    public function __construct(
        AdapterInterface $db,
        HydratorInterface $hydrator,
        User $prototype
    ) {
        $this->db            = $db;
        $this->hydrator      = $hydrator;
        $this->prototype = $prototype;
    }

    /**
     * Adds given user into it's repository
     * 
     * @param json
     */
    public function replace($content)
    {
        return ['result' => false, 'description' => '', 'statusCode' => 405];
    }
    
}