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
        $collection = $this->db->products; //{$this->collectionName};
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
        $storeId = $params['where']['store_id'];
        $pageNo = $params['pageNo'];
        $cursor = $this->findAll(['pageNo' => $pageNo, 'where' => ['store_id' => $storeId]]);
        //$cursor = $this->findAll($params);
        $result = [];
        foreach ($cursor['body'] as &$c) {
            // find product by product_id
            $products = $c['products'];
            foreach ($products as $product) {
                $productId = $product['product_id'];
                $quantity = $product['quantity'];
                $arr = (array) $this->db->products->find(['id' => $productId]/* , ['_id' => 0] */)->toArray()[0];
//                $arr = $arr[0];
                unset($arr['_id']);
                $arr['quantity'] = $quantity;

                $flag = substr_count($arr['title'], $params['where']['title']['$regex']) || empty($params['where']['title']['$regex']);
                $flag2 = $arr['category_id'] == $params['where']['category_id'] || !isset($params['where']['category_id']);

                if (($flag && $flag2)) {
                    $result[] = $arr;
                }
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
        $updateResult = null;
        foreach ($stockBalance['data'] as $balance) {
            $updateResult = $collection->updateOne(['store_id' => $balance['store_id'], 'provider_id' => $balance['provider_id']],
                    ['$set' => ["products.$[element].quantity" => $balance['products'][0]['quantity']]],
                    ['arrayFilters' => [["element.product_id" => ['$eq' => $balance['products'][0]['product_id']]]]]);
        }

        return $updateResult;
    }

//    public function replaceStockBalance1($stockBalance)
//    {
//        $collection = $this->db->{$this->collectionName};
//        // find the stock balance document
//        foreach ($stockBalance['data'] as $balance) {
//            $p = $balance['products'][0]; // We take the first one
//            $foundDocument = $collection->findOne(['store_id' => $balance['store_id'], 'provider_id' => $balance['provider_id']]); //->toArray();
//            if (null != $foundDocument) {
//                foreach ($foundDocument['products'] as &$product) {
//                    if ($p['product_id'] == $product['product_id']) {
//                        $product['quantity'] = $p['quantity'];
//                    }
//                }
//            }
//            $collection->deleteMany([
//                'store_id' => $balance['store_id'],
//                'provider_id' => $balance['provider_id'],
//            ]);
//            $updateResult = $collection->insertOne($foundDocument);
//        }
//
//        return $updateResult;
//    }
//    public function replaceStockBalance($stockBalance)
//    {
//        $collection = $this->db->{$this->collectionName};
//        foreach($stockBalance['data'] as $balance) {
//            foreach($balance['products'] as $product) {
//                $collection->deleteMany([
//                    'store_id' => $balance['store_id'],
//                    'product_id' => $product['product_id'],
//                ]);
//            }
//            $updateResult = $collection->insertOne($balance);
//        }
//        return $updateResult;
//    }

    public function deleteMany(string $collectionName, array $params = [])
    {
        $collection = $this->db->$collectionName;
        $deleteResult = $collection->deleteMany($params);
        return $deleteResult;
    }

}

//$updateResult = $collection->findOneAndUpdate(['store_id' => $balance['store_id'], 'provider_id' => $balance['provider_id'], 'products.$.product_id' => $balance['products'][0]['product_id']], ['$set' => [ 'products.$.quantity' => '123' ]], []);
            //db.stock_balance.updateOne({store_id: '000000033', provider_id: '00007'}, {$set: {"products.$[element].quantity": '321'}}, {arrayFilters: [{"element.product_id": {$eq: '000000000082'} }]})
            //$updateResult = $collection->findOne(['store_id' => $balance['store_id'], 'provider_id' => $balance['provider_id'], 'products.$.product_id' => $balance['products'][0]['product_id']]);
