<?php

// ControlPanel\src\Service\StoreManager.php

namespace ControlPanel\Service;

use Laminas\Hydrator\ClassMethodsHydrator;
use ControlPanel\Service\CurlRequestManager;

/**
 * Description of StoreManager
 *
 * @author alex
 */
class StoreManager
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
        return ['store1', 'store2', 'store3', 'store4',];
    }


}
