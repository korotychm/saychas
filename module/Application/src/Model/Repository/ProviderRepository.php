<?php
// src/Model/Repository/ProviderRepository.php

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
//use Laminas\Db\ResultSet\ResultSet;
//use Laminas\Hydrator\ReflectionHydrator;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Json\Json;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Application\Model\Entity\Provider;
use Application\Model\RepositoryInterface\ProviderRepositoryInterface;

class ProviderRepository implements ProviderRepositoryInterface
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
     * @var Provider
     */
    private Provider $providerPrototype;
    
    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param Provider $providerPrototype
     */
    public function __construct(
        AdapterInterface $db,
        HydratorInterface $hydrator,
        Provider $providerPrototype
    ) {
        $this->db            = $db;
        $this->hydrator      = $hydrator;
        $this->providerPrototype = $providerPrototype;
    }

    /**
     * Returns a list of providers width limit and order
     *
     * @return Provider[]
     */
    public function findAll($order="id ASC", $limit=100, $offset=0  )
    {
        $sql    = new Sql($this->db);
        $select = $sql->select('provider');
        $select ->order($order);
        $select ->limit($limit);
        $select ->offset($offset);
        $stmt   = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();

 
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            return [];
        }

        $resultSet = new HydratingResultSet(
            $this->hydrator,
            $this->providerPrototype
        );
        $resultSet->initialize($result);
        return $resultSet;
    }
    
     /**
     * Returns a list of providers from only availble stores,  width limit and order
     *
     * @return Provider[]
     */
   public function findAvailableProviders ($param,$order="id ASC", $limit=100, $offset=0 )
   {
        $sql    = new Sql($this->db);
        
        $subSelectAvailbleStore = $sql->select('store');
        $subSelectAvailbleStore ->columns(['provider_id']);
        $subSelectAvailbleStore ->where->in('id', $param);
        
        $select = $sql->select('provider');
        $select ->columns(['*']);
        $select ->
                where->in('id', $subSelectAvailbleStore);
        
        //$select -> where(["id in ?" => (new Select())->columns(["provider_id"])->from("store")->where($where)]);
        
        $select ->order($order);
        $select ->limit($limit);
        $select ->offset($offset);
        
        $stmt   = $sql->prepareStatementForSqlObject($select);
//        $selectString = $sql->buildSqlString($select);
        $result = $stmt->execute();
     
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {return [];}
         $resultSet = new HydratingResultSet(
            $this->hydrator,
            $this->productPrototype
        );
        $resultSet->initialize($result);
        
        
        return $resultSet;
        
    }
    
    /**/
    /**
     * Returns a single provider.
     *
     * @param  int $id Identifier of the provider to return.
     * @return Provider
     */    
    public function find($id)
    {
        $sql       = new Sql($this->db);
        $select    = $sql->select('provider');
        $select->where(['id = ?' => $id]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result    = $statement->execute();
        
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            throw new RuntimeException(sprintf(
                'Failed retrieving test with identifier "%s"; unknown database error.',
                $id
            ));
        }

        $resultSet = new HydratingResultSet($this->hydrator, $this->providerPrototype);
        $resultSet->initialize($result);
        $provider = $resultSet->current();

        if (! $provider) {
            throw new InvalidArgumentException(sprintf(
                'Provider with identifier "%s" not found.',
                $id
            ));
        }

        return $provider;
    }
    
    /**
     * Adds given provider into it's repository
     * @param json
     */
    public function replace($content)
    {
        try {
            $result = Json::decode($content, \Laminas\Json\Json::TYPE_ARRAY);
        }catch(\Laminas\Json\Exception\RuntimeException $e){
           return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
        }
        foreach($result['data'] as $row) {
            $sql = sprintf("replace INTO `provider`( `id`, `title`, `description`, `icon`) VALUES ( '%s', '%s', '%s', '%s' )", $row['id'], $row['title'], $row['description'], $row['icon']);
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
     * Delete providers specified by json array of objects
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
        $delete->from('provider');
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