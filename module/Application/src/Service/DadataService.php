<?php

// src\Service\Factory\ImageHelperFunctionsService.php

namespace Application\Service;

use Laminas\Config\Config;
//use Laminas\Json\Json;
//use Laminas\Session\Container;
use Application\Resource\Resource;
//use Application\Model\Entity\ReviewImage;

//use Application\Model\Entity\ProductFavorites;
//use Application\Model\Entity\Basket;
//use Application\Model\RepositoryInterface\HandbookRelatedProductRepositoryInterface;

/**
 * Description of DadataService
 *
 */
class DadataService
{

    

    private $url,
            $token;
            

    public function __construct($url, $token, $secret ) {
        $this->url = $url;
        $this->token = $token;
        $this->secret = $secret;
        
    }

    public function clean($data) {
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => array(
                    'Content-type: application/json',
                    'Authorization: Token ' . $this->token,
                    'X-Secret: ' . $this->secret
                    ),
                'content' => json_encode($data),
            ),
        );

        $context = stream_context_create($options);
        $result = file_get_contents($this->url, false, $context);
        return $result;
    }
    
    
    
}
    