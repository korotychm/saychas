<?php

// ControlPanel\src\Service\ProductManager.php

namespace ControlPanel\Service;

use ControlPanel\Service\CurlRequestManager;
//use ControlPanel\Model\Traits\Loadable;
use ControlPanel\Contract\LoadableInterface;
use Application\Resource\Resource;
use Application\Model\Repository\CategoryRepository;
use Application\Model\Entity\Country;
use Application\Model\Entity\Brand;
use Application\Model\Entity\Color;
use Application\Model\Entity\Price;
use Application\Model\Entity\Provider;
use Application\Model\Entity\CharacteristicValue;
use Application\Model\Entity\HandbookRelatedProduct as Product;
use Application\Model\Entity\Characteristic;

/**
 * Description of ProductManager
 *
 * @author alex
 */
class ProductManager extends ListManager implements LoadableInterface
{

    //use Loadable;

    /**
     * @var string
     */
    //public const COLLECTION_NAME = 'products';
    
    //public static $collection_name = 'products';
    
    protected $collectionName = 'products';

    /**
     * @var string
     */
    protected $dbName = 'saychas_cache';

    /**
     * @var Laminas\Config\Config
     */
    protected $config;

    /**
     * @var CurlRequestManager
     */
    protected $curlRequestManager;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepo;

    /**
     * @var laminas.entity.manager
     */
    protected $entityManager;

    /**
     * Constructor
     *
     * @param Laminas\Config\Config $config
     * @param CurlRequestManager $curlRequestManager
     * @param \MongoDB\Client $mclient
     */
    public function __construct($config, CurlRequestManager $curlRequestManager, \MongoDB\Client $mclient, $entityManager, CategoryRepository $categoryRepo)
    {
        $this->config = $config;
        $this->curlRequestManager = $curlRequestManager;
        $this->mclient = $mclient;
        $this->db = $this->mclient->{$this->dbName};
        $this->categoryRepo = $categoryRepo;
        $this->entityManager = $entityManager;

        $this->entityManager->initRepository(Country::class);
        $this->entityManager->initRepository(Brand::class);
        $this->entityManager->initRepository(Color::class);
        $this->entityManager->initRepository(Price::class);
        $this->entityManager->initRepository(Provider::class);
        $this->entityManager->initRepository(CharacteristicValue::class);
        $this->entityManager->initRepository(Product::class);
        $this->entityManager->initRepository(Characteristic::class);
    }
    
    public function findFilters($params)
    {
        $collection = $this->db->{$this->collectionName};
        $cursor = $collection->find($params['where'], ['projection' => ['_id' => 0, 'category_id' => 1, 'brand_id' => 1]])->toArray();
        $categories = [];
        $brands = [];
        foreach($cursor as &$c) {
            if(!empty($c['category_id'])) {
                $category = $this->categoryRepo->findCategory(['id' => $c['category_id']]);
                $c['category_name'] = (null == $category) ? '' : $category->getTitle();
                $categories[] = [$c['category_id'], $c['category_name'], ];
            }
            if(!empty($c['brand_id'])) {
                $brand = Brand::find(['id' => $c['brand_id']]);
                $c['brand_name'] = (null == $brand) ? '' : $brand->getTitle();
                $brands[] = [$c['brand_id'], $c['brand_name'],];
            }
        }
        return ['categories' => $categories, 'brands' => $brands];
    }
    
    private function findCategories($params)
    {
        $collection = $this->db->{$this->collectionName};
        $results = $collection->distinct('category_id', $params['where']);
        $accumulator = [];
        foreach($results as &$c) {
            //$c1 = $c;
            if(!empty($c)) {
                $category = $this->categoryRepo->findCategory(['id' => $c]);
                $category_name = (null == $category) ? '' : $category->getTitle();
                $accumulator[] = [$c, $category_name, ];
            }
        }
        return $accumulator;
    }
    
