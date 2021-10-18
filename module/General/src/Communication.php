<?php

// General\Communication.php

namespace General;

use Laminas\Json\Json;
use Laminas\Json\Exception\RuntimeException;

/**
 * Description of Communication
 *
 * @author alex
 */
class Communication
{
    public static function sendCurlRequest($url, $content)
    {
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
        try {
            $arr = Json::decode($response, Json::TYPE_ARRAY);
            return $arr;
        } catch (RuntimeException $e) {
            return ['result' => false, 'message' => $e->getMessage() . ' ' . $response];
        }
    }
}
