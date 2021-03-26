<?php
// src/Model/Repository/BrandRepository.php

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
use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Application\Model\Entity\Brand;
use Application\Model\RepositoryInterface\BrandRepositoryInterface;

class BrandRepository implements BrandRepositoryInterface
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
     * @var Brand
     */
    private Brand $brandPrototype;
    
    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param Brand $brandPrototype
     */
    public function __construct(
        AdapterInterface $db,
        HydratorInterface $hydrator,
        Brand $brandPrototype
    ) {
        $this->db            = $db;
        $this->hydrator      = $hydrator;
        $this->brandPrototype = $brandPrototype;
    }

    /**
     * Returns a list of prices
     *
     * @return Brand[]
     */
    public function findAll()
    {
        $sql    = new Sql($this->db);
        $select = $sql->select('brand');
        $stmt   = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();

 
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            return [];
        }

        $resultSet = new HydratingResultSet(
            $this->hydrator,
            $this->brandPrototype
        );
        $resultSet->initialize($result);
        return $resultSet;
    }

    /**
     * Returns a single price.
     *
     * @param  int $id Identifier of the price to return.
     * @return Price
     */    
    public function find($id)
    {
        $sql       = new Sql($this->db);
        $select    = $sql->select('brand');
        $select->where(['id = ?' => $id]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result    = $statement->execute();
        
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            throw new RuntimeException(sprintf(
                'Failed retrieving test with identifier "%s"; unknown database error.',
                $id
            ));
        }

        $resultSet = new HydratingResultSet($this->hydrator, $this->brandPrototype);
        $resultSet->initialize($result);
        $brand = $resultSet->current();

        if (! $brand) {
            throw new InvalidArgumentException(sprintf(
                'Brand with identifier "%s" not found.',
                $id
            ));
        }

        return $brand;
    }
    
    /**
     * Adds given brand into it's repository
     * 
     * @param json
     */
    public function replace($content)
    {
        try {
            $result = Json::decode($content, \Laminas\Json\Json::TYPE_ARRAY);
        }catch(\Laminas\Json\Exception\RuntimeException $e){
           return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
        }

        if((bool) $result['truncate']) {
            $this->db->query("truncate table brand")->execute();
        }

        foreach($result['data'] as $row) {
            $sql = sprintf("replace INTO `brand`(`id`, `title`, `description`, `logo`) VALUES ( '%s', '%s', '%s', '%s')",
                    $row['id'], $row['title'], $row['description'], $row['logo']);
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
     * Delete prices specified by json array of objects
     * @param $json
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
        $delete = $sql->delete()->from('brand')->where(['id' => $total]);

        $selectString = $sql->buildSqlString($delete);
        try {
            $this->db->query($selectString, $this->db::QUERY_MODE_EXECUTE);
            return ['result' => true, 'description' => '', 'statusCode' => 200];
        }catch(InvalidQueryException $e){
            return ['result' => false, 'description' => "error executing $sql", 'statusCode' => 418];
        }
    }
    
}