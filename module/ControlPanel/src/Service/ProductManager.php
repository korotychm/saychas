<?php

// ControlPanel\src\Service\ProductManager.php

namespace ControlPanel\Service;

use Laminas\Hydrator\ClassMethodsHydrator;
use ControlPanel\Service\CurlRequestManager;

/**
 * Description of ProductManager
 *
 * @author alex
 */
class ProductManager
{

    protected $config;
    protected $curlRequestManager;

    public function __construct($config, CurlRequestManager $curlRequestManager)
    {
        $this->config = $config;
        $this->curlRequestManager = $curlRequestManager;
    }
    
    public function getAll()
    {
//        $url = $this->config['parameters']['1c_provider_links']['lk_product_info'];
//        $answer = $this->curlRequestManager->sendCurlRequest($url, [], true);
//
//        return $answer;
         return ['product1', 'product2', 'product3', 'product4',];
    }


}