    private function findBrands($params)
    {
        $collection = $this->db->{$this->collectionName};
        $results = $collection->distinct('brand_id', $params['where']);
        $accumulator = [];
        foreach($results as &$c) {
            if(!empty($c)) {
                $brand = Brand::find(['id' => $c]);
                $brand_name = (null == $brand) ? '' : $brand->getTitle();
                $accumulator[] = [$c, $brand_name,];
            }
        }
        return $accumulator;
    }

    /**
     * Find all documents and lookup mysql fields that are referenced by mongodb fields
     * 
     * @param type $params
     * @return array
     */
    public function findDocuments($params)
    {
//        $this->findCharacteristics($params[ 'where']['product_id']);

        $cursor = $this->findAll($params);
        $categories = [];
        $brands = [];
        foreach ($cursor['body'] as &$c) {
            if(!empty($c['category_id'])) {
                //$category = $this->productManager->findCategoryById(['id' => $c['category_id']]);
                $category = $this->categoryRepo->findCategory(['id' => $c['category_id']]);
                $c['category_name'] = (null == $category) ? '' : $category->getTitle();
                $categories[] = [$c['category_id'], $c['category_name'], ];
            }

            if(!empty($c['brand_id'])) {
                $brand = Brand::find(['id' => $c['brand_id']]);
                $c['brand_name'] = (null == $brand) ? '' : $brand->getTitle();
                $brands[] = [$c['brand_id'], $c['brand_name'],];
            }

            if(!empty($c['country'])) {
                $country = Country::find(['id' => $c['country']]);
                $c['country_name'] = (null == $country) ? '' : $country->getTitle();
            }

            if(!empty($c['color'])) {
                $color = Color::find(['id' => $c['color']]);
                $c['color_name'] = (null == $color) ? '' : $color->getTitle();
            }
            
            if(!empty($c['id'])) {
                $price = Price::find([ 'product_id' => $c['id'] ]);
                $c['price'] = (null == $price) ? 0 : $price->getPrice();
                $c['old_price'] = (null == $price) ? 0 : $price->getOldPrice();
                $c['discount'] = (null == $price) ? 0 : $price->getDiscount();
            }

        }

        $cursor['filters']['categories'] = $this->findCategories($params);
        $cursor['filters']['brands'] = $this->findBrands($params);

        return $cursor;
    }
    
    private function fullCharacteristicId($categoryId, $characteristicId) : string
    {
        return $characteristicId.'-'.$categoryId;
    }
    
    private function getAvailableCharacteristicValues($characteristic) : array
    {
        $result = [];
        switch($characteristic['type']) {
            case Resource::CHAR_VALUE_REF:
                $result = CharacteristicValue::findAll(['where' => ['characteristic_id' => $characteristic['id'] ] ])->toArray();
                break;
//            case Resource::BRAND_REF:
//                $result = Brand::findAll([])->toArray();
//                break;
//            case Resource::COLOR_REF:
//                $result = Color::findAll([])->toArray();
//                break;
//            case Resource::COUNTRY_REF:
//                $result = Country::findAll([])->toArray();
//                break;
        }
        return $result;
    }
    
    private function array_insert_after($key, array &$array, $new_key, $new_value) {
      if (array_key_exists($key, $array)) {
        $new = array();
        foreach ($array as $k => $value) {
          $new[$k] = $value;
          if ($k === $key) {
            $new[$new_key] = $new_value;
          }
        }
        $array = $new;
        return $new;
      }
      return false;
    }

