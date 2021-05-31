<?php
// src\Service\ExternalCommunicationService.php

namespace Application\Service;

use Laminas\Config\Config;
use Laminas\Session\Container;

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
        
//        $login = $this->config['1C_order']['login'];
//        $pass = $this->config['1C_order']['password'];
        $content = [
            "phone" => (int) $phone, // 9160010203, // $phone
            "code" => (int) $code, // 7777,
        ];
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
        $arr = json_decode($response);
        
        return $arr;
    }
}
