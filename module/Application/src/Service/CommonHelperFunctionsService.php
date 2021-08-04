<?php

// src\Service\Factory\CommonHelperFunctionsService.php

namespace Application\Service;

use Laminas\Config\Config;
use Application\Model\Entity\Basket;
use Application\Model\Entity\ClientOrder;
use Application\Model\Entity\Delivery;
//use Laminas\Session\Container;
//use Laminas\Json\Json;
//use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;

/**
 * Description of CommonHelperFunctionsService
 *
 * @author alex
 */
class CommonHelperFunctionsService
{

    /**
     * @var Config
     */
    private $config;
    
    public function __construct($config)
    {
        $this->config = $config;
    }
    
    public function example()
    {
//        Basket::findAll([]);
//        ClientOrder::findAll([]);
//        Delivery::findAll([]);
    }


}
