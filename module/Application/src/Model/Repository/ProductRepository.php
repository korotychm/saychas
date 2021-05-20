<?php
// src/Model/Repository/ProductRepository.php

namespace Application\Model\Repository;

//use InvalidArgumentException;
//use RuntimeException;
// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
//use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Db\Sql\Sql;
//use Laminas\Db\Sql\Expression;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Where;
use Laminas\Json\Json;
use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;
use Application\Model\Entity\Product;
use Application\Model\RepositoryInterface\ProductRepositoryInterface;
use Application\Model\RepositoryInterface\CharacteristicValueRepositoryInterface;
use Application\Model\RepositoryInterface\CharacteristicValue2RepositoryInterface;
use Application\Model\RepositoryInterface\CharacteristicRepositoryInterface;
use Application\Model\RepositoryInterface\ProductImageRepositoryInterface;
use Ramsey\Uuid\Uuid;

class ProductRepository extends Repository implements ProductRepositoryInterface
{
    /**
     * @var string
     */
    protected $tableName="product";

    /**
     * @var Product
     */
    protected Product $prototype;

    /**
     * @var CharacteristicValueRepositoryInterface
     */
    protected CharacteristicValueRepositoryInterface $characteristicValueRepository;// $predefCharValueRepo;

    /**
     * @var CharacteristicValueRepositoryInterface
     */
    protected CharacteristicValue2RepositoryInterface $characteristicValue2Repository;

    /**
     * @var CharacteristicRepositoryInterface
     */
    protected CharacteristicRepositoryInterface $characteristics;

    /**
     * @var ProductImageRepositoryInterface
     */
    protected ProductImageRepositoryInterface $productImages;

    /**
     * @var string
     */
    protected $catalogToSaveImages;


    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param Product $prototype
     */
    public function __construct(
        AdapterInterface $db,
        HydratorInterface $hydrator,
        Product $prototype,
        CharacteristicValueRepositoryInterface $characteristicValueRepository,
        CharacteristicRepositoryInterface $characteristics,
        ProductImageRepositoryInterface $productImages,
        CharacteristicValue2RepositoryInterface $characteristicValue2Repository,
        $catalogToSaveImages
    ) {
        $this->db                               = $db;
        $this->hydrator                         = $hydrator;
        $this->prototype                        = $prototype;
        $this->characteristicValueRepository    = $characteristicValueRepository;
        $this->characteristics                  = $characteristics;
        $this->productImages                    = $productImages;
        $this->characteristicValue2Repository   = $characteristicValue2Repository;
        $this->catalogToSaveImages              = $catalogToSaveImages;
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
                ['http_url'],
                $select::JOIN_LEFT
            )
            ->join(
                ['brand' => 'brand'],
                'p.brand_id = brand.id',
                ['brand_title'=>'title'],
                $select::JOIN_LEFT
            )
            ->where->in('p.provider_id', $subSelectAvailbleStore)
            /*->where->and
            ->where->equalTo('pr.store_id', $storeId) /**/
            ->where->and
            ->where->equalTo('b.store_id', $storeId)
            //->where(['id'=>['000000013', '000000003', '000000014']])
            //->group('p.id')
        ;
//        if(isset($params['filter'])) {
//            $select->where(['id'=>['000000013', '000000003', '000000014']]);
//        }

//      Do not delete the following line
//      $selectString = $sql->buildSqlString($select);
//      echo $selectString
//      exit;

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
        /*/foreach($resultSet as $product) {
            echo $product->getId().' '.$product->getTitle(). ' '. $product->getVendorCode(). ' price = ' . $product->getPrice() . ' rest = ' . $product->getRest() . '<br/>';
        }
        exit;/**/

