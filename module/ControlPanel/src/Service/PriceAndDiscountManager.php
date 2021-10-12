<?php

// ControlPanel\src\Service\PriceAndDiscountManager.php

namespace ControlPanel\Service;

use ControlPanel\Service\CurlRequestManager;
//use ControlPanel\Model\Traits\Loadable;
use ControlPanel\Contract\LoadableInterface;
use Application\Model\Repository\CategoryRepository;
use Application\Model\Entity\HandbookRelatedProduct as Product;

/**
 * Description of PriceAndDiscountManager
 *
 * @author alex
 */
class PriceAndDiscountManager extends ListManager implements LoadableInterface
{

//    use Loadable;

    protected $collectionName = 'price_and_discount';

    /**
     * @var string
     */
    protected $dbName = 'saychas_cache';

    /**
     * @var CategoryRepository
     */
    protected $categoryRepo;

    /**
     * @var Laminas\Config\Config
     */
    protected $config;
    protected $entityManager;

    /**
     * @var CurlRequestManager
     */
    protected $curlRequestManager;

    /**
     * Constructor
     *
     * @param Laminas\Config\Config $config
     * @param CurlRequestManager $curlRequestManager
     * @param \MongoDB\Client $mclient
     */
    public function __construct($config, CurlRequestManager $curlRequestManager, \MongoDB\Client $mclient, $entityManager, $categoryRepo)
    {
        $this->config = $config;
        $this->curlRequestManager = $curlRequestManager;
        $this->mclient = $mclient;
        $this->db = $this->mclient->{$this->dbName};
        $this->categoryRepo = $categoryRepo;
        $this->entityManager = $entityManager;
        $this->entityManager->initRepository(Product::class);
    }

    private function findCategories($params)
    {
        $collection = $this->db->{$this->collectionName};
        $results = $collection->distinct('category_id', $params['where']);
        $accumulator = [];
        foreach ($results as &$c) {
            //$c1 = $c;
            if (!empty($c)) {
                $category = $this->categoryRepo->findCategory(['id' => $c]);
                $category_name = (null == $category) ? '' : $category->getTitle();
                $accumulator[] = [$c, $category_name,];
            }
        }
        return $accumulator;
    }

    /**
     * Find store documents for specified provider
     *
     * @param array $params
     * @return array
     */
    public function findDocuments($params)
    {
//        $params['where']['product_name'] = $params['where']['title'];
        $title = $params['where']['title'];
        unset($params['where']['title']);
        $cursor = $this->findAll($params);
        $categories = [];
        $result = [];
        foreach ($cursor['body'] as &$c) {
            $flag = substr_count($c['product_name'], $title['$regex']) || empty($title['$regex']);
            $flag2 = $c['category_id'] == $params['where']['category_id'] || !isset($params['where']['category_id']);
//            if(!empty($c['category_id'])) {
//                $category = $this->categoryRepo->findCategory(['id' => $c['category_id']]);
//                $c['category_name'] = (null == $category) ? '' : $category->getTitle();
//                $categories[] = [$c['category_id'], $c['category_name'], ];
//            }

            if (($flag && $flag2)) {
                $category = $this->categoryRepo->findCategory(['id' => $c['category_id']]);
                $c['category_name'] = (null == $category) ? '' : $category->getTitle();
                $categories[] = [$c['category_id'], $c['category_name'],];
                $result[] = $c;
                continue;
            }
//            if(!empty($c['product_id'])) {
//                $entity = Product::find(['id' => $c['product_id']]);
//                $c['title'] = null != $entity ? $entity->getTitle() : '';
//            }
        }

        $cursor['body'] = $result;

        $cursor['filters']['categories'] = $this->findCategories($params);
        return $cursor;
    }

    public function updateServerDocument($headers, $content = [])
    {
        $url = $this->config['parameters']['1c_provider_links']['lk_update_price_and_discount'];
        $result = $this->curlRequestManager->sendCurlRequestWithCredentials($url, $content, $headers);
        return $result;
    }

//    public function addServerDocument($headers, $content = [])
//    {
//        $url = $this->config['parameters']['1c_provider_links']['lk_add_price_and_discount'];
//        $result = $this->curlRequestManager->sendCurlRequestWithCredentials($url, $content, $headers);
//        return $result;
//    }

    public function replacePriceAndDiscount($priceAndDiscount)
    {
        $collection = $this->db->{$this->collectionName};
        $collection->deleteMany([
            'id' => $priceAndDiscount['id'],
            'provider_id' => $priceAndDiscount['provider_id'],
        ]);
        $updateResult = $collection->insertOne($priceAndDiscount);
        return $updateResult;
    }

}