    private function fillUpProductHeader(&$product) : void
    {
        $provider = Provider::find(['id' => $product['provider_id']]);
        //$product['provider_name'] = (null == $provider) ? '' : $provider->getTitle();
        $this->array_insert_after('provider_id', $product, 'provider_name', ( (null == $provider) ? '' : $provider->getTitle() ) );
        $brand = Brand::find(['id' => $product['brand_id']]);
        //$product['brand_name'] = (null == $brand) ? '' : $brand->getTitle();
        $this->array_insert_after('brand_id', $product, 'brand_name', ( (null == $brand) ? '' : $brand->getTitle() ) );
        $country = Country::find(['id' => $product['country_id']]);
        //$product['country_name'] = (null == $country) ? '' : $country->getTitle();
        $this->array_insert_after('country_id', $product, 'country_name', ( (null == $country) ? '' : $country->getTitle() ) );
        $color = Color::find(['id' => $product['color_id']]);
        //$product['color_name'] = (null == $color) ? '' : $color->getTitle();
        $this->array_insert_after('color_id', $product, 'color_name', ( (null == $color) ? '' : $color->getTitle() ) );
        $category = $this->categoryRepo->findCategory(['id' => $product['category_id']]);
        //$product['category_name'] = (null == $category) ? '' : $category->getTitle();
        $this->array_insert_after('category_id', $product, 'category_name', ( (null == $category) ? '' : $category->getTitle() ) );
    }
    
    public function replaceProduct($product)
    {
        $this->fillUpProductHeader($product);
        $collection = $this->db->{$this->collectionName};
        $collection->deleteMany([
            'id' => $product['id'],
            'provider_id' => $product['provider_id'],
        ]);
        $updateResult = $collection->insertOne($product);
//        $updateResult = $collection->replaceOne(
//            ['id' => $product['id']],f
//            $product
//        );
//        return ['matched_count' => $updateResult->getMatchedCount(), 'modified_count' => $updateResult->getModifiedCount()];
        return $updateResult;
    }
    
    public function getHandbooks()
    {
        return [
            'brands' => Brand::findAll([])->toArray(),
            'colors' =>  Color::findAll([])->toArray(),
            'countries' => Country::findAll([])->toArray(),
        ];
    }
    
    public function findProduct(string $productId)
    {
        $product = (array) $this->find(['id' => $productId]);
        $characteristics = (array) $product['characteristics'];
//        $provider = Provider::find(['id' => $product['provider_id']]);
//        $product['provider_name'] = $provider->getTitle();
//        $product['provider_description'] = $provider->getDescription();
//        $b = Brand::find(['id' => $product['brand_id']]);
//        $product['brand_name'] = (null == $b) ? '' : $b->getTitle();
        $product['brands'] = Brand::findAll([])->toArray();
        $product['colors'] = Color::findAll([])->toArray();
        $product['countries'] = Country::findAll([])->toArray();
        
        //foreach($product->characteristics as &$c) {
        foreach($characteristics as &$c) {
            //$charact = Characteristic::find(['id' => $this->fullCharacteristicId($product['category_id'], $c['id'])]);
            $charact = Characteristic::find(['id' => $this->fullCharacteristicId($product['parent_category_id'], $c['id'])]);
            $c['characteristic_name'] = (null == $charact) ? '' : $charact->getTitle();
            switch ($c['type']) {
                case Resource::HEADER:
                    $c['real_value'] = $c['value'];
                    break;
                case Resource::STRING:
                    $c['real_value'] = $c['value'];
                    break;
                case Resource::INTEGER:
                    $c['real_value'] = $c['value'];
                    break;
                case Resource::BOOLEAN:
                    $c['real_value'] = $c['value'];
                    break;
                case Resource::CHAR_VALUE_REF:
                    if(is_object($c['value'])) {
                        $c['value'] = (array) $c['value'];
                    }
                    $entity = CharacteristicValue::find(['id' => $c['value']]);
                    $c['title'] = $c['real_value'] =  null == $entity ? '' : $entity->getTitle();// $entity->getTitle();
                    $c['available_values'] = $this->getAvailableCharacteristicValues($c);
                    break;
                case Resource::PROVIDER_REF:
                    if(is_object($c['value'])) {
                        $c['value'] = (array) $c['value'];
                    }
                    $entity = Provider::find(['id' => $c['value']]);
                    $c['title'] = $c['real_value'] =  null == $entity ? '' : $entity->getTitle();// $entity->getTitle();
//                    $c['available_providers'] = $this->getAvailableCharacteristicValues($c);
                    break;
                case Resource::BRAND_REF:
                    if(is_object($c['value'])) {
                        $c['value'] = (array) $c['value'];
                    }
                    $entity = Brand::find(['id' => $c['value']]);
                    $c['title'] = $c['real_value'] =  null == $entity ? '' : $entity->getTitle();// $entity->getTitle();
//                    $c['available_brands'] = $this->getAvailableCharacteristicValues($c);
                    break;
                case Resource::COLOR_REF:
                    if(is_object($c['value'])) {
                        $c['value'] = (array) $c['value'];
                    }
                    $entity = Color::find(['id' => $c['value']]);
                    $c['title'] =  null == $entity ? '' : $entity->getTitle();// $entity->getTitle();
                    $c['real_value'] =  null == $entity ? '' : $entity->getValue();// $entity->getValue();
//                    $c['available_colors'] = $this->getAvailableCharacteristicValues($c);
                    break;
                case Resource::COUNTRY_REF:
                    if(is_object($c['value'])) {
                        $c['value'] = (array) $c['value'];
                    }
                    $entity = Country::find(['id' => $c['value']]);
                    $c['title'] =  null == $entity ? '' : $entity->getTitle(); // $entity->getTitle();
                    $c['real_value'] =  null == $entity ? '' : $entity->getCode(); // $entity->getCode();
//                    $c['available_countries'] = $this->getAvailableCharacteristicValues($c);
                    break;
                default:
                    throw new \Exception('Characteristic of the given type does not exist');
            }
        }
        $product['characteristics'] = $characteristics;
        return $product;//->characteristics;
    }
    
