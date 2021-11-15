<?php

// src\Service\Factory\ImageHelperFunctionsService.php

namespace Application\Service;

//use Laminas\Config\Config;
//use Laminas\Json\Json;
//use Laminas\Session\Container;
//use Application\Resource\Resource;
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

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $secret;

    public function __construct($url, $token="", $secret="")
    {
        $this->url = $url;
        $this->token = $token;
        $this->secret = $secret;
    }

    /**
     * @param array $data
     * @return json
     */
    public function getDadata($data)
    {
        $options = ['method' => 'POST', 'content' => json_encode($data),];
        $options['header'] = ['Content-type: application/json', 'Authorization: Token ' . $this->token, 'X-Secret: ' . $this->secret,];
        $context = stream_context_create(["http" => $options]);
        $result = file_get_contents($this->url, false, $context);
        return $result;
    }

}
