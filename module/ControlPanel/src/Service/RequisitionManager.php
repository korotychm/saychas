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

    private function findStatuses()
    {
        return $this->config['parameters']['requisition_statuses'];
    }
    
    private function statusesToKeyValue(array $statuses)
    {
        $result = [];
        foreach($statuses as $st) {
            $result[$st[0]] = $st[1];
        }
        
        return $result;
    }

    private function filter(array $cursor)
    {
        $haystack = ['01', '02', '03'];
        $haystack2= ['04', '05'];
        
        $filter1 = array_filter($cursor, function($var) use ($haystack) {
                if(in_array($var['status_id'], $haystack)) {
                    return true;
                }
                return false;
        });
        $filter2 = array_filter($cursor, function($var) use ($haystack2) {
                if(in_array($var['status_id'], $haystack2)) {
                    return true;
                }
                return false;
        });
        
        usort($filter1, function($a, $b) {
                //if( $a['status_id'] == $b['status_id'] ) {
                if( $a['date'] == $b['date'] ) {
                        return 0;
                }

                //return ( (int) $a['status_id'] < (int) $b['status_id']) ? -1 : 1;
                return ( (int) $a['date'] < (int) $b['date']) ? -1 : 1;
        });

        usort($filter2, function($a, $b) {
                //if( $a['status_id'] == $b['status_id'] ) {
                if( $a['date'] == $b['date'] ) {
                        return 0;
                }

                //return ( (int) $a['status_id'] > (int) $b['status_id']) ? -1 : 1;
                return ( (int) $a['date'] > (int) $b['date']) ? -1 : 1;
        });

        $filter = array_merge($filter1, $filter2);
        
        return $filter;
        
    }
    
    /**
     * Find store documents for specified provider
     *
     * @param array $params
     * @return array
     */
    public function findDocuments($params)
    {
        $pageNo = $params['pageNo'];
        
        /** We added sort key here */
        $cursor = $this->findAll(['pageNo' => $pageNo, 'where' => $params['where'], 'sort' => $params['sort']]);
        
        /** The following code is temporarily commented out as we need to check filter method prior to using it */
        //$cursor['body'] = $this->filter($cursor['body']);
        
        $collection = $this->db->stores;
        
        $stores = $collection->find(
        ['provider_id' => $params['where']['provider_id']],
        [
//            'skip' => 0 >= $limits['min'] ? 0 : $limits['min'] - 1,
//            'limit' => $this->pageSize,
//            'sort' => $params['sort'],
            'projection' => ['id' => 1, 'title' =>1, '_id' => 0 ],
        ])->toArray();
        

        $cursor['filters']['statuses'] = $this->findStatuses();
        $cursor['filters']['stores'] = $stores;
        
        return $cursor;
    }

    public function updateServerDocument($headers, $content = [])
    {
        $url = $this->config['parameters']['1c_provider_links']['lk_update_requisition'];
        $result = $this->curlRequestManager->sendCurlRequestWithCredentials($url, $content, $headers);
        
        return $result;
    }
    
    public function updateRequisitionStatus($headers, $content = [])
    {
        $url = $this->config['parameters']['1c_provider_links']['lk_update_requisition_status'];
        $result = $this->curlRequestManager->sendCurlRequestWithCredentials($url, $content, $headers);
        
        return $result;
    }
    
    public function setRequisitionStatus($id, $statusId)
    {
        $collection = $this->db->{$this->collectionName};
        $si = str_pad($statusId, 2, "0", STR_PAD_LEFT);
        $statuses = $this->statusesToKeyValue($this->findStatuses());
        //error_log(print_r($statuses, true), 1, 'alex@localhost');
        $updateResult = $collection->updateOne(['id' => $id],
                ['$set' => ['status_id' => $si, 'status' => $statuses[$si] ]]);
        
        return $updateResult;
    }
    
    public function getRequisitionStatus($id)
    {
        $requisition = $this->find(['id' => $id]);
        if(null == $requisition) {
            return [
                'result' => false,
                'error_description' => 'requisition with given number not found',
            ];
        }
        return ['result' => true, 'status' => $requisition['status'], 'status_id' => $requisition['status_id']];
    }

    public function replaceRequisition($requisition)
    {
        $collection = $this->db->{$this->collectionName};
        $updateResult = $collection->updateOne(['id' => $requisition['id']],
                ['$set' => ['status_id' => $requisition['status_id'], 'status' => $requisition['status'], 'items' => $requisition['items'] ]]);

        return $updateResult;
    }

    public function deleteMany(string $collectionName, array $params = [])
    {
        $collection = $this->db->$collectionName;
        $deleteResult = $collection->deleteMany($params);
        return $deleteResult;
    }

}
