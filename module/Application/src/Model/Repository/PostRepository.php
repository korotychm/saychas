<?php
// src/Model/Repository/PostRepository.php

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

class PostRepository extends Repository implements RepositoryInterface
{
    /**
     * @var string
     */
    protected $tableName="post";

    /**
     * @var Post
     */
    protected Post $prototype;
    
    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param Post $prototype
     */
    public function __construct(
        AdapterInterface $db,
        HydratorInterface $hydrator,
        Post $prototype
    ) {
        $this->db            = $db;
        $this->hydrator      = $hydrator;
        $this->prototype = $prototype;
    }
    
    /**
     * Adds given post into it's repository
     * 
     * @param json
     */
    public function replace($content)
    {
        return ['result' => false, 'description' => '', 'statusCode' => 405];
    }
    
}