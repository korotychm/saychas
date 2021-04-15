<?php

/*
 * Here comes the text of your license
 * Each line should be prefixed with  * 
 */

namespace Application\Model\Repository;

use InvalidArgumentException;
use RuntimeException;
// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Json\Json;
use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;
use Application\Model\RepositoryInterface\RepositoryInterface;

/**
 * Description of Repository
 *
 * @author alex
 */
abstract class Repository implements RepositoryInterface
{
    /**
     * @var AdapterInterface
     */
    protected AdapterInterface $db;
    
    /**
     * @var HydratorInterface
     */
    protected HydratorInterface $hydrator;
    
    /**
     * Returns a list of entities
     *
     * @return Entity[]
     */
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
     * Returns a single entity.
     *
     * @param  array $params
     * @return null|Entity
     */
    public function find($params)
    {
        $sql       = new Sql($this->db);
        $select    = $sql->select($this->tableName);
        // $select->where(['id = ?' => $params['id']]);
        $select->where($params);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result    = $statement->execute();
        
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            throw new RuntimeException(sprintf(
                //Failed retrieving data with identifier
                'Failed retrieving data with filter "%s"; unknown database error.', implode(';', $params)
            ));
        }

        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $resultSet->initialize($result);
        $entity = $resultSet->current();

        if (! $entity) {
            /**
             * We comment out the following code for now
             * as we want our function to return default value instead of null
             * 
                throw new InvalidArgumentException(sprintf(
                    $this->tableName . ' with identifier "%s" not found.', '<Filter>'
                     $params['id']
                ));
            */
            $entity = null; // not found
//            // Return default
//            $entity = clone $this->prototype;
        }

        return $entity;
    }
    
    /**
     * Returns the first found entity or the default one
     * if no entities found
     * @param array $params
     * @return Entity
     */
    public function findFirstOrDefault($params)
    {
        $found = $this->find($params);
        if( null == $found ) {
            $found = clone $this->prototype;
            return $found;
        }
        return $found;
    }
    
    /**
     * Returns a single brand.
     *
     * @param  array $params
     * @return Entity
     */
//    public function find($params)
//    {
//        $sql       = new Sql($this->db);
//        $select    = $sql->select($this->tableName);
//        // $select->where(['id = ?' => $params['id']]);
//        $select->where($params);
//
//        $statement = $sql->prepareStatementForSqlObject($select);
//        $result    = $statement->execute();
//        
//        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
//            throw new RuntimeException(sprintf(
//                //Failed retrieving data with identifier
//                'Failed retrieving data with filter "%s"; unknown database error.', implode(';', $params)
//            ));
//        }
//
//        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
//        $resultSet->initialize($result);
//        $entity = $resultSet->current();
//
//        if (! $entity) {
//            $entity = null; // not found
//        }
//
//        return $entity;
//    }
    
    /**
     * Delete products specified by json array of objects
     * @param json
     */
    public function delete($json) {
        try {
            $result = Json::decode($json, Json::TYPE_ARRAY);
        }catch(LaminasJsonRuntimeException $e){
           return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
        }
        $total = [];
        foreach ($result as $item) {
            array_push($total, $item['id']);
        }
        $sql    = new Sql($this->db);
        $delete = $sql->delete()->from($this->tableName)->where(['id' => $total]);

        $selectString = $sql->buildSqlString($delete);
        try {
            $this->db->query($selectString, $this->db::QUERY_MODE_EXECUTE);
            return ['result' => true, 'description' => '', 'statusCode' => 200];
        }catch(InvalidQueryException $e){
            return ['result' => false, 'description' => "error executing $sql", 'statusCode' => 418];
        }
    }
    
}
