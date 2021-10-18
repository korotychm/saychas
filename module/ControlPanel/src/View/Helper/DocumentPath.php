<?php

// /module/src/View/Helper/DocumentPath.php

namespace ControlPanel\View\Helper;

use Laminas\View\Helper\AbstractHelper;

/**
 * This view helper is used to get path to provider document
 */
class DocumentPath extends AbstractHelper
{

    private $providerId = null;
    private $documentPath = '';

    public function __construct($providerId, $documentPath)
    {
        $this->providerId = $providerId;
        $this->documentPath = $documentPath;
    }

    /**
     * Gets path to a provider's document
     *
     * @param string $catalog
     * @param array $params
     * @return string
     */
    public function __invoke(string $catalog, array $params = []): string
    {
        if (isset($params['provider_id'])) {
            $this->providerId = $params['provider_id'];
        }

        $path = "public{$this->documentPath}/P_{$this->providerId}/$catalog";
        return $path;
    }

}
