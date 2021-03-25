<?php
// src/Model/Repository/StoreRepository.php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Model\Repository;

use InvalidArgumentException;
use RuntimeException;
// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Json\Json;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
//use Laminas\Db\TableGateway\TableGateway;
//use Laminas\Db\Sql\ExpressionInterface;
//use Laminas\Db\Sql\Predicate;
//use Laminas\Db\Sql\Predicate\PredicateSet;
//use Laminas\Db\Sql\Predicate\In;
use Laminas\Db\Sql\Where;
use Application\Model\Entity\Store;
use Application\Model\RepositoryInterface\StoreRepositoryInterface;

class StoreRepository implements StoreRepositoryInterface
{
    /**
     * @var AdapterInterface
     */
    private AdapterInterface $db;

    /**
     * @var HydratorInterface
     */
    private HydratorInterface $hydrator;

    /**
     * @var Store
     */
    private Store $storePrototype;
    
    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param Store $storePrototype
     */
    public function __construct(
        AdapterInterface $db,
        HydratorInterface $hydrator,
        Store $storePrototype
    ) {
        $this->db            = $db;
        $this->hydrator      = $hydrator;
        $this->storePrototype = $storePrototype;
    }

    /**
     * Returns a list of stores
     *
     * @return Store[]
     */
    public function findAll()
    {
        $sql    = new Sql($this->db);
        $select = $sql->select('store');
        $stmt   = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();

        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            return [];
        }

        $resultSet = new HydratingResultSet(
            $this->hydrator,
            $this->storePrototype
        );
        $resultSet->initialize($result);
        return $resultSet;
    }

    /**
     * Returns a single store.
     *
     * @param  int $id Identifier of the store to return.
     * @return Store
     */    
    public function find($id)
    {
        $sql       = new Sql($this->db);
        $select    = $sql->select('store');
        $select->where(['id = ?' => $id]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result    = $statement->execute();
        
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            throw new RuntimeException(sprintf(
                'Failed retrieving test with identifier "%s"; unknown database error.',
                $id
            ));
        }

        $resultSet = new HydratingResultSet($this->hydrator, $this->storePrototype);
        $resultSet->initialize($result);
        $store = $resultSet->current();

        if (! $store) {
            throw new InvalidArgumentException(sprintf(
                'Store with identifier "%s" not found.',
                $id
            ));
        }

        return $store;
    }
    
    /**
     * Function finds available stores of a specific provider
     * 
     * @param int $providerId
     * @param array $param
     * @return Store[]
     */
    public function findStoresByProviderIdAndExtraCondition($providerId, $param)
    {
        $sql = new Sql($this->db);
        
        $where = new Where();
        $where->equalTo('provider_id', $providerId);
        $where->in('id', $param);

        $select = $sql->select()->from('store')->columns(["id", "provider_id", "title", "description", "address", "geox", "geoy", "icon"])->where($where);
        
//        $selectString = $sql->buildSqlString($select);
        
        $stmt   = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();
        
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            return [];
        }

        $resultSet = new HydratingResultSet(
            $this->hydrator,
            $this->storePrototype
        );
        $resultSet->initialize($result);
        return $resultSet;

    }
    
    /**
     * Adds given store into it's repository
     * 
     * @param json $content
     */
    public function replace($content)
    {
        try {
            $result = Json::decode($content, \Laminas\Json\Json::TYPE_ARRAY);
        }catch(\Laminas\Json\Exception\RuntimeException $e){
           return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
        }

        if((bool) $result['truncate']) {
            $this->db->query("truncate table `store`")->execute();
        }

        foreach($result['data'] as $row) {
            $sql = sprintf("replace INTO `store`( `id`, `provider_id`, `title`, `description`, `address`, `geox`, `geoy`, `icon`) VALUES ( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )",
                    $row['id'], $row['provider_id'], $row['title'], $row['description'], $row['address'], $row['geox'], $row['geoy'], $row['icon']);
            try {
                $query = $this->db->query($sql);
                $query->execute();
            }catch(InvalidQueryException $e){
                return ['result' => false, 'description' => "error executing $sql", 'statusCode' => 418];
            }
        }
        return ['result' => true, 'description' => '', 'statusCode' => 200];
    }
    
    /**
     * Delete stores specified by json array of objects
     * @param json
     */
    public function delete($json) {
        try {
            $result = Json::decode($json, \Laminas\Json\Json::TYPE_ARRAY);
        }catch(\Laminas\Json\Exception\RuntimeException $e){
           return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
        }
        $total = [];
        foreach ($result as $item) {
            array_push($total, $item['id']);
        }
        $sql    = new Sql($this->db);
        $delete = $sql->delete();
        $delete->from('store');
        $delete->where(['id' => $total]);
        $selectString = $sql->buildSqlString($delete);
        try {
            $this->db->query($selectString, $this->db::QUERY_MODE_EXECUTE);
            return ['result' => true, 'description' => '', 'statusCode' => 200];
        }catch(InvalidQueryException $e){
            return ['result' => false, 'description' => "error executing $sql", 'statusCode' => 418];
        }
    }    
    
}