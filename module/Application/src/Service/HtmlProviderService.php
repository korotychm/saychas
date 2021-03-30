<?php
// Application\src\Service\HtmlProviderService.php

namespace Application\Service;

use Application\Service\ServiceInterface\HtmlProviderServiceInterface;

class HtmlProviderService implements HtmlProviderServiceInterface
{
    /**
     * Returns Html string
     * @return string
     */
    public function testHtml()
    {
        return '<h1>Hello world!!!</h1>';
    }
    
}