<?php

// ControlPanel\src\Service\CurlRequestManager.php

namespace ControlPanel\Service;

use Laminas\Json\Json;
use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;

/**
 * Description of CurlRequestManager
 *
 * @author alex
 */
class CurlRequestManager
{
    /**
     * Send curl request.
     *
     * @param string $url
     * @param array $content
     * @return array
     */
    public function sendCurlRequest($url, $content)
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
        $info = curl_getinfo($curl);
        $httpCode = $info['http_code'];

        try {
            $arr = Json::decode($response, Json::TYPE_ARRAY);
            return array_merge(['http_code' => $httpCode], $arr);
        } catch (LaminasJsonRuntimeException $e) {
            return ['result' => 10, 'message' => $e->getMessage()];
        }
    }
}
