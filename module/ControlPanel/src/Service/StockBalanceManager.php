<?php

// ControlPanel\src\Service\StockBalanceManager.php

namespace ControlPanel\Service;

use ControlPanel\Service\CurlRequestManager;
//use ControlPanel\Model\Traits\Loadable;
use ControlPanel\Contract\LoadableInterface;
use Application\Model\Repository\CategoryRepository;
use Application\Model\Entity\HandbookRelatedProduct as Product;

/**
 * Description of StockBalanceManager
 *
 * @author alex
 */
class StockBalanceManager extends ListManager implements LoadableInterface
{

//    use Loadable;

    protected $collectionName = 'stock_balance';

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
        $collection = $this->db->products;//{$this->collectionName};
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
    
    /**
     * Find store documents for specified provider
     * 
     * @param array $params
     * @return array
     */
    public function findDocuments($params)
    {
        $cursor = $this->findAll($params);
        $result = [];
        foreach ($cursor['body'] as &$c) {
            //unset($c['_id']);
            // find product by product_id
            $products = $c['products'];
            foreach($products as $product) {
                $productId = $product['product_id'];
                $quantity = $product['quantity'];
                $arr = (array) $this->db->products->find(['id' => $productId]/*, ['_id' => 0]*/)->toArray();
                $arr = $arr[0];
                unset($arr['_id']);
                $arr['quantity'] = $quantity;
                $result[] = $arr;
            }
        }
        $cursor['body'] = $result;
        unset($params['where']['store_id']);
        $cursor['filters']['categories'] = $this->findCategories($params);        
        
        return $cursor;
    }

    public function updateServerDocument($headers, $content = [])
    {
        $url = $this->config['parameters']['1c_provider_links']['lk_update_balance'];
        $result = $this->curlRequestManager->sendCurlRequestWithCredentials($url, $content, $headers);
        return $result;
    }
    
    public function replaceStockBalance($stockBalance)
    {
        $collection = $this->db->{$this->collectionName};
        $collection->deleteMany([
            'id' => $stockBalance['id'],
            'provider_id' => $stockBalance['provider_id'],
        ]);
        $updateResult = $collection->insertOne($stockBalance);
        return $updateResult;
    }
    
    
}

