<?php

// src\Service\AcquiringCommunicationService.php

namespace Application\Service;

use Laminas\Config\Config;
use Laminas\Session\Container;
use Laminas\Json\Json;
//use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;
//use Application\Model\Entity;
use Application\Model\Entity\Basket;
use Application\Model\Entity\HandbookRelatedProduct;
use Application\Model\RepositoryInterface\HandbookRelatedProductRepositoryInterface;

/**
 * Description of AcquiringCommunicationService
 *
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
    private $entityManager;

    public function __construct($config,
            HandbookRelatedProductRepositoryInterface $productRepository, $entityManager)
    {
        $this->productRepository = $productRepository;
        $this->tinkoffApiParams = $config['parameters']['TinkoffMerchantAPI'];
        $this->entityManager = $entityManager;
        $this->entityManager->initRepository(HandbookRelatedProduct::class);
        $this->entityManager->initRepository(Basket::class);
        
    }

    /*
     * создает идетификатор платежа и ссылку на платежную форму
     * @return Json
     */
    public function initTinkoff($args)
    {
        return $this->buildQueryTinkoff('Init', $args);
    }

    /*
     * Возвращает статус платежа
     * @return Json
     */
    public function getStateTinkoff($args)
    {
        return $this->buildQueryTinkoff('GetState', $args);
    }

    /*
     * Подтверждает платеж 
     * @return Json
     */
    public function confirmTinkoff($args)
    {
        return $this->buildQueryTinkoff('Confirm', $args);
    }

    /*
     * Осуществляет рекуррентный (повторный) платеж — безакцептное списание денежных
     * средств со счета банковской карты Покупателя
     * @return Json
     */
    public function chargeTinkoff($args)
    {
        return $this->buildQueryTinkoff('Charge', $args);
    }
     
    /*
     * Отменяет платеж 
     * @return Json
     */
    public function cancelTinkoff($args)
    {
        return $this->buildQueryTinkoff('Cancel', $args);
    }

     /*
     * Регистрирует покупателя в терминале
     * @return Json
     */
    public function addCustomerTinkoff($args)
    {
        return $this->buildQueryTinkoff('AddCustomer', $args);
    }

     /*
     * Возвращает данные покупателя, зарегестрированного в терминале
     * @return Json
     */
    public function getCustomerTinkoff($args)
    {
        return $this->buildQueryTinkoff('GetCustomer', $args);
    }

    /*
     * Удаляет покупателя из терминала
     * @return Json
     */
    public function removeCustomerTinkoff($args)
    {
        return $this->buildQueryTinkoff('RemoveCustomer', $args);
    }

    /*
     * Возвращает список привязанных карт покупателя
     * @return Json
     */
    public function getCardListTinkoff($args)
    {
        return $this->buildQueryTinkoff('GetCardList', $args);
    }

    /*
     * Отвязывает карту покупателя
     * @return Json
     */
    public function removeCardTinkoff($args)
    {
        return $this->buildQueryTinkoff('RemoveCard', $args);
    }

     /**
     * Get order items
     *
     * @param object 
     * @return array
     */
    public function getOrderItems($data)
    {
        $return['Amount'] = 0;
         foreach ($data as $basketItem) {
            $total = (int) $basketItem->getTotal();
            $oldprice = $basketItem->getPrice();
            $discount = $basketItem->getDiscount();
            $price = ($oldprice - $oldprice * $discount / 100);
            $productId = $basketItem->getProductId();
            $product = $this->productRepository->find(['id' => $productId]);
            $vatValue = (int) $product->getVat();
            $vat = ($vatValue < 0) ? "none" : "vat" . $vatValue;
            $return['Items'][] = [
                'Name' => $product->getTitle(), 
                'Quantity' => $total,
                'PaymentObject' => "commodity",
                'Amount' => $price * $total,
                'Price' => $price,
                'Tax' => $vat,
            ];
            $return['Amount'] += ($price * $total);
        }
        return $return;
    }
    
    public function returnProductsToBasket($order_id, $userId)
    {
       $orderProducts = $this->basketRepository->findAll(["where" => ["order_id" => $order_id], "columns" =>["product_id"], "group"=>["product_id"] ])->toArray();  
       $returnProduct = ArrayHelper::extractId($orderProducts);

       foreach ($returnProduct as $productId){
            
            if (empty($productadd = HandbookRelatedProduct::findAll(['id' => $productId])->current())){
                continue;
            }
            
            if (empty($productaddPrice = $productadd->getPrice())){
                continue;
            }
           
            $basketItem = Basket::findFirstOrDefault(['user_id' => $userId, 'product_id' => $productId, 'order_id' => "0"]);
            $basketItemTotal = (int) $basketItem->getTotal(); 
            $basketItem->setUserId($userId)->setProductId($productId)->setPrice($productaddPrice)->setTotal($basketItemTotal + 1);
            $basketItem->persist(['user_id' => $userId, 'product_id' => $productId, 'order_id' => "0"]);
            $returnedProduct[] = $productId;
       }   
        
       return $returnedProduct ?? [];
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
       return hash('sha256', $token); 
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
                if ($arg[strlen($arg) - 1] !== '/')
                    $arg .= '/';
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
        //return $args;
        $return=[];//     $return['error'] = false;
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
            curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json',]);

            $out = curl_exec($curl);
            $json = Json::decode($out, Json::TYPE_ARRAY);

            if ($json) {
                if ($json["ErrorCode"] !== "0") {
                    $return['error'] = "Error " . $json["ErrorCode"] . "! " . $json["Message"] . " " . $json["Details"] . "!";
                } else {
                    $return['answer'] = $json;
                }
            }
            curl_close($curl);
            
        } else {
            $return['error'] = "Can not create connection to ' . $api_url . ' with args ' . $args, 404)";
        }
       return $return; 
    }

}
