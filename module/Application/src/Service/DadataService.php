<?php

// src\Service\Factory\DadataService.php

namespace Application\Service;

/**
 * DadataService
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

    public function __construct($url, $token = "", $secret = "")
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
