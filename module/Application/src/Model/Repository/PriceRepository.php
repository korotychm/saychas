<?php
// src/Model/Repository/PriceRepository.php

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
use Laminas\Db\Sql\Sql;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
//use Laminas\Db\Sql\Select;
//use Laminas\Db\Sql\Where;
use Application\Model\Entity\Price;
use Application\Model\RepositoryInterface\PriceRepositoryInterface;

class PriceRepository implements PriceRepositoryInterface
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
     * @var Price
     */
    private Price $pricePrototype;
    
    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param Price $pricePrototype
     */
    public function __construct(
        AdapterInterface $db,
        HydratorInterface $hydrator,
        Price $pricePrototype
    ) {
        $this->db            = $db;
        $this->hydrator      = $hydrator;
        $this->pricePrototype = $pricePrototype;
    }

    /**
     * Returns a list of prices
     *
     * @return Price[]
     */
    public function findAll()
    {
        $sql    = new Sql($this->db);
        $select = $sql->select('price');
        $stmt   = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();

 
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            return [];
        }

        $resultSet = new HydratingResultSet(
            $this->hydrator,
            $this->pricePrototype
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
        $select    = $sql->select('price');
        $select->where(['id = ?' => $id]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result    = $statement->execute();
        
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            throw new RuntimeException(sprintf(
                'Failed retrieving test with identifier "%s"; unknown database error.',
                $id
            ));
        }

        $resultSet = new HydratingResultSet($this->hydrator, $this->pricePrototype);
        $resultSet->initialize($result);
        $price = $resultSet->current();

        if (! $price) {
            throw new InvalidArgumentException(sprintf(
                'Price with identifier "%s" not found.',
                $id
            ));
        }

        return $price;
    }
    
    /**
     * Adds given price into it's repository
     * 
     * @param json
     */
    public function replace($content)
    {        
        $result = json_decode($content, true);
        foreach($result as $row) {
            $sql = sprintf("replace INTO `price`(`product_id`, `store_id`, `reserve`, `unit`, `price`) VALUES ( '%s', '%s', %u, '%s', %u)",
                    $row['product_id'], $row['store_id'], $row['reserve'], $row['unit'], $row['price']);
            try {
                $query = $this->db->query($sql);
                $query->execute();
            }catch(InvalidQueryException $e){
                return ['result' => false, 'description' => "error executing $sql"];
            }
        }
        return ['result' => true, 'description' => ''];
    }
    
    
}