        return $resultSet;
    }

    public function filterProductsByStores2($params=[])
    {
        $sql = new Sql($this->db);
        $w = new Where();
        $w->in('s.id', $params);
        $select = new Select();
        $select->quantifier(Select::QUANTIFIER_DISTINCT);
        $select->from(['s'=>'store'])->columns(['id', 'provider_id', 'title'])
                ->join(['p' => 'provider'], 'p.id = s.provider_id', [], $select::JOIN_INNER)
                ->join(['pr' => 'product'], 'pr.provider_id = s.provider_id', ['product_id'=>'id', 'product_title' => 'title'], $select::JOIN_INNER)
                ->join(['sb' => 'stock_balance'], 'sb.product_id = pr.id AND sb.store_id = s.id', ['rest' => 'rest'], $select::JOIN_LEFT)
                ->order(['id ASC'])->where($w);

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

//    private function createStoreFilter($params)
//    {
//        $w = new Where();
//        $w->in('s.id', $params);
//        $sel = new Select();
//        $sel->from(['s' => 'store'])->columns(['*'])
//                ->join(['p' => 'provider'], 'p.id = s.provider_id', [], $sel::JOIN_LEFT)
//                ->where($w);
//        $w2 = new Where();
//        $w2->in('pr.provider_id', $sel);
//        return $w;
//    }

    private function packParams($params)
    {
        $a = [];
        foreach($params['filter'] as $p) {
           $a[] = "find_in_set('$p', param_value_list)"; 
        }
        $res = ' 1';
        if(count($a) > 0) {
            $res = '('.implode(' OR ', $a).')';
        }
        return $res;
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
/** Number 1
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

End of number 1 */

        /** Number 2 */

//      select pr.*, ss.id as store_id, ss.title as store_title
//      from product pr
//      left join
//          (select s.* from store s left join provider p on p.id = s.provider_id where s.id in ('000000001','000000002','000000003','000000004','000000005') ) ss
//          on ss.provider_id=pr.provider_id
//      where ss.id is not null order by pr.id;



        $whereAppend= ($params['equal'])?'and pr.id = "'.$params['equal'].'"':'';
        //exit ($whereAppend);

        $w = new Where();
        if($params['in']) {
            $w->in('s.id', $params['in']);
        }

        $sel = new Select();
        $sel->from(['s' => 'store'])->columns(['*'])
                ->join(['p' => 'provider'], 'p.id = s.provider_id', [], $sel::JOIN_LEFT)->where($w);

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
//            ->join(
//                ['b' => 'stock_balance'],
//                'pr.id = b.product_id',
//                ['rest'],
//                $select::JOIN_LEFT
//            )
            ->join(
                ['img' => 'product_image'],
                'pr.id = img.product_id',
                ['http_url'],
                $select::JOIN_LEFT
            )
            ->join(
                ['brand' => 'brand'],
                'pr.brand_id = brand.id',
                ['brand_title'=>'title'],
                $select::JOIN_LEFT
            )
                ->join(
                        ['ss' => $sel],
                        'ss.provider_id = pr.provider_id',
                        ['store_id'=>'id', 'store_title'=>'title'],
                        $select::JOIN_LEFT
                )->where('1 '.$whereAppend ); //ss.id is not null

//        $params['filter'] = ['000000003', '000000013', '000000014'];
        $s = '';
        if (isset($params['filter'])) {
            $s = $this->packParams($params);
            $select->where($s);
        }

//        $selString = $sql->buildSqlString($select);
//        exit($selString);

        if ($params['order']) { $select->order($params['order']); }
        if ($params['limit']) { $select->limit($params['limit']); }
        if ($params['offset']) { $select->offset($params['offset']); }

        /** End of number 2 */

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

    private function separateCharacteristics(array $characteristics)
    {
        $value_list = [];
        $var_list = [];
        foreach($characteristics as $c) {
            $found = $this->characteristics->find(['id'=>$c->id]);
            if(null == $found) {
                throw new \Exception("Unexpected db error: characteristic with id ".$c->id." is not found");
            }
            if( ( $this->characteristics::REFERENCE_TYPE == $found->getType()
                    || $this->characteristics::HEADER_TYPE == $found->getType() )
                    && !empty($c->value) ) {
                $value_list[] = $c->value;
            }else{
                $var_list[] = $c;
            }
        }

        return ['value_list'=>implode(",", $value_list), 'var_list' => Json::encode($var_list)];
    }

    private function extractNonEmptyImages(array $data)
    {
        $result = [];
        foreach ($data as $d) {
            if(count($d->images) > 0) {
                array_push($result, $d);
            }
        }
        return $result;
    }

    public function fetchImages(array $images)
    {
        $ftp_server = "nas01.saychas.office";
        $username = "1C";
        $password = "ree7EC2A";

        // perform connection
        $conn_id = ftp_connect($ftp_server);
        $login_result = ftp_login($conn_id, $username, $password);
        if( (!$conn_id) || (!$login_result)) {
            throw new \Exception('FTP connection has failed! Attempted to connect to nas01.saychas.office for user '.$username.'.');
        }

        foreach($images as $image) {
            $local_file = realpath($this->catalogToSaveImages)."/".$image;
            $server_file = "/1CMEDIA/PhotoTovarov/".$image;

            // trying to download $server_file and save it to $local_file
            if( !ftp_get($conn_id, $local_file, $server_file, FTP_BINARY) ) {
                //throw new \Exception('Could not complete the operation');
            }
        }
        // close connection
        ftp_close($conn_id);
    }

    /**
     * Replace product characteristic
     * @param type $characteristic
     * @return string
     */
    private function replaceCharacteristic($characteristic)
    {
        if(!empty($characteristic->value)) {
            $myuuid = Uuid::uuid4();
            $myid = md5($myuuid->toString());
            $sql = sprintf("replace into characteristic_value( `id`, `title`, `characteristic_id`) values('%s', '%s', '%s')", $myid, $characteristic->value, $characteristic->id);

            $q = $this->db->query($sql);
            $q->execute();

            return $myid;
        }
        return '';

    }

    /**
     * Replace product characteristics from $var_list.
     * Put the result in $arr[value_list].
     * 
     * @param array $arr
     * @param array $var_list
     */
    private function replaceCharacteristicsFromList(array &$arr, array $var_list)
    {
        foreach ($var_list as $var) {
            $v = $this->replaceCharacteristic($var);
            $arr['value_list'] = trim($arr['value_list'].",".$v, ',');
        }
    }
    
    private function updateCharacteristicsValueList($product) : array
    {
        $arr = $this->separateCharacteristics($product->characteristics);

        if(count($product->characteristics) > 0)
        {
            $var_list = Json::decode($arr['var_list']);
            try {
                $this->replaceCharacteristicsFromList($arr, $var_list);
            }catch(InvalidQueryException $e){
                return ['result' => false, 'description' => "error executing sql statement. " . $e->getMessage(), 'statusCode' => 418];
            }
        }
        
        return $arr;
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

        $products = $this->extractNonEmptyImages($result->data);

        $this->productImages->replace($products); // $products - products that have non empty array of images

        foreach ($products as $p) {
            try {
                /** returns array of successfully downloaded images */
                $this->fetchImages($p->images);
            } catch(\Exception $e) {
                return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
            }
        }

        /** $result->data - products */
        foreach($result->data as $product) {

            $arr = $this->separateCharacteristics($product->characteristics);

            if(count($product->characteristics) > 0)
            {
                $var_list = Json::decode($arr['var_list']);
/**
//                foreach ($var_list as $var) {
//                    if(!empty($var->value)) {
//                        $myuuid = Uuid::uuid4();
//                        $myid = md5($myuuid->toString());
//                        $sql = sprintf("replace into characteristic_value( `id`, `title`, `characteristic_id`) values('%s', '%s', '%s')", $myid, $var->value, $var->id);
//
//                        try {
//                            $q = $this->db->query($sql);
//                            $q->execute();
//                        }catch(InvalidQueryException $e){
//                            return ['result' => false, 'description' => "error executing $sql", 'statusCode' => 418];
//                        }
//                        $arr['value_list'] = trim($arr['value_list'].",".$myid, ',');
//                    }
//                    $v = $this->replaceCharacteristic($var);
//                    $arr['value_list'] = trim($arr['value_list'].",".$v, ',');
//                }
*/
                try {
                    $this->replaceCharacteristicsFromList($arr, $var_list);
                }catch(InvalidQueryException $e){
                    return ['result' => false, 'description' => "error executing sql statement. ".$e->getMessage(), 'statusCode' => 418];
                }

            }
            
            $arr1 = $this->updateCharacteristicsValueList($product);

            $sql = sprintf("replace INTO `product`( `id`, `provider_id`, `category_id`, `title`, `description`, `vendor_code`, `param_value_list`, `param_variable_list`, `brand_id` ) VALUES ( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )",
                    $product->id, $product->provider_id, $product->category_id, $product->title, $product->description, $product->vendor_code, $arr1['value_list'], $arr1['var_list'], $product->brand_id);
            
            try {
                $query = $this->db->query($sql);
                $query->execute();
            }catch(InvalidQueryException $e){
                return ['result' => false, 'description' => "error executing $sql", 'statusCode' => 418];
            }
        }
        return ['result' => true, 'description' => '', 'statusCode' => 200];
    }

}