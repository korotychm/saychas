<?php
// src\Service\ExternalCommunicationService.php

namespace Application\Service;

use Laminas\Config\Config;
use Laminas\Session\Container;
use Laminas\Json\Json;
use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;

/**
 * Description of ExternalCommunicationService
 *
 * @author alex
 */
class ExternalCommunicationService {
    
    /**
     * 
     * @var Config
     */
    private $config;
    
    public function __construct($config) {
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
    
    protected function sendCurlRequest($url, $content)
    {
//        $login = $this->config['1C_order']['login'];
//        $pass = $this->config['1C_order']['password'];
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
        }catch(LaminasJsonRuntimeException $e){
            return ['result' => 0, 'message' => $e->getMessage()];
        }
        
    }

    /**
     * Set client info.
     * 
     * @param array $content
     * @return array
     */
    public function setClientInfo(array $content) : array
    {
        /** $content structure to be sent
         *   $content = [
         *       "name" => $name,
         *       "surname" => $surname,
         *       "middle_name" => $middle_name,
         *       "phone" => (int) $phone,
         *   ];
         */
        $url = $this->config['parameters']['1c_request_links']['get_client_info'];
        return $this->sendCurlRequest($url, $content);
    }
    
    public function getClientInfo(array $content)
    {
        $url = $this->config['parameters']['1c_request_links']['get_client_info'];
        return $this->sendCurlRequest($url, $content);
    }
}
