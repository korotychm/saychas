<?php

// src/Helper/CryptHelper.php

namespace Application\Helper;

use Application\Resource\Resource;

/**
 * CryptHelper
 */
class CryptHelper
{

    public function encrypt($text)
    {
        
        return self::crypting($text, Resource::CRYPT_TOKEN,  Resource::CRYPT_TYPE);
        
    }
    
    public function decrypt($text)
    {
        return self::crypting($text, Resource::CRYPT_TOKEN,  Resource::CRYPT_TYPE, false);
    }

    private function crypting($text,  $password,  $encryptType = "", $encryptIt = true)
    {
        $ciphers = openssl_get_cipher_methods();
        $foundEncType = false;

        for ($pointer = 0; $pointer < count($ciphers); $pointer = $pointer + 1) {
            if ($ciphers[$pointer] == $encryptType) {
                $foundEncType = true;
            }
        }

        if (!$foundEncType) {
            $encryptType = "RC2-64-CBC";
        }
        
        if ($encryptIt) {
            $ivlen = openssl_cipher_iv_length($encryptType );
            $iv = openssl_random_pseudo_bytes($ivlen);
            $newText = openssl_encrypt($text, $encryptType, $password, $options=0, $iv);
        } else {
            $newText = openssl_decrypt($text, $encryptType, $password);
        }

        return $newText;
    }

}
