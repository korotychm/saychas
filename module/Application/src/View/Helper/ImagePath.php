<?php
namespace Application\View\Helper;

use Laminas\View\Helper\AbstractHelper;

/**
 * This view helper is used to get path to images
 */
class ImagePath extends AbstractHelper 
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
        $path = $baseUrl.'/'.$subpath[$pathFragment];
        
        return $path;
    }
}