    public function findProduct2($product)
    {
//        $product = $this->find(['id' => $productId]);
//        $provider = Provider::find(['id' => $product['provider_id']]);
//        $product['provider_name'] = $provider->getTitle();
//        $product['provider_description'] = $provider->getDescription();
//        $b = Brand::find(['id' => $product['brand_id']]);
//        $product['brand_name'] = (null == $b) ? '' : $b->getTitle();

//        $product['brands'] = Brand::findAll([])->toArray();
//        $product['colors'] = Color::findAll([])->toArray();
//        $product['countries'] = Country::findAll([])->toArray();
        
        foreach($product['characteristics'] as &$c) {
            //$charact = Characteristic::find(['id' => $this->fullCharacteristicId($product['category_id'], $c['id'])]);
            $charact = Characteristic::find(['id' => $this->fullCharacteristicId($product['parent_category_id'], $c['id'])]);
            $c['characteristic_name'] = (null == $charact) ? '' : $charact->getTitle();
            switch ($c['type']) {
                case Resource::HEADER:
                    $c['real_value'] = $c['value'];
                    break;
                case Resource::STRING:
                    $c['real_value'] = $c['value'];
                    break;
                case Resource::INTEGER:
                    $c['real_value'] = $c['value'];
                    break;
                case Resource::BOOLEAN:
                    $c['real_value'] = $c['value'];
                    break;
                case Resource::CHAR_VALUE_REF:
                    $entity = CharacteristicValue::find(['id' => $c['value']]);
                    $c['title'] = $c['real_value'] =  null == $entity ? '' : $entity->getTitle();
                    $c['available_values'] = $this->getAvailableCharacteristicValues($c);
                    break;
                case Resource::PROVIDER_REF:
                    $entity = Provider::find(['id' => $c['value']]);
                    $c['title'] = $c['real_value'] =  null == $entity ? '' : $entity->getTitle();
//                    $c['available_providers'] = $this->getAvailableCharacteristicValues($c);
                    break;
                case Resource::BRAND_REF:
                    $entity = Brand::find(['id' => $c['value']]);
                    $c['title'] = $c['real_value'] =  null == $entity ? '' : $entity->getTitle();
//                    $c['available_brands'] = $this->getAvailableCharacteristicValues($c);
                    break;
                case Resource::COLOR_REF:
                    $entity = Color::find(['id' => $c['value']]);
                    $c['title'] =  null == $entity ? '' : $entity->getTitle();
                    $c['real_value'] =  null == $entity ? '' : $entity->getValue();
//                    $c['available_colors'] = $this->getAvailableCharacteristicValues($c);
                    break;
                case Resource::COUNTRY_REF:
                    $entity = Country::find(['id' => $c['value']]);
                    $c['title'] = null == $entity ? '' : $entity->getTitle();
                    $c['real_value'] =  null == $entity ? '' : $entity->getCode();
//                    $c['available_countries'] = $this->getAvailableCharacteristicValues($c);
                    break;
                default:
                    throw new \Exception('Characteristic of the given type does not exist');
                    break;
            }
        }
        return $product;//->characteristics;
    }

