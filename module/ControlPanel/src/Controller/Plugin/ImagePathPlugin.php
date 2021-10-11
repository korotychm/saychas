<?php

// ControlPanel/src/Controller/Plugin/ImagePathPlugin.php

namespace ControlPanel\Controller\Plugin;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * This controller plugin is used to access image path from controller;
 */
class ImagePathPlugin extends AbstractPlugin
{


    private $imagePath = null;

    public function __construct(array $imagePath)
    {
        $this->imagePath = $imagePath;
    }

    public function __invoke(string $pathFragment, $params = [])
    {
        $baseUrl = $this->imagePath['base_url'];
        $subpath = $this->imagePath['subpath'];
        $path = $baseUrl . '/' . $subpath[$pathFragment];

        return $path;
    }
    
}
