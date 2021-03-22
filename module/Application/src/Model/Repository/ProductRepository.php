<?php
// src/Model/Repository/ProductRepository.php

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
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Where;
use Laminas\Json\Json;
use Laminas\Json\Exception;
use Application\Model\Entity\Product;
use Application\Model\RepositoryInterface\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
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
     * @var Product
     */
    private Product $productPrototype;
    
    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param Product $productPrototype
     */
    public function __construct(
        AdapterInterface $db,
        HydratorInterface $hydrator,
        Product $productPrototype
    ) {
        $this->db            = $db;
        $this->hydrator      = $hydrator;
        $this->productPrototype = $productPrototype;
    }

    /**
     * Returns a list of products
     *
     * @return Product[]
     */
    public function findAll($limit=100, $offset=0, $order="id ASC")
    {
        $sql    = new Sql($this->db);
        $select = $sql->select('product');
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
            $this->productPrototype
        );
        $resultSet->initialize($result);
        return $resultSet;
    }
    
    

    /**
     * Returns a single product.
     *
     * @param  int $id Identifier of the product to return.
     * @return Product
     */    
    public function find($id)
    {
        $sql       = new Sql($this->db);
        $select    = $sql->select('product');
        $select->where(['id = ?' => $id]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result    = $statement->execute();
        
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            throw new RuntimeException(sprintf(
                'Failed retrieving test with identifier "%s"; unknown database error.',
                $id
            ));
        }

        $resultSet = new HydratingResultSet($this->hydrator, $this->productPrototype);
        $resultSet->initialize($result);
        $product = $resultSet->current();

        if (! $product) {
            throw new InvalidArgumentException(sprintf(
                'Product with identifier "%s" not found.',
                $id
            ));
        }

        return $product;
    }
    
    /**
     * Function obtains products from specified store that belongs to a specified provider from available stores.
     * 
     * The store is also listed as accessible
     * 
     * @param int $storeId
     * @param array $param
     * @return Product[]
     */
    public function findProductsByProviderIdAndExtraCondition($storeId, $param)  {
        $sql = new Sql($this ->db);
        $subSelectAvailbleStore = $sql ->select('store');
        $subSelectAvailbleStore ->columns(['provider_id']);
        $subSelectAvailbleStore 
            ->where->equalTo('id', $storeId)
            ->where->and 
            ->where->in('id', $param);
        
        $select = $sql->select('product');
        $select ->columns(['*']);
        $select ->where->in('provider_id', $subSelectAvailbleStore);
     /* $select ->order($order);
        $select ->limit($limit);
        $select ->offset($offset);/**/
         /*
        $where = new Where();
        $where->equalTo('id', $storeId);
        $where->in('id', $param);
        *
        $select = $sql->select()->from('product')->columns(["id", "category_id", "title"])->from("product")->
                where(["provider_id in ?" => (new Select())->columns(["provider_id"])->from("store")->
                        where($where)]);
         /**/
        
        $stmt   = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();
//      $selectString = $sql->buildSqlString($select);
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            return [];
        }
        $resultSet = new HydratingResultSet(
            $this->hydrator,
            $this->productPrototype
        );
        $resultSet->initialize($result);
        return $resultSet;
        
    }
    
    /**
     * Adds given product into it's repository
     * 
     * @param json
     */
    public function replace($content)
    {
        try {
            $result = Json::decode($content);
        }catch(\Laminas\Json\Exception\RuntimeException $e){
           return ['result' => false, 'statusCode' => 400, 'description' => $e->getMessage()];
        }
        
        foreach($result as $row) {
            $sql = sprintf("replace INTO `product`( `id`, `provider_id`, `category_id`, `title`, `description`, `vendor_code`) VALUES ( '%s', '%s', %u, '%s', '%s', '%s' )",
                    $row->id, $row->provider_id, $row->category_id, $row->title, $row->description, quotemeta($row->vendor_code));
            try {
                $query = $this->db->query($sql);
                $query->execute();
            }catch(InvalidQueryException $e){
                return ['result' => false, 'statusCode' => 418, 'description' => "error executing $sql"];
            }
        }
        return ['result' => true, 'statusCode' => 200, 'description' => ''];
    }
    
    /**
     * Delete products specified by json array of objects
     * @param json
     */
    public function delete($json) {
        /** @var id[] */
        try {
            $result = json_decode($json, true);
            $total = [];
            foreach ($result as $item) {
                array_push($total, $item['id']);
            }
            $sql    = new Sql($this->db);
            $delete = $sql->delete()->from('product')->where(['id' => $total]);

            $selectString = $sql->buildSqlString($delete);
            $this->db->query($selectString, $this->db::QUERY_MODE_EXECUTE);
            return ['result' => true, 'description' => ''];

        }catch(InvalidQueryException $e){
            return ['result' => false, 'description' => $e->getMessage()];
        }
    }
    
}