    public function updateDocument($params)
    {
        $collection = $this->db->{$this->collectionName};
        $updateResult = $collection->updateOne(
                $params['where'],
                ['$set' => $params['set']]
        );

        //print_r($updateResult);
        return $updateResult;
    }
    
    public function updateServerDocument($headers, $content = [])
    {
        $url = $this->config['parameters']['1c_provider_links']['lk_update_product'];
        $result = $this->curlRequestManager->sendCurlRequestWithCredentials($url, $content, $headers);
        return $result;
    }
    
    public function updateServerPriceAndDiscount($headers, $content = [])
    {
        $url = $this->config['parameters']['1c_provider_links']['lk_update_price'];
        $answer = [
            'date' => $content['date'],
            //'provider_id' => $content['provider_id'],
            'product_id' => $content['product_id'],
            'price' => max( (int) $content['price'], (int) $content['old_price']),
            'discount' => (int) $content['discount'],
        ];
        $result = $this->curlRequestManager->sendCurlRequestWithCredentials($url, ['data' => [$answer]], $headers);
        
        return $result;
    }
    
    public function addServerDocument($headers, $content = [])
    {
        $url = $this->config['parameters']['1c_provider_links']['lk_add_product'];
        $result = $this->curlRequestManager->sendCurlRequestWithCredentials($url, $content, $headers);
        return $result;
    }

    private function buildProduct($headers, $categoryId)
    {
        //$product = $data['product'];

        $url = $this->config['parameters']['1c_provider_links']['lk_get_info_by_category'];
        $product = $this->curlRequestManager->sendCurlRequestWithCredentials($url, ['category_id' => $categoryId], $headers);
        
        return $product;
    }
    
    public function requestCategoryCharacteristics($headers, $data)
    {
//        $productId = $data['product']['id'];
//        $providerId = $data['product']['provider_id'];
//        $categoryId = $data['product']['category_id'];
        $newCategoryId = $data['new_category_id'];
        
        /** Lookup product from cache using $newCategoryId */
        /** load it from cache if found */
        
        //$product = $this->find(['id' => $productId, 'provider_id' => $providerId, 'category_id' => $newCategoryId]);
        
//        if(true || null == $product) {
//            $product = $this->buildProduct($headers, $newCategoryId /* $data */);
//        }

        /** Save current document with specified productId and categoryId */
        
        return $this->buildProduct($headers, $newCategoryId);
        
    }
    
    public function deleteProductImage($fileName)
    {
        $baseUrl = $this->config['parameters']['image_path']['base_url'];
        $uploads = $this->config['parameters']['image_path']['subpath']['cpanel_product'];
        $uploadsDir = 'public'.$baseUrl.'/'.$uploads;
        $fileName = $uploadsDir.'/'.$fileName;
        return ['result' => unlink($fileName)];
    }
    
