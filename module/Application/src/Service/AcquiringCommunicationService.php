<?php

// src\Service\AcquiringCommunicationService.php

namespace Application\Service;

use Laminas\Config\Config;
use Laminas\Session\Container;
use Laminas\Json\Json;
use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;
//use Application\Model\Entity;
use Application\Model\Entity\ClientOrder;
use Application\Model\Entity\HandbookRelatedProduct;
use Application\Model\RepositoryInterface\HandbookRelatedProductRepositoryInterface;

/**
 * Description of AcquiringCommunicationService
 *
 * @author plusweb
 */
class AcquiringCommunicationService
{

    /**
     *
     * @var Config
     */
    private $config;
    private $productRepository;

    public function __construct($config,
            HandbookRelatedProductRepositoryInterface $productRepository)
    {
       $this->config = $config;
       $this->productRepository = $productRepository;
    }
    public function tinkoffInit($param)
    {
        
    }
    public function getBasketData ($data)
    {
        $return['basket_total'] = 0;
        foreach ($data as $basketItem){
            $total = $basketItem->getTotal();
            $oldprice = $basketItem->getPrice();
            $price = ($oldprice - $oldprice*$discount/100);
            $discount = $basketItem->getDiscount();
            
            $productId = $basketItem->getProductId();
            $product = $this->productRepository->find(['id' => $productId]);
            $productTitle = $product->getTitle();
            
            $return['basket_items'][] = [
                "id" => $productId,
                'productTitle' =>  $productTitle,
                "total" => $total,
                "oldprice" => $oldprice,
                "discount" => $discount,
                'price' => $price,
                'tax' => $product->getTax(),
            ];
            $return['basket_total']+=($price*$total);
        }
        return $return;
    }
    

}
