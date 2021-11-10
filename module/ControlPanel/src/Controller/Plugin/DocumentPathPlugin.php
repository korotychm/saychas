<?php

// ControlPanel/src/Controller/Plugin/DocumentPathPlugin.php

namespace ControlPanel\Controller\Plugin;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * This controller plugin is used to access image path from controller;
 */
class DocumentPathPlugin extends AbstractPlugin
{


    private $path = null;

    /**
     * Base path.
     * 
     * @param array $path
     */
    public function __construct(array $path)
    {
        $this->path = $path;
    }

    public function __invoke(string $catalog, array $params = []): string
    {
        if (isset($params['provider_id'])) {
            $this->providerId = $params['provider_id'];
        }

        $path = "public{$this->path['base_url']}/P_{$this->providerId}/$catalog";
        return $path;
    }
    
    
}
