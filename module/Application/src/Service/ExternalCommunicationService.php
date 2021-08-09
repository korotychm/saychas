<?php

// src\Service\ExternalCommunicationService.php

namespace Application\Service;

use Laminas\Config\Config;
use Laminas\Session\Container;
use Laminas\Json\Json;
use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;
//use Application\Model\Entity;
use Application\Model\Entity\ClientOrder;

/**
 * Description of ExternalCommunicationService
 *
 * @author alex
 */
class ExternalCommunicationService
{

    /**
     *
     * @var Config
     */
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Sends a registration sms
     *
     * Returns Html string
     * @return string
     */
    public function sendRegistrationSms($phone, $code)
    {
        $url = $this->config['parameters']['1c_request_links']['send_registration_code'];

        $content = [
            "phone" => (int) $phone, // 9160010203, // $phone
            "code" => (int) $code, // 7777,
        ];
        return $this->sendCurlRequest($url, $content);
    }

    public function sendBasketData($content)
    {
        $url = $this->config['parameters']['1c_request_links']['create_order'];

        if (!$content["products"])
            return false;
        if (empty($content["products"]))
            return false;

        while (list($id, $value) = each($content["products"])) {
            $store[$value["store"]][] = [
                "id" => $id, "count" => $value["count"], "price" => $value["price"], "discont" => (int) $value["discont"]
            ];
            $productupdate[]=$id;
            //$coontent["delevery"][] = $store[$value['store']];
        }
        //(надо вынести в resource)
        $limit = ($content["ordermerge"]) ? 1 : 4; //лимит счетчика для наполнения  массива магазинов в доставке 
        
        $i = 1; // счетчик индекса массива для добавления элеметнтов  для обычной доставки
        $j = 0; //счетчик индекса массива для добавления элеметнтов  для объедененной доставки 
        //$q = -1;
        while (list($key, $val) = each($store)) {
            $i++;
            //$selfdelevery = false;
            if ($content['selfdelevery'] and in_array($key, $content['selfdelevery'])) {

                $selfdeliv[] = ["store" => $key, "products" => $val];
            } else {
                if ($i < $limit) {
                    $i = 1;
                    $j++;
                }
                $delivery[$j][] = ["store" => $key, "products" => $val];
            }
        }

        if (!empty($delivery)) {
            while (list($key, $val) = each($delivery))
                $deliv[] = $val;
        }
        $content["deliveryes"] = ["selfdelivery" => $selfdeliv, 'delivery' => $deliv];
        //$content["delevery"]=$store;
        $return['basketinfo']['userGeoLocation'] = $content['userGeoLocation'] = ($content['userGeoLocation']) ? Json::decode($content['userGeoLocation']) : [];
        unset(
                $content['timepointtext1'],
                $content['timepointtext3'],
                $content['timepointtext3'],
                // $content['selfdelevery'], 
                $content["products"]);
        /* unset($content['userGeoLocation']);
          $content['userGeoLocation'] = []; */
        //return $content;
        
        $return['basketinfo']['username'] = $content['username'];
        $return['products'] = $productupdate;
        $return['response'] = $this->sendCurlRequest($url, $content);
   
        
        return $return;
        /*try {
            $arr = Json::decode($answer, Json::TYPE_ARRAY);
            return $arr;
        } catch (LaminasJsonRuntimeException $e) {
            //return ['result' => 10, 'message' => $e->getMessage().' '.$response];
            return ['result' => false, 'message' => $e->getMessage() . ' >>> ' . $answer];
        }*/
        
        
        /*$response = file_get_contents(
          $url,
          false,
          stream_context_create([
          'http' => [
          'method' => 'POST',
          'header' => 'Content-type: application/x-www-form-urlencoded',
          'content' => http_build_query($content)]
          ])*/
        
        //$return = json_decode($answer, true);
        //$order = ClientOrder::findFirstOrDefault([]);
       //return "<pre>" .print_r($return, true) ."</pre>";
        //return $return;
        
        
    }
    
