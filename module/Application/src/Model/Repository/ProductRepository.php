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
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Where;
use Laminas\Json\Json;
use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;
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
     * @param  array $params
     * @return Product
     */    
    public function find($params)
    {
        $sql       = new Sql($this->db);
        $select    = $sql->select('product');
        $select->where(['id = ?' => $params['id']]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result    = $statement->execute();
        
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            throw new RuntimeException(sprintf(
                'Failed retrieving test with identifier "%s"; unknown database error.',
                $params['id']
            ));
        }

        $resultSet = new HydratingResultSet($this->hydrator, $this->productPrototype);
        $resultSet->initialize($result);
        $product = $resultSet->current();

        if (! $product) {
            throw new InvalidArgumentException(sprintf(
                'Product with identifier "%s" not found.',
                $params['id']
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
     * @param array $params
     * @return Product[]
     */
    public function findProductsByProviderIdAndExtraCondition($storeId, $params=[])  {
        $sql = new Sql($this ->db);
        $subSelectAvailbleStore = $sql ->select('store');
        $subSelectAvailbleStore ->columns(['provider_id']);
        $subSelectAvailbleStore 
            ->where->equalTo('id', $storeId);
         /* ->where->and 
            ->where->in('id', $params);/**/
                
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
            ->join(
                ['img' => 'product_image'],
                'p.id = img.product_id',
                ['url_http'],           
                $select::JOIN_LEFT  
            )
            ->join(
                ['brand' => 'brand'],
                'p.brand_id = brand.id',
                ['brandtitle'=>'title'],           
                $select::JOIN_LEFT  
            )   
            ->where->in('p.provider_id', $subSelectAvailbleStore)
            /*->where->and
            ->where->equalTo('pr.store_id', $storeId) /**/  
            ->where->and
            ->where->equalTo('b.store_id', $storeId)
            //->group('p.id')
        ;
        
//      Do not delete the following line
//      $selectString = $sql->buildSqlString($select);  exit(date("r")."<hr>".$selectString);
       
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
        /*/foreach($resultSet as $product) {
            echo $product->getId().' '.$product->getTitle(). ' '. $product->getVendorCode(). ' price = ' . $product->getPrice() . ' rest = ' . $product->getRest() . '<br/>';
        }
        exit;/**/
        
        return $resultSet;
    }

    /**
     * Function obtains products of a provider that belong to a set of available stores.
     * 
     * The store is also listed as accessible
     * 
     * @param int $storeId
     * @param array $params
     * @return Product[]
     */
    public function filterProductsByStores(/*$storeId,*/ $params=[])
    {
        $sql    = new Sql($this->db);
        /** Number 1 */
//        $w = new \Laminas\Db\Sql\Where();
//        $w->in('s.id', ['000000003', '000000001']);
//        $sel = new Select();
//        $sel->from(['s' => 'store'])
//                ->columns(['provider_id'])
//                ->join(
//                    ['p' => 'provider'], 'p.id = s.provider_id', [], $sel::JOIN_LEFT
//                )->where($w);
//        $w2 = new \Laminas\Db\Sql\Where();
//        $w2->in('pr.provider_id', $sel);
//        $sel2 = new Select();
//        $sel2->from(['pr' => 'product'])
//                ->columns(['*'])
//                ->where($w2);
        /** End of number 1 */
        
        /** Number 2 */
        
//      select pr.*, ss.id as store_id, ss.title as store_title
//      from product pr
//      left join
//          (select s.* from store s left join provider p on p.id = s.provider_id where s.id in ('000000001','000000002','000000003','000000004','000000005') ) ss
//          on ss.provider_id=pr.provider_id
//      where ss.id is not null order by pr.id;        
        
        $w = new Where();
        $w->in('s.id', $params);
        
        $sel = new Select();
        $sel->from(['s' => 'store'])->columns(['*'])->join(['p' => 'provider'], 'p.id = s.provider_id', [], $sel::JOIN_LEFT)->where($w);
        
        $w2 = new Where();
        $w2->in('pr.provider_id', $sel);
        $select = new Select();
        $select->from(['pr' => 'product'])
                ->columns(['*'])
            ->join(
                ['pri' => 'price'],
                'pr.id = pri.product_id',
                ['price'],
                $select::JOIN_LEFT
            )
            ->join(
                ['b' => 'stock_balance'],
                'pr.id = b.product_id',
                ['rest'],
                $select::JOIN_LEFT
            )
            ->join(
                ['img' => 'product_image'],
                'pr.id = img.product_id',
                ['url_http'],
                $select::JOIN_LEFT
            )->join(
                ['brand' => 'brand'],
                'pr.brand_id = brand.id',
                ['brandtitle'=>'title'],
                $select::JOIN_LEFT  
            )
                ->join(
                        ['ss' => $sel],
                        'ss.provider_id = pr.provider_id',
                        ['store_id'=>'id', 'store_title'=>'title'],
                        $select::JOIN_LEFT
                )->where('ss.id is not null');
        /** End of number 2 */
        

//        $selString = $sql->buildSqlString($select);
//        print_r($selString);
//        exit;


//        Do not remove the following lines
//        exit(date("r")."<hr>".$sql->buildSqlString($select));
       
        $stmt   = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();

        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            return [];
        }
        
        echo '<pre>';
        print_r($result);
        echo '</pre>';
        exit;
       
        $resultSet = new HydratingResultSet(
            $this->hydrator,
            $this->productPrototype
        );
        $resultSet->initialize($result);
        
        return $resultSet;
        
    }
    
    public function filterProductsByStores1(/*$storeId,*/ $params=[])  {

        $sql = new Sql($this ->db);
        $subSelectAvailbleStore = $sql ->select('store');
        $subSelectAvailbleStore ->columns(['provider_id']);
        $where = new \Laminas\Db\Sql\Where();
        $where->in('st.id', $params);
        $where->and;
        $where->equalTo('b.store_id','st.id');
        
        $select = $sql->select('');
        $select
            ->from(['p' => 'product'])
            ->columns(['*'])
            ->join(
                ['pr' => 'price'],
                'p.id = pr.product_id',
                ['price'],           
                $select::JOIN_LEFT  
            ) 
            ->join(
                ['b' => 'stock_balance'],
                'p.id = b.product_id',
                ['rest'],           
                $select::JOIN_LEFT  
            )      
            ->join(
                ['img' => 'product_image'],
                'p.id = img.product_id',
                ['url_http'],           
                $select::JOIN_LEFT  
            )
            ->join(
                ['brand' => 'brand'],
                'p.brand_id = brand.id',
                ['brandtitle'=>'title'],           
                $select::JOIN_LEFT  
            );
        $where = new \Laminas\Db\Sql\Where();
        $where->in('st.id', $params);
   
         $select ->join(
                   ['st'=>'store'], 
                    $where,
                    ['store_id' => 'id','store_title' => 'title', 'store_address' => 'address' ],
                    $select::JOIN_LEFT
            )
            ->where->in('p.provider_id', $subSelectAvailbleStore)
            /*->where->and
            ->where->in('b.store_id', $params)*/    
        ;
        

//        Do not delete the following line    /**/          $selectString = $sql->buildSqlString($select);  exit(date("r")."<hr>".$selectString);    echo $selectString;       exit;
//         $selectString = $sql->buildSqlString($select);  exit(date("r")."<hr>".$selectString);    echo $selectString;       exit;
       
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
    
    public function filterProductsByCategories($products, $categories)
    {
        $filteredProducts = [];
        foreach ($products as $product) {
            if(in_array($product->getCategoryId(), $categories)) {
                $filteredProducts[] = $product;
            }
        }
        return $filteredProducts;
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
        
        if((bool) $result->truncate) {
            $this->db->query("truncate table product")->execute();
        }

        foreach($result->data as $row) {
            $sql = sprintf("replace INTO `product`( `id`, `provider_id`, `category_id`, `title`, `description`, `vendor_code`, `param_value_list`, `param_variable_list`, `brand_id` ) VALUES ( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )",
                    $row->id, $row->provider_id, $row->category_id, $row->title, $row->description, $row->vendor_code, '', '', $row->brand_id);
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