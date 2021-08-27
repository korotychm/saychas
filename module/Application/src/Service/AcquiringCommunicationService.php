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
    private $tinkoffApiParams;
    

    public function __construct($config,
            HandbookRelatedProductRepositoryInterface $productRepository)
    {
       $this->productRepository = $productRepository;
       $this->tinkoffApiParams = $config['parameters']['TinkoffMerchantAPI'];
    }
        
    public function getBasketData ($data)
    {
        $return['Amount'] = 0;
        //$return['t_url']= $this->tinkoffApiParams["api_url"];
        foreach ($data as $basketItem){
            $return['count']++;
            $total = $basketItem->getTotal();
            $oldprice = $basketItem->getPrice();
            $discount = $basketItem->getDiscount();
            $price = ($oldprice - $oldprice*$discount/100);
            $productId = $basketItem->getProductId();
            $product = $this->productRepository->find(['id' => $productId]);
            $productTitle = $product->getTitle();
            $vatValue = (int)$product->getVat();
            $vat=($vatValue < 0)?"none":"vat".$vatValue;
            
            $return['Items'][] = [
                //"id" => $productId,
               'Name' =>  $productTitle,
               'Quantity' => $total,
               'PaymentObject' => "commodity",
                //"oldprice" => $oldprice,
                //"discount" => $discount,
                'Amount' => $price * $total,
                'Price' => $price,
                'Tax' => $vat,
            ];
            $return['Amount']+=($price*$total);
        }
        return $return;
    }
    
      /**
     * Build query
     *
     * @param $args, $path
     * @return array
     */
   
    public function buildQueryTinkoff($path, $args)
    {
        $api_url = $this->tinkoffApiParams["api_url"];
        if (is_array($args)) {
            if (!array_key_exists('TerminalKey', $args)) {
                $args['TerminalKey'] = $this->tinkoffApiParams["terminal"];
            }
            if (!array_key_exists('Token', $args)) {
                $args['Token'] = $this->genTokenTinkoff($args);
            }
        }
        $url = $this->combineUrlTinkoffl($api_url, $path);
        return $this->sendRequestTinkoff($url, $args);
    }

    /**
     * Generates Token
     *
     * @param $args
     * @return string
     */
    private function genTokenTinkoff($args)
    {
        $token = '';
        $args['Password'] = $this->tinkoffApiParams["token"];
        ksort($args);

        foreach ($args as $arg) {
            if (!is_array($arg)) {
                $token .= $arg;
            }
        }
        $token = hash('sha256', $token);

        return $token;
    }
    
    /**
     * build URL
     *
     * @return string
     */
    private function combineUrlTinkoffl()
    {
        $args = func_get_args();
        $url = '';
        foreach ($args as $arg) {
            if (is_string($arg)) {
                if ($arg[strlen($arg) - 1] !== '/') $arg .= '/';
                $url .= $arg;
            } else {
                continue;
            }
        }

        return $url;
    }
    
    /**
     * send curl  request
     *
     * @param $api_url, $args
     * @return array
     */
    private function sendRequestTinkoff($api_url, $args)
    {
       // exit (print_r($api_url));
        //exit ("<pre>".print_r($args, true)."</pre>");
        return [$api_url, $args];
        //$return['error'] = false;
        $return['error'] = false;
        if (is_array($args)) {
            $args = Json::encode($args);
        }

        if ($curl = curl_init()) {
            curl_setopt($curl, CURLOPT_URL, $api_url);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $args);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
            ));

            $out = curl_exec($curl);
            $json = Json::decode($out, Json::TYPE_ARRAY);

            if ($json) {
                if ($json["ErrorCode"] !== "0") {
                    $return['error'] = "Error ".$json["ErrorCode"]."! ". $json["Message"]." ".$json["Details"]."!";
                } else {
                    $return['answer'] = $json; 
                }
            }
            //curl_close($curl);
            return $return;

        } else {
            throw new HttpException('Can not create connection to ' . $api_url . ' with args ' . $args, 404);
        }
    }
    
    public function initTinkoff($args)
    {
        return $this->buildQueryTinkoff('Init', $args);
    }


    public function getStateTinkoff($args)
    {
        return $this->buildQueryTinkoff('GetState', $args);
    }

    public function confirmTinkoff($args)
    {
        return $this->buildQueryTinkoff('Confirm', $args);
    }

    public function chargeTinkoff($args)
    {
        return $this->buildQueryTinkoff('Charge', $args);
    }

    public function addCustomerTinkoff($args)
    {
        return $this->buildQueryTinkoff('AddCustomer', $args);
    }

    public function getCustomerTinkoff($args)
    {
        return $this->buildQueryTinkoff('GetCustomer', $args);
    }

    public function removeCustomerTinkoff($args)
    {
        return $this->buildQueryTinkoff('RemoveCustomer', $args);
    }

    public function getCardListTinkoff($args)
    {
        return $this->buildQueryTinkoff('GetCardList', $args);
    }

    public function removeCardTinkoff($args)
    {
        return $this->buildQueryTinkoff('RemoveCard', $args);
    }
    
    

}
