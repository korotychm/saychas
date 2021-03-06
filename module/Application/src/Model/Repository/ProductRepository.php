<?php

// src/Model/Repository/ProductRepository.php

namespace Application\Model\Repository;

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
use Application\Model\RepositoryInterface\CharacteristicValueRepositoryInterface;
use Application\Model\RepositoryInterface\CharacteristicValue2RepositoryInterface;
use Application\Model\RepositoryInterface\CharacteristicRepositoryInterface;
use Application\Model\RepositoryInterface\ProductImageRepositoryInterface;
//use Ramsey\Uuid\Uuid;
use Laminas\Escaper\Escaper;

class ProductRepository extends Repository implements ProductRepositoryInterface
{

    /**
     * @var string
     */
    protected $tableName = "product";

    /**
     * @var Product
     */
    protected Product $prototype;

    /**
     * @var CharacteristicValueRepositoryInterface
     */
    protected CharacteristicValueRepositoryInterface $characteristicValueRepository;

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

//    private $mclient;

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
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;
        $this->characteristicValueRepository = $characteristicValueRepository;
        $this->characteristics = $characteristics;
        $this->productImages = $productImages;
        $this->characteristicValue2Repository = $characteristicValue2Repository;
        $this->catalogToSaveImages = $catalogToSaveImages;

        parent::__construct();
//        $this->mclient = new \MongoDB\Client(
//            'mongodb://saychas:saychas@localhost/saychas'
//        );
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
    public function findProductsByProviderIdAndExtraCondition($storeId, $params = [])
    {
        $sql = new Sql($this->db);
        $subSelectAvailbleStore = $sql->select('store');
        $subSelectAvailbleStore->columns(['provider_id']);
        $subSelectAvailbleStore
        ->where->equalTo('id', $storeId);

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
                        ['http_url'],
                        $select::JOIN_LEFT
                )
                ->join(
                        ['brand' => 'brand'],
                        'p.brand_id = brand.id',
                        ['brand_title' => 'title'],
                        $select::JOIN_LEFT
                )
        ->where->in('p.provider_id', $subSelectAvailbleStore)
        ->where->and
        ->where->equalTo('b.store_id', $storeId)
        ;

        $stmt = $sql->prepareStatementForSqlObject($select);
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