    public function createClientOrder ($content, $order)
    {
        
        //$content['basketinfo'];
        $basketinfo = Json::encode($content['basketinfo']);
        $deliveryes = Json::encode($content['response']['deliveryes']);
        $orderId = $content['response']['order'];
//        $order = ClientOrder::findFirstOrDefault(['order_id'=>$orderId]);
        return $order;
        $order->setOrderId($orderId); 
        $order->setDeliveryInfo($deliveryes); 
        $order->setBasketInfo($basketinfo); 
        $order->setDateCreated(time());
        $order->persist();
        
    }
    
    
    /**
     * Send curl request.
     *
     * @param string $url
     * @param array $content
     * @return array
     */
    protected function sendCurlRequest($url, $content)
    {
//        $login = $this->config['1C_order']['login'];
//        $pass = $this->config['1C_order']['password'];
        /* $response = file_get_contents(
          $url,
          false,
          stream_context_create([
          'http' => [
          'method' => 'POST',
          'header' => 'Content-type: application/x-www-form-urlencoded',
          'content' => http_build_query($content)]
          ])
          ); */

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        //curl_setopt($curl, CURLOPT_HTTPHEADER, ['BOM_required: false', 'charset_response: UTF-8']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($content));

        //curl_setopt($curl, CURLOPT_USERPWD, $login.":".$pass);
        curl_setopt($curl, CURLOPT_SSLVERSION, 3);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($curl);
//        $arr = json_decode($response, true);

        try {
            $arr = Json::decode($response, Json::TYPE_ARRAY);
            return $arr;
        } catch (LaminasJsonRuntimeException $e) {
            //return ['result' => 10, 'message' => $e->getMessage().' '.$response];
            return ['result' => false, 'message' => $e->getMessage() . ' ' . $response];
        }
    }

    /**
     * Set client info.
     *   Example parameter to pass to setClientInfo:
     *   $content structure to be sent
     *   $content = [
     *       "name" => "name1", // mandatory
     *       "surname" => "surname1",
     *       "middle_name" => "middle_name",
     *       "phone" => (int) 9185356024, // mandatory
     *       "email" => "my@mail.ru"
     *   ];
     * @param array $content
     * @return array
     */
    public function setClientInfo(array $content): array
    {
        $url = $this->config['parameters']['1c_request_links']['set_client_info'];
        $content['phone'] = (int) $content['phone'];
        return $this->sendCurlRequest($url, $content);
    }

    /**
     * Get client info.
     *
     * Example parameter to pass to getClientInfo:
     * $content = [
     *      "id" => "000000001",
     * ];
     *
     * @param array $content
     * @return array
     */
    public function getClientInfo(array $content)
    {
        $url = $this->config['parameters']['1c_request_links']['get_client_info'];
        $content['phone'] = (int) $content['phone'];
        return $this->sendCurlRequest($url, $content);
    }

    /**
     * Update client info.
     *
     * Example parameter to pass to updateClientInfo:
     * $content = [
     *      "id" => "000000001", // mandatory
     *       "name" => "name1", // optional
     *       "surname" => "surname1", // optional
     *       "middle_name" => "middle_name", // optional
     *       "phone" => (int) 9185356024, // optional
     *       "email" => "my@mail.ru" // optional
     * ];
     *
     * @param array $content
     * @return array
     */
    public function updateClientInfo(array $content)
    {
        $url = $this->config['parameters']['1c_request_links']['update_client_info'];
        $content['phone'] = (int) $content['phone'];
        return $this->sendCurlRequest($url, $content);
    }

    /**
     * Change password.
     *
     *   $content = [
     *       "id" => $id,
     *       "old_password" => $oldPassword,
     *       "new_password" => $newPassword,
     *       "new_password2" => $newPassword,
     *   ];
     * @param array $content
     * @return array
     */
    public function changePassword(array $content): array
    {
        $url = $this->config['parameters']['1c_request_links']['change_client_password'];
        if ($content['new_password'] != $content['new_password2']) {
            return ['result' => -1, 'message' => 'Passwords are not equal'];
        }

        return $this->sendCurlRequest($url, $content);
    }

    /**
     * Send new credentials(Это пара ID и Password)
     * 
     * $content = [
     *      'id' => 'userId',
     *      'password' => 'password',
     * ]
     * 
     * @param array $content
     */
    public function sendCredentials(array $content)
    {
        $url = $this->config['parameters']['1c_request_links']['update_client_info'];
        return $this->sendCurlRequest($url, $content);
    }

    /**
     * Login client
     * 
     *  $content = [
     *       "phone" => "9160010204",
     *       "password" => "1112233T"
     *  ];
     * @param array $content
     * @return array
     */
    public function clientLogin(array $content): array
    {
        $url = $this->config['parameters']['1c_request_links']['client_login'];

        return $this->sendCurlRequest($url, $content);
    }

}
