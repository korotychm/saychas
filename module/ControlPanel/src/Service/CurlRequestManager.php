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

    public function sendCurlRequestWithCredentials($url, $content, $curlHeaders = [])
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        if(count($curlHeaders) > 0) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $curlHeaders);
        }
        //curl_setopt($curl, CURLOPT_HTTPHEADER, ['BOM_required: false', 'charset_response: UTF-8']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($content));

        //curl_setopt($curl, CURLOPT_USERPWD, $login.":".$pass);
        curl_setopt($curl, CURLOPT_SSLVERSION, 3);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($curl);
        $info = curl_getinfo($curl);
        
        try {
            $arr = Json::decode($response, Json::TYPE_ARRAY);
            return ['http_code' => $info['http_code'], 'data' => $arr];
            //return array_merge(['http_code' => $info['http_code']], $arr);
        } catch (LaminasJsonRuntimeException $e) {
            return ['result' => 10, 'message' => $e->getMessage()];
        }

    }

    public function getFakeAnswer($url = 'lk_get_all_users')
    {
        $jsonAnswer = '';
        if('lk_get_all_users' === $url) {
            $jsonAnswer = $this->lkGetAllUser();
        }elseif('lk_provider_login' === $url){
            $jsonAnswer = $this->lkProviderLogin(true);// true - provider logged in;
        }elseif('lk_create_user' === $url){
            $jsonAnswer = $this->lkCreateUser(true);// true - user created;
        }elseif('lk_update_user' === $url){
            $jsonAnswer = $this->lkUpdateUser(true);// true - user updated successfully
        }

        try {
            $httpCode = 200;
            $arr = Json::decode($jsonAnswer, Json::TYPE_ARRAY);
            return array_merge(['http_code' => $httpCode], $arr);
        } catch (LaminasJsonRuntimeException $e) {
            return ['result' => 10, 'message' => $e->getMessage()];
        }

    }

    private function lkGetAllUser()
    {
        $jsonAnswer =<<<EOL
        [
            {
                "login": "Vasya",
                "password": "ODx7hdsGXJ",
                "access_is_allowed": true,
                "roles": [
                    "000000003",
                    "000000002"
                ]
            },
            {
                "login": "Vanya",
                "password": "d9CLwaDnCo",
                "access_is_allowed": false,
                "roles": [
                    "000000005"
                ]
            },
            {
                "login": "Vasya",
                "password": "ODx7hdsjuT",
                "access_is_allowed": false,
                "roles": [
                    "000000002",
                    "000000003"
                ]
            }
        ]
EOL;
        return $jsonAnswer;

    }

    private function lkProviderLogin($answer = true)
    {
        $jsonAnswer = '';

        if($answer) {
            $jsonAnswer = <<<EOL
            {
                "result": true,
                "user_id": "000000001",
                "roles": [
                    "000000003",
                    "000000002"
                ],
                "errorDescription": ""
            }
EOL;
        }else{
            $jsonAnswer = <<<EOLL
            {
                "result": false,
                "user_id": "",
                "roles": [],
                "errorDescription": "Пользователь с такими логином и паролем не найден."
            }
EOLL;
        }

        return $jsonAnswer;
    }

    private function lkCreateUser($answer = true)
    {
        $jsonAnswer = '';

        if($answer) {
            $jsonAnswer = <<<EOL
            {
                "result": true
            }
EOL;
        }else{
            $jsonAnswer = <<<EOLL
            {
                "errorDescription": "Пользователь с такими логином и паролем уже существует."
            }
EOLL;
        }

        return $jsonAnswer;
    }

    private function lkUpdateUser($answer = true)
    {
        $jsonAnswer = '';

        if($answer) {
            $jsonAnswer = <<<EOL
            {
                "result": true,
                "errorDescription": ""
            }
EOL;
        }else{
            $jsonAnswer = <<<EOLL
            {
                "result": false,
                "errorDescription": "Максимальная длина параметра [password] равна 10."
            }
EOLL;
        }
        return $jsonAnswer;
    }

}
