<?php

// /module/src/View/Helper/DocumentPath.php

namespace Application\View\Helper;

use Laminas\View\Helper\AbstractHelper;

/**
 * This view helper is used to get path to provider document
 */
class DocumentPath extends AbstractHelper
{

    private $providerId = null;

    public function __construct($providerId)
    {
        $this->providerId = $providerId;
    }

    public function __invoke($params = [])
    {
//        $baseUrl = $this->documentPath['base_url'];
//        $subpath = $this->documentPath['subpath'];
//        $path = $baseUrl . '/' . $subpath[$pathFragment];

        /**
         * Example
         */
//          var banzaii = "<? //= $this->documentPath(['vonzaii' => 'VONZAII']) ?// >";
//          console.log(banzaii);
        
        return $this->providerId; //$providerId;
    }

}
