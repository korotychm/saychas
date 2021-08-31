<?php

// ControlPanel\src\Service\ProductManager.php

namespace ControlPanel\Service;

use ControlPanel\Service\CurlRequestManager;
use ControlPanel\Model\Traits\Loadable;
use ControlPanel\Contract\LoadableInterface;
use Application\Model\Repository\CategoryRepository;
use Application\Model\Entity\Country;
use Application\Model\Entity\Brand;
use Application\Model\Entity\Color;
use Application\Model\Entity\Price;

/**
 * Description of ProductManager
 *
 * @author alex
 */
class ProductManager implements LoadableInterface
{

    use Loadable;

    /**
     * @var string
     */
    public const COLLECTION_NAME = 'products';

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
    }

    public function findAll($params)
    {
        if (isset($params['pageNo'])) {
            $limits = $this->calcLimits($params['pageNo']);
            $collection = $this->db->{$this->collectionName};
            $cursor = $collection->find
            (
                $params['where'],
                [
                    'skip' => $limits['min'] - 1,
                    'limit' => $this->pageSize,
                    'projection' => [
                        'id' => 1,
                        'title' => 1,
                        'category_id' => 1,
                        'brand_id' => 1,
                        'description' => 1,
                        'vendor_code' => 1,
                        'provider_id' => 1,
                        'color' => 1,
                        'country' => 1,
                        'characteristics' => 1, // ['id' => 1, 'type' => 1],
                        '_id' => 0
                    ],
                ]
            );
            $result['body'] = $cursor->toArray();
            $result['limits'] = $limits;
            return $result;
        }
        return [];
    }

    public function findDocuments($params)
    {
        $cursor = $this->findAll($params);
        foreach ($cursor['body'] as &$c) {
//            if (empty($c['category_id'])) {
//                continue;
//            }
            if(!empty($c['category_id'])) {
                //$category = $this->productManager->findCategoryById(['id' => $c['category_id']]);
                $category = $this->categoryRepo->findCategory(['id' => $c['category_id']]);
                $c['category_name'] = $category->getTitle();
            }

//            if (empty($c['brand_id'])) {
//                continue;
//            }
            
            if(!empty($c['brand_id'])) {
                $brand = Brand::find(['id' => $c['brand_id']]);
                $c['brand_name'] = $brand->getTitle();
            }

//            if (empty($c['country'])) {
//                continue;
//            }

            if(!empty($c['country'])) {
                $country = Country::find(['id' => $c['country']]);
                $c['country_name'] = $country->getTitle();
            }

//            if (empty($c['color'])) {
//                continue;
//            }

            if(!empty($c['color'])) {
                $color = Color::find(['id' => $c['color']]);
                $c['color_name'] = $color->getTitle();
            }
            
//            if(empty($c['id'])) {
//                continue;
//            }

            if(!empty($c['id'])) {
                $price = Price::find([ 'product_id' => $c['id'] ]);
                $c['price'] = $price->getPrice();
            }
            
        }

        return $cursor;
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