    public function filterProductsByStores2($params = [])
    {
        $sql = new Sql($this->db);
        $w = new Where();
        $w->in('s.id', $params);
        $select = new Select();
        $select->quantifier(Select::QUANTIFIER_DISTINCT);
        $select->from(['s' => 'store'])->columns(['id', 'provider_id', 'title'])
                ->join(['p' => 'provider'], 'p.id = s.provider_id', [], $select::JOIN_INNER)
                ->join(['pr' => 'product'], 'pr.provider_id = s.provider_id', ['product_id' => 'id', 'product_title' => 'title'], $select::JOIN_INNER)
                ->join(['sb' => 'stock_balance'], 'sb.product_id = pr.id AND sb.store_id = s.id', ['rest' => 'rest'], $select::JOIN_LEFT)
                ->order(['id ASC'])->where($w);

        $stmt = $sql->prepareStatementForSqlObject($select);
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

    private function packParams($params)
    {
        $a = [];
        foreach ($params['filter'] as $p) {
            $a[] = "find_in_set('$p', param_value_list)";
        }
        $res = ' 1';
        if (count($a) > 0) {
            $res = '(' . implode(' OR ', $a) . ')';
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
    public function filterProductsByStores(/* $storeId, */ $params = [])
    {
        $sql = new Sql($this->db);

        $whereAppend = !empty($params['equal']) ? 'and pr.url = "' . $params['equal'] . '"' : '';

        $w = new Where();
        if (!empty($params['in'])) {
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
                        ['price', 'old_price', 'discount'],
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
                        ['brand_title' => 'title'],
                        $select::JOIN_LEFT
                )
                ->join(
                        ['ss' => $sel],
                        'ss.provider_id = pr.provider_id',
                        ['store_id' => 'id', 'store_title' => 'title'],
                        $select::JOIN_LEFT
                )->where('1 ' . $whereAppend); //ss.id is not null
//        $params['filter'] = ['000000003', '000000013', '000000014'];
        $s = '';
        if (isset($params['filter'])) {
            $s = $this->packParams($params);
            $select->where($s);
        }

        if (isset($params['where'])) {
            $select->where($params['where']);
        }

        if (!empty($params['order'])) {
            $select->order($params['order']);
        }
        if (!empty($params['limit'])) {
            $select->limit($params['limit']);
        }
        if (!empty($params['offset'])) {
            $select->offset($params['offset']);
        }

        /** End of number 2 */
        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();

        if (!$result instanceof ResultInterface || !$result->isQueryResult()) {
            return [];
        }
        //exit (count($result));

        $resultSet = new HydratingResultSet(
                $this->hydrator,
                $this->prototype
        );
        $resultSet->initialize($result);

        //exit(print_r($resultSet));
        return $resultSet;
    }

    public function filterProductsByCategories($products, $categories)
    {
        $filteredProducts = [];
        foreach ($products as $product) {
            if (in_array($product->getCategoryId(), $categories)) {
                $filteredProducts[] = $product;
            }
        }

        return $filteredProducts;
    }

//    private function separateCharacteristics(array $characteristics)
//    {
//        $value_list = [];
//        $var_list = [];
//        foreach ($characteristics as $c) {
//            $found = $this->characteristics->find(['id' => $c->id]);
//            if (null == $found) {
//                throw new \Exception("Unexpected db error: characteristic with id " . $c->id . " is not found");
//            }
//            if (( $this->characteristics::REFERENCE_TYPE == $found->getType() || $this->characteristics::HEADER_TYPE == $found->getType() ) && !empty($c->value)) {
//                $value_list[] = $c->value;
//            } else {
//                $var_list[] = $c;
//            }
//        }
//
//        return ['value_list' => implode(",", $value_list), 'var_list' => Json::encode($var_list)];
//    }

    private function extractNonEmptyImages(array $data)
    {
        $result = [];
        foreach ($data as $d) {
            if (count($d->images) > 0) {
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
        if ((!$conn_id) || (!$login_result)) {
            throw new \Exception('FTP connection has failed! Attempted to connect to nas01.saychas.office for user ' . $username . '.');
        }

        foreach ($images as $image) {
            $local_file = realpath($this->catalogToSaveImages) . "/" . $image;
            $server_file = "/1CMEDIA/PhotoTovarov/" . $image;

            // trying to download $server_file and save it to $local_file
            if (!ftp_get($conn_id, $local_file, $server_file, FTP_BINARY)) {
                //throw new \Exception('Could not complete the operation');
            }
        }
        // close connection
        ftp_close($conn_id);
    }

    /**
     * @param array $params
     * @return bool
     */
    private function deleteProductCharacteristics($product_id): bool
    {
        $sql = new Sql($this->db);
        // deleting existing product data
        $delete = $sql->delete()->from('product_characteristic')->where(['product_id' => $product_id]);
        $deleteString = $sql->buildSqlString($delete);
        try {
            $this->db->query($deleteString, $this->db::QUERY_MODE_EXECUTE);
        } catch (InvalidQueryException $e) {
            print_r($deleteString . $e->getMessage());
            exit;
        }
        return true;
    }

    private function saveProductCharacteristics($params): bool
    {
        foreach ($params as $param) {
            $sql = new Sql($this->db);
            $insert = $sql->insert()->into('product_characteristic')
                    ->columns(['product_id', 'characteristic_id', 'type', 'sort_order', 'value'])
                    ->values($param);
            $insertString = $sql->buildSqlString($insert);
            try {
                $this->db->query($insertString, $this->db::QUERY_MODE_EXECUTE);
            } catch (InvalidQueryException $e) {
                print_r($insertString . $e->getMessage());
                exit;
            }
        }
        return true;
    }

    /**
     * Adds given product into it's repository
     *
     * @param json
     */
    public function replace($content)
    {
        try {
            $result = Json::decode($content, Json::TYPE_OBJECT);
        } catch (LaminasJsonRuntimeException $e) {
            return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
        }

//        $tableName = $this->tableName;
//        $this->mclient->saychas->$tableName->drop();
//        $this->mclient->saychas->$tableName->insertMany($result['data']);
        //printf("Inserted %d document(s)\n", $this->mclient->saychas->products);
//        return ['result' => true, 'description' => sprintf("Inserted %d document(s)\n", $this->mclient->saychas->products), 'statusCode' => 200];
//        exit;

        if ((bool) $result->truncate) {
            $this->db->query("truncate table product")->execute();
        }

        $products = $this->extractNonEmptyImages($result->data);
        //$products = $this->extractNonEmptyImages($result['data']);

        $this->productImages->replace($products); // $products - products that have non empty array of images

        /** We shall delete the following comment later on */
//        foreach ($products as $p) {
//            try {
//                /** returns array of successfully downloaded images */
//                $this->fetchImages($p->images);
//            } catch (\Exception $e) {
//                return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
//            }
//        }
        /** End of comment to be deleted */
        $jsonCharacteristics = '';
        /** $result->data - products */
        foreach ($result->data as $product) {

            $prods = [];
            $prodChs = [];
            if (count($product->characteristics) > 0) {

                try {
                    $jsonCharacteristics = json_encode($product->characteristics, JSON_UNESCAPED_UNICODE);
//                    $jsonCharacteristics = Json::encode($product->characteristics, JSON_UNESCAPED_UNICODE);
                } catch (LaminasJsonRuntimeException $e) {
                    return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
                }

//                $jsonCharacteristics = json_encode($product->characteristics, JSON_UNESCAPED_UNICODE);

                $current = [];
                foreach ($product->characteristics as $prodChar) {

                    //$found = $this->characteristics->find(['id' => $prodChar->id]);
                    $found = $this->characteristics->find(['id' => implode('-', [$prodChar->id, $product->category_id])]);
                    if (null == $found) {
                        continue;
                        throw new \Exception("Unexpected db error: characteristic with id " . " is not found");
                    }
                    if (!(CharacteristicRepository::HEADER_TYPE == $found->getType() || CharacteristicRepository::STRING_TYPE == $found->getType()) && !empty($prodChar->value)) {
                        $isList = $found->getIsList();
                        if ($isList && is_array($prodChar->value)) {
                            foreach ($prodChar->value as $v) {
                                $prodChs['product_id'] = $product->id;
                                $prodChs['characteristic_id'] = implode('-', [$prodChar->id, $product->category_id]);
                                $prodChs['sort_order'] = $prodChar->index;
                                $prodChs['value'] = $v;
                                $prodChs['type'] = $found->getType();
                                $prods[] = $prodChs;
                            }
                        } else {
                            $prodChs['product_id'] = $product->id;
                            $prodChs['characteristic_id'] = implode('-', [$prodChar->id, $product->category_id]);
                            $prodChs['sort_order'] = $prodChar->index;
                            $prodChs['value'] = $prodChar->value;
                            $prodChs['type'] = $found->getType();
                            $prods[] = $prodChs;
                        }
                    }

//                    if (( $this->characteristics::REFERENCE_TYPE == $found->getType() )) {
//                        $myid = $prodChar->value;
//                        $current[] = $myid;
//                    }else{
//                        $myuuid = Uuid::uuid4();
//                        $myid = md5($myuuid->toString());
//                        $current[] = $myid;
//                        if(!is_array($prodChar->value)) {
//                            $sql = sprintf("replace into characteristic_value( `id`, `title`, `characteristic_id`) values('%s', '%s', '%s')", $myid, $prodChar->value, $var->id);
//                        }else if(is_array($prodChar->value)) {
//                            $title = implode(',', $prodChar->value);
//                            $sql = sprintf("replace into characteristic_value( `id`, `title`, `characteristic_id`) values('%s', '%s', '%s')", $myid, $title, $prodChar->id);
//                        }else{
//                            throw new \Exception('Value must be either a scalar or an array');
//                        }
//                        try {
//                            $q = $this->db->query($sql);
//                            $q->execute();
//                        } catch (InvalidQueryException $e) {
//                            return ['result' => false, 'description' => "error executing $sql", 'statusCode' => 418];
//                        }
//                    }
                }
                $filteredCurrent = array_filter($current);
                $curr = implode(',', $filteredCurrent);
            }

            $this->deleteProductCharacteristics($product->id);
            $this->saveProductCharacteristics($prods);

            $escaper = new Escaper('utf-8');
            $sql = sprintf("replace INTO `product`( `id`, `provider_id`, `category_id`, `parent_category_id`, `title`, `url`, `description`, `vendor_code`, `param_value_list`, `param_variable_list`, `brand_id`, `vat` ) "
                    . "VALUES ( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )",
                    $product->id, $product->provider_id, $product->category_id, $product->parent_category_id, 
                    addslashes($escaper->escapeHtml($product->title)),   
                    mb_substr(addslashes($escaper->escapeHtml($product->url)),0, 120 ), 
                    addslashes($escaper->escapeHtml($product->description)), 
                    $product->vendor_code, addslashes($curr), 
                    addslashes($jsonCharacteristics), 
                    $product->brand_id, $product->vat );
//            $sql = sprintf("replace INTO `product`( `id`, `provider_id`, `category_id`, `title`, `description`, `vendor_code`, `param_value_list`, `param_variable_list`, `brand_id` ) VALUES ( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )",
//                    $product->id, $product->provider_id, $product->category_id, $product->title, $product->description, $product->vendor_code, $curr, $jsonCharacteristics, $product->brand_id);
            //if($product->id == '000000000016' || $product->id == '000000000036' || $product->id == '000000000003') {
            //mail('user@localhost', 'product->characteristics', print_r($jsonCharacteristics, true));
            //}

            try {
//                print_r($sql);
//                continue;
                $query = $this->db->query($sql);
                $query->execute();
            } catch (InvalidQueryException $e) {
                //return ['result' => false, 'description' => "$e error executing $sql", 'statusCode' => 418];
                $error.="{$e->getMessage()}\r\n";
                continue;
            }
        }
        return ['result' => true, 'description' => $error ?? '', 'statusCode' => 200];
    }

    /**
     * set rating product_user_rating and set average rating product_rating
     *
     *  @param type array
     */
    public function setProductRating($param)
    {
        $query = "CALL set_product_rating('" . $param["product_id"] . "','" . $param["user_id"] . "','" . $param["rating"] . "');";
        //return [$query];
        try {
            $res = $this->db->query($query)->execute();
        } catch (InvalidQueryException $e) {
            return ['result' => false, 'description' => "error executing {$e->getMessage()}"];
        }
        foreach ($res as $return) {
            $return['result'] = true;
            return $return;
        }
    }
    
    /**
     * get counts  user_rating
     *
     *  @param string $productId
     */
    public function getCountsProductRating($productId)
    {
        $query = "SELECT count(IF (`rating` = 50, `rating`, NULL)) as five, count(if (`rating` = 40, `rating`, NULL)) as four, count(if (`rating` = 30, `rating`, NULL)) as three, count(if (`rating` = 20, `rating`, NULL)) as two, count(if (`rating` = 10,`rating`, NULL)) as one FROM `product_user_rating` WHERE `product_id` = '$productId' ";
        //return [$query];
        try {
            $res = $this->db->query($query)->execute();
        } catch (InvalidQueryException $e) {
            return ['result' => false, 'description' => "error executing {$e->getMessage()}"];
        }
        foreach ($res as $return) {
           // $return['result'] = true;
            return $return;
        }
    }
    
    
//BEGIN
//    DECLARE average_rating INT;
//    REPLACE INTO `product_user_rating` (`product_id`, `user_id`, `rating`) VALUES (productid, userid, ratingvalue) ;
//    SELECT AVG(`rating`) INTO average_rating FROM `product_user_rating`;
//    IF average_rating > 0 THEN
//            REPLACE INTO `product_rating`(`product_id`, `rating`) VALUES (productid,average_rating);
//    END IF;
//    SELECT average_rating;
//END
}