    public function copyProductImage($fileName)
    {
        $baseUrl = $this->config['parameters']['image_path']['base_url'];
        $productUrl = 'public'.$baseUrl.'/'.$this->config['parameters']['image_path']['subpath']['product'];
        $productUrl = $productUrl.'/'.$fileName;
        $descProductUrl = 'public'.$baseUrl.'/'.$this->config['parameters']['image_path']['subpath']['cpanel_product'];
        $descProductUrl = $descProductUrl.'/'.$fileName;
        
        $result = exec ("cp " . $productUrl . " " . $descProductUrl);
        return $result;
    }
    
    public function copyProductImage2($fileName)
    {
        $baseUrl = $this->config['parameters']['image_path']['base_url'];
        $productUrl = 'public'.$baseUrl.'/'.$this->config['parameters']['image_path']['subpath']['product'];
        $productUrl = $productUrl.'/'.$fileName;
        $descProductUrl = 'public'.$baseUrl.'/'.$this->config['parameters']['image_path']['subpath']['cpanel_product'];
        $descProductUrl = $descProductUrl.'/'.$fileName;

        $data = file_get_contents($productUrl);

        $handle = fopen($descProductUrl, "w");
        fwrite($handle, $data);
        fflush($handle);
        fclose($handle);
    }
    
    public function copyProductImage1($fileName)
    {
        $baseUrl = $this->config['parameters']['image_path']['base_url'];
        $productUrl = 'public'.$baseUrl.'/'.$this->config['parameters']['image_path']['subpath']['product'];
        $descProductUrl = 'public'.$baseUrl.'/'.$this->config['parameters']['image_path']['subpath']['cpanel_product'];
        $descProductUrl = $descProductUrl.'/'.$fileName;
        $productUrl = $productUrl.'/'.$fileName;
        $result = copy($productUrl, $descProductUrl);
        
        return ['result' => $result];
    }    

    public function findTest()
    {
        //$query = array('$text' => array('$search'=> 'vivo'));
        $collection = $this->db->products;
        //'city' => ['$regex' => '^garden', '$options' => 'i'],
        $cursor = $collection->find([
                    //'title' => new \MongoDB\BSON\Regex('True', 'i'),
                    'title' => ['$regex' => 'vivo', '$options' => 'i'],
                        //'title' => ['$text' => ['$search' => 'vivo']], //- drops indexes for some reason
                        ], ['_id' => 0])->toArray();
//        $cursor = $collection->find($query);
        echo '<pre>';
        print_r($cursor);
        echo '</pre>';
        exit;
    }

}

//        $collection = $this->mclient->saychas_cache->profile;
//        $document = $collection->findOne();
//        var_dump( $document );
//        exit;






//        $this->dropCollection('banzaii');
//        $this->insertManyInto('banzaii', [['name' => 'name1', 'value' => 'value1'], ['name' => 'name2', 'value' => 'value2']]);
//        $count = $this->countCollection('banzaii');








        //$mongoProfile = $this->mclient->selectDatabase('saychas_cache')->selectCollection('profile')->findOne();






