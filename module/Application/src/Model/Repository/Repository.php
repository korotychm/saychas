<?php

/*
 * Here comes the text of your license
 * Each line should be prefixed with  *
 */

namespace Application\Model\Repository;

//use InvalidArgumentException;
use RuntimeException;
// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Json\Json;
use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;
use Laminas\Log\Logger;
//use Laminas\Log\Writer\Stream as StreamWriter;
use Application\Model\RepositoryInterface\RepositoryInterface;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Application\Helper\ArrayHelper;

/**
 * Description of Repository
 *
 * @author alex
 */
abstract class Repository implements RepositoryInterface
{

    /**
     * @var Laminas\Log\Logger
     */
    protected Logger $logger;

    /**
     * @var AdapterInterface
     */
    protected AdapterInterface $db;

    /**
     * @var HydratorInterface
     */
    protected HydratorInterface $hydrator;
    
    protected $mclient;


//    public function __construct()
//    {
//        $this->logger = new Logger();
//        $writer = new StreamWriter('php://output');
//        $this->logger->addWriter($writer);
//    }
    
    public function __construct()
    {
        $this->mclient = new \MongoDB\Client(
            'mongodb://saychas:saychas@localhost/saychas'
        );
    }

    /**
     * Return tableName
     * 
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }
    
    /**
     * Returns a list of entities
     *
     * @return Entity[]
     */
    public function findAll($params)
    {
        $sql = new Sql($this->db);
        $select = $sql->select($this->tableName);
        if (isset($params['columns'])) {
            $select->columns($params['columns']);
        }
        if (isset($params['order'])) {
            $select->order($params['order']);
        }
        if (isset($params['limit'])) {
            $select->limit($params['limit']);
        }
        if (isset($params['offset'])) {
            $select->offset($params['offset']);
        }
        
        if (isset($params['group'])) {
            $select->group($params['group']);
        }
        
        if (isset($params['having'])) {
            $select->having($params['having']);
        }
        
        if (isset($params['where'])) {
            $select->where($params['where']);
        }
        if (isset($params['sequence'])) {
            $select->where(['id' => $params['sequence']]);
        }
        if (isset($params['joins'])) {
            $joins = $params['joins']->getJoins();
            foreach($joins as $join) {
                $select->join($join['name'], $join['on'], $join['columns'], $join['type'] );
            }
        }

        $stmt = $sql->prepareStatementForSqlObject($select);
        $res = $sql->buildSqlString($select);
        
        $result = $stmt->execute();

        if (!$result instanceof ResultInterface || !$result->isQueryResult()) {
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
        $sql = new Sql($this->db);
        $select = $sql->select($this->tableName);
        $select->where($params);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        if (!$result instanceof ResultInterface || !$result->isQueryResult()) {
            throw new RuntimeException(sprintf(
                                    //Failed retrieving data with identifier
                                    'Failed retrieving data with filter "%s"; unknown database error.', implode(';', $params)
            ));
        }

        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $resultSet->initialize($result);
        $entity = $resultSet->current();

        if (!$entity) {
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
        if (null == $found) {
            $found = clone $this->prototype;
            return $found;
        }
        return $found;
    }

    /**
     * Persists $entity
     *
     * @param Entity $entity
     * @param Entity $params
     * @param \Laminas\Hydrator\ClassMethodsHydrator $hydrator
     * @return void
     */
    public function persist($entity, $params, $hydrator = null)
    {

        if (null == $hydrator) {
            $hydrator = new \Laminas\Hydrator\ClassMethodsHydrator(); //ReflectionHydrator(); //ClassMethodsHydrator();
        }

        // $params['id'] = $entity->getId();
        $u = $this->find($params);
//        $us = $this->findAll(['where' => $params])->toArray();
        $found = true;
        if(null == $u) {
            $found = false;
        }
        
        $id = null;
        $assoc = $hydrator->extract($entity);
        //if(null != $u) {
//        if($found) {
//            $pk = $u->primaryKey();
//            if(is_string($pk) && !empty($pk)) {
//                $pkname = $u->primaryKey();
//                $id = $u->$pkname;
//                $assoc[$u->primaryKey()] = $id;
//            }else if(is_array($pk) && count($pk)) {
//                $keys = array_keys($assoc);
//                foreach($pk as $k) {
//                    if(in_array($k, $keys)) {
//                        $assoc[$k] = $id[$k] = $u->$k;
//                    }
//                }
//            }else{
//                throw new \Exception('Primary key ($pk) must be either a string or an array');
//            }
//        }
        if($found) {
            $pk = $entity->primaryKey();
            if(is_string($pk) && !empty($pk)) {
                //$pkname = $entity->primaryKey();
                //$id = $entity->$pkname;
                $id = $entity->$pk;
                $assoc[$entity->primaryKey()] = $id;
            }else if(is_array($pk) && count($pk) > 0) {
                $keys = array_keys($assoc);
                foreach($pk as $k) {
                    if(in_array($k, $keys)) {
                        $assoc[$k] = $id[$k] = $entity->$k;
                    }
                }
            }
            /*else{
                throw new \Exception('Primary key name ($pk) must be either a string or an array');
            }*/
        }
        $auto = $entity->autoIncrementKey();
        if(/*!$found &&*/ !empty($auto) && array_key_exists($auto, $assoc) ) {
            unset($assoc[$auto]);
        }
        $values = array_values($assoc);
        $names = array_keys($assoc);

        $sql = new Sql($this->db);
        //if (empty($u)) {
        if(!$found) {
            $sqlObj = $sql->insert();
            $sqlObj->into($this->tableName);
            $sqlObj->columns($names);
            $sqlObj->values($values);
//            $id = $this->db->getDriver()->getLastGeneratedValue();
        } else {
            $sqlObj = $sql->update($this->tableName);
            $sqlObj->set($assoc);
            $sqlObj->where($params);
        }
        
        try {
            $stmt = $sql->prepareStatementForSqlObject($sqlObj);
            $stmt->execute();
            //if(null == $u) {
            //if(!$found && !empty($u->autoIncrementKey())) {
            if(!$found && !empty($entity->autoIncrementKey())) {
                $id = $this->db->getDriver()->getLastGeneratedValue();
            }
        } catch (InvalidQueryException $ex) {
//            $error = $ex->getMessage();
//            return ['result' => false, 'description' => "error executing statement. " . ' ' . $ex->getMessage(), 'statusCode' => 418];
            throw new RuntimeException($ex->getMessage());
        }
        return $id;
    }

    /**
     * Adds given entity into it's repository
     *
     * @param json
     */
    public function replace($content) {
        try {
            $result = Json::decode($content, \Laminas\Json\Json::TYPE_ARRAY);
        } catch (\Laminas\Json\Exception\RuntimeException $e) {
            return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
        }

//        $tableName = $this->tableName;
//        $this->mclient->saychas->$tableName->drop();
//        $this->mclient->saychas->$tableName->insertMany($result['data']);
        
        if ((bool) $result['truncate']) {
            $this->db->query("truncate table {$this->tableName}")->execute();
        }

        foreach ($result['data'] as $row) {
            $this->hydrator->hydrate($row, $this->prototype);
            try {
                $this->persist($this->prototype, ['id' => $this->prototype->getId()]);
            } catch (InvalidQueryException $e) {
                return ['result' => false, 'description' => "error executing", 'statusCode' => 418];
            }
        }
        return ['result' => true, 'description' => '', 'statusCode' => 200];
    }

    /**
     * Adds given user into it's repository
     *
     * @param json
     */
//    public function replace($content)
//    {
//        return ['result' => false, 'description' => '', 'statusCode' => 405];
//    }

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
    
    public function remove($params)
    {
        $sql = new Sql($this->db);
        $delete = $sql->delete($this->tableName);
        if (isset($params['where'])) {
            $delete->where($params['where']);
        }
        $stmt = $sql->prepareStatementForSqlObject($delete);
        $result = $stmt->execute();
        return $result;
    }

    /**
     * Delete products specified by json array of objects
     * @param json
     */
    public function delete($json)
    {
        try {
            $result = Json::decode($json, Json::TYPE_ARRAY);
        } catch (LaminasJsonRuntimeException $e) {
            return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
        }
         $total = ArrayHelper::extractId($result, "id");  
        
//        $total = [];
//        foreach ($result as $item) {
//            array_push($total, $item['id']);
//        }
        $sql = new Sql($this->db);
        $delete = $sql->delete()->from($this->tableName)->where(['id' => $total]);

        $selectString = $sql->buildSqlString($delete);
        try {
            $this->db->query($selectString, $this->db::QUERY_MODE_EXECUTE);
            return ['result' => true, 'description' => '', 'statusCode' => 200];
        } catch (InvalidQueryException $e) {
            return ['result' => false, 'description' => "error executing $sql", 'statusCode' => 418];
        }
    }

}











//        else{
//            $autoIncrement = $entity->autoIncrementKey();
//            if(key_exists($autoIncrement, $assoc)) {
//                unset($assoc[$autoIncrement]);
//            }
//        }
//            $keys = array_keys($assoc);
//            if(in_array($entity->primaryKey(), $keys)) {
////                $id = $assoc[$entity->primaryKey()];
//                unset($assoc[$entity->primaryKey()]);
//            }
