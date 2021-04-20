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

    public function findAll($params)
    {
        $sql    = new Sql($this->db);
        $select = $sql->select($this->tableName);
        if(isset($params['order']))     { $select->order($params['order']); }
        if(isset($params['limit']))     { $select->limit($params['limit']); }
        if(isset($params['offset']))    { $select->offset($params['offset']); }
        if(isset($params['where']))     { $select->where($params['where']); }
        if(isset($params['sequence']))  { $select->where(['id'=>$params['sequence']]); }//{ $select->where->in('id', $params['sequence']); } // 
        
        $stmt   = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();

 
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            return [];
        }

        $resultSet = new HydratingResultSet(
            $this->hydrator,
            $this->prototype
        );
        $resultSet->initialize($result);
        return $resultSet;
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