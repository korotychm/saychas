<?php

// src/Helper/CryptHelper.php

namespace Application\Helper;

use Application\Resource\Resource;

/**
 * CryptHelper
 */
class CryptHelper
{

    /**
     * 
     * @param string $text
     * @return string //encrypted text
     */
    public function encrypt($text)
    {
        
        return self::crypting($text, Resource::CRYPT_TOKEN,  Resource::CRYPT_TYPE);
        
    }
    
    /**
     * 
     * @param string $text
     * @return string //decrypted text
     */
    public function decrypt($text)
    {
        return self::crypting($text, Resource::CRYPT_TOKEN,  Resource::CRYPT_TYPE, false);
    }

    /**
     * 
     * @param string $text
     * @param string $password
     * @param string $encryptType
     * @param bool $encryptIt
     * @return string
     */
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
        
        $ivlen = openssl_cipher_iv_length($encryptType );
        $iv = str_repeat("z", $ivlen);
        
        if ($encryptIt) {
            
            $newText = openssl_encrypt($text, $encryptType, $password, OPENSSL_RAW_DATA,  $iv );
        } else {
            $newText = openssl_decrypt($text, $encryptType, $password, OPENSSL_RAW_DATA,  $iv );
        }

        return $newText;
    }

}
