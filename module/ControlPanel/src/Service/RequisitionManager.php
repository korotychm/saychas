<?php

// ControlPanel\src\Service\RequisitionManager.php

namespace ControlPanel\Service;

use ControlPanel\Service\CurlRequestManager;
//use ControlPanel\Model\Traits\Loadable;
use ControlPanel\Contract\LoadableInterface;
use Application\Model\Repository\CategoryRepository;
use Application\Model\Entity\HandbookRelatedProduct as Product;

/**
 * Description of RequisitionManager
 *
 * @author alex
 */
class RequisitionManager extends ListManager implements LoadableInterface
{

//    use Loadable;

    protected $collectionName = 'requisitions';

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
        $providerId = $params['where']['provider_id'];
        $pageNo = $params['pageNo'];
        $isActive = filter_var($params['where']['is_archive'], FILTER_VALIDATE_BOOLEAN);
        $rating = (int) $params['where']['rating'];
        if($isActive) {
            $filter = ['provider_id' => $providerId, 'response' => ['$ne' => ''], 'rating' => $rating ];
        }else{
            $filter = ['provider_id' => $providerId, 'response' => ['$eq' => ''], 'rating' => $rating ];
        }
        
        if(0 == $rating) {
            unset($filter['rating']);
        }
        
        $cursor = $this->findAll(['pageNo' => $pageNo, 'where' => $filter]);
        
        return $cursor;
    }

    public function updateServerDocument($headers, $content = [])
    {
        $url = $this->config['parameters']['1c_provider_links']['lk_update_requisition'];
        $result = $this->curlRequestManager->sendCurlRequestWithCredentials($url, $content, $headers);
        
        return $result;
    }

    public function replaceRequisition($requisition)
    {
        $collection = $this->db->{$this->collectionName};
        $updateResult = $collection->updateOne(['id' => $requisition['id'], 'uid' => $requisition['uid']],
                ['$set' => ['response' => $requisition['response'], 'date_responsed' => $requisition['date_responsed']]]);
//        foreach ($stockBalance['data'] as $balance) {
//            $updateResult = $collection->updateOne(['store_id' => $balance['store_id'], 'provider_id' => $balance['provider_id']],
//                    ['$set' => ["products.$[element].quantity" => $balance['products'][0]['quantity']]],
//                    ['arrayFilters' => [["element.product_id" => ['$eq' => $balance['products'][0]['product_id']]]]]);
//        }

        return $updateResult;
    }

    public function deleteMany(string $collectionName, array $params = [])
    {
        $collection = $this->db->$collectionName;
        $deleteResult = $collection->deleteMany($params);
        return $deleteResult;
    }

}
