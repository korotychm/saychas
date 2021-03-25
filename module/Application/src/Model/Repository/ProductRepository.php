<?php
// src/Model/Repository/ProductRepository.php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Model\Repository;

use InvalidArgumentException;
//use RuntimeException;
// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
//use Laminas\Db\Sql\Select;
//use Laminas\Db\Sql\Where;
use Laminas\Json\Json;
use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;
use Application\Model\Entity\Product;
use Application\Model\Entity\ProductExtended;
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
         /* ->where->and 
            ->where->in('id', $param)/**/
        ;        
        $select = $sql->select('');
        $select
            ->from(['p' => 'product'])
            ->columns(['*'])
            ->join(
                ['pr' => 'price'],
                'p.id = pr.product_id',
              //'(p.id = pr.product_id and pr.store_id = '.$storeId.")",
                ['price'],           
                $select::JOIN_LEFT  
            ) 
            ->join(
                ['b' => 'stock_balance'],
                'p.id = b.product_id',
                ['rest'],           
                $select::JOIN_LEFT  
            )      
            ->where->in('p.provider_id', $subSelectAvailbleStore)
            ->where->and
            ->where->equalTo('pr.store_id', $storeId) /**/  
            ->where->and
            ->where->equalTo('b.store_id', $storeId) /**/  
            //->group('p.id')
        ;
        $stmt   = $sql->prepareStatementForSqlObject($select);
        
        
        $result = $stmt->execute();
        $selectString = $sql->buildSqlString($select);  
       // exit(date("r")."<hr/> ".$selectString);
        
       
        
        $query = $this->db->query($selectString);
        $result = $query->execute();

        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) return [];
       
//        $hydrator = new \Laminas\Hydrator\ReflectionHydrator();
        foreach ($result as $r){
            $product     = $this->hydrator->hydrate($r, new ProductExtended());
            //$data     = $hydrator->extract(new Product());
            echo $product->getId().' '.$product->getTitle(). ' '. $product->getVendorCode(). ' price = ' . $product->getPrice() . '<br/>';
        }
        exit;
//        
//        $hydrator = new \Laminas\Hydrator\ReflectionHydrator();
//        $product     = $hydrator->hydrate($r, new ProductExtended());

//        $resultSet = new HydratingResultSet(
//            $this->hydrator,
//            $this->productPrototype
//        );
//        $resultSet->initialize($result);
//        foreach($resultSet as $product) {
//            echo $product->getId().' '.$product->getTitle(). ' '. $product->getVendorCode(). ' price = ' . $product->getPrice() . '<br/>';
//        }
//        exit;
//        return $resultSet;
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
        }catch(LaminasJsonRuntimeException $e){
           return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
        }
        
        if((bool) $result['truncate']) {
            $this->db->query("truncate table `product`")->execute();
        }

        foreach($result->data as $row) {
            $sql = sprintf("replace INTO `product`( `id`, `provider_id`, `category_id`, `title`, `description`, `vendor_code`, `param_value_list`, `param_variable_list` ) VALUES ( '%s', '%s', %u, '%s', '%s', '%s', '%s', '%s' )",
                    $row->id, $row->provider_id, $row->category_id, $row->title, $row->description, $row->vendor_code, '', '');
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
        $delete = $sql->delete()->from('product')->where(['id' => $total]);

        $selectString = $sql->buildSqlString($delete);
        try {
            $this->db->query($selectString, $this->db::QUERY_MODE_EXECUTE);
            return ['result' => true, 'description' => '', 'statusCode' => 200];
        }catch(InvalidQueryException $e){
            return ['result' => false, 'description' => "error executing $sql", 'statusCode' => 418];
        }
    }
    
}