//    /**
//     * Drop collection from db
//     *
//     * @param $collection
//     * @return result
//     */
//    protected function dropCollection($collection)
//    {
//        $result = $this->db->dropCollection($collection);
//        return $result;
//    }
//
//    /**
//     * Insert array of documents into collection
//     *
//     * @param string $collectionName
//     * @param array $documents
//     * @return type
//     */
//    protected function insertManyInto($collectionName, array $documents)
//    {
//        $collection = $this->db->$collectionName;
//        return $collection->insertMany($documents);
//    }
//
//    /**
//     * Count documents in a collection
//     *
//     * @param string $collectionName
//     * @return int
//     */
//    protected function countCollection($collectionName = self::COLLECTION_NAME)
//    {
//        $collection = $this->db->$collectionName;
//        return $collection->count();
//    }
//
//    /**
//     * Set product number per page
//     *
//     * @param type $pageSize
//     * @return $this
//     */
//    public function setPageSize($pageSize)
//    {
//        $this->pageSize = $pageSize;
//        return $this;
//    }
//
//    /**
//     * Calculate max and min values
//     * in the product array for page with pageNumber
//     *
//     * @param int $pageNumber
//     * @return array
//     */
//    public function calcLimits($pageNumber)
//    {
//        $limits = ['min' => 0, 'max' => 0];
//
//        $this->collectionSize = $this->countCollection();
//
//        if (0 < $this->collectionSize) {
//            $div = (int) ($this->collectionSize / $this->pageSize);
//            $mod = $this->collectionSize % $this->pageSize;
//            $limits['min'] = ($pageNumber - 1) * $this->pageSize + 1;
//            $limits['min'] = ($limits['min'] > $div * $this->pageSize + $mod) ? $div * $this->pageSize + 1 : $limits['min'];
//            $limits['max'] = $pageNumber * $this->pageSize;
//            $limits['max'] = ($limits['max'] > $this->collectionSize ? $this->collectionSize : $limits['max']);
//        }
//
//        return $limits;
//    }






//
//    /**
//     * @var db
//     */
//    protected $db;
//
//    /**
//     * @var int
//     */
//    protected $pageSize = 3;
//
//    /**
//     * @var int
//     */
//    protected $collectionSize;




//    /**
//     * @var \MongoDB\Client
//     */
//    protected $mclient;
//





//    /**
//     * @var string
//     */
//    protected $collectionName = self::COLLECTION_NAME;













    /**
     * Load all products from 1C for logged in user and store them into db
     *
     * @param array $credentials
     * @return array
     */
//    public function loadAll($url, array $credentials = [])
//    {
////        $url = $this->config['parameters']['1c_provider_links']['lk_product_info'];
//
//        $answer = $this->curlRequestManager->sendCurlRequestWithCredentials($url, [], $credentials);
//
//        $this->dropCollection(self::COLLECTION_NAME);
//
//        $this->insertManyInto(self::COLLECTION_NAME, $answer['data']);
//
//        $this->collectionSize = $this->countCollection();
//
////        $cursor = $this->findAll(['pageNo' => 3])->toArray();
//
//        //$limits = $this->calcLimits(2);
//
//        return $answer;
//    }











//    public function findCategoryById($params)
//    {
//        $id = $params['id'];
//        $category = $this->categoryRepo->findCategory(['id'=>$id]);
//        return $category;
//    }
//
//    public function findBrandById($params)
//    {
//        $id = $params['id'];
//        $brand = $this->brandRepo->find(['id' => $id]);
//        return $brand;
//    }
















//    public function findAll($params)
//    {
//        if (isset($params['pageNo'])) {
//            $limits = $this->calcLimits($params['pageNo']);
//            $collection = $this->db->{$this->collectionName};
//            $c = $collection->count($params['where']);
//            $cursor = $collection->find
//            (
//                $params['where'],
//                [
//                    'skip' => $limits['min'] - 1,
//                    'limit' => $this->pageSize,
//                    'projection' => [],
////                    'projection' => [
////                        'id' => 1,
////                        'title' => 1,
////                        'category_id' => 1,
////                        'brand_id' => 1,
////                        'description' => 1,
////                        'vendor_code' => 1,
////                        'provider_id' => 1,
////                        'color' => 1,
////                        'country' => 1,
////                        'characteristics' => 1, // ['id' => 1, 'type' => 1],
////                        'images' => 1,
////                        '_id' => 0
////                    ],
//                ]
//            );
//            $result['body'] = $cursor->toArray();
//            $result['limits'] = $limits;
//            $result['limits']['total'] = $this->calcLimits($params['pageNo'], $c)['total'];
//            return $result;
//        }
//        return [];
//